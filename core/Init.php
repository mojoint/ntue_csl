<?php
defined('CORE_PATH') or define('CORE_PATH', __DIR__.'/');
defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');

require_once CORE_PATH . 'Config.php';
require_once CORE_PATH . 'Core.php';
require_once CORE_PATH . 'lib/Kendo/Autoload.php';
require_once CORE_PATH . 'lib/PHPExcel.php';
$init = new Core;
$init->run();
