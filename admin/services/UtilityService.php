<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class UtilityService
{
    private $model;
    private $lib;

    public function __construct($model, $lib)
    {
        $this->model = $model;
        $this->lib = $lib;
    }

    public function getSiteSettings()
    {
        return $this->model->fetch_Global_Site_Setting_Detail();
    }
}