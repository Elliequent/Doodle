<?php

ob_start();                                                                     // Output buffering (useful for php function)

try {

    $con = new PDO("mysql:dbname=doodle; host=localhost", "root", "");          // Database connection variables
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);                // Setting PDO errormode to warning

} catch(PDOException $e) {

    echo "Connection failed: " . $e->getMessage();                              // Displays error message

}

?>