<?php

class AgentController extends Controller {
    function dashboard() {
        if (isset($_SESSION['agent'])) {
            $this->assign('title', '華語文-機構');
            $this->assign('header', $this->headers());
            $this->assign('sidebar', $this->sidebars('dashboard'));
            $this->render();
        } else {
            $this->redirect();
        }
    }

    function fill($quarter_id = 0) {
        if (isset($_SESSION['agent'])) {
            $this->assign('title', '華語文-機構');
            $this->assign('header', $this->headers());
            $this->assign('sidebar', $this->sidebars('fill'));
            $academic_agency_fill = (new AgentModel)->dbQuery('academic_agency_fill', array('agency_id'=>$_SESSION['agent']['agency_id']));
            if (sizeof($academic_agency_fill)) {
                $_SESSION['agent']['era_id'] = $academic_agency_fill[0]['era_id'];
                $_SESSION['agent']['quarter'] = $academic_agency_fill[0]['quarter'];
                $_SESSION['agent']['quarter_id'] = $academic_agency_fill[0]['id'];
                $this->assign('academic_agency_fill', $academic_agency_fill);
                $this->assign('academic_era_quarter', $academic_agency_fill[0]['cname']);
                $this->assign('era_id', $academic_agency_fill[0]['era_id']);
                $this->assign('quarter', $academic_agency_fill[0]['quarter']);
                $this->assign('quarter_id', $academic_agency_fill[0]['id']);
                $this->assign('academic_agency_class', (new AgentModel)->dbQuery('academic_agency_class', array('agency_id'=>$_SESSION['agent']['agency_id'], 'era_id'=>$academic_agency_fill[0]['era_id'], 'quarter'=>$academic_agency_fill[0]['quarter'])));
            } else { 
                $this->assign('quarter_id', $quarter_id);
            }
            $this->render();
        } else {
            $this->redirect();
        }
    }

    function filladd($mojo) {
        if (isset($_SESSION['agent'])) {
            $this->assign('title', '華語文-機構');
            $this->assign('header', $this->headers());
            $this->assign('sidebar', $this->sidebars('fill'));
            $this->assign('mojo', $mojo);
            $this->assign('era_id', $_SESSION['agent']['era_id']);
            $this->assign('quarter', $_SESSION['agent']['quarter']);
            $this->assign('quarter_id', $_SESSION['agent']['quarter_id']);
            $this->assign('country_list', (new AgentModel)->dbQuery('refs_country_list'));
            $this->assign('content_list', (new AgentModel)->dbQuery('refs_content_list'));
            $this->assign('major_list', (new AgentModel)->dbQuery('refs_major_list'));
            $this->assign('minor_list', (new AgentModel)->dbQuery('refs_minor_list'));
            $this->assign('target_list', (new AgentModel)->dbQuery('refs_target_list'));
            $this->render();
        } else {
            $this->redirect();
        }
    }

    function fillmod($mojo) {
        if (isset($_SESSION['agent'])) {
            $this->assign('title', '華語文-機構');
            $this->assign('header', $this->headers());
            $this->assign('sidebar', $this->sidebars('fill'));
            $this->assign('era_id', $_SESSION['agent']['era_id']);
            $this->assign('quarter', $_SESSION['agent']['quarter']);
            $this->assign('quarter_id', $_SESSION['agent']['quarter_id']);
            $this->assign('mojo', $mojo);
            $academic_agency_class = (new AgentModel)->dbQuery('academic_agency_class_query', array('class_id'=>$mojo));
            foreach($academic_agency_class as $key=>$val) {
                $academic_agency_class[$key]['note'] = base64_encode($val['note']);
            }
            $this->assign('academic_agency_class', $academic_agency_class);
            $academic_agency_class_country = (new AgentModel)->dbQuery('academic_agency_class_country_query', array('class_id'=>$mojo));
            foreach($academic_agency_class_country as $key=>$val) {
                $academic_agency_class_country[$key]['note'] = base64_encode($val['note']);
            }
            $this->assign('academic_agency_class_country', $academic_agency_class_country);
            $this->assign('country_list', (new AgentModel)->dbQuery('refs_country_list'));
            $this->assign('content_list', (new AgentModel)->dbQuery('refs_content_list'));
            $this->assign('major_list', (new AgentModel)->dbQuery('refs_major_list'));
            $this->assign('minor_list', (new AgentModel)->dbQuery('refs_minor_list'));
            $this->assign('target_list', (new AgentModel)->dbQuery('refs_target_list'));
            $this->render();
        } else {
            $this->redirect();
        }
    }

    function info() {
        if (isset($_SESSION['agent'])) {
            $this->assign('title', '華語文-機構');
            $this->assign('header', $this->headers());
            $this->assign('sidebar', $this->sidebars('info'));
            $this->assign('academic_agency', (new AgentModel)->dbQuery('academic_agency', array('agency_id'=>$_SESSION['agent']['agency_id'])));
            $this->assign('academic_agency_hr', (new AgentModel)->dbQuery('academic_agency_hr', array('agency_id'=>$_SESSION['agent']['agency_id'])));
            $this->assign('academic_agency_contact', (new AgentModel)->dbQuery('academic_agency_contact', array('agency_id'=>$_SESSION['agent']['agency_id'])));
            $this->render();
        } else {
            $this->redirect();
        }
    }

    function report() {
        if (isset($_SESSION['agent'])) {
            $this->assign('title', '華語文-機構');
            $this->assign('header', $this->headers());
            $this->assign('sidebar', $this->sidebars('report'));
            $this->assign('academic_era', (new AgentModel)->dbQuery('academic_era'));
            $this->assign('institution_code', $_SESSION['agent']['institution_code']);
           
            $this->render();
        } else {
            $this->redirect();
        }
    }

    function unlock() {
        if (isset($_SESSION['agent'])) {
            $this->assign('title', '華語文-機構');
            $this->assign('header', $this->headers());
            $this->assign('sidebar', $this->sidebars('unlock'));
            $this->assign('academic_era', (new AgentModel)->dbQuery('academic_era'));
            $this->assign('academic_class', (new AgentModel)->dbQuery('academic_class'));
            $this->assign('academic_agency_unlock', (new AgentModel)->dbQuery('academic_agency_unlock', array('agency_id'=> $_SESSION['agent']['agency_id'])));
            $this->render();
        } else {
            $this->redirect();
        }
    }

    function message() {
        if (isset($_SESSION['agent'])) {
            $this->assign('title', '華語文-機構');
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
        $html .=   '<h2>華語文教育機構招生績效系統</h2>';
        $html .=   '</div>';
        $html .= '</div>';
        return $html;
    }

    function sidebars($tag) {
        $currents = array('fill'=>'', 'info'=>'', 'report'=>'', 'unlock'=>'', 'message'=>'');
        $currents[$tag] = 'current';
        $agency_name = (isset($_SESSION['agent']))? $_SESSION['agent']['academic_institution_cname'] : '機構';
        $html  = '<h2 id="logo"><a href="#">'. $agency_name .'</a></h2>';
        $html .= '<nav id="nav">';
        $html .=  '<ul>';
        $html .=   '<li class="'. $currents['fill'] .'"><a href="/agent/fill/">填報績效</a></li>';
        $html .=   '<li class="'. $currents['info'] .'"><a href="/agent/info/">機構資料</a></li>';
        $html .=   '<li class="'. $currents['report'] .'"><a href="/agent/report/">機構報表</a></li>';
        $html .=   '<li class="'. $currents['unlock'] .'"><a href="/agent/unlock/">修改申請</a></li>';
        $html .=   '<li class="'. $currents['message'] .'"><a href="/agent/message/">留言板</a></li>';
        $html .=   '<li class=""><a href="/agent/logout/">系統登出</a></li>';
		$html .=  '</ul>';
		$html .= '</nav>';
        return $html;
    }

    function topbars() {
        $agent = (isset($_SESSION['agent']))? $_SESSION['agent']['username'] : '';
        $html  = '<nav id="topbar">';
        $html .=  '<ul>';
        $html .=   '<li><a id="btn-agent-profile" href="#">'. $agent .'&nbsp;<i class="fa fa-user-circle" aria-hidden="true"></i></a></li>';
        $html .=   '<li><a id="btn-agent-help" href="#"><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></li>';
        $html .=   '<li><a id="btn-agent-manual" href="#"><i class="fa fa-newspaper-o" aria-hidden="true"></i></a></li>';
        $html .=  '</ul>';
        $html .= '</nav>';
        return $html;
    }
}
