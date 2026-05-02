<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class AuthProfileController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function check_user_login($data)
    {
        $paramArr = [];

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        $paramArr['user_email'] = $post('user_email');
        $paramArr['user_pswd'] = md5($post('user_pswd'));
        $paramArr['user_type'] = $post('user_type');
        $paramArr['user_signin_method'] = $post('user_signin_method');
        //Validating captch & collecting response 
        $recaptcha_response = $post('g-recaptcha-response');

        $validate_captcha = true; //$this->lib->checkCaptchaResponse($recaptcha_response);

        if ($validate_captcha) {
            $returnArr = $this->interface->check_User_Login($paramArr);

            if ($returnArr['check'] == 'success') {
                //Setting cookies for browser
                if ($_POST['remember_me'] == 'on') {
                    setcookie('user_email', $_POST['user_email'], time() + 86400 * 30);
                    setcookie('user_pswd', $_POST['user_pswd'], time() + 86400 * 30);
                } else {
                    setcookie('user_email', '', time() + 86400 * 30);
                    setcookie('user_pswd', '', time() + 86400 * 30);
                }
            }
        } else {
            $returnArr = array('check' => 'failure', 'msg' => 'Not a valid captcha response; Please try again.');
        }

        return $returnArr;
    }

    public function manage_user_profile($data)
    {
        //Declaring necessary variables
        $formDataArr = [];
        $returnArr = [];

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Permission Check
        // -----------------------------
        $user_role_slug = "manage_profile";

        if (!$this->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Collect Data
        // -----------------------------
        $formDataArr['user_nicename'] = $post('user_nicename');
        $formDataArr['user_contact']  = $post('user_contact');
        $formDataArr['user_email']    = $post('user_email');

        // status (default = active)
        $formDataArr['user_status'] = $post('user_status') ?: 'active';

        // -----------------------------
        // Password Handling
        // -----------------------------
        $userPass = $post('user_pass');

        if (!empty($userPass)) {
            $formDataArr['user_pass'] = md5($userPass);
        } else {
            $formDataArr['user_pass'] = $post('user_hidden_password');
        }

        // -----------------------------
        // Role (array → serialize)
        // -----------------------------
        $formDataArr['user_role'] = !empty($_POST['user_role'])
            ? serialize($_POST['user_role'])
            : null;

        // -----------------------------
        // User Type
        // -----------------------------
        $pageRoute = $post('page_route');

        $formDataArr['user_type'] = ($pageRoute === 'edit_admin_profile')
            ? 'admin'
            : $_SESSION['user_type'];

        // -----------------------------
        // DB Operation
        // -----------------------------
        return $this->interface->manage_Profile_Data($formDataArr);
    }

    public function manage_franchise_profile($data)
    {
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        $formDataArr = [];
        $dir = 'franchise';

        $formDataArr['fran_row_id'] = $_SESSION['user_id'];

        // -----------------------------
        // PERMISSION CHECK
        // -----------------------------
        if (!$this->checkUserRolePermission('manage_profile', "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission!"];
        }

        // -----------------------------
        // PASSWORD HANDLING
        // -----------------------------
        $fran_pass = $post('fran_pass');

        if (!empty($fran_pass)) {
            $formDataArr['fran_pass'] = md5($fran_pass);
            $formDataArr['fran_og_pass'] = $fran_pass;
        } else {
            $formDataArr['fran_pass'] = $_POST['fran_hidden_password'];
            $formDataArr['fran_og_pass'] = $_POST['fran_hidden_og_password'];
        }

        // -----------------------------
        // BASIC FIELDS
        // -----------------------------
        $fields = [
            'center_name',
            'owner_name',
            'fran_phone',
            'fran_email',
            'fran_address',
            'fran_description'
        ];

        foreach ($fields as $field) {
            $formDataArr[$field] = $post($field);
        }

        // -----------------------------
        // FILE HANDLING (GENERIC)
        // -----------------------------
        $formDataArr['fran_image'] = $this->lib->handleFileUpload([
            'input'        => 'fran_image',
            'hidden'       => $_POST['hidden_fran_image'] ?? '',
            'default'      => 'profile_small_old.png',
            'dir'          => $dir,
            'row_id'       => $formDataArr['fran_row_id'],
        ]);

        $formDataArr['fran_pdf_name'] = $this->lib->handleFileUpload([
            'input'        => 'fran_pdf_name',
            'hidden'       => $_POST['hidden_fran_pdf'] ?? '',
            'default'      => 'COMPUTER-COURSE.pdf',
            'dir'          => $dir,
            'row_id'       => $formDataArr['fran_row_id'],
        ]);

        // -----------------------------
        // DB CALL
        // -----------------------------
        return $this->interface
            ->edit_Franchise_Profile($formDataArr);
    }

    public function check_user_email_availability($data)
    {
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // INPUT DATA
        // -----------------------------
        $payload = [
            'user_email' => $post('user_email'),
            'user_type'  => $post('user_type'),
            'user_id'    => (int) $post('user_id')
        ];

        // -----------------------------
        // VALIDATION (Basic)
        // -----------------------------
        if (empty($payload['user_email']) || empty($payload['user_type'])) {
            return [
                'check' => 'failure',
                'message' => 'Required fields missing'
            ];
        }

        // -----------------------------
        // CALL MODEL
        // -----------------------------
        return $this->interface
            ->check_User_Email_Availability($payload);
    }
}
