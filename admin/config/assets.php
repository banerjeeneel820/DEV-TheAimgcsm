<?php

return [

    'common' => [
        'css' => [
            'toastr/toastr.min',
            'sweetalert/sweetalert'
        ],
        'js' => [
            'toastr/toastr.min',
            'sweetalert/sweetalert.min',
        ]
    ],

    'datatable' => [
        'css' => [
            'dataTables/datatables.min'
        ],
        'js' => [
            'dataTables/datatables.min',
            'dataTables/dataTables.bootstrap4.min'
        ]
    ],

    'prettycheckbox' => [
        'css' => [
            'pretty-checkbox/pretty-checkbox.min',
        ],
        'js' => []
    ],

    'fancybox' => [
        'css' => [
            'fancybox/jquery.fancybox.min'
        ],
        'js' => [
            'fancybox/jquery.fancybox'
        ]
    ],

    'select2' => [
        'css' => [
            'select2/select2.min'
        ],
        'js' => [
            'select2/select2.full.min'
        ]
    ],

    'datapicker' => [
        'css' => [
            'datapicker/datepicker3'
        ],
        'js' => [
            'datapicker/bootstrap-datepicker'
        ]
    ],

    'tablefilter' => [
        'css' => [],
        'js' => [
            'table-filter/filter-table.min'
        ]
    ],

    'iCheck' => [
        'css' => [
            'iCheck/custom',
        ],
        'js' => [
            'iCheck/icheck.min'
        ]
    ],

    'printThis' => [
        'css' => [
            'printThis/print.min',
        ],
        'js' => [
            'printThis/print.min'
        ]
    ],

    'dropzone' => [
        'css' => [
            'dropzone/dropzone.min',
        ],
        'js' => [
            'dropzone/dropzone.min'
        ]
    ],

    'footable' => [
        'css' => [
            'footable/footable.core',
        ],
        'js' => [
            'footable/footable.all.min'
        ]
    ],

    'franchise_list' => [
        'groups' => ['common', 'datatable', 'fancybox', 'prettycheckbox']
    ],

    'manage_franchise_form' => [
        'groups' => ['common', 'iCheck', 'fancybox']
    ],

    'course_list' => [
        'groups' => ['common', 'datatable', 'fancybox', 'prettycheckbox']
    ],

    'manage_course_form' => [
        'groups' => ['common', 'iCheck', 'fancybox']
    ],

    'student_list' => [
        'groups' => ['common', 'tablefilter', 'fancybox', 'prettycheckbox','select2','datapicker']
    ],

    'manage_student_form' => [
        'groups' => ['common', 'iCheck', 'fancybox', 'select2','datapicker']
    ],

    'student_admission_list' => [
        'groups' => ['common', 'datatable', 'fancybox', 'prettycheckbox', 'select2', 'printThis']
    ],

    'tmp_student_list' => [
        'groups' => ['common', 'tablefilter', 'fancybox', 'prettycheckbox', 'select2', 'datapicker', 'printThis']
    ],

    'student_receipt_list' => [
        'groups' => ['common', 'datatable', 'tablefilter', 'fancybox', 'prettycheckbox', 'select2', 'datapicker', 'iCheck', 'printThis']
    ],

    'exam_list' => [
        'groups' => ['common', 'datatable', 'prettycheckbox']
    ],

    'manage_exam_form' => [
        'groups' => ['common', 'iCheck', 'fancybox', 'select2', 'datapicker']
    ],

    'manage_exam_questions' => [
        'groups' => ['common', 'iCheck']
    ],

    'due_student_list' => [
        'groups' => ['common', 'prettycheckbox', 'fancybox', 'select2', 'tablefilter']
    ],

    'gallery_list' => [
        'groups' => ['common', 'prettycheckbox', 'fancybox', 'select2', 'iCheck', 'datatable', 'dropzone']
    ],

];
