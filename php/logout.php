<?php
require_once('connect_db.php');
session_start();
session_destroy();
header('Location: /index.php');
?>