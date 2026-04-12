<?php
defined('ROOTPATH') OR exit('No direct script access allowed');

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
        $post = fn($key) => $this->GlobalLibraryHandlerObj->postDataSanitize($key);

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
}