<?php
defined('ROOTPATH') or exit('No direct script access allowed');

return [

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

    'interfaceModel' => function ($container) {
        return new GlobalInterfaceModel(
            $container->get('db')
        );
    },

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
            $container->get('interfaceModel'),
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
            $container->get('interfaceModel'),
            $container->get('lib'),
        );
    },

    'courseFranchiseService' => function ($container) {
        return new CourseFranchiseService(
            $container->get('interfaceModel'),
            $container->get('lib')
        );
    },

    'studentService' => function ($container) {
        return new StudentService(
            $container->get('interfaceModel'),
            $container->get('lib'),
            $container->get('permissionService'),
            $container->get('cacheService'),
            $container->get('studentReceiptService'),
            $container->get('globalValidationController')
        );
    },

    'studentReceiptService' => function ($container) {
        return new StudentReceiptService(
            $container->get('interfaceModel'),
            $container->get('lib'),
            $container->get('permissionService'),
            $container->get('cacheService'),
            $container->get('globalValidationController')
        );
    },

    'examService' => function ($container) {
        return new ExamService(
            $container->get('interfaceModel'),
            $container->get('lib'),
            $container->get('permissionService'),
        );
    },

    'cmsService' => function ($container) {
        return new CmsService(
            $container->get('interfaceModel'),
            $container->get('lib')
        );
    },

    'utilityService' => function ($container) {
        return new UtilityService(
            $container->get('interfaceModel'),
            $container->get('lib')
        );
    },

];
