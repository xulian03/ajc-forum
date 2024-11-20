<?php
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // error reporting
    $connection = new mysqli('127.0.0.1', 'root', '', 'ajc-forum');
    $connection->set_charset('utf8mb4');

    if(!$connection) {
        header("Location: home.php");
    }
?>