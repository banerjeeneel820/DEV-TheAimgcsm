<?php
    include_once(__DIR__."/../constants.php");
    
    use Dompdf\Dompdf; 
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\Style\Border;
    use PhpOffice\PhpSpreadsheet\Style\Color;

    defined('ROOTPATH') OR exit('No direct script access allowed');

    //print_r($_POST);exit;

    $export_table = $_POST['export_table'];
    //Creating object for global controller
    $GlobalInterfaceControllerObj = new GlobalInterfaceController();
    //Creating object for global library
    $GlobalLibraryHandlerObj = new GlobalLibraryHandler();

    //Checking runtime folder existance
    $GlobalLibraryHandlerObj->checkRunTimeFolderExistance();

    $spreadsheet = new Spreadsheet();
    $Excel_writer = new Xlsx($spreadsheet);
    
    $export_html_style = '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">';
    $export_html_style .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';

    //Excel sheet header style array
     $styleArray = [
        'font' => [
            'bold' => true,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
        ],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
         /*'outline' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
            'color' => array('argb' => 'FFFF0000'),
        ],*/
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
            'rotation' => 90,
            'startColor' => [
                'argb' => 'FFFF0000',
            ],
            'endColor' => [
                'argb' => 'FFA0A0A0',
            ],
        ],
    ];

    $export_html_style .= '
       <style>
        /*STYLED TABLE MODIFIED CSS ---*/
        .styled-table {
              border: 2px solid black;
              border-collapse: collapse;
              margin: 25px 0;
              font-size: 0.7em;
              font-family: sans-serif;
              min-width: 800px;
              box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
          }

          .styled-table thead tr {
              background-color: #2548a1;
              color: #ffffff;
              text-align: center;
          }

          .styled-table th,
          .styled-table td {
              padding: 12px 15px;
              border: 2px solid black;
          }

          .styled-table tbody tr {
              border-bottom: 1px solid #dddddd;
          }

          .styled-table tbody tr:nth-of-type(even) {
              background-color: #f3f3f3;
          }

          .styled-table tbody tr:last-of-type {
              border-bottom: 2px solid #009879;
          }

          .styled-table tbody tr.active-row {
              font-weight: bold;
              color: #009879;
          }
          /*------END HERE -----*/

          /*----SELECT 2 MODIFIED CSS ---*/
          .select2-selection__rendered {
                line-height: 31px !important;
            }
            .select2-container .select2-selection--single {
                height: 38px !important;
            }
            .select2-selection__arrow {
                height: 34px !important;
            }
            /*------END HERE -----*/
       </style>';

    switch ($export_table){

        case 'student':

          if(empty($_POST['protocol'])){
            $exportParamArr['record_status'] = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['record_status']));
            
            $exportParamArr['course_id'] = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['course_id']));
            $exportParamArr['franchise_id'] = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['franchise_id']));
            
            $exportParamArr['search_string'] = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['search_string']));

            $exportParamArr['created'] = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['created']));

            $exportParamArr['search_start'] = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['search_start']));
            $exportParamArr['search_end'] = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['search_end']));

            //fetching student data
            $studentDataObj = $GlobalInterfaceControllerObj->fetch_Global_Student_Recipts($exportParamArr);  
            $studentListArr = json_decode(json_encode($studentDataObj),true);
          }
          else{
            $dataArr['fetchType'] = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['fetchType']));

            //fetching student data
            $studentDataObj = $GlobalInterfaceControllerObj->fetch_Dashboard_Student_Data($dataArr);  
            $studentListArr = json_decode(json_encode($studentDataObj['data']),true); 
          }

          $export_method = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['export_method']));
                  
          //print_r($studentListArr);exit;
          
          if(count($studentListArr)>0){
              if($export_method == "pdf"){
                 
                  $export_html = $export_html_style;

                  $export_html .= '
                    <div class="container px-0">
                      <div class="row mt-4">
                         <div class="col-12 col-lg-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center text-150">
                                        <i class="fa fa-book fa-2x text-success-m2 mr-1"></i>
                                        <span class="text-default-d3" style="font-weight:600;font-size:18px;color:green;">THE AIMGCSM STUDENT RECORDS</span>
                                        <i class="fa fa-book fa-2x text-success-m2 mr-1"></i>
                                    </div>
                                </div>
                             </div>    
                            <hr class="row brc-default-l1 mx-n1 mb-4" />
                          </div>
                       </div>  

                       <div class="row mt-4"> 
                          <table class="styled-table text-center">
                           <thead class="cursor-pointer">
                               <tr>
                                <th>SL No.</th>
                                <th>Name & Father Name</th>
                                <th>Phone & Email</th>
                                <th>Student ID & Course</th>
                                <th>Franchise & Course Season</th>
                                <th>Studen Gender & Date of Birth</th>
                                <th>Qualification & Marital Status</th>
                                <th>Student Status & Result</th>
                                <th>Rceipt Created for Student</th>
                               </tr>
                            </thead>';

                         foreach($studentListArr as $index => $student){
                            $export_html .= '
                              <tbody>
                                 <tr>
                                  <td>'.($index+1).'</td>
                                  <td>
                                    <b>Student Name:</b> '.$student["stu_name"].'<br><br>
                                    <b>Father Name:</b> '.$student["stu_father_name"].  
                                  '</td>
                                  <td>
                                    <b>Contact No:</b> '.$student["stu_phone"].'<br><br>
                                    <b>Email:</b> '.$student["stu_email"].  
                                  '</td>
                                  <td>
                                    <b>Student ID:</b> '.$student["stu_id"].'<br><br>
                                    <b>Course:</b> '.$student["course_title"].  
                                  '</td>
                                  <td>
                                    <b>Franchise:</b> '.$student["center_name"].'<br><br>
                                  </td>
                                  <td>
                                    <b>Gender:</b> '.ucfirst($student["stu_gender"]).'<br><br>
                                    <b>DOB:</b> '.date("jS F, Y",strtotime($student["stu_dob"])).  
                                  '</td>
                                   <td>
                                    <b>Qualification:</b> '.$student["stu_qualification"].'<br><br>
                                    <b>Marital Status:</b> '.ucfirst($student["stu_marital_status"]).  
                                  '</td>
                                   <td>
                                    <b>Student Status:</b> '.ucfirst($student["student_status"]).'<br><br>
                                    <b>Result:</b> '.ucfirst($student["stu_result"]).  
                                  '</td>
                                  <td>'
                                    .$student["receipt_count"].
                                  '</td>
                                 </tr>
                               </tbody>';
                          }
                          $export_html .= '</table></div></div>';

                   //echo $export_html;exit;   

                   $file_upload_dir =  USER_UPLOAD_DIR.'runtime_upload/'."Student_Export_Data.pdf";
                   $file_url = USER_UPLOAD_URL.'runtime_upload/'."Student_Export_Data.pdf";

                   $dompdf = new Dompdf();
                   $dompdf->set_option('isRemoteEnabled', true);
                   $dompdf->set_paper("a4", "landscape");
                   $dompdf->load_html($export_html);
                   $dompdf->render();
                   $file = $dompdf->output();
                   file_put_contents($file_upload_dir, $file);

                   echo json_encode(array('check'=> 'success','file_upload_dir'=>$file_upload_dir,'file_url'=>$file_url));
                   exit;
              }else{
             
              $spreadsheet->setActiveSheetIndex(0);
              $activeSheet = $spreadsheet->getActiveSheet();

              $spreadsheet->getActiveSheet()->getStyle('A1:P1')->applyFromArray($styleArray);
              $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
              $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
              
              //Set sheet header cloumn width
              $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(40, 'pt');
              $cellHeaderArr = array('B','C','D','E','F','G','H','I','J','K','L','M','N','O','P');

              foreach($cellHeaderArr as $cell){
                $spreadsheet->getActiveSheet()->getColumnDimension($cell)->setWidth(130, 'pt');//setAutoSize(true);
              }
              //cell text alignment
              $spreadsheet->getActiveSheet()->getStyle('A1:P1')->getAlignment()->setHorizontal('center');
              $spreadsheet->getActiveSheet()->getStyle('A1:P1')->getAlignment()->setVertical('center');
                
              $activeSheet->setCellValue('A1', 'SL No.');
              $activeSheet->setCellValue('B1', 'Student Name');
              $activeSheet->setCellValue('C1', "Father's Name");
              $activeSheet->setCellValue('D1', 'Student Email');
              $activeSheet->setCellValue('E1', 'Contact No');
              $activeSheet->setCellValue('F1', 'Student ID');
              $activeSheet->setCellValue('G1', 'Course');
              $activeSheet->setCellValue('H1', 'Franchise');
              $activeSheet->setCellValue('I1', 'Date of Birth');
              $activeSheet->setCellValue('J1', 'Gender');
              $activeSheet->setCellValue('K1', 'Qualification');
              $activeSheet->setCellValue('L1', 'Student Address');
              $activeSheet->setCellValue('M1', 'Marital Status');
              $activeSheet->setCellValue('N1', 'Student Status');
              $activeSheet->setCellValue('O1', 'Receipt Count');
              $activeSheet->setCellValue('P1', 'Result');

              if(count($studentListArr) > 0) {
                  $i = 2;
                  foreach($studentListArr as $index => $student){

                      if($student['student_status'] == "course_complete"){
                         $student_status = "Course Complete";
                      }else{
                         $student_status = ucfirst($student['student_status']);
                      }
                      //cell text alignment
                      $spreadsheet->getActiveSheet()->getStyle('A'.$i.':P'.$i)->getAlignment()->setHorizontal('center');
                      $spreadsheet->getActiveSheet()->getStyle('A'.$i.':P'.$i)->getAlignment()->setVertical('center');

                      //Wrap text
                      $spreadsheet->getActiveSheet()->getStyle('A'.$i.':P'.$i)->getAlignment()->setWrapText(true);    

                      $activeSheet->setCellValue('A'.$i , $index+1);
                      $activeSheet->setCellValue('B'.$i , $student['stu_name']);
                      $activeSheet->setCellValue('C'.$i , $student['stu_father_name']);
                      $activeSheet->setCellValue('D'.$i , $student['stu_email']);
                      $activeSheet->setCellValue('E'.$i , $student['stu_phone']);
                      $activeSheet->setCellValue('F'.$i , $student['stu_id']);
                      $activeSheet->setCellValue('G'.$i , $student['course_title']);
                      $activeSheet->setCellValue('H'.$i , $student['center_name']);
                      $activeSheet->setCellValue('I'.$i , date("jS F, Y",strtotime($student['stu_dob'])));
                      $activeSheet->setCellValue('J'.$i , ucfirst($student['stu_gender']));
                      $activeSheet->setCellValue('K'.$i , $student['stu_qualification']);
                      $activeSheet->setCellValue('L'.$i , $student['stu_address']);
                      $activeSheet->setCellValue('M'.$i , ucfirst($student['stu_marital_status']));
                      $activeSheet->setCellValue('N'.$i , $student_status);
                      $activeSheet->setCellValue('O'.$i , $student['receipt_count']);
                      $activeSheet->setCellValue('P'.$i , ucfirst($student['stu_result']));
                      $i++;
                  }
              }
              
              $file_upload_dir =  USER_UPLOAD_DIR.'runtime_upload/Student_Data.xlsx';
              $file_url = USER_UPLOAD_URL.'runtime_upload/Student_Data.xlsx';
  
              $Excel_writer->save($file_upload_dir);

              echo json_encode(array('check'=> 'success','file_upload_dir'=>$file_upload_dir,'file_url'=>$file_url));

           }    
          }else{
            echo json_encode(array('check'=> 'failure','msg'=>"No recoed found to export."));
          } 
       
          break;

       case 'receipt':
          if(empty($_POST['protocol'])){
            $exportParamArr['record_status'] = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['record_status']));

            $exportParamArr['course_id'] = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['course_id']));
            $exportParamArr['franchise_id'] = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['franchise_id']));

            $exportParamArr['created'] = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['created']));

            if(!empty($_POST['receipt_season_start'])){
              $receipt_season_start = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['receipt_season_start']));
              $receipt_season_start = str_replace('/', '-', $receipt_season_start);
              $exportParamArr['receipt_season_start'] = date('Y-m-d', strtotime($receipt_season_start));
            }  

            if(!empty($_POST['receipt_season_end'])){
              $receipt_season_end = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['receipt_season_end']));
              $receipt_season_end = str_replace('/', '-', $receipt_season_end);
              $exportParamArr['receipt_season_end'] = date('Y-m-d', strtotime($receipt_season_end));
            }  

            if(!empty($_POST['student_id'])){
                $exportParamArr['student_id'] = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['student_id']));
               //fetching receipt data
               $receiptDataObj = $GlobalInterfaceControllerObj->fetch_Single_Student_Receipt($exportParamArr['student_id'],$exportParamArr);  
               $receiptListArr = json_decode(json_encode($receiptDataObj),true);
            }else{
               //fetching receipt data
               $receiptDataObj = $GlobalInterfaceControllerObj->fetch_Global_Receipts($exportParamArr);  
               $receiptListArr = json_decode(json_encode($receiptDataObj),true);  
            }
            
          }elseif($_POST['protocol'] == "dashboard"){
            $dataArr['fetchType'] = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['fetchType']));
            //fetching student data
            $receiptDataObj = $GlobalInterfaceControllerObj->fetch_Dashboard_Receipt_Data($dataArr);  
            $receiptListArr = json_decode(json_encode($receiptDataObj['data']),true); 
          }

          $export_method = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['export_method']));
                  
          //print_r($receiptListArr);exit;
          
          if(count($receiptListArr)>0){
              if($export_method == "pdf"){
                 
                  $export_html = $export_html_style;

                  $export_html .= '
                    <div class="container px-0">
                      <div class="row mt-4">
                         <div class="col-12 col-lg-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center text-150">
                                        <i class="fa fa-book fa-2x text-success-m2 mr-1"></i>
                                        <span class="text-default-d3" style="font-weight:600;font-size:18px;color:green;">THE AIMGCSM STUDENT RECEIPT RECORDS</span>
                                        <i class="fa fa-book fa-2x text-success-m2 mr-1"></i>
                                    </div>
                                </div>
                             </div>    
                            <!-- .row -->
                            <hr class="row brc-default-l1 mx-n1 mb-4" />
                          </div>
                       </div>  

                       <div class="row mt-4"> 
                          <table class="styled-table text-center">
                           <thead class="cursor-pointer">
                               <tr>
                                <th>SL No.</th>
                                <th>Receipt ID</th>
                                <th>Receipt Created</th>
                                <th>Receipt Amount</th>
                                <th>Student Name</th>
                                <th>Student Contact No & Email</th>
                                <th>Course & Franchise</th>
                                <th>Student ID & Result</th>
                               </tr>
                            </thead>';

                         foreach($receiptListArr as $index => $receipt){
                           
                              $export_html .= '
                                <tbody>
                                   <tr>
                                    <td>'.($index+1).'</td>
                                    <td>'
                                      .$receipt["receipt_id"].
                                    '</td>
                                    <td>'.date('jS F, Y',strtotime($receipt["created_at"])).'</td>
                                    <td>'.$receipt["receipt_amount"].'</td>
                                    <td>'.$receipt["stu_name"].'</td>
                                    <td>
                                      <b>Contact No:</b> '.$receipt["stu_phone"].'<br><br>
                                      <b>Email:</b> '.$receipt["stu_email"].  
                                    '</td>
                                    <td>
                                      <b>Course:</b> '.$receipt["course_title"].'<br><br>  
                                      <b>Franchise:</b> '.$receipt["center_name"].'
                                    </td>
                                    <td>
                                      <b>Student ID:</b> '.$receipt["stu_id"].'<br><br>
                                      <b>Result:</b> '.ucfirst($receipt["stu_result"]).  
                                    '</td>
                                   </tr>
                                 </tbody>';
                         }
                         $export_html .= '</table></div></div>';

                   //echo $export_html;exit;   
                   
                   $file_upload_dir =  USER_UPLOAD_DIR.'runtime_upload/'."Student_Export_Data.pdf";
                   $file_url = USER_UPLOAD_URL.'runtime_upload/'."Student_Export_Data.pdf";

                   $dompdf = new Dompdf();
                   //$dompdf->set_option('isRemoteEnabled', true);
                   $dompdf->set_paper("a4", "landscape");
                   $dompdf->load_html($export_html);
                   $dompdf->render();
                   $file = $dompdf->output();
                   file_put_contents($file_upload_dir, $file);

                   echo json_encode(array('check'=> 'success','file_upload_dir'=>$file_upload_dir,'file_url'=>$file_url));
                   exit;
              }else{

              $spreadsheet->setActiveSheetIndex(0);
              $activeSheet = $spreadsheet->getActiveSheet();

              $spreadsheet->getActiveSheet()->getStyle('A1:K1')->applyFromArray($styleArray);
              $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
              $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
              
              //Set sheet header cloumn width
              $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(40, 'pt');
              $cellHeaderArr = array('B','C','D','E','F','G','H','I','J','K');

              foreach($cellHeaderArr as $cell){
                $spreadsheet->getActiveSheet()->getColumnDimension($cell)->setWidth(130, 'pt');//setAutoSize(true);
              }
              //cell text alignment
              $spreadsheet->getActiveSheet()->getStyle('A1:K1')->getAlignment()->setHorizontal('center');
              $spreadsheet->getActiveSheet()->getStyle('A1:K1')->getAlignment()->setVertical('center');
                
              $activeSheet->setCellValue('A1', 'SL No.');
              $activeSheet->setCellValue('B1', 'Receipt ID');
              $activeSheet->setCellValue('C1', 'Receipt Created');
              $activeSheet->setCellValue('D1', 'Receipt Amount');
              $activeSheet->setCellValue('E1', 'Student Name');
              $activeSheet->setCellValue('F1', 'Student Email');
              $activeSheet->setCellValue('G1', 'Contact No');
              $activeSheet->setCellValue('H1', 'Student ID');
              $activeSheet->setCellValue('I1', 'Student Result');
              $activeSheet->setCellValue('J1', 'Course');
              $activeSheet->setCellValue('K1', 'Franchise');

              if(count($receiptListArr) > 0) {
                  $i = 2;
                  foreach($receiptListArr as $index => $receipt){                      
                      //cell text alignment
                      $spreadsheet->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getAlignment()->setHorizontal('center');
                      $spreadsheet->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getAlignment()->setVertical('center');

                      //Wrap text
                      $spreadsheet->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getAlignment()->setWrapText(true);   

                      $activeSheet->setCellValue('A'.$i , $index+1);
                      $activeSheet->setCellValue('B'.$i , $receipt['receipt_id']);
                      $activeSheet->setCellValue('C'.$i , date('jS F, Y',strtotime($receipt["created_at"])));
                      $activeSheet->setCellValue('D'.$i , "Rs. ".$receipt['receipt_amount']);
                      $activeSheet->setCellValue('E'.$i , $receipt['stu_name']);
                      $activeSheet->setCellValue('F'.$i , $receipt['stu_email']);
                      $activeSheet->setCellValue('G'.$i , $receipt['stu_phone']);
                      $activeSheet->setCellValue('H'.$i , $receipt['stu_id']);
                      $activeSheet->setCellValue('I'.$i , ucfirst($receipt['stu_result']));
                      $activeSheet->setCellValue('J'.$i , $receipt['course_title']);
                      $activeSheet->setCellValue('K'.$i , $receipt['center_name']);
                      $i++;
                  }
              }

              $file_upload_dir =  USER_UPLOAD_DIR.'runtime_upload/Receipt_Data.xlsx';
              $file_url = USER_UPLOAD_URL.'runtime_upload/Receipt_Data.xlsx';
  
              $Excel_writer->save($file_upload_dir);

              echo json_encode(array('check'=> 'success','file_upload_dir'=>$file_upload_dir,'file_url'=>$file_url));
           }    
          }else{
             echo json_encode(array('check'=> 'failure','msg'=>"No recoed found to export.")); 
          } 
       
          break;

      case 'franchise':
          
          $record_status = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['record_status']));
           
          //fetching student data
          $franchiseDataObj = $GlobalInterfaceControllerObj->fetch_Global_Franchise($record_status);  
          $franchiseListArr = json_decode(json_encode($franchiseDataObj),true);
          
          $export_method = mysqli_real_escape_string(DB::$WRITELINK,trim($_POST['export_method']));
                  
          //print_r($franchiseListArr);exit;

          if($export_method == "pdf"){
             
              $export_html = $export_html_style;

              $export_html .= '
                <div class="container px-0">
                  <div class="row mt-4">
                     <div class="col-12 col-lg-12">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center text-150">
                                    <i class="fa fa-book fa-2x text-success-m2 mr-1"></i>
                                    <span class="text-default-d3" style="font-weight:600;font-size:18px;color:green;">THE AIMGCSM STUDENT RECEIPT RECORDS</span>
                                    <i class="fa fa-book fa-2x text-success-m2 mr-1"></i>
                                </div>
                            </div>
                         </div>    
                        <!-- .row -->
                        <hr class="row brc-default-l1 mx-n1 mb-4" />
                      </div>
                   </div>  

                   <div class="row mt-4"> 
                      <table class="styled-table text-center">
                       <thead class="cursor-pointer">
                           <tr>
                            <th>SL No.</th>
                            <th>Franchise Name</th>
                            <th>Owner Name</th>
                            <th>Franchise ID</th>
                            <th>Franchise Contact No & Email</th>
                            <th>Franchise Address</th>
                            <th>Status & Total No. of Student Enrolled</th>
                           </tr>
                        </thead>';

                    foreach($franchiseListArr as $index => $franchise){
                       
                      $export_html .= '
                        <tbody>
                           <tr>
                            <td>'.($index+1).'</td>
                            <td>'
                              .$franchise["center_name"].
                            '</td>
                            <td>'.$franchise["owner_name"].'</td>
                            <td>'.$franchise["fran_id"].'</td>
                            <td>
                              <b>Contact No:</b> '.$franchise["fran_phone"].'<br><br>
                              <b>Email:</b> '.$franchise["fran_email"].  
                            '</td>
                            <td>'.$franchise["fran_address"].'</td>
                            <td>
                              <b>Student ID:</b> '.ucfirst($franchise["record_status"]).'<br><br>
                              <b>Total no of Enrolled Student Count:</b> '.$franchise["enrolled_student_count"].  
                            '</td>
                           </tr>
                         </tbody>';
                     }
                     $export_html .= '</table></div></div>';

               //echo $export_html;exit;   
               
               $file_upload_dir =  USER_UPLOAD_DIR.'runtime_upload/'."Franchise_Export_Data.pdf";
               $file_url = USER_UPLOAD_URL.'runtime_upload/'."Franchise_Export_Data.pdf";

               $dompdf = new Dompdf();
               //$dompdf->set_option('isRemoteEnabled', true);
               $dompdf->set_paper("a4", "landscape");
               $dompdf->load_html($export_html);
               $dompdf->render();
               $file = $dompdf->output();
               file_put_contents($file_upload_dir, $file);

               echo json_encode(array('check'=> 'success','file_upload_dir'=>$file_upload_dir,'file_url'=>$file_url));
               exit;
           }else{
             
              $spreadsheet->setActiveSheetIndex(0);
              $activeSheet = $spreadsheet->getActiveSheet();
              
              //Styling the sheet
              //$spreadsheet->getActiveSheet()->getStyle('A1:I1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF0000');

              $spreadsheet->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleArray);
              $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
              $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
              
              //Set sheet header cloumn width
              $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(40, 'pt');

              $cellHeaderArr = array('B','C','D','E','F','G','H','I');

              foreach($cellHeaderArr as $cell){
                $spreadsheet->getActiveSheet()->getColumnDimension($cell)->setWidth(150, 'pt');//setAutoSize(true);
              }
              //cell text alignment
              $spreadsheet->getActiveSheet()->getStyle('A1:I1')->getAlignment()->setHorizontal('center');
              $spreadsheet->getActiveSheet()->getStyle('A1:I1')->getAlignment()->setVertical('center');
                                         
              $activeSheet->setCellValue('A1', 'SL No.');
              $activeSheet->setCellValue('B1', 'Franchise Name');
              $activeSheet->setCellValue('C1', 'Owner Name');
              $activeSheet->setCellValue('D1', 'Franchise ID');
              $activeSheet->setCellValue('E1', 'Contact No');
              $activeSheet->setCellValue('F1', 'Franchise Email');
              $activeSheet->setCellValue('G1', 'Franchise Address');
              $activeSheet->setCellValue('H1', 'Franchise Status');
              $activeSheet->setCellValue('I1', 'Total No of Student Enrolled');

              if(count($franchiseListArr) > 0) {
                  $i = 2;
                  foreach($franchiseListArr as $index => $franchise){
                      /*if($i%2 == 0){
                        $cell_color = "42F560";
                      }else{
                        $cell_color = "EDDC40";  
                      }

                      //Filling cell color
                      $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($cell_color);*/

                      //cell text alignment
                      $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal('center');
                      $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setVertical('center');

                      //Wrap text
                      $spreadsheet->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setWrapText(true);

                      $activeSheet->setCellValue('A'.$i , $index+1);
                      $activeSheet->setCellValue('B'.$i , $franchise['center_name']);
                      $activeSheet->setCellValue('C'.$i , $franchise['owner_name']);
                      $activeSheet->setCellValue('D'.$i , $franchise['fran_id']);
                      $activeSheet->setCellValue('E'.$i , $franchise['fran_phone']);
                      $activeSheet->setCellValue('F'.$i , $franchise['fran_email']);
                      $activeSheet->setCellValue('G'.$i , $franchise['fran_address']);
                      $activeSheet->setCellValue('H'.$i , ucfirst($franchise['record_status']));
                      $activeSheet->setCellValue('I'.$i , $franchise['enrolled_student_count']);
                      $i++;
                  }
              }
 
              $file_upload_dir =  USER_UPLOAD_DIR.'runtime_upload/Franchise_Data.xlsx';
              $file_url = USER_UPLOAD_URL.'runtime_upload/Franchise_Data.xlsx';
  
              $Excel_writer->save($file_upload_dir);

              echo json_encode(array('check'=> 'success','file_upload_dir'=>$file_upload_dir,'file_url'=>$file_url));
          }    
       
          break;
    }      
?>