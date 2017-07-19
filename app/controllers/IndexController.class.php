<?php

class IndexController extends Controller {
    function index() {
        $this->assign('title', '華語文');
        $this->assign('header', $this->headers());
        $this->assign('content', $this->contents());
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
            $this->redirect();
        } else {
            $this->redirect();
        }
    }

    function admin() {
        //if ($_POST['g-recaptcha-response']) {
            $reg_username = '/^([a-zA-Z0-9]){3,50}$/';
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
                    $this->redirect();
                }
            } else {
                $this->redirect();
            }
        //} else {
        //    $this->redirect();
        //}
    }

    function agent() {
        //if ($_POST['g-recaptcha-response']) {
            $reg_username = '/^([a-zA-Z0-9]){3,50}$/';
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
                    $this->redirect();
                }
            } else {
                $this->redirect();
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
                    <div class="g-recaptcha" data-sitekey="6LdLuCMUAAAAANHGd41Qo7Mo2jGT_xFD3iALDo1O"></div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-6">
                        <a href="/index/agent" class="btn btn-lg btn-block btn-primary g-recaptcha" id="btn-login-agent" data-sitekey="6LdLuCMUAAAAANHGd41Qo7Mo2jGT_xFD3iALDo1O" data-callback="/index/agent/">教育機構登入</a>
                    </div>
                    <div class="col-xs-6">
                        <a href="/index/admin/" class="btn btn-lg btn-block btn-danger g-recaptcha" id="btn-login-admin" data-sitekey="6LdLuCMUAAAAANHGd41Qo7Mo2jGT_xFD3iALDo1O" data-callback="/index/admin/">管理員登入</a>
                    </div>
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
