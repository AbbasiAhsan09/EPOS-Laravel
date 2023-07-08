<?php
/*
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Backup\Tasks\Backup\BackupJobFactory;
use Spatie\Backup\Tasks\Cleanup\CleanupJobFactory;
use Spatie\Backup\BackupDestination\BackupDestinationFactory;
use Spatie\Backup\Notifications\Notifiable;
use Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification;

class DatabaseBackupAndEmail extends Command
{
    use Notifiable;

   
    protected $signature = 'backup:email';

    protected $description = 'Backup Database and email ';

    public function handle()
    {
        $this->info('Cleaning up old backups...');

        $cleanupJob = CleanupJobFactory::createFromArray(config('backup'))->run();

        if ($cleanupJob->wasSuccessful()) {
            $this->info('Cleanup completed successfully.');
        } else {
            $this->error('Cleanup failed.');
        }
    }

    protected function sendBackupEmail()
    {
        $this->info('Sending backup via email...');

        $backupDestination = BackupDestinationFactory::createFromArray(config('backup.backup.destination'));

        $backupPath = $backupDestination->getNewestBackup()->path();

        $emailSubject = 'Database Backup - ' . date('Y-m-d H:i:s');
        $emailRecipient = 'ahsanabbasi5657@gmail.com'; // Specify the email address to which you want to send the backup

        // Attach the backup file to the email
        $this->laravel->make('mailer')->send([], [], function ($message) use ($backupPath, $emailSubject, $emailRecipient) {
            $message->to($emailRecipient)
                ->subject($emailSubject)
                ->attach($backupPath);
        });

        $this->info('Backup sent via email.');
    }
}
*/
