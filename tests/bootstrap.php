<?php
error_reporting( -1 );
$loader = require __DIR__ . "/../vendor/autoload.php";
$loader->addPsr4('OrientDB\\', __DIR__.'/OrientDB');
date_default_timezone_set('UTC');
