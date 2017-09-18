<?php 
class Controller
{
    protected $_controller;
    protected $_action;
    protected $_view;
 
    public function __construct($controller, $action)
    {   
        $this->_controller = $controller;
        $this->_action = $action;
        $this->_view = new View($controller, $action);
    }   

    public function assign($name, $value)
    {   
        $this->_view->assign($name, $value);
    }   

    public function debugger($key, $val)                                        
    {   
    }   

    public function redirect($key='') {
        switch($key)
        {   
        case 'agent':
            header("Location:". APP_URL .'/agent/dashboard/');
            break;
        case 'admin':
            header("Location:". APP_URL .'/admin/dashboard/');
            break;
        case 'error_activate':
            if (isset($_SESSION)) {
                session_destroy();
                session_start();
            }
            $_SESSION['error_code'] = 'error_activate';
            header("Location:". APP_URL );
            break;
        case 'activated':
            if (isset($_SESSION)) {
                session_destroy();
                session_start();
            }
            $_SESSION['error_code'] = 'activated';
            header("Location:". APP_URL );
            break;
        case 'error_login':
            if (isset($_SESSION)) {
                session_destroy();
                session_start();
            }
            $_SESSION['error_code'] = 'error_login';
            header("Location:". APP_URL );
            break;
        default:
            if (isset($_SESSION)) {
                session_destroy();
            }
            header("Location:". APP_URL );
        }   
    }   

    public function render()
    {   
        $this->_view->render();
    }   
}
