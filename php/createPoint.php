<?php
require_once('connect_db.php');

if (isset($_POST)) {
    $query = "INSERT INTO points (name, region) VALUES ('$_POST[point]', $_POST[regions])";
    mysqli_query($link, $query);
}
header('Location: /index.php');

?>