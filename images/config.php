<?php

return [
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
    // Hidden files and folders
    //**********************
    // set the names of any folders you want hidden (eg "hidden_folder1", "hidden_folder2" ) Remember all folders with these names will be hidden (you can set any exceptions in config.php files on folders)
    'hidden_folders' => ['og','icons'],
    
    // set the names of any files you want hidden. Remember these names will be hidden in all folders (eg "this_document.pdf", "that_image.jpg" )
    'hidden_files' => [
        'config.php',
        'index.html',
        'BarPay.jpg',
        'cv_amex_card.gif',
        'cv_card.gif',
        'einzug.gif',
        'ladebalken.gif',
        'left.png',
        'loading.gif',
        'middle.png',
        'pdf.png',
        'pixel_black.gif',
        'pixel_silver.gif',
        'pixel_trans.gif',
        'placeholder.php',
        'right.png',
        'trust-ssl.png',
        'trusted.gif',
        'trusted_bewerten_de.gif',
        'trusted_bewerten_en.gif',
        'trusted_siegel.gif',
        'trusted_video_teaser.gif',
        'heidelpay.gif',
        'trusted-info-de.png',
    ],
];
