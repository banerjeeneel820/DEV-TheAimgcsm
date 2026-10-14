<?php
defined('ROOTPATH') or exit('No direct script access allowed');

use App\Core\Database;
use App\Libraries\GlobalLibraryHandler;

use App\Controllers\Admin\GlobalValidationController;

use App\Services\Admin\AuthService;
use App\Services\Admin\PermissionService;
use App\Services\Admin\CacheService;
use App\Services\Admin\PdfService;
use App\Services\Admin\CmsService;
use App\Services\Admin\CourseFranchiseService;
use App\Services\Admin\DashboardService;
use App\Services\Admin\ExamService;
use App\Services\Admin\ExcelService;
use App\Services\Admin\ExportService;
use App\Services\Admin\ImportService;
use App\Services\Admin\NewsService;
use App\Services\Admin\StudentService;
use App\Services\Admin\StudentReceiptService;
use App\Services\Admin\TemplateService;
use App\Services\Admin\UtilityService;

use App\Models\Admin\GlobalInterfaceModel;
use App\Models\Admin\AuthModel;
use App\Models\Admin\PermissionModel;
use App\Models\Admin\CmsModel;
use App\Models\Admin\CourseFranchiseModel;
use App\Models\Admin\DashboardModel;
use App\Models\Admin\ExamModel;
use App\Models\Admin\NewsModel;
use App\Models\Admin\StudentModel;
use App\Models\Admin\StudentReceiptModel;
use App\Models\Admin\TemplateModel;
use App\Models\Admin\UtilityModel;
/*
|--------------------------------------------------------------------------
| Model Registry
|--------------------------------------------------------------------------
*/

$modelBindings = [
    'interfaceModel' => GlobalInterfaceModel::class,
    'authModel'   => AuthModel::class,
    'permissionModel'   => PermissionModel::class,
    'cmsModel'   => CmsModel::class,
    'courseFranchiseModel'   => CourseFranchiseModel::class,
    'dashboardModel'   => DashboardModel::class,
    'examModel'   => ExamModel::class,
    'newsModel'   => NewsModel::class,
    'studentModel'   => StudentModel::class,
    'studentReceiptModel'   => StudentReceiptModel::class,
    'templateModel'   => TemplateModel::class,
    'utilityModel'   => UtilityModel::class,
];

$bindings = [

    /*
    |--------------------------------------------------------------------------
    | CORE
    |--------------------------------------------------------------------------
    */

    'db' => function ($container) {
        return new Database();
    },

    'lib' => function ($container) {
        return new GlobalLibraryHandler(
            $container->get('db')
        );
    },

    /*
    |--------------------------------------------------------------------------
    | MODELS
    |--------------------------------------------------------------------------
    */

    // 'interfaceModel' => function ($container) {
    //     return new GlobalInterfaceModel(
    //         $container->get('db')
    //     );
    // },

    /*
    |--------------------------------------------------------------------------
    | VALIDATORS
    |--------------------------------------------------------------------------
    */

    'globalValidationController' => function ($container) {
        return new GlobalValidationController(
            $container->get('lib')
        );
    },

    /*
    |--------------------------------------------------------------------------
    | SERVICES
    |--------------------------------------------------------------------------
    */

    'permissionService' => function ($container) {
        return new PermissionService(
            $container->get('permissionModel'),
            $container->get('lib')
        );
    },

    'authService' => function ($container) {
        return new AuthService(
            $container->get('authModel'),
            $container->get('courseFranchiseModel'),
            $container->get('lib')
        );
    },

    'cacheService' => function ($container) {
        return new CacheService();
    },

    'pdfService' => function ($container) {
        return new PdfService();
    },

    'excelService' => function ($container) {
        return new ExcelService();
    },

    'exportService' => function ($container) {
        return new ExportService(
            $container->get('interfaceModel'),
            $container->get('lib'),
            $container->get('pdfService'),
            $container->get('excelService'),
        );
    },

    'importService' => function ($container) {
        return new ImportService(
            $container->get('interfaceModel'),
            $container->get('lib'),
            $container->get('excelService'),
        );
    },

    'utilityService' => function ($container) {
        return new UtilityService(
            $container->get('utilityModel'),
            $container->get('lib'),
        );
    },

    'courseFranchiseService' => function ($container) {
        return new CourseFranchiseService(
            $container->get('courseFranchiseModel'),
            $container->get('lib')
        );
    },

    'studentService' => function ($container) {
        return new StudentService(
            $container->get('studentModel'),
            $container->get('courseFranchiseModel'),
            $container->get('cmsModel'),
            $container->get('lib'),
            $container->get('permissionService'),
            $container->get('cacheService'),
            $container->get('studentReceiptService'),
            $container->get('globalValidationController')
        );
    },

    'studentReceiptService' => function ($container) {
        return new StudentReceiptService(
            $container->get('studentReceiptModel'),
            $container->get('cmsModel'),
            $container->get('studentModel'),
            $container->get('utilityModel'),
            $container->get('templateModel'),
            $container->get('lib'),
            $container->get('permissionService'),
            $container->get('cacheService'),
            $container->get('globalValidationController')
        );
    },

    'examService' => function ($container) {
        return new ExamService(
            $container->get('examModel'),
            $container->get('lib'),
            $container->get('permissionService'),
        );
    },

    'cmsService' => function ($container) {
        return new CmsService(
            $container->get('cmsModel'),
            $container->get('lib')
        );
    },

    'newsService' => function ($container) {
        return new NewsService(
            $container->get('newsModel'),
            $container->get('lib')
        );
    },

    'templateService' => function ($container) {
        return new TemplateService(
            $container->get('templateModel'),
            $container->get('utilityModel'),
            $container->get('lib')
        );
    },

    'dashboardService' => function ($container) {
        return new DashboardService(
            $container->get('dashboardModel'),
            $container->get('lib')
        );
    },

];

/*
|--------------------------------------------------------------------------
| REGISTER MODEL BINDINGS
|--------------------------------------------------------------------------
*/

foreach ($modelBindings as $key => $modelClass) {

    $bindings[$key] = function ($container) use ($modelClass) {

        return new $modelClass(
            $container->get('db')
        );
    };
}

return $bindings;
