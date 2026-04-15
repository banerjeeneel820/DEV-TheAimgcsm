<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class ExportService extends BaseController
{
    private $pdfService;
    private $excelService;

    public function __construct()
    {
        parent::__construct();
        $this->pdfService = new PdfService();
        $this->excelService = new ExcelService();
    }

    public function formatDate($date)
    {
        if (empty($date)) return null;
        return date('Y-m-d', strtotime(str_replace('/', '-', $date)));
    }

    public function exportStudent($post)
    {

        $export_method = $post('export_method') ?? '';

        $studentListArr = $this->getStudentData($post);

        if (empty($studentListArr)) {
            return ['check' => 'failure', 'message' => 'No record found'];
        }

        if ($export_method === 'pdf') {
            
            // Check if only 
            if (count($studentListArr) > 400) {
                return [
                    'check' => 'failure',
                    'message' => 'Too much data for PDF. Please export as Excel.'
                ];
            }

            $filename = 'Student_Data_' . time() . '.pdf';
            $html = $this->buildStudentHtml($studentListArr);
            return $this->pdfService->generate($html, $filename, 'export_data');
        }

        return $this->excelService->exportStudent($studentListArr);
    }

    private function getStudentData($post)
    {

        $protocol = $post('protocol');

        if ($protocol == "dashboard") {
            $dataArr['fetchType'] = $post('fetchType');

            //fetching student data
            $result = $this->GlobalInterfaceControllerObj->fetch_Dashboard_Student_Data($dataArr)['data'];
        } else {
            // -----------------------------
            // BASIC FIELDS
            // -----------------------------
            $exportParamArr['record_status'] = $post('record_status');

            $exportParamArr['course_id'] = $post('course_id');
            $exportParamArr['franchise_id'] = $post('franchise_id');

            $exportParamArr['search_string'] = $post('search_string');

            $exportParamArr['created'] = $this->formatDate($post('created'));

            $exportParamArr['search_start'] = $this->formatDate($post('search_start'));
            $exportParamArr['search_end'] = $this->formatDate($post('search_end'));

            $result = $this->GlobalInterfaceControllerObj->fetch_Global_Student_Recipts($exportParamArr);
        }

        return json_decode(json_encode($result), true);
    }

    private function buildStudentHtml($students)
    {
        ob_start();
        include ROOTPATH . '/views/exports/student.php';
        $html = ob_get_clean();

        return $html;
    }
}
