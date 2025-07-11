<?php
include_once(__DIR__ . "/../constants.php");

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

defined('ROOTPATH') or exit('No direct script access allowed');

//print_r($_POST);exit;

$import_table = $_POST['import_table'];
//Creating object for global controller
$GlobalControllerInterfaceObj = new GlobalInterfaceController();
//Creating object for global library
$GlobalLibraryHandlerObj = new GlobalLibraryHandler();

//Checking runtime folder existance
$GlobalLibraryHandlerObj->checkRunTimeFolderExistance();

//Allowed mime types
$allowedFileType = [
   'application/vnd.ms-excel',
   'text/xls',
   'text/xlsx',
   'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
];

if (in_array($_FILES["import_data_file"]["type"], $allowedFileType)) {

   if ($_FILES["import_data_file"]["size"] > 0) {
      $targetPath = USER_UPLOAD_DIR . 'runtime_upload/' . $_FILES['import_data_file']['name'];
      move_uploaded_file($_FILES['import_data_file']['tmp_name'], $targetPath);
  
      $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
      $spreadSheet = $Reader->load($targetPath);
      $excelSheet = $spreadSheet->getActiveSheet();
      $spreadSheetArr = $excelSheet->toArray();
  
      // Remove the header row
      $finalSpreadSheetArr = array_slice($spreadSheetArr, 1);
  
      // Remove empty rows
      $finalSpreadSheetArr = array_filter($finalSpreadSheetArr, function ($row) {
          return array_filter($row); // Removes rows where all values are empty
      });
  
      //print_r($finalSpreadSheetArr);
      //exit;
  }
    
} else {
   echo json_encode(array('check' => 'failure', 'msg' => 'Invalid File Type. Upload Excel File.'));
   return false;
}

switch ($import_table) {

   case 'exam_questions':

      foreach ($finalSpreadSheetArr as $index => $line) {
         if (!empty($line[0]) && !empty($line[1]) && !empty($line[2]) && !empty($line[3]) && !empty($line[4]) && !empty($line[5]) && !empty($line[6])) {

            $exam_id = $line[0];

            //Fetch last questions ordering
            $questionOrderData = $GlobalControllerInterfaceObj->fetch_Last_Question_Ordering($exam_id);

            $paramArr['exam_id'] = $line[0];
            $paramArr['ques'] = $line[1];
            $paramArr['opt1'] = $line[2];
            $paramArr['opt2'] = $line[3];
            $paramArr['opt3'] = $line[4];
            $paramArr['opt4'] = $line[5];
            $paramArr['cor_ans'] = $line[6];
            $paramArr['ordering'] = $questionOrderData->ordering + 1;

            $paramArr['record_status'] = 'active';

            //print_r($paramArr);exit;
            //Insert student data into student table
            $GlobalControllerInterfaceObj->import_Exam_Questions($paramArr);
         } else {
            echo json_encode(array('check' => 'failure', 'message' => 'Improper format of the uploaded file'));
         }
      }
      unlink($targetPath);
      echo json_encode(array('check' => 'success', 'message' => 'Data import is successfully completed.'));

      break;

   case 'city':

      foreach ($finalSpreadSheetArr as $index => $line) {
         if (!empty($line[0])) {
            $paramArr['name'] = $line[0];
            $paramArr['record_status'] = "blocked";

            //Insert category data into category table
            $GlobalControllerInterfaceObj->import_Global_City($paramArr);
         }
      }
      unlink($targetPath);
      echo json_encode(array('check' => 'success', 'message' => 'Data import is successfully completed.'));

      break;
   
   case 'students_monthly_fees':

      $currentUserType = $_SESSION['user_type'];
      $currentUserID = $_SESSION['user_id'];
      $user_role_slug = 'view_due_students';
      //Check user permission for this section
      $action_permission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug);

      if ($action_permission) {
         
         foreach($finalSpreadSheetArr as $index=> $line){ 
            if(!empty($line[0])){  
               $stu_id = $line[0];
               $monthly_course_fees = $line[1];

               //Fetching student details from db
               $studentDetailArr = json_decode(json_encode($GlobalControllerInterfaceObj->fetch_Global_Single_Student($stu_id)),true);

               if($currentUserType == "franchise" && $studentDetailArr['franchise_id'] != $currentUserID){
                  continue;
               }

               $paramArr['stu_id'] = $stu_id;
               $paramArr['monthly_course_fees'] = $monthly_course_fees;

               //Update student monthly course fees
               $GlobalControllerInterfaceObj->update_student_monthly_course_fees($paramArr);
            }   
         }
         unlink($targetPath);
         $response = array('check'=>'success','message'=>'Data import is successfully completed.');
      }else{
         $response = array('check'=>'failure','message'=>"You don't have permission to perform this action.");
      }
              
      echo json_encode($response);

      break;    
}
