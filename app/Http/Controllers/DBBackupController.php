<?php

namespace App\Http\Controllers;
use Spatie\Dropbox\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

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

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            config("database.connections.{$database}.username"),
            config("database.connections.{$database}.password"),
            config("database.connections.{$database}.host"),
            config("database.connections.{$database}.database"),
            "{$backupPath}/{$filename}"
        );

        exec($command);

        // Optional: If you want to gzip the backup file
        exec("gzip {$backupPath}/{$filename}");
        $files = \File::files($backupPath);


        // Optional: Clean up older backups if needed
        $this->cleanUpOldBackups($backupPath);
        $file = $this->getLastFileFromFolder(storage_path('app/backups'));
        $this->uploadFile($file);

        return 'Backup created and uploaded!';
    }
    private function cleanUpOldBackups($backupPath)
    {
        $files = \File::files($backupPath);
        $maxBackups = 2;

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
    $dropboxClient = new Client(config('services.dropbox.token'));
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
$dropboxClient = new Client(config('services.dropbox.token'));

// Specify the folder path to delete files from
$folderPath = $this->folderPath;

// Get a list of all files in the folder
$files = $dropboxClient->listFolder($folderPath);
// dd($files["entries"] );
// Loop through the files and delete them
foreach ($files["entries"] as $file) {
    $dropboxClient->delete($file['path_display']);
}
}
}
