<?php
defined('ROOTPATH') OR exit('No direct script access allowed');

class BaseController
{
    protected $GlobalLibraryHandlerObj;
    protected $GlobalValidationControllerObj;
    protected $GlobalInterfaceControllerObj;

    public function __construct()
    {
        $this->GlobalLibraryHandlerObj = new GlobalLibraryHandler();
        $this->GlobalValidationControllerObj = new GlobalValidationController();
        $this->GlobalInterfaceControllerObj = new GlobalInterfaceController();

        // moved from ajax controller
        $this->GlobalLibraryHandlerObj->checkRunTimeFolderExistance();
    }

    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}