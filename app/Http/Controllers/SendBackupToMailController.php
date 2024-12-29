<?php

namespace App\Http\Controllers;

use App\Mail\SendBackupMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendBackupToMailController extends Controller
{
    function getRecentBackupFilename()
    {
        try {
            // Get the path to the directory
            $directory = storage_path('app/EPOS');

            // Get all files in the directory
            $files = File::files($directory);

            // Sort the files by their last modified time
            usort($files, function ($a, $b) {
                return filemtime($b) - filemtime($a);
            });

            // Get the most recent file
            $mostRecentFile = reset($files);

            // Get the filename
            return $mostRecentFile->getFilename();

            // // Get the file path
            // $filePath = $mostRecentFile->getPathname();

            // dd($filename, $filePath);

        } catch (\Throwable $th) {
            throw $th;
        }
    }


    function DbBackup()
    {
        // Creating new backup
        try {
            Artisan::call("backup:run");
        } catch (\Throwable $th) {
            throw $th;
        }

        $this->removeAllBackupExceptRecent(2);
        $filename = $this->getRecentBackupFilename() ?? "";

        if ($filename) {
            $filePath = storage_path('app/EPOS/' . $filename);

            // Check if the file exists
            if (Storage::exists('EPOS/' . $filename)) {
                // Check if the BACKUP_ONLINE flag is set to true
                if (env('BACKUP_ONLINE', false)) {
                    // Send the file as a backup email
                    try {
                        $email = new SendBackupMail($filePath);
                        Mail::to('ahsansarim56@gmail.com')->send($email);
            
                        return response()->json(['Backup sent to the email'], 200);
                    } catch (\Exception $e) {
                        // Log the error if email sending fails
                        \Log::error("Error sending backup email: " . $e->getMessage());
            
                        return response()->json(['error' => 'Failed to send backup email'], 500);
                    }
                }
            
                // If no backup email is required, just download the file
                return response()->download($filePath, $filename);
            } else {
                // File does not exist
                return response()->json(['error' => 'File not found'], 404);
            }
            
        }
    }


    function removeAllBackupExceptRecent($recentCount = 2)
    {
        try {
            // Define the directory path
            $directory = storage_path('app/EPOS/');
            
            // Get all files in the directory
            $files = File::files($directory);
            
            // Sort files by last modified time (you can use other criteria like creation time)
            usort($files, function ($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            
            // Keep the two most recent files, delete the rest
            $keepFiles = array_slice($files, 0, $recentCount);
            
            foreach ($files as $file) {
                if (!in_array($file, $keepFiles)) {
                    // dd($file->getPathname());
                    // Delete the file
                    $path = storage_path('app/EPOS/'.$file->getFilename());
                    unlink($path);
                }
            }

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
