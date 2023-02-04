<?php
if(!file_exists(__DIR__.'/vendor/autoload.php')) {
    // not composer used load all files manually
    $files_to_include = [
        'Models/DbConnection.php',
        'Models/ModelException.php'
    ];

    foreach ($files_to_include as $file) {
        $path =  __DIR__.'/Api/'.$file;

        if(file_exists($path)) {
            require $path;
        } else {
            throw new RuntimeException('The file ' . $file . ' Cannot been found. Checked the path ' . $path);
        }
    }
} else {
    require __DIR__.'/vendor/autoload.php';
}