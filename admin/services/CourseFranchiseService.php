<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class CourseFranchiseService
{
    private $memObj;

    public function __construct(
        private GlobalInterfaceModel $model,
        private GlobalLibraryHandler $lib,
    ){}

    public function getFranchiseData($record_status)
    {
        
        $paramArr['record_status'] = $record_status;

        // If no cache system
        if ($this->memObj == null) {
            return $this->model->fetch_Global_Franchise($paramArr);
        }

        $cacheKey = "franchise_data_{$record_status}";

        $cached = $this->memObj->get($cacheKey);

        if ($cached) {
            return $cached;
        }

        $data = $this->model->fetch_Global_Franchise($paramArr);

        $this->memObj->set($cacheKey, $data);

        return $data;
    }

    public function getSingleFranchise($id)
    {
        if (!$id) return [];

        return $this->model->fetch_Global_Single_Franchise($id);
    }

    public function getFranchiseId($data)
    {
        if ($_SESSION['user_type'] === 'franchise') {
            return $_SESSION['user_id'];
        }

        return $data['id'] ?? null;
    }

    public function getCourseData($record_status)
    {
        $cacheKey = "course_data_{$record_status}";

        // If no memcache, fallback directly
        if ($this->memObj === null) {
            return $this->model->fetch_Global_Course($record_status);
        }

        // Try cache
        $response = $this->memObj->get($cacheKey);

        if ($response) {
            return $response;
        }

        // Fetch from DB
        $response = $this->model->fetch_Global_Course($record_status);

        // Store in cache
        $this->memObj->set($cacheKey, $response);

        return $response;
    }

    public function getCourseById($courseId)
    {
        if (empty($courseId)) {
            return [];
        }

        return $this->model->fetch_Global_Single_Course($courseId);
    }

    public function create_Frnachise_ID()
    {
        //Creating new Franchise id method
        $franchiseDetail = $this->model->fetch_Last_Franchise_Detail();
        $last_fran_id = $franchiseDetail[0]->fran_id;

        if ($last_fran_id != null) {
            $last_fran_id_part_2 = substr($last_fran_id, 5);
            $last_fran_id_part_2++;
        } else {
            $last_fran_id_part_2 = 1;
        }

        $current_fran_id = "WBMGF" . $last_fran_id_part_2;

        return $current_fran_id;
    }

    public function checkSlugAvailibility($type, $field, $slug)
    {
        return $this->model
        ->check_Slug_Availibility($type, $field, $slug);
    }

    public function fetch_Active_Course_Franchise_Data()
    {

        $activeCourseFranchiseArr = [];

        //Fetch franchise data based on memcached
        if ($this->memObj == null) {
            $activeCourseFranchiseArr['franchise'] = $this->model->fetch_Global_Franchise("active");
        } else {
            $response = $this->memObj->get("franchise_data_active");
            //Check if data stored in memcached
            if ($response) {
                $activeCourseFranchiseArr['franchise'] = $response;
            } else {
                $response = $this->model->fetch_Global_Franchise("active");
                $this->memObj->set("franchise_data_active", $response);
                //Set data into a key of memcached
                $activeCourseFranchiseArr['franchise'] = $response;
            }
        }

        //Fetch course data based on memcached
        if ($this->memObj == null) {
            $activeCourseFranchiseArr['course'] = $this->model->fetch_Global_Course("active");
        } else {
            $response = $this->memObj->get("course_data_active");
            //Check if data stored in memcached
            if ($response) {
                $activeCourseFranchiseArr['course'] = $response;
            } else {
                $response = $this->model->fetch_Global_Course("active");
                $this->memObj->set("course_data_active", $response);
                //Set data into a key of memcached
                $activeCourseFranchiseArr['course'] = $response;
            }
        }

        return $activeCourseFranchiseArr;
    }

    public function saveFranchiseData($formDataArr){
        return $this->model
        ->manage_Global_Franchise($formDataArr);
    }

    public function saveCourseData($formDataArr){
        return $this->model
        ->manage_Global_Course($formDataArr);
    }
   
}
