<?php 
$db = new mysqli('localhost', 'cs143', '', 'CS143');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
} 

?>