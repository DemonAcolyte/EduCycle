<?php
$db_server = "localhost";
$db_user = "s22103168_bookExchange";
$db_pass = "miggy912";
$db_name = "s22103168_bookExchange";

$conn = "";

try {
    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
} catch (mysqli_sql_exception) {
    echo "Error: Cannot connect.";
}
