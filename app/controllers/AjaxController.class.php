<?php

class AjaxController extends Controller {
    public function admin($key, $val) {
        $json = array("code"=>0);
        switch($key) 
        {
        case 'academic_agency':
            switch( $val ) 
            {
            case 'add':
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_add', array('institution_code'=>$_POST['institution_code'], 'cname'=>$_POST['cname']));
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'del':
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_del', array('id'=>$_POST['id']));
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'get':
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_get');
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'mod':
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_mod', array('id'=>$_POST['id'], 'institution_code'=>$_POST['institution_code'], 'cname'=>$_POST['cname']));
                $json = array("code"=>1, "data"=>$res);
                break;
            } 
            break;
        case 'academic_agency_agent':
            switch( $val ) 
            {
            case 'add':
                $timestamp = time();
                $session = base64_encode(MD5Prefix . $_POST['username'] . '@@@' . $timestamp . '@@@' . $_POST['agency_id']);
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_agent_add', array('agency_id'=>$_POST['agency_id'], 'username'=>$_POST['username'], 'email'=>$_POST['email'], 'userpass'=>$session, 'timestamp'=>$timestamp));
                if (1 == $res['code']) {
                    $url = APP_URL .'/email/activate/'. $timestamp .'/'. $session .'/' . $_POST['username'] . '/' . $_POST['email'];
                    $this->mailer('add', $_POST['username'], $_POST['email'], $url);
                }
                $json = array("code"=>$res['code'], "data"=>$res);
                break;
            case 'del':
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_agent_del', array('id'=>$_POST['id'], 'agency_id'=>$_POST['agency_id']));
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'get':
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_agent_get');
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'mod':
                $timestamp = time();
                $session = base64_encode(MD5Prefix . $_POST['id'] . '@@@' . $timestamp . '@@@' . $_POST['agency_id']);
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_agent_mod', array('id'=>$_POST['id'], 'agency_id'=>$_POST['agency_id'], 'email'=>$_POST['email'], 'userpass'=>$session, 'timestamp'=>$timestamp));
                $url = APP_URL .'/email/activate/'. $timestamp . '/' . $session .'/'. $res[0]['username'] .'/'. $_POST['email'];
                $this->mailer('mod', $res[0]['username'], $_POST['email'], $url);

                $res = (new AjaxModel)->dbQuery('admin_academic_agency_agent_get');
                $json = array("code"=>1, "data"=>$res);
                break;
            } 
            break;
        case 'academic_agency_unlock':
            switch( $val )
            {
            case 'yes':
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_unlock_yes', array('agency_id'=> $_POST['agency_id'], 'id'=> $_POST['id'], 'online'=>$_POST['online'], 'offline'=>$_POST['offline']));
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'no':
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_unlock_no', array('agency_id'=> $_POST['agency_id'], 'id'=> $_POST['id']));
                $json = array("code"=>1, "data"=>$res);
                break;
            } 
            break;
        case 'academic_era':
            switch( $val )
            {
            case 'add':
                $res = (new AjaxModel)->dbQuery('admin_academic_era_add');
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'mod':
                $res = (new AjaxModel)->dbQuery('admin_academic_era_quarter_mod', array('id'=>$_POST['id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'], 'online'=>$_POST['online'], 'offline'=>$_POST['offline']));    
                $json = array("code"=>1, "data"=>$res);
                break;
            }
            break;
        case 'academic_class':
            switch( $val )
            {
            case 'add':
                $res = (new AjaxModel)->dbQuery('admin_academic_class_add');
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'sel':
                break;
            case 'mod':
                $res = (new AjaxModel)->dbQuery('admin_academic_class_mod', array('checks'=>$_POST['checks']));
                $json = array("code"=>1, "data"=>$res);
                break;
            }
            break;
        case 'profile':
            switch( $val )
            {
            case 'add':
                break;
            case 'sel':
                break;
            case 'mod':
                if (isset($_POST['username'])) {
                    $username = str_replace('NTUE', "", $_POST['username']);

                    if (isset($_POST['email'])) {
                        $res = (new AjaxModel)->dbQuery('admin_profile_email_mod', array('username'=>$username, 'email'=>$_POST['email'],'session'=>$_SESSION['admin']['session']));
                    }
                    if (isset($_POST['userpass'])) {
                        $res = (new AjaxModel)->dbQuery('admin_profile_userpass_mod', array('username'=>$username, 'userpass'=>$_POST['userpass'],'session'=>$_SESSION['admin']['session']));
                    }
                    $json = array("code"=>1, "data"=>$res);
                }
                break;
            }
            break;
        } 
        echo json_encode($json);
    } 

    public function agent($key, $val) {
        $json = array("code"=>0);
        switch($key) 
        {
        case 'academic_agency':
            switch( $val ) 
            {
            case 'get':
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_get', array('id'=>$_POST['id']));
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'mod':
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_mod', array('id'=>$_POST['id'], 'cname'=>$_POST['cname'], 'zipcode'=>$_POST['zipcode'], 'address'=>$_POST['address'], 'established'=>$_POST['established'], 'approval'=>$_POST['approval'], 'note'=>$_POST['note']));
                $json = array("code"=>1, "data"=>$res, "posts"=>$_POST);
                break;
            }
            break;
        case 'academic_agency_class':
            switch( $val )
            {
            case 'add':
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_class_add', array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'], 'major_code'=>$_POST['major_code'], 'minor_code'=>$_POST['minor_code'], 'cname'=>$_POST['cname'], 'weekly'=>$_POST['weekly'], 'weeks'=>$_POST['weeks'], 'adjust'=>$_POST['adjust'], 'content_code'=>$_POST['content_code'], 'target_code'=>$_POST['target_code'], 'people'=>$_POST['people'], 'hours'=>$_POST['hours'], 'total_hours'=>$_POST['total_hours'], 'revenue'=>$_POST['revenue'], 'subsidy'=>$_POST['subsidy'], 'turnover'=>$_POST['turnover'], 'note'=>$_POST['note'], 'country'=>$_POST['country']));
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'del':
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_class_del', array('id'=>$_POST['id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'], 'quarter_id'=>$_POST['quarter_id'], 'agency_id'=>$_POST['agency_id']));
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'get':
                break;
            case 'mod':
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_class_mod', array('cname'=>$_POST['cname'], 'weekly'=>$_POST['weekly'], 'weeks'=>$_POST['weeks'], 'adjust'=>$_POST['adjust'], 'content_code'=>$_POST['content_code'], 'target_code'=>$_POST['target_code'], 'people'=>$_POST['people'], 'hours'=>$_POST['hours'], 'total_hours'=>$_POST['total_hours'], 'revenue'=>$_POST['revenue'], 'subsidy'=>$_POST['subsidy'], 'turnover'=>$_POST['turnover'], 'note'=>$_POST['note'], 'class_id'=>$_POST['class_id'], 'country'=>$_POST['country'], 'agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter_id'=>$_POST['quarter_id'], 'quarter'=>$_POST['quarter']));
                $json = array("code"=>1, "data"=>$res);
                break;
            }
            break;
        case 'academic_agency_class_country':
            switch( $val )
            {
            case 'add':
                break;
            case 'del':
                break;
            case 'get':
                break;
            case 'mod':
                break;
            }
            break;
        case 'academic_agency_contact':
            switch( $val ) 
            {
            case 'add':
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_contact_add', array('agency_id'=>$_POST['agency_id'], 'cname'=>$_POST['cname'], 'title'=>$_POST['title'], 'manager'=>$_POST['manager'], 'staff'=>$_POST['staff'], 'role'=>$_POST['role'], 'area_code'=>$_POST['area_code'], 'phone'=>$_POST['phone'], 'ext'=>$_POST['ext'], 'email'=>$_POST['email'], 'spare_email'=>$_POST['spare_email'], 'primary'=>$_POST['primary']));
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'del':
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_contact_del', array('agency_id'=>$_POST['agency_id'], 'id'=>$_POST['id']));
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'get':
                $res = (new AjaxModel)->dbQuery('agent_cademic_agency_contact', array('id'=>$_POST['id']));
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'mod':
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_contact_mod', array('agency_id'=>$_POST['agency_id'], 'id'=>$_POST['id'], 'cname'=>$_POST['cname'], 'title'=>$_POST['title'], 'manager'=>$_POST['manager'], 'staff'=>$_POST['staff'], 'role'=>$_POST['role'], 'area_code'=>$_POST['area_code'], 'phone'=>$_POST['phone'], 'ext'=>$_POST['ext'], 'email'=>$_POST['email'], 'spare_email'=>$_POST['spare_email'], 'primary'=>$_POST['primary']));
                $json = array("code"=>1, "data"=>$res);
                break;
            }
            break;
        case 'academic_agency_hr':
            switch( $val ) 
            {
            case 'add':
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_hr_add', array('agency_id'=>$_POST['agency_id']));
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'get':
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_hr', array('agency_id'=>$_POST['agency_id']));
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'mod':
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_hr_mod', array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'administration'=>$_POST['administration'], 'subject'=>$_POST['subject'], 'adjunct'=>$_POST['adjunct'], 'reserve'=>$_POST['reserve'], 'others'=>$_POST['others'], 'note'=>$_POST['note']));
                $json = array("code"=>1, "data"=>$res);
                break;
            }
            break;
        case 'academic_agency_unlock':
            switch( $val ) 
            {
            case 'mod':
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_unlock', array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'], 'note'=>$_POST['note'], 'work_days'=>$_POST['work_days'], 'minors'=>$_POST['minors']));
                $json = array("code"=>1, "data"=>$res);
                break;
            }
            break;
        case 'profile':
            switch( $val )
            {
            case 'mod':
                if (isset($_POST['username'])) {
                    $username = str_replace('NTUE', "", $_POST['username']);

                    if (isset($_POST['email'])) {
                        $res = (new AjaxModel)->dbQuery('agent_profile_email_mod', array('agency_id'=>$_POST['agency_id'], 'username'=>$username, 'email'=>$_POST['email']));
                    }
                    if (isset($_POST['userpass'])) {
                        $res = (new AjaxModel)->dbQuery('agent_profile_userpass_mod', array('agency_id'=>$_POST['agency_id'], 'username'=>$username, 'userpass'=>$_POST['userpass']));
                    }
                    $json = array("code"=>1, "data"=>$res);
                }
                break;
            }
            break;
        }
        echo json_encode($json);
    }

    public function uploads($key, $val) {
        $json = array("code"=>0);
        switch($key) 
        {
        case 'academic_agency_class_country':
            $json['post'] = $_POST;    
            $json['mojo'] = $val;
            break;
        }
        echo json_encode($json);
    }

    public function refs($key, $val) {
        $json = array("code"=>0);
        switch($key) 
        {
        case 'academic_institution':
            $res = (new AjaxModel)->dbQuery('refs_academic_institution');
            $json = array("code"=>1, "data"=>$res);
            break;
        case 'academic_agency':
            $res = (new AjaxModel)->dbQuery('refs_academic_agency');
            $json = array("code"=>1, "data"=>$res);
            break;
        case 'area_list':
            $res = (new AjaxModel)->dbQuery('refs_area_list');
            $json = array("code"=>1, "data"=>$res);
            break;
        case 'content_list':
            $res = (new AjaxModel)->dbQuery('refs_content_list');
            $json = array("code"=>1, "data"=>$res);
            break;
        case 'country_list':
            $res = (new AjaxModel)->dbQuery('refs_country_list');
            $json = array("code"=>1, "data"=>$res);
            break;
        case 'major_list':
            $res = (new AjaxModel)->dbQuery('refs_major_list');
            $json = array("code"=>1, "data"=>$res);
            break;
        case 'minor_list':
            $res = (new AjaxModel)->dbQuery('refs_minor_list');
            $json = array("code"=>1, "data"=>$res);
            break;
        case 'target_list':
            $res = (new AjaxModel)->dbQuery('refs_target_list');
            $json = array("code"=>1, "data"=>$res);
            break;
        }        
        echo json_encode($json);
    }

    public function mailer($type, $username, $email, $url) {
        switch($type)
        {
        case 'add':
            $subject = '華語文教育機構績效系統通知信';
            break;
        case 'mod':
            $subject = '華語文教育機構績效系統重設密碼通知信';
            break;
        }
        $message = '您好，您在華語文教育機構招生填報系統的使用者帳號為['. $username .']，請透過以下連結網址設定登入密碼：['. $url .']';
        if (isset($_SESSION) && isset($_SESSION['admin'])) {
            $from = $_SESSION['admin']['email'];
        } else {
            $from = 'xelalee@gmail.com';
        }
        $from = 'wenyu0421@tea.ntue.edu.tw';

        $headers = 'From: 許文諭<' . $from . "> \r\n".
            'Reply-To: ' . $from . " \r\n".
            'X-Mailer: PHP/'. phpversion();

        mail( $email, $subject, $message, $headers );
    }
}
