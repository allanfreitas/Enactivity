<?php

// debug mode on
defined('YII_DEBUG') or define('YII_DEBUG', true);

// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

// This is the test Web application configuration. Any writable
// CWebApplication properties can be configured here.
// Overrides any settings from main.inc.php
return CMap::mergeArray(
	require(dirname(__FILE__).'/main.inc.php'),
	array(
		'components'=>array(
			'db'=>array(
					'connectionString' => 'mysql:host=127.0.0.1;dbname=poncla_yii',
					'emulatePrepare' => true,
					'enableProfiling'=>true,
					'username' => 'root',
					'password' => '',
					'charset' => 'utf8',
					'enableParamLogging'=>false
			),
	
			'log'=>array(
				'class'=>'CLogRouter',
				'routes'=>array(
					// output errors and warning to runtime file
					array(
						'class'=>'CFileLogRoute',
						'levels'=>'error, warning',
					),
					// show log messages on web pages
					array(
						'class'=>'CWebLogRoute',
					),
				),
			),
		),
		
		//'theme'=>'', //use yii-default
	)
);