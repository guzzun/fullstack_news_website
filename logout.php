<?php
require 'config.php';
session_start();
session_destroy();

include_once 'logs.php';
write_logs("logout");

header('Location: http://' . $_SERVER['SERVER_NAME'] . $path);