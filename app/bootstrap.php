<?php
if(!file_exists(__DIR__.'/vendor/autoload.php')) {
    // not composer used load all files manually
    $files_to_include = [
        'Models/DbConnection.php',
        'Models/ModelException.php',
        'Controllers/ControllerException.php',
        'Controllers/ControllerResponse.php',
        'Controllers/Controller.php',
        'Controllers/ActorController.php',
        'Controllers/FilmController.php',
        'Controllers/GenreController.php',
        'Models/Entities/EntityException.php',
        'Models/Entities/Validator.php',
        'Models/Entities/Entity.php',
        'Models/Entities/FilmEntity.php',
        'Models/Entities/GenreEntity.php',
        'Models/Entities/ActorEntity.php',
        'Models/Model.php',
        'Models/RecordNotFoundException.php',
        'Models/ActorModel.php',
        'Models/FilmModel.php',
        'Models/GenreModel.php',
        'Router.php'
    ];
    $path =  __DIR__.'/Api/';
    foreach ($files_to_include as $file) {
       $file_path = $path.$file;

        if(file_exists($file_path)) {
            require $file_path;
        } else {
            throw new RuntimeException('The file ' . basename($file) . ' Cannot been found in given path: ' . $file_path);
        }
    }
} else {
    require __DIR__.'/vendor/autoload.php';
}