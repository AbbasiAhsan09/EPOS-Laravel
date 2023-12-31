<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Spatie\Dropbox\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Spatie\Dropbox\TokenProvider;


class DBBackupController extends Controller
{
    public $folderPath;
    

    public function __construct()
    {
       $this->folderPath = '/'.env('DROPBOX_BACKUP_PATH').'/'; 
    }


    public function DbBackup()
{
    // Create a backup of the default database connection
    $database = config('database.default');
    $backupPath = storage_path('app/backups');
    $filename = 'backup_' . date('Y-m-d_His') . '.sql';
    
    $descriptorSpec = [
        0 => ['pipe', 'r'], // stdin
        1 => ['pipe', 'w'], // stdout
        2 => ['pipe', 'w'], // stderr
    ];
    
    $command = sprintf(
        '"C:\\laragon\\bin\\mysql\\mysql-5.7.33-winx64\\bin\\mysqldump" --user=%s --password=%s --host=%s %s',
        escapeshellarg(config("database.connections.{$database}.username")),
        escapeshellarg(config("database.connections.{$database}.password")),
        escapeshellarg(config("database.connections.{$database}.host")),
        escapeshellarg(config("database.connections.{$database}.database"))
    );

    $process = proc_open($command, $descriptorSpec, $pipes, $backupPath);

    if (is_resource($process)) {
        fclose($pipes[0]); // Close stdin as we're not writing any input
        
        $backupFile = fopen("{$backupPath}/{$filename}", 'w');
        stream_copy_to_stream($pipes[1], $backupFile); // Read stdout and write to backup file
        fclose($pipes[1]);
        
        $errors = stream_get_contents($pipes[2]); // Read stderr
        
        fclose($backupFile);
        proc_close($process);
        
        // if (!empty($errors)) {
        //     dump($errors);
        //     // Handle errors if needed
        //     return 'Backup creation failed!';
        // }

        // Optional: If you want to gzip the backup file
        $gzipProcess = new Process(['gzip', "{$backupPath}/{$filename}"]);
        $gzipProcess->run();

        // Optional: Clean up older backups if needed
        $this->cleanUpOldBackups($backupPath);
        $file = $this->getLastFileFromFolder(storage_path('app/backups'));
        $this->uploadFile($file);

        return 'Backup created and uploaded!';
    }

    return 'Failed to start backup process!';
}

    private function cleanUpOldBackups($backupPath)
    {
        $files = \File::files($backupPath);
        $maxBackups = 3;

        if (count($files) > $maxBackups) {
            $sortedFiles = collect($files)->sortByDesc(function ($file) {
                return $file->getMTime();
            });

            $oldestFiles = $sortedFiles->splice($maxBackups);

            foreach ($oldestFiles as $file) {
                \File::delete($file);
            }
        }
    }


    public function getLastFileFromFolder($folderPath)
    {
        // Get all files in the folder
        $files = \File::files($folderPath);

        // Sort the files by last modified timestamp in descending order
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        // Get the first file from the sorted array (last modified)
        $lastFile = $files[0] ?? null;

        // Return the file path or do further processing
        return $lastFile ? $lastFile : null;
    }

    public function uploadFile($file)
{

    // dd($file);
    // Generate a unique file name
    $fileName = $file->getFileName();
    // dd($fileName);

    // Store the file locally (optional)
    // $file->storeAs('uploads', $fileName);

    // Get an instance of the Dropbox client
    $tokenGet = $this->getToken(config('services.dropbox.app_key'),config('services.dropbox.secret'), config('services.dropbox.refresh_token'));
    $dropboxClient = new Client($tokenGet);
    $this->clearDropBoxFolder($this->folderPath);
    // Upload the file to Dropbox
    $dropboxClient->upload(
        $this->folderPath . $fileName,
        fopen(storage_path('app/backups/' . $fileName), 'rb')
    );

    
    // Delete the file from local storage if desired
    // Storage::delete('uploads/' . $fileName);

    // Perform any other necessary tasks

    return('File uploaded successfully');
}


public function clearDropBoxFolder($path)
{
    // Instantiate the Dropbox client
    $tokenGet = $this->getToken(config('services.dropbox.app_key'),config('services.dropbox.secret'), config('services.dropbox.refresh_token'));
    $dropboxClient = new Client($tokenGet);

    // Specify the folder path to delete files from
    $folderPath = $this->folderPath;

    // Get a list of all files in the folder
    $files = $dropboxClient->listFolder($folderPath);

    // Sort files by modification time (you might adjust this based on your criteria)
    usort($files['entries'], function ($a, $b) {
        return strtotime($a['client_modified']) - strtotime($b['client_modified']);
    });

    // Keep the last file in the array (to exclude it from deletion)
    $lastFile = array_pop($files['entries']);

    // Loop through the files (except the last one) and delete them
    foreach ($files['entries'] as $file) {
        $dropboxClient->delete($file['path_display']);
    }
    
    // Optionally, return information about the last file
    return $lastFile;
}

public function getToken($key, $secret, $refreshToken)
{
    try {
        $client = new \GuzzleHttp\Client();
        $res = $client->request("POST", "https://{$key}:{$secret}@api.dropbox.com/oauth2/token", [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]
        ]);
        
        if ($res->getStatusCode() == 200) {
            return json_decode($res->getBody(), TRUE)['access_token'];
        } else {
            info(json_decode($res->getBody(), TRUE));
            return false;
        }
    } catch (\Exception $e) {
        return $e;
        return false;
    }


}

}