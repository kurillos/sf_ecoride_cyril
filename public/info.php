<?php

echo "<h1>PHP Environment Check</h1>";

echo "<h2>Web Server PHP Configuration</h2>";
echo "<strong>PHP Version:</strong> " . PHP_VERSION . "<br>";
echo "<strong>php.ini file:</strong> " . php_ini_loaded_file() . "<br>";

echo "<h2>MongoDB Extension</h2>";
if (extension_loaded('mongodb')) {
    echo "<p style='color:green;'>The 'mongodb' extension is loaded.</p>";
    echo "<strong>MongoDB extension version:</strong> " . phpversion('mongodb') . "<br>";
} else {
    echo "<p style='color:red;'>The 'mongodb' extension is NOT loaded.</p>";
    echo "<p>You need to enable the <code>extension=mongodb</code> in your web server's php.ini file: <strong>" . php_ini_loaded_file() . "</strong></p>";
}

echo "<hr><h2>Full PHP Info</h2>";
echo "<p><em>Be careful with sharing this information. Delete this file after debugging.</em></p>";
phpinfo();
