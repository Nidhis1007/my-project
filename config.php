<?php
define('DB', 'events_db');
define('USER', 'root');
define('PASS', '');

$hostname_db = 'localhost';
$database_db = DB;
$username_db = USER;
$password_db = PASS;


try {
    $events_db = new mysqli($hostname_db, $username_db, $password_db, $database_db);

    if ($events_db->connect_error) {
        die("Connection failed: " . $events_db->connect_error);
    }

    $events_db->set_charset("utf8");

    //echo "Connected successfully to the database.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>
