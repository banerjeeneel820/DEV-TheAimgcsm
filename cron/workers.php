<?php
error_reporting(0);
//ini_set("display_errors", 1);

if (php_sapi_name() !== 'cli') {
    // If not called from CLI, stop immediately
    http_response_code(403);
    exit('Access forbidden.');
}

include_once(__DIR__ . "/../admin/constants.php");

class SiteBackupWorker
{
    private $globalController;
    private $globalLibrary;
    private $logFile;
    private $startTime;

    public function __construct()
    {
        $this->globalController = new GlobalInterfaceController();
        $this->globalLibrary = new GlobalLibraryHandler();
        $this->logFile = __DIR__ . '/queue_jobs.log';
        $this->startTime = date('Y-m-d H:i:s');

        $this->ensureLogFile();
        $this->checkRunningTask();
    }

    private function checkRunningTask()
    {
        // Check if there's a running task
        $checkRunningTask = $this->globalController->check_Task_Status("running");
        if (!empty($checkRunningTask)) {
            echo "ℹ️ There is already a running task on the process.\n";
            exit;
        }
    }

    private function ensureLogFile()
    {
        if (!file_exists($this->logFile)) {
            $handle = fopen($this->logFile, 'w');
            if ($handle) {
                fclose($handle);
                chmod($this->logFile, 0644);
            }
        }
    }

    public function create_Site_Backup_Queue_Job()
    {

        $formDataArr['action'] = "create";
        $formDataArr['job_type'] = "site_backup_creation";

        //Call create queue job method
        $createRspns = $this->globalController->manage_Queue_Jobs($formDataArr);

        if ($createRspns['check'] == "success") {
            echo "✔️ Backup process has been successfully queued.\n";
            $cronLogArr = array('check' => 'success', "message" => "Backup job is successfully queued!");
        } else {
            $cronLogArr = array('check' => 'failure', "message" => "Something went wrong, please try later!");
        }

        // Log cron response into file
        $this->globalLibrary->logServerData($this->logFile, $cronLogArr);
    }

    public function run()
    {
        try {
            // Check if there's a pending task
            $checkPendingTask = $this->globalController->check_Task_Status();

            if (empty($checkPendingTask)) {
                echo "ℹ️ No pending backup tasks.\n";
                // Queue a backup job for future
                //$this->create_Site_Backup_Queue_Job();
                return;
            }

            echo "✔️ Backup process has been initiated...\n";

            $siteBackupFiles = $this->globalLibrary->fetchSiteBackupFiles();

            // DB file path
            $dbFilePath = SITE_BACKUP_DIR . 'theaimgcsm_' . date('Y-m-d_H-i-s') . '_' . time() . '_db_backup.sql';

            //Upload file path
            $uploadsFilePath = SITE_BACKUP_DIR . 'uploads_' . date('Y-m-d_H-i-s') . '_' . time() . '_backup.zip';

            // Updating status of cuurent task to running
            $updateCronArr = [
                'action' => "update",
                'status' => "running",
            ];
            $this->globalController->manage_Queue_Jobs($updateCronArr);

            $dbFileCreated = $this->globalLibrary->createDBBak($dbFilePath);
            $endTime = date('Y-m-d H:i:s');

            if ($dbFileCreated) {
                $zipFileCreated = $this->globalLibrary->createUploadsZip($uploadsFilePath);
                $endTime = date('Y-m-d H:i:s');

                if ($zipFileCreated) {
                    // Delete previous site backup files
                    foreach ($siteBackupFiles as $file) {
                        $file_url = SITE_BACKUP_DIR . $file->name;
                        unlink($file_url);
                    }

                    $cronLogArr = [
                        'status' => 'success',
                        'task__id' => $checkPendingTask->id,
                        'message' => "Backup successfully created between: {$this->startTime} to {$endTime}"
                    ];
                } else {
                    unlink($dbFilePath);
                    $cronLogArr = [
                        'status' => 'failure',
                        'task__id' => $checkPendingTask->id,
                        'message' => "Backup failed: uploads backup failed between: {$this->startTime} to {$endTime}"
                    ];
                }
            } else {
                $cronLogArr = [
                    'status' => 'failure',
                    'task__id' => $checkPendingTask->id,
                    'message' => "Backup failed: DB backup failed between: {$this->startTime} to {$endTime}"
                ];
            }

            // Log cron response into file
            $this->globalLibrary->logServerData($this->logFile, $cronLogArr);

            // Update task status in DB
            $updateCronArr = [
                'action' => "update",
                'task__id' => $checkPendingTask->id,
                'status' => $cronLogArr['status'] == "success" ? "completed" : "failed",
                'response' => $cronLogArr['message']
            ];
            $this->globalController->manage_Queue_Jobs($updateCronArr);

            echo "\n✔️ " . $cronLogArr['message'] . "\n";
        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
        }
    }
}

// ---------------------------------
// Run worker
// ---------------------------------
$worker = new SiteBackupWorker();
$worker->run();
