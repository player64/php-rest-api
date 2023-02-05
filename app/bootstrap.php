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