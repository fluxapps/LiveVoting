<?php
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$ilias_http = strstr($actual_link, 'Customizing', true);
header('Location: '.$ilias_http."error.php");