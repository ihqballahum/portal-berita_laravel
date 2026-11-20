<?php
require __DIR__ . '/../includes/bootstrap.php';
$_SESSION = array();
session_destroy();
header('Location: ' . url('admin/login.php'));
exit;
