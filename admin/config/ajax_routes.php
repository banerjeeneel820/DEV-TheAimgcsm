<?php
return [

    // Auth & Profile
    'check_user_login' => ['AuthProfileController', 'check_user_login'],

    // Franchise & Course
    'manageGlobalFranchise' => ['CourseFranchiseController', 'manage_franchise'],
    'manageGlobalCourse' => ['CourseFranchiseController', 'manage_course'],

    // Student
    'manageGlobalStudent' => ['StudentController', 'manage_student'],
    'manageStudentAdmission' => ['StudentController', 'manage_student_admission'],
    'manageTempStudents' => ['StudentController', 'manage_temp_student'],

    // Student Receipt
    'manageStudentReceipt' => ['StudentReceiptController', 'manage_receipt'],
];