<?php

class IndexController extends Controller {
    function index() {
        $this->assign('title', '華語文');
        $this->assign('header', $this->headers());
        $this->assign('content', $this->contents());
        if (isset($_SESSION) && isset($_SESSION['error_code'])) {
            $this->assign('error_code', $_SESSION['error_code']);
            unset($_SESSION['error_code']);
        }
        $this->render();
    }


    function activate() {
        $reg_userpass = '/^(.+){3,80}$/';
        $email = $_POST['email'];
        $timestamp = $_POST['timestamp'];
        $session = $_POST['session'];
        $userpass = $_POST['userpass'];
        $userpass = $_POST['checkpass'];
        if (preg_match($reg_userpass, $userpass)) {
            $res = (new IndexModel)->dbQuery('verify', array('email'=>$email, 'timestamp'=>$timestamp, 'userpass'=>$session));
            if (1 == sizeof($res)) {
                $cnt = (new IndexModel)->dbQuery('activate', array('email'=>$email, 'timestamp'=>$timestamp, 'userpass'=>$userpass));      
            }
            $this->redirect('activated');
        } else {
            $this->redirect('error_activate');
        }
    }

    function admin() {
debugger('mhho','admin login');
        //if ($_POST['g-recaptcha-response']) {
            $reg_username = '/^([a-zA-Z0-9_]){3,50}$/';
            $reg_userpass = '/^(.+){3,80}$/';
            $username = $_POST['username'];
            $userpass = $_POST['userpass'];
    
            if (preg_match($reg_username, $username) && preg_match($reg_userpass, $userpass)) {
                $res = (new IndexModel)->dbQuery('admin', array('username'=>$username, 'userpass'=>$userpass));
                if (1 == sizeof($res)) {
                    $this->assign('username', $res[0]['username']);
                    $_SESSION['admin'] = $res[0];
                    $_SESSION['username'] = $username;
                    $this->redirect('admin');
                } else {
                    $this->redirect('error_login');
                }
            } else {
                $this->redirect('error_login');
            }
        //} else {
        //    $this->redirect();
        //}
    }

    function agent() {
debugger('mhho','agent login');
        //if ($_POST['g-recaptcha-response']) {
            $reg_username = '/^([a-zA-Z0-9_]){3,50}$/';
            $reg_userpass = '/^(.+){3,80}$/';
            $username = $_POST['username'];
            $userpass = $_POST['userpass'];
    
            if (preg_match($reg_username, $username) && preg_match($reg_userpass, $userpass)) {
                $res = (new IndexModel)->dbQuery('agent', array('username'=>$username, 'userpass'=>$userpass));
                if (1 == sizeof($res)) {
                    $this->assign('username', $res[0]['username']);
                    $_SESSION['agent'] = $res[0];
                    $_SESSION['username'] = $username;
                    $this->redirect('agent');
                } else {
                    $this->redirect('error_login');
                }
            } else {
                $this->redirect('error_login');
            }
        //} else {
        //    $this->redirect();
        //}
    }

    function contents() {
        $html = 
        '<section id="sec-login">
            <form method="POST" id="form-login" class="form-horizontal">
                <div class="form-group col-xs-12">
                    <label for="username">帳號</label>
                    <input type="text" id="username" name="username" placeholder="請輸入您的帳號" class="form-control" />
                </div>
                <div class="form-group col-xs-12">
                    <label for="userpass">密碼</label>
                    <input type="password" id="userpass" name="userpass" placeholder="請輸入您的密碼" class="form-control" />
                </div>
                <div class="form-group col-xs-12">
                    <div id="recaptcha" class="g-recaptcha" data-sitekey="6LdLuCMUAAAAANHGd41Qo7Mo2jGT_xFD3iALDo1O" data-callback="mojocallback" data-size="invisible"></div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-5">
                        <a href="/index/agent" class="btn btn-lg btn-block btn-primary " id="btn-login-agent" >教育機構登入</a>
                    </div>
                    <div class="col-xs-1"></div>
                    <div class="col-xs-5">
                        <a href="/index/admin/" class="btn btn-lg btn-block btn-danger " id="btn-login-admin" >管理員登入</a>
                    </div>
                    <div class="col-xs-1"></div>
                </div>
            </form>
        </section>';
        return $html;
    }

    function headers() {
        $html = 
                '<div class="row">
                    <div class="col-xs-12">
                        <h1>華語文教育機構招生績效系統</h1>
                    </div>
                </div>';
        return $html;
    }
}
