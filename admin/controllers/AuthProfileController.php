<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class AuthProfileController extends BaseController
{   
    private $permissionService;
    private $authService;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->permissionService = $container->get(PermissionService::class);
        $this->authService = $container->get(AuthService::class);
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
            $returnArr = $this->authService->checkUserLogin($paramArr);

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

    public function manage_profile_data($data)
    {
        if ($_SESSION['user_type'] == 'developer') {
            $profileData =  $this->edit_Developer_Profile_Data($data);
       } elseif ($_SESSION['user_type'] == 'admin') {
            $profileData =  $this->edit_Admin_Profile_Data($data);
       } elseif ($_SESSION['user_type'] == 'franchise') {
            $data['fetch_type'] = 'edit_profile';
            $profileData =  $this->edit_Franchise_Profile_Data($data);
       }

       return $profileData;
    }

    /*
    |--------------------------------------------------------------------------
    | Edit developer profile data methods
    |--------------------------------------------------------------------------
    */
    public function edit_Developer_Profile_Data($data)
    {
        $user_role_slug = 'manage_profile';

        // Load assets
        $assets = Asset::load("user_profile");

        // Permission check (centralized)
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        if (!$hasPermission) {
            return $this->page(
                [
                    'profile_data' => [],
                    'page_type' => 'edit_profile'
                ],
                'Manage My Profile',
                $assets,
                false,
                false // page_permission
            );
        }

        // Get filters safely
        $user_id = (int) $_SESSION['user_id'];

        // Fetch category data through service
        $profileData = $this->authService->getDevProfileData($user_id);

        //var_dump($profileData);exit;

        return $this->page(
            [
                'profile_data' => $profileData,
                'page_type' => 'edit_profile'
            ],
            'Manage My Profile',
            $assets,
            false,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit admin profile data methods
    |--------------------------------------------------------------------------
    */
    public function edit_Admin_Profile_Data($data)
    {
        $user_role_slug = 'manage_profile';

        // Load assets
        $assets = Asset::load("user_profile");

        // Permission check (centralized)
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        if (!$hasPermission) {
            return $this->page(
                [
                    'profile_data' => [],
                    'page_type' => 'edit_profile'
                ],
                'Manage My Profile',
                $assets,
                false,
                false // page_permission
            );
        }

        // Get filters safely
        $user_id = (int) $_SESSION['user_id'];

        // Fetch category data through service
        $profileData = $this->authService->getAdminProfileData($user_id);

        //var_dump($profileData);exit;

        return $this->page(
            [
                'profile_data' => $profileData,
                'page_type' => 'edit_profile'
            ],
            'Manage My Profile',
            $assets,
            false,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit franchise profile data methods
    |--------------------------------------------------------------------------
    */
    public function edit_Franchise_Profile_Data()
    {
        $user_role_slug = 'manage_profile';

        // Load assets
        $assets = Asset::load("user_profile");

        // Permission check (centralized)
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        if (!$hasPermission) {
            return $this->page(
                [
                    'profile_data' => [],
                    'page_type' => 'edit_profile'
                ],
                'Manage My Profile',
                $assets,
                false,
                false // page_permission
            );
        }

        // Get filters safely
        $user_id = (int) $_SESSION['user_id'];

        // Fetch category data through service
        $profileData = $this->authService->getFranchiseProfileData($user_id);

        //var_dump($profileData);exit;

        return $this->page(
            [
                'profile_data' => $profileData,
                'page_type' => 'edit_profile'
            ],
            'Manage My Profile',
            $assets,
            false,
            true
        );
    }

     /*
    |--------------------------------------------------------------------------
    | Edit admin profile data from dev session methods
    |--------------------------------------------------------------------------
    */
    public function manage_admin_profile_data($data)
    {
        $user_role_slug = 'manage_profile';

        // Load assets
        $assets = Asset::load("user_profile");

        // Permission check (centralized)
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        if (!$hasPermission) {
            return $this->page(
                [
                    'profile_data' => [],
                    'page_type' => 'edit_profile'
                ],
                'Manage My Profile',
                $assets,
                false,
                false // page_permission
            );
        }

        // Get filters safely
        $user_id = (int) $_SESSION['user_id'];

        if ($user_id > 0) {
        //Fetching franchise detail
            $profileData = $this->authService->getAdminProfileData($user_id);
        } else {
            $profileData = array();
        }

        // Fetch category data through service
        $profileData = $this->authService->getAdminProfileData($user_id);

        //var_dump($profileData);exit;

        return $this->page(
            [
                'profile_data' => $profileData,
                'page_type' => 'edit_profile'
            ],
            'Manage My Profile',
            $assets,
            false,
            true
        );
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

        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
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
        return $this->authService->manageProfileData($formDataArr);
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
        if (!$this->permissionService->checkUserRolePermission('manage_profile', "hard")) {
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
        return $this->authService
            ->editFranchiseProfile($formDataArr);
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
        return $this->authService
            ->checkUserEmailAvailability($payload);
    }

    public function destroy_session_data()
    {
        session_destroy();
		header("Location: ".SITE_URL);
    }
}
