<?php

class EmailController extends Controller {
    function activate( $key, $val, $username, $email ) {
        $this->assign('title', '華語文');
        $this->assign('header', $this->headers());
        $this->assign('content', $this->contents( $key, $val, $username, $email ));
        $this->render();
    }

    function contents( $timestamp, $session, $username, $email ) {
        $html = 
        '<section id="sec-activate">
            <form method="POST" id="form-activate" class="form-horizontal">
                <div class="form-group col-xs-12">
                    <h3>帳號 : '. $username .'</h3>
                    <h3>信箱 : '. $email .'</h3>
                    <input type="hidden" id="username" name="username" class="form-control" value="'. $username .'"/>
                    <input type="hidden" id="email" name="email" class="form-control" value="'. $email .'"/>
                    <input type="hidden" id="timestamp" name="timestamp" class="form-control" value="'. $timestamp .'"/>
                    <input type="hidden" id="session" name="session" class="form-control" value="'. $session .'"/>
                </div>
                <div class="form-group col-xs-12">
                    <input type="text" id="userpass" name="userpass" placeholder="請輸入您的密碼" class="form-control" />
                    <i id="icon-userpass" class="btn fa fa-eye"></i>
                </div>
                <div class="form-group col-xs-12">
                    <input type="text" id="checkpass" name="checkpass" placeholder="請再次輸入您的密碼" class="form-control" />
                    <i id="icon-checkpss" class="btn fa fa-eye"></i>
                </div>
                <div class="form-group col-xs-12">
                    <div class="g-recaptcha" data-sitekey="6LdLuCMUAAAAANHGd41Qo7Mo2jGT_xFD3iALDo1O"></div>
                </div>
                <div class="form-group col-xs-12">
                    <div class="col-xs-6">
                        <a href="/index/activate" class="btn btn-lg btn-block btn-primary g-recaptcha" id="btn-activate-agent" data-sitekey="6LdLuCMUAAAAANHGd41Qo7Mo2jGT_xFD3iALDo1O" data-callback="/email/activate/">重設密碼</a>
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
