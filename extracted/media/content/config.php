<?php

$newConfig = [
    //*************************
    //Permissions configuration
    //******************
    'delete_files'                            => true,
    'create_folders'                          => true,
    'delete_folders'                          => true,
    'upload_files'                            => true,
    'rename_files'                            => true,
    'rename_folders'                          => true,
    'duplicate_files'                         => true,
    'copy_cut_files'                          => true, // for copy/cut files
    'copy_cut_dirs'                           => true, // for copy/cut directories
    'chmod_files'                             => true, // change file permissions
    'chmod_dirs'                              => true, // change folder permissions
    'preview_text_files'                      => false, // eg.: txt, log etc.
    'edit_text_files'                         => false, // eg.: txt, log etc.
    'create_text_files'                       => false, // only create files with exts. defined in $editable_text_file_exts
    
    //**********************
    //Allowed extensions (lowercase insert)
    //**********************
    'ext_file' => [
        'doc',
        'docx',
        'rtf',
        'pdf',
        'xls',
        'xlsx',
        'txt',
        'csv',
        'html',
        'xhtml',
        'psd',
        'sql',
        'log',
        'fla',
        'xml',
        'ade',
        'adp',
        'mdb',
        'accdb',
        'ppt',
        'pptx',
        'odt',
        'ots',
        'ott',
        'odb',
        'odg',
        'otp',
        'otg',
        'odf',
        'ods',
        'odp',
        'css',
        'ai',
        'kmz',
        'dwg',
        'dxf',
        'hpgl',
        'plt',
        'spl',
        'step',
        'stp',
        'iges',
        'igs',
        'sat',
        'cgm',
        'mobi',
        'epub',
        'php', // added php
    ],
    
    //**********************
    // Hidden files and folders
    //**********************
    // set the names of any folders you want hidden (eg "hidden_folder1", "hidden_folder2" ) Remember all folders with these names will be hidden (you can set any exceptions in config.php files on folders)
    'hidden_folders' => [],
    
    // set the names of any files you want hidden. Remember these names will be hidden in all folders (eg "this_document.pdf", "that_image.jpg" )
    'hidden_files' => [
        'config.php',
        'index.html',
    ],
];

$newConfig['ext'] = array_merge($config['ext'], $newConfig['ext_file']);

return $newConfig;
