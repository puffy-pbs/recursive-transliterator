<?php

$directories = scandir('.');

spl_autoload_register(function ($name) use ($directories) {
    foreach ($directories as $directory) {
        $path = $directory . DIRECTORY_SEPARATOR . $name . '.php';
        if (file_exists($path)) {
            require($path);
            break;
        }
    }
});
