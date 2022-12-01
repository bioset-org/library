<?php
spl_autoload_register(function ($class) {

    $dirs=scandir(__DIR__);
    foreach($dirs as $dir)
    {
        if($dir=="." or $dir=="..")
            continue;
        $base_dir = (__DIR__ ."/$dir");
        if(!is_dir($base_dir))
            continue;
        //Include a file with a class by its name (DB = classes/DB.php)

        $file = $base_dir . '/' . $class . '.php';

        //If the file exists, require it
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});