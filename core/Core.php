<?php
class Core
{
    public function run()
    {   
        date_default_timezone_set('UTC');
        spl_autoload_register(array($this, 'loadClass'));
        
        session_start();
        $this->removeMagicQuotes();
        $this->unregisterGlobals();
        $this->Route();
    }   

    public function Route()
    {   
        $controllerName = 'Index';
        $action = 'index';
        $param = array();

        $url = isset($_GET['url'])? $_GET['url'] : false;

        if ($url) {
            $urlArray = explode('/', $url);
            $urlArray = array_filter($urlArray);
            $controllerName = ucfirst($urlArray[0]);
    
            array_shift($urlArray);
            $action = $urlArray ? $urlArray[0] : 'index';

            array_shift($urlArray);
            $param = $urlArray ? $urlArray : array();
        }   

        if ('Public' == $controllerName) {
            exit;
        }

        $controller = $controllerName . 'Controller';
        $dispatch = new $controller($controllerName, $action);

        if ((int)method_exists($controller, $action)) {
            call_user_func_array(array($dispatch, $action), $param);
        } else {
            exit($controller . " error");                                       
        }   
    }   

    public function stripSlashesDeep($value)
    {
        $value = is_array($value) ? array_map(array($this, 'stripSlashesDeep'), $value) : stripslashes($value);
        return $value;
    }

    public function removeMagicQuotes()
    {
        if (get_magic_quotes_gpc()) {
            $_GET = isset($_GET) ? $this->stripSlashesDeep($_GET ) : '';
            $_POST = isset($_POST) ? $this->stripSlashesDeep($_POST ) : '';
            $_COOKIE = isset($_COOKIE) ? $this->stripSlashesDeep($_COOKIE) : '';
            $_SESSION = isset($_SESSION) ? $this->stripSlashesDeep($_SESSION) : '';
        }
    }

    public function unregisterGlobals()
    {
        if (ini_get('register_globals')) {
            $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
           foreach ($array as $value) {
                foreach ($GLOBALS[$value] as $key => $var) {
                    if ($var === $GLOBALS[$key]) {
                        unset($GLOBALS[$key]);
                    }
                }
            }
        }
    }

    public static function loadClass($class)
    {
        $core = CORE_PATH . $class . '.class.php';
        $controllers = APP_PATH . 'app/controllers/' . $class . '.class.php';
        $models = APP_PATH . 'app/models/' . $class . '.class.php';

        if (file_exists($core)) {
            include $core;
        } elseif (file_exists($controllers)) {
            include $controllers;
        } elseif (file_exists($models)) {
            include $models;
        }
    }
}
