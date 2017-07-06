<?php
class View
{
    protected $variables = array();
    protected $_controller;
    protected $_action;

    function __construct($controller, $action)
    {   
        $this->_controller = $controller;
        $this->_action = $action;
    }   
 
    public function assign($name, $value)                                       
    {   
        $this->variables[$name] = $value;
    }   

    public function render()
    {   
        extract($this->variables);
        $defaultHeader = APP_PATH . 'app/views/header.php';
        $defaultFooter = APP_PATH . 'app/views/footer.php';
        $defaultLayout = APP_PATH . 'app/views/layout.php';

        $controllerFolder = lcfirst( $this->_controller );

        $controllerHeader = APP_PATH . 'app/views/' . $controllerFolder . '/header.php';
        $controllerFooter = APP_PATH . 'app/views/' . $controllerFolder . '/footer.php';
        $controllerLayout = APP_PATH . 'app/views/' . $controllerFolder . '/' . $this->_action . '.php'; 

        if (file_exists($controllerHeader)) {
            include ($controllerHeader);
        } else {
            include ($defaultHeader);
        }   

        if (file_exists($controllerLayout)) {
            include ($controllerLayout);
        } else {
            include ($defaultLayout);
        }   
    
        if (file_exists($controllerFooter)) {
            include ($controllerFooter);
        } else {
            include ($defaultFooter);
        }   
    }   
}

