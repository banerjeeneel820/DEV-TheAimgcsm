<?php
return [

    // Auth & Profile Routes
    'check_user_login' => ['AuthProfileController', 'check_user_login'],
    'manageProfileData' => ['AuthProfileController', 'manage_user_profile'],
    'manageFranchiseProfile' => ['AuthProfileController', 'manage_franchise_profile'],
    'checkUserEmailAvailability' => ['AuthProfileController', 'check_user_email_availability'],

    // Franchise & Course Routes
    'manageGlobalFranchise' => ['CourseFranchiseController', 'manage_franchise'],
    'manageGlobalCourse' => ['CourseFranchiseController', 'manage_course'],

    // Student Routes
    'manageGlobalStudent' => ['StudentController', 'manage_student'],
    'manageStudentAdmission' => ['StudentController', 'manage_student_admission'],
    'manageTempStudents' => ['StudentController', 'manage_temp_student'],
    'changeStudentStatus' => ['StudentController', 'change_student_status'],
    'updateStudentBulkStatus' => ['StudentController', 'update_student_bulk_status'],
    'fetchStudentDetailInModal' => ['StudentController', 'fetch_student_detail_modal'],

    // Student Receipt Routes
    'manageStudentReceipt' => ['StudentReceiptController', 'manage_receipt'],
    'fetchReceiptTotal' => ['StudentReceiptController', 'fetch_total_receipt'],
    'exportStudentReceiptPdf' => ['StudentReceiptController', 'export_student_receipt'],
    'exportTempStudentReceipt' => ['StudentReceiptController', 'export_temp_student_receipt'],

    // Exam Routes
    'manageGlobalExam' => ['ExamController', 'manage_exam'],
    'fetchQuestions' => ['ExamController', 'fetch_limited_questions'],
    'fetchAllQuestions' => ['ExamController', 'fetch_all_questions'],
    'manageExamQuestions' => ['ExamController', 'manage_exam_questions'],
    'sortExamQuestions' => ['ExamController', 'sort_exam_questions'],
    'deleteAllQuestions' => ['ExamController', 'delete_all_questions'],
    'setExamValidationLog' => ['ExamController', 'set_exam_validation_log'],
    'manageExamAnswer' => ['ExamController', 'manage_exam_answer'],
    'flagQuestionForReview' => ['ExamController', 'flag_question_review'],
    'recordViewdQuestions' => ['ExamController', 'record_viewd_questions'],

    // Cms Routes
    'manageGalleryItem' => ['CmsController', 'manage_gallery'],
    'galleryBulkUploader' => ['CmsController', 'gallery_bulk_uploader'],
    'manageParentCategory' => ['CmsController', 'manage_parent_category'],
    'manageGlobalCity' => ['CmsController', 'manage_global_city'],
    'manageEmailTemplate' => ['CmsController', 'manage_email_template'],
    'manageHomeSlider' => ['CmsController', 'manage_home_slider'],
    'manageGlobalNews' => ['CmsController', 'manage_global_news'],

    // Utility Routes
    'updateGlobalStatusRecord' => ['UtilityController', 'update_global_status_status'],
    'globalFeaturedStatusUpdate' => ['UtilityController', 'update_global_featured_status'],
    'globalVerifiedStatusUpdate' => ['UtilityController', 'update_global_verified_status'],
    'deleteGlobalData' => ['UtilityController', 'delete_global_data'],
    'cleanDynamicContentFolder' => ['UtilityController', 'clean_runtime_content'],
    'clearCacheFolder' => ['UtilityController', 'clear_site_cache'],
    'removeFileFromServer' => ['UtilityController', 'remove_file_from_server'],
    'createSiteBackupQueueJob' => ['UtilityController', 'create_site_backup_queue_job'],
    'updateSiteSettings' => ['UtilityController', 'update_site_setting'],

    // Import & Export Routes
    'manageExportData' => ['ExportController', 'handle_export_data'],
    'manageImportData' => ['ImportController', 'handle_import_data'],
];
