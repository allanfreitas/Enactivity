<?php

// change the following paths if necessary
//$yiit=dirname(__FILE__).'/../../../../../yii_framework/yiit.php';
$yiit=dirname(__FILE__).'/../../../yii_framework/yiit.php';
$config=dirname(__FILE__).'/../config/server.test.php';

$_SERVER['SERVER_NAME'] = 'http://localhost';

require_once($yiit);
require_once(dirname(__FILE__).'/WebTestCase.php');

Yii::createWebApplication($config);
