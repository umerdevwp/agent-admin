<?php
#require 'vendor/autoload.php';
require (__DIR__) . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::create(__DIR__);
$dotenv->load();