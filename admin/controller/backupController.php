<?php
include_once(__DIR__ . "/../constants.php");

defined('ROOTPATH') or exit('No direct script access allowed');

//print_r($_POST);exit;

$action = trim($_POST['action']);

//Creating object for global controller
$GlobalControllerInterfaceObj = new GlobalInterfaceController();
//Creating object for global library
$GlobalLibraryHandlerObj = new GlobalLibraryHandler();

//Checking runtime folder existance
$GlobalLibraryHandlerObj->checkRunTimeFolderExistance();

$checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission("manage_site_backup");
$cookie_name = "backupCount";

setcookie("backupCount", "", time() - 3600, "/");

if ($checkActionPermission) {

    if ($action == "createBackupOnServer") {

        if (!empty($_COOKIE[$cookie_name])) {
            $backupCount = $_COOKIE[$cookie_name];
        } else {
            $backupCount = null;
        }

        $backupLimit = $_SESSION['user_type'] == "developer" ? true : ($backupCount == 2 ? false : true);
        $setCookie = $_SESSION['user_type'] == "developer" ? false : true;

        //var_dump($_COOKIE[$cookie_name]);exit;

        if ($backupLimit) {

            //Fetch current site backup files
            $siteBackupFiles = $GlobalLibraryHandlerObj->fetchSiteBackupFiles();

            // DB file path
            $dbFilePath = SITE_BACKUP_DIR . 'theaimgcsm_' . date('Y-m-d_H-i-s') . '_' . time() . '_db_backup.sql';

            //Upload file path
            $uploadsFilePath = SITE_BACKUP_DIR . 'uploads_' . date('Y-m-d_H-i-s') .'_'. time(). '_backup.zip';

            //Creating database backup file 
            $dbFileCreated = $GlobalLibraryHandlerObj->createDBBak($dbFilePath);

            //Take bakup of uploads folder if database bakup file successfully created
            if ($dbFileCreated) {
                $zipFileCreated = $GlobalLibraryHandlerObj->createUploadsZip($uploadsFilePath);

                if ($zipFileCreated) {
                    //Remove all previous site backup files
                    foreach ($siteBackupFiles as $index => $file) {
                        $file_url = SITE_BACKUP_DIR . $file->name;
                        unlink($file_url);
                    }

                    if ($setCookie) {
                        //Set backup count in cookies
                        $newBackupCount = intval($backupCount + 1);
                        setcookie($cookie_name, $newBackupCount, time() + (86400 * 1), "/");
                    }

                    $returnArr = array('check' => 'success', "message" => "Backup is successfully created!");
                } else {
                    unlink($dbFilePath);
                    $returnArr = array('check' => 'failure', "message" => "Backup failed!");
                }
            } else {
                $returnArr = array('check' => 'failure', "message" => "Backup failed!");
            }
        } else {
            $returnArr = array('check' => 'failure', "message" => "Backup limit exhausted!");
        }
    } else {
        $returnArr = array('check' => 'failure', "message" => "No action found!");
    }
} else {
    $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
}

echo json_encode($returnArr);
