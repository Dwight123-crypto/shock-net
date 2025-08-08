<?php

// Define paths
$currentHtaccess = '/home2/rypaci/public_html/shock-net/.htaccess';
$newHtaccessZip = '/home2/rypaci/public_html/shock-net/.htaccess-11032022.zip';

// Check if .htaccess has a PHP 7+ handler
$currentContent = file_get_contents($currentHtaccess);
if (preg_match('/AddHandler application\/x-httpd-php[0-9]+/', $currentContent)) {
    // Extract the zip file
    $zip = new ZipArchive;
    if ($zip->open($newHtaccessZip) === TRUE) {
        $zip->extractTo(dirname($currentHtaccess));
        $zip->close();

        // Optionally, restart your web server (e.g., Apache)
        // exec('sudo service apache2 restart');
    }
}
?>