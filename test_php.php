<?php
// Force PHP to show errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set content type to plain text to see raw output
header('Content-Type: text/plain');

// Basic PHP info
echo "PHP is working!\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";

// Test if we can write files
$test_file = __DIR__ . '/test_write.txt';
$write_test = @file_put_contents($test_file, 'Test write');
echo "Can write files: " . ($write_test !== false ? 'Yes' : 'No') . "\n";
if ($write_test !== false) {
    unlink($test_file);
}

// Test mail function
$mail_test = @mail('test@example.com', 'Test', 'Test');
echo "Mail function available: " . ($mail_test ? 'Yes' : 'No') . "\n";

// Print some server variables
echo "\nServer Information:\n";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "SCRIPT_FILENAME: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";

// Check if common PHP functions are available
$functions = array('json_encode', 'mail', 'file_get_contents', 'curl_init');
echo "\nFunction Availability:\n";
foreach ($functions as $function) {
    echo $function . ': ' . (function_exists($function) ? 'Available' : 'Not available') . "\n";
}

// Check loaded PHP modules
echo "\nLoaded PHP Modules:\n";
$modules = get_loaded_extensions();
echo implode(", ", $modules);
?>
