<?php
declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $baseDir = __DIR__ . '/../';

    // Special handling for PHPMailer
    if (str_starts_with($class, 'PHPMailer\\PHPMailer')) {
        $relativePath = 'vendor/phpmailer/phpmailer/src/' . str_replace('PHPMailer\\PHPMailer\\', '', $class) . '.php';
    } 
    else if (str_starts_with($class, 'PhpOffice\\PhpSpreadsheet')) {
        $relativePath = 'vendor/PhpOffice/PhpSpreadsheet/' . str_replace('PhpOffice\\PhpSpreadsheet\\', '', $class) . '.php';
    }
    else if (str_starts_with($class, 'Psr\\SimpleCache')) {
        $relativePath = 'vendor/Psr/SimpleCache/' . str_replace('Psr\\SimpleCache\\', '', $class) . '.php';
    }
    else if (str_starts_with($class, 'ZipStream\\')) {
        // Remove the "ZipStream\" prefix and map to vendor/ZipStream/
        $relativePath = 'vendor/ZipStream/' . str_replace('ZipStream\\', '', $class) . '.php';
        $relativePath = str_replace('\\', '/', $relativePath);
    }
    else if (str_starts_with($class, 'MyCLabs\\Enum')) {
        $relativePath = 'vendor/MyCLabs/Enum/' . str_replace('MyCLabs\\Enum\\', '', $class) . '.php';
    }
    else {
        $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    }

    $file = $baseDir . $relativePath;

    if (is_file($file)) {
        require $file;
    } else {
        throw new RuntimeException("Autoloader: Class file not found for [$class] at path [$file]");
    }
});
