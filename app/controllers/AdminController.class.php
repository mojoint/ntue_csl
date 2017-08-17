<?php

class AdminController extends Controller {
    function dashboard() {
        if (isset($_SESSION['admin'])) {
            $this->assign('title', '華語文-管理者');
            $this->assign('header', $this->headers());
            $this->assign('sidebar', $this->sidebars('dashboard'));
            $this->render();
        } else {
            $this->redirect();
        }
    }

    function status() {
        if (isset($_SESSION['admin'])) {
            $this->assign('title', '華語文-管理者');
            $this->assign('header', $this->headers());
            $this->assign('sidebar', $this->sidebars('status'));
            $this->render();
        } else {
            $this->redirect();
        }
    }

    function report() {
        if (isset($_SESSION['admin'])) {
            $this->assign('title', '華語文-管理者');
            $this->assign('header', $this->headers());
            $this->assign('sidebar', $this->sidebars('report'));
            $this->assign('academic_era', (new AdminModel)->dbQuery('academic_era'));
            $this->render();
        } else {
            $this->redirect();
        }
    }

    function postman() {
        if (isset($_SESSION['admin'])) {
            $this->assign('title', '華語文-管理者');
            $this->assign('header', $this->headers());
            $this->assign('sidebar', $this->sidebars('postman'));
            $this->render();
        } else {
            $this->redirect();
        }
    }

    function maintain() {
        if (isset($_SESSION['admin'])) {
            $this->assign('title', '華語文-管理者');
            $this->assign('header', $this->headers());
            $this->assign('sidebar', $this->sidebars('maintain'));
            $this->assign('academic_agency',  (new AdminModel)->dbQuery('academic_agency'));
            $this->assign('academic_agency_agent', (new AdminModel)->dbQuery('academic_agency_agent'));
            $this->render();
        } else {
            $this->redirect();
        }
    }

    function unlock() {
        if (isset($_SESSION['admin'])) {
            $this->assign('title', '華語文-管理者');
            $this->assign('header', $this->headers());
            $this->assign('sidebar', $this->sidebars('unlock'));
            $this->assign('academic_agency_unlock', (new AdminModel)->dbQuery('academic_agency_unlock'));
            $this->render();
        } else {
            $this->redirect();
        }
    }

    function settings() {
        if (isset($_SESSION['admin'])) {
            $this->assign('title', '華語文-管理者');
            $this->assign('header', $this->headers());
            $this->assign('sidebar', $this->sidebars('settings'));
            $academic_era = (new AdminModel)->dbQuery('academic_era');
            $this->assign('academic_era', $academic_era);
            $this->assign('academic_era_quarter', (new AdminModel)->dbQuery('academic_era_quarter'));
            $this->assign('academic_class', (new AdminModel)->dbQuery('academic_class', array('era_id'=>$academic_era[0]['id'])));
            $this->render();
        } else {
            $this->redirect();
        }
    }

    function message() {
        if (isset($_SESSION['admin'])) {
            $this->assign('title', '華語文-管理者');
            $this->assign('header', $this->headers());
            $this->assign('sidebar', $this->sidebars('message'));
            $this->render();
        } else {
            $this->redirect();
        }
    }

    function logout() {
        $this->redirect();
    }    

    function headers() {
        $html  = $this->topbars();
        $html .= '<div class="row">';
        $html .=  '<div class="col-xs-12">';
        $html .=   '<h1>華語文教育機構績效管理系統</h1>';
        $html .=   '</div>';
        $html .= '</div>';
        return $html;
    }

    function sidebars( $current ) {
        $currents = array('status'=>'', 'postman'=>'', 'report'=>'', 'maintain'=>'', 'unlock'=>'', 'settings'=>'', 'message'=>'');
        $currents[$current] = 'current';
     
        $html  = '<h2 id="logo"><a href="#">管理者</a></h2>';
        $html .= '<nav id="nav">';
        $html .=  '<ul>';
        $html .=   '<li class="'. $currents['status'] .'"><a href="/admin/status/">填報狀況</a></li>';
        $html .=   '<li class="'. $currents['report'] .'"><a href="/admin/report/">管理報表</a></li>';
        $html .=   '<li class="'. $currents['postman'] .'"><a href="/admin/postman/">小郵差</a></li>';
        $html .=   '<li class="'. $currents['maintain'] .'"><a href="/admin/maintain/">資料維護</a></li>';
        $html .=   '<li class="'. $currents['unlock'] .'"><a href="/admin/unlock/">解鎖管理</a></li>';
        $html .=   '<li class="'. $currents['settings'] .'"><a href="/admin/settings/">系統設定</a></li>';
        $html .=   '<li class="'. $currents['message'] .'"><a href="/admin/message/">留言回覆</a></li>';
        $html .=   '<li class=""><a href="/admin/logout/">系統登出</a></li>';
		$html .=  '</ul>';
		$html .= '</nav>';
        return $html;
    }

    function topbars() {
        $html  = '<nav id="topbar">';
        $html .=  '<ul>';
        $html .=   '<li><a href="#" class="btn" id="btn-admin"><b></b>&nbsp;<i class="fa fa-user-circle" aria-hidden="true"></i>&nbsp;</a></li>';
        $html .=   '<li><a href="#" class="btn" id="btn-help"><i class="fa fa-question-circle-o" aria-hidden="true"></i>&nbsp;</a></li>';
        $html .=   '<li><a href="#" class="btn" id="btn-manual"><i class="fa fa-newspaper-o" aria-hidden="true"></i>&nbsp;</a></li>';
        $html .=  '</ul>';
        $html .= '</nav>';
        return $html;
    }
}
