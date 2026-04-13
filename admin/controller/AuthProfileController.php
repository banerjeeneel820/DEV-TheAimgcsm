<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class UserProfileController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function check_user_login($data)
    {
        $paramArr = [];

        // helper
        $post = fn ($key) => $this->GlobalLibraryHandlerObj->postDataSanitize($key);

        $paramArr['user_email'] = $post('user_email');
        $paramArr['user_pswd'] = md5($post('user_pswd'));
        $paramArr['user_type'] = $post('user_type');
        $paramArr['user_signin_method'] = $post('user_signin_method');
        //Validating captch & collecting response 
        $recaptcha_response = $data('g-recaptcha-response');

        $validate_captcha = true; //$GlobalLibraryHandlerObj->checkCaptchaResponse($recaptcha_response);

        if ($validate_captcha) {
            $returnArr = $this->GlobalInterfaceControllerObj->check_User_Login($paramArr);

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
        $post = fn ($key) => $this->GlobalLibraryHandlerObj->postDataSanitize($key);

        // -----------------------------
        // Permission Check
        // -----------------------------
        $user_role_slug = "manage_profile";

        if (!$this->GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard")) {
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
        return $this->GlobalInterfaceControllerObj->manage_Profile_Data($formDataArr);

    }
    
}
