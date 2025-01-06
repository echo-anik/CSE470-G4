<?php
// public/index.php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define a constant to prevent direct access to views
define('APP_INIT', true);

// Include the Router
require_once __DIR__ . '/../core/Router.php';

// Instantiate and route
$router = new Router();
$router->route();
?>