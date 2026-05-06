<?php

return [

    // Dashboard Routes
    'home' => [
        'admin'     => 'utility/main_dashboard.php',
        'developer' => 'utility/main_dashboard.php',
        'franchise' => 'franchise/franchise_dashboard.php',
        'student'   => 'exam/student_exam_list.php',
    ],

    // Franchise Routes
    'view_franchises' => 'franchise/view_franchises.php',
    'add_franchise'   => 'franchise/manage_franchise.php',
    'edit_franchise'  => 'franchise/manage_franchise.php',
    'edit_franchise_profile'   => 'franchise/edit_franchise_profile.php',
    
    // Course Routes
    'view_courses'    => 'course/view_courses.php',
    'add_course'   => 'course/manage_course.php',
    'edit_course'  => 'course/manage_course.php',
    
    // Student Routes
    'view_students'   => 'student/view_students.php',
    'add_student'   => 'student/manage_student.php',
    'edit_student'  => 'student/manage_student.php',
    'clone_student'  => 'student/clone_student.php',
    'student_admission'  => 'student/manage_student_admission.php',
    'manage_temp_students'  => 'student/manage_temp_students.php',
    
    // Receipt Routes
    'view_receipts'   => 'receipt/manage_receipt.php',
    'view_due_students'   => 'receipt/view_due_students.php',

    // Exam Routes
    'view_exams'   => 'exam/view_exams.php',
    'add_exam'     => 'exam/manage_exam.php',
    'edit_exam'    => 'exam/manage_exam.php',
    'manage_questions'    => 'exam/manage_questions.php',
    'start_exam'   => 'exam/start_student_exam.php',

    // News Routes
    'view_news'   => 'news/view_news.php',
    'add_news'   => 'news/manage_news.php',
    'edit_news'  => 'news/manage_news.php',

    // Email Template Routes
    'view_email_templates'   => 'email_template/view_email_templates.php',
    'add_email_template'   => 'email_template/manage_email_template.php',
    'edit_email_template'  => 'email_template/manage_email_template.php',

    // CMS Routes
    'view_category'   => 'category/view_category.php',
    'view_enquiry'   => 'enquiry/view_enquiry.php',
    'gallery'   => 'gallery/gallery.php',
    'home_sliders'   => 'settings/home_sliders.php',
    'manage_cities'   => 'settings/manage_cities.php',
    'edit_site_setting'   => 'settings/edit_site_setting.php',
    
    // Auth Profile Routes
    'edit_profile'   => 'settings/edit_profile.php',
    'edit_admin_profile'   => 'settings/edit_profile.php',

    // Utility Routes
    'no_access'        => 'utility/no_access.php',
    'not_found'        => 'utility/not_found.php',
    'under_maintenance'=> 'utility/under_maintenance.php',
    'debug'            => 'utility/debug.php',
    'login'            => 'utility/login.php',
];