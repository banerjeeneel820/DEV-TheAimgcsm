<?php
return [
    // Auth & Profile Routes
    'edit_admin_profile' => ['AuthProfileController', 'manage_admin_profile_data'],
    'edit_profile' => ['AuthProfileController', 'manage_profile_data'],
    'logout' => ['AuthProfileController', 'destroy_session_data'],

    // Franchise & Course Routes
    'view_franchises'     => ['CourseFranchiseController', 'fetch_franchise_data'],
    'add_franchise'     => ['CourseFranchiseController', 'manage_franchise_data_view'],
    'edit_franchise'     => ['CourseFranchiseController', 'manage_franchise_data_view'],

    'view_courses'     => ['CourseFranchiseController', 'fetch_course_data'],
    'add_course'     => ['CourseFranchiseController', 'manage_course_data_view'],
    'edit_course'     => ['CourseFranchiseController', 'manage_course_data_view'],

    // Student Routes   
    'view_students'       => ['StudentController', 'fetch_student_data'],
    'view_due_students'      => ['StudentController', 'fetch_due_student_data'],
    'student_admission'     => ['StudentController', 'manage_student_admission_data_view'],
    'manage_temp_students'     => ['StudentController', 'manage_temp_student_data_view'],
    'add_student'     => ['StudentController', 'manage_student_data_view'],
    'edit_student'     => ['StudentController', 'manage_student_data_view'],
    'clone_student'     => ['StudentController', 'manage_student_data_view'],

    // Student Receipt Routes
    'view_receipts'       => ['StudentReceiptController', 'manage_receipt_data_view'],
    'view_due_students'       => ['StudentReceiptController', 'fetch_due_student_Data'],

    // Exam Routes
    'view_exams'     => ['ExamController', 'fetch_exam_data'],
    'add_exam'     => ['ExamController', 'manage_exam_data_view'],
    'edit_exam'     => ['ExamController', 'manage_exam_data_view'],
    'manage_questions'     => ['ExamController', 'manage_exam_question_view'],
    'start_exam'     => ['ExamController', 'manage_start_exam_view'],

    // Cms Routes
    'view_category' => ['CmsController', 'fetch_category_data'],
    'gallery' => ['CmsController', 'fetch_gallery_data'],

    'view_email_templates' => ['CmsController', 'fetch_email_template_data'],
    'add_email_template' => ['CmsController', 'manage_email_template_data_view'],
    'edit_email_template' => ['CmsController', 'manage_email_template_data_view'],

    'view_news' => ['CmsController', 'fetch_news_data'],
    'add_news' => ['CmsController', 'manage_news_data_view'],
    'edit_news' => ['CmsController', 'manage_news_data_view'],

    'view_enquiry' => ['CmsController', 'fetch_enquiry_data'],
    'home_sliders' => ['CmsController', 'manage_home_slider_data_view'],
    'manage_cities' => ['CmsController', 'manage_city_data_view'],
    'edit_site_setting' => ['CmsController', 'manage_settings_data_view'],

    // Dashboard Routes
    'home' => ['DashboardController', 'fetch_dashboard_data'],

];
