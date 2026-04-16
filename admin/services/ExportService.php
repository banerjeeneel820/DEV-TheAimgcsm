<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class ExportService extends BaseController
{
    private $pdfService;
    private $excelService;

    public function __construct()
    {
        parent::__construct();

        $this->pdfService = new PdfService(new PdfFactory());
        $this->excelService = new ExcelService();
    }

    // -----------------------------
    // COMMON HELPERS
    // -----------------------------
    protected function getPostData($post, array $keys)
    {
        $data = [];

        foreach ($keys as $key) {
            $data[$key] = $post($key);
        }

        return $data;
    }
    private function fail($message)
    {
        return ['check' => 'failure', 'message' => $message];
    }

    private function generateFilename($prefix, $ext)
    {
        return $prefix . '_' . time() . '.' . $ext;
    }

    private function isPdfLimitExceeded($data, $limit = 400)
    {
        return count($data) > $limit;
    }

    public function formatDate($date)
    {
        return empty($date)
            ? null
            : date('Y-m-d', strtotime(str_replace('/', '-', $date)));
    }

    private function toArray($data)
    {
        return json_decode(json_encode($data), true);
    }

    private function renderView($view, $data = [])
    {
        extract($data);

        ob_start();
        include ROOTPATH . "/views/exports/{$view}.php";
        return ob_get_clean();
    }

    // -----------------------------
    // COMMON FILTER BUILDER
    // -----------------------------
    private function buildCommonFilters($input, $dateFields = [])
    {
        $params = [
            'record_status' => $input['record_status']
        ];

        if ($input['course_id']) {
            $params['course_id'] = $input['course_id'];
            $params['course_name'] = $input['course_name'];
        }

        if ($input['franchise_id']) {
            $params['franchise_id'] = $input['franchise_id'];
            $params['franchise_name'] = $input['franchise_name'];
        }

        if ($input['search_string']) {
            $params['search_string'] = $input['search_string'];
        }

        if ($input['created']) {
            $params['created'] = $this->formatDate($input['created']);
        }

        foreach ($dateFields as $field) {
            if ($input[$field]) {
                $params[$field] = $this->formatDate($input[$field]);
            }
        }

        if ($input['student_id']) {
            $params['student_id'] = $input['student_id'];
        }

        return $params;
    }

    // -----------------------------
    // DATA FORMATTERS
    // -----------------------------
    private function formatData(array $data, array $rules = [])
    {
        return array_map(function ($row) use ($rules) {

            foreach ($rules as $field => $rule) {

                // Skip if field doesn't exist
                if (!array_key_exists($field, $row)) continue;

                $value = $row[$field];

                switch ($rule) {

                    case 'date':
                        $row[$field] = !empty($value)
                            ? date('jS F, Y', strtotime($value))
                            : null;
                        break;

                    case 'ucfirst':
                        $row[$field] = ucfirst($value ?? '');
                        break;

                    case 'amount_or_dash':
                        $row[$field] = !empty($value) ? $value : '------';
                        break;

                    case 'default_dash':
                        $row[$field] = $value ?? '------';
                        break;

                    case 'verified_status':
                        $row[$field] = ($value == '1') ? 'Verified' : 'Not verified';
                        break;

                    case 'student_status':
                        $row[$field] = ($value === 'course_complete')
                            ? 'Course Complete'
                            : ucfirst($value ?? '');
                        break;

                    default:
                        // Custom callback support
                        if (is_callable($rule)) {
                            $row[$field] = $rule($value, $row);
                        }
                }
            }

            return $row;
        }, $data);
    }

    // -----------------------------
    // STUDENT EXPORT
    // -----------------------------
    public function exportStudent($post)
    {
        // Extract all required fields at once
        $input = $this->getPostData($post, [
            'export_method',
            'protocol',
            'fetchType',
            'record_status',
            'course_id',
            'course_name',
            'franchise_id',
            'franchise_name',
            'search_string',
            'created',
            'search_start',
            'search_end',
            'student_id'
        ]);

        $exportMethod = $input['export_method'] ?? 'excel';

        $result = $this->getStudentData($input);

        $students = $result['data'];
        $criteria = $result['filter'];

        if (empty($students)) {
            return $this->fail('No record found');
        }

        // Format receipt data before generating csv file
        $students = $this->formatData($students, [
            'stu_dob' => 'date',
            'stu_gender' => 'ucfirst',
            'stu_marital_status' => 'ucfirst',
            'stu_result' => 'ucfirst',
            'student_status' => 'student_status'
        ]);

        if ($exportMethod === 'pdf') {
            return $this->handleStudentPdf($students, $criteria);
        }

        return $this->excelService->exportStudent($students, $criteria);
    }

    private function handleStudentPdf($students, $criteria)
    {
        if ($this->isPdfLimitExceeded($students)) {
            return $this->fail('Too much data for PDF. Please export as Excel.');
        }

        $html = $this->renderView('student', [
            'students' => $students,
            'criteriaText' => $criteria
        ]);

        return $this->pdfService->generate(
            $html,
            $this->generateFilename('Student_Data', 'pdf'),
            'export_data'
        );
    }

    private function getStudentData($input)
    {
        if ($input['protocol'] === "dashboard") {
            $data = [
                'fetchType' => $input['fetchType']
            ];

            $result = $this->GlobalInterfaceControllerObj
                ->fetch_Dashboard_Student_Data($data)['data'];

            return [
                'data' => $this->toArray($result),
                'filter' => ''
            ];
        }

        // Internal helper (as you wanted earlier)
        $params = $this->buildCommonFilters($input, [
            'search_start',
            'search_end'
        ]);

        $result = $this->GlobalInterfaceControllerObj
            ->fetch_Global_Student_Recipts($params);

        $students = $this->toArray($result);

        // Build criteria (same as receipt now)
        $criteria = $this->GlobalLibraryHandlerObj
            ->buildExportCriteria($params);

        return [
            'data' => $students,
            'filter' => $criteria
        ];
    }

    // -----------------------------
    // RECEIPT EXPORT
    // -----------------------------
    public function exportReceipt($post)
    {
        // Extract all required fields at once
        $input = $this->getPostData($post, [
            'export_method',
            'protocol',
            'fetchType',
            'record_status',
            'course_id',
            'course_name',
            'franchise_id',
            'franchise_name',
            'created',
            'receipt_season_start',
            'receipt_season_end',
            'student_id'
        ]);

        $exportMethod = $input['export_method'] ?? 'excel';

        $result = $this->getReceiptData($input);

        $receipts = $result['data'];
        $criteria = $result['filter'];

        if (empty($receipts)) {
            return $this->fail('No record found');
        }

        // Format receipt data before generating csv file
        $receipts = $this->formatData($receipts, [
            'receipt_amount' => 'amount_or_dash',
            'late_fine' => 'amount_or_dash',
            'extra_fees' => 'amount_or_dash',
            'extra_fees_description' => 'default_dash',
            'created_at' => 'date',
            'verified_status' => 'verified_status'
        ]);

        if ($exportMethod === 'pdf') {
            return $this->handleReceiptPdf($receipts, $criteria);
        }

        return $this->excelService->exportReceipt($receipts, $criteria);
    }

    private function handleReceiptPdf($receipts, $criteria)
    {
        if ($this->isPdfLimitExceeded($receipts)) {
            return $this->fail('Too much data for PDF. Please export as Excel.');
        }

        $html = $this->renderView('receipt', [
            'receipts' => $receipts,
            'criteriaText' => $criteria
        ]);

        return $this->pdfService->generate(
            $html,
            $this->generateFilename('Receipt_Data', 'pdf'),
            'export_data'
        );
    }

    private function getReceiptData($input)
    {
        if ($input['protocol'] === "dashboard") {
            $data = [
                'fetchType' => $input['fetchType']
            ];

            $result = $this->GlobalInterfaceControllerObj
                ->fetch_Dashboard_Receipt_Data($data)['data'];

            return [
                'data' => $this->toArray($result),
                'filter' => ''
            ];
        }

        $params = $this->buildCommonFilters($input, [
            'receipt_season_start',
            'receipt_season_end'
        ]);

        //print_r($params);exit;

        if ($input['student_id']) {
            $result = $this->GlobalInterfaceControllerObj
                ->fetch_Single_Student_Receipt($input['student_id'], $params);
        } else {
            $result = $this->GlobalInterfaceControllerObj
                ->fetch_Global_Receipts($params);
        }

        $receipts = $this->toArray($result);

        $criteria = $this->GlobalLibraryHandlerObj
            ->buildExportCriteria($params);

        return [
            'data' => $receipts,
            'filter' => $criteria
        ];
    }

    // -----------------------------
    // FRANCHISE EXPORT
    // -----------------------------
    public function exportFranchise($post)
    {
        // Extract all required fields at once
        $input = $this->getPostData($post, [
            'export_method',
            'record_status',
            'owned_status'
        ]);

        $exportMethod = $input['export_method'] ?? 'excel';

        $result = $this->getFranchiseData($input);

        $franchises = $result['data'];
        $criteria = $result['filter'];

        if (empty($franchises)) {
            return $this->fail('No record found');
        }

        // Format receipt data before generating csv file
        $franchises = $this->formatData($franchises, [
            'record_status' => 'ucfirst',
            'owned_status' => 'ucfirst'
        ]);

        if ($exportMethod === 'pdf') {
            return $this->handleFranchisePdf($franchises, $criteria);
        }

        return $this->excelService->exportFranchise($franchises, $criteria);
    }

    private function handleFranchisePdf($franchises, $criteria)
    {
        if ($this->isPdfLimitExceeded($franchises)) {
            return $this->fail('Too much data for PDF. Please export as Excel.');
        }

        $html = $this->renderView('franchise', [
            'franchises' => $franchises,
            'criteriaText' => $criteria
        ]);

        return $this->pdfService->generate(
            $html,
            $this->generateFilename('Franchise_Data', 'pdf'),
            'export_data'
        );
    }


    private function getFranchiseData($input)
    {
        // Internal helper (as you wanted earlier)
        $params = $this->buildCommonFilters($input);

        $result = $this->GlobalInterfaceControllerObj
            ->fetch_Global_Franchise($params);

        $franchises = $this->toArray($result);

        // Build criteria (same as receipt now)
        $criteria = $this->GlobalLibraryHandlerObj
            ->buildExportCriteria($params);

        return [
            'data' => $franchises,
            'filter' => $criteria
        ];
    }
}
