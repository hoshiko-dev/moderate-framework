<?php
// Utils
$loader = require '../vendor/autoload.php';
$config=[];
include_once '../config/config.php';
require_once '../app/Utils/ModerateFwUtils.php';
require_once '../app/Utils/Utils.php';

$app = MfBase\MfWrapper::create($config,$loader);

$app->run();