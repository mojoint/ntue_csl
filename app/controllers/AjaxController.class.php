<?php

class AjaxController extends Controller {
    public function admin($key, $val) {
        if (!isset($_SESSION)) { exit; }
        $json = array("code"=>0);
        $data = array();
        switch($key) 
        {
        case 'academic_agency':
            switch( $val ) 
            {
            case 'add':
                //$res = (new AjaxModel)->dbQuery('admin_academic_agency_add', array('institution_code'=>$_POST['institution_code'], 'cname'=>$_POST['cname']));
                $data = array('institution_code'=>$_POST['institution_code'], 'cname'=>$_POST['cname']);
                $id = (new AjaxModel)->dbLogger('admin', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_add', $data);
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'del':
                //$res = (new AjaxModel)->dbQuery('admin_academic_agency_del', array('id'=>$_POST['id']));
                $data = array('id'=>$_POST['id']);
                $id = (new AjaxModel)->dbLogger('admin', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_del', $data);
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'get':
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_get');
                $id = (new AjaxModel)->dbLogger('admin', $key, $val, implode('@@@', $data));
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'mod':
                //$res = (new AjaxModel)->dbQuery('admin_academic_agency_mod', array('id'=>$_POST['id'], 'institution_code'=>$_POST['institution_code'], 'cname'=>$_POST['cname']));
                $data = array('id'=>$_POST['id'], 'institution_code'=>$_POST['institution_code'], 'cname'=>$_POST['cname'], 'agency_state'=>$_POST['agency_state']);
                $id = (new AjaxModel)->dbLogger('admin', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_mod', $data);
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
                //$res = (new AjaxModel)->dbQuery('admin_academic_agency_agent_add', array('agency_id'=>$_POST['agency_id'], 'username'=>$_POST['username'], 'email'=>$_POST['email'], 'userpass'=>$session, 'timestamp'=>$timestamp));
                $data = array('agency_id'=>$_POST['agency_id'], 'username'=>$_POST['username'], 'email'=>$_POST['email'], 'userpass'=>$session, 'timestamp'=>$timestamp);
                $id = (new AjaxModel)->dbLogger('admin', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_agent_add', $data);
                if (1 == $res['code']) {
                    $url = APP_URL .'/email/activate/'. $timestamp .'/'. $session .'/' . $_POST['username'] . '/' . $_POST['email'];
                    $this->mailer('add', $_POST['username'], $_POST['email'], $url);
                }
                $json = array("code"=>$res['code'], "data"=>$res);
                break;
            case 'del':
                //$res = (new AjaxModel)->dbQuery('admin_academic_agency_agent_del', array('id'=>$_POST['id'], 'agency_id'=>$_POST['agency_id']));
                $data = array('id'=>$_POST['id'], 'agency_id'=>$_POST['agency_id']);
                $id = (new AjaxModel)->dbLogger('admin', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_agent_del', $data);
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'get':
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_agent_get');
                $id = (new AjaxModel)->dbLogger('admin', $key, $val, implode('@@@', $data));
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'mod':
                $timestamp = time();
                $session = base64_encode(MD5Prefix . $_POST['id'] . '@@@' . $timestamp . '@@@' . $_POST['agency_id']);
                //$res = (new AjaxModel)->dbQuery('admin_academic_agency_agent_mod', array('id'=>$_POST['id'], 'agency_id'=>$_POST['agency_id'], 'email'=>$_POST['email'], 'userpass'=>$session, 'timestamp'=>$timestamp));
                $data = array('id'=>$_POST['id'], 'agency_id'=>$_POST['agency_id'], 'email'=>$_POST['email'], 'userpass'=>$session, 'timestamp'=>$timestamp);
                $id = (new AjaxModel)->dbLogger('admin', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_agent_mod', $data);
                $url = APP_URL .'/email/activate/'. $timestamp . '/' . $session .'/'. $res[0]['username'] .'/'. $_POST['email'];
                $this->mailer('mod', $res[0]['username'], $_POST['email'], $url);

                $res = (new AjaxModel)->dbQuery('admin_academic_agency_agent_get');
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'chk':
                $res =  (new AjaxModel)->dbQuery('admin_check_new_user_add',array('username'=>$_POST['username']));
                $json = array('code'=>1,'data'=>$res);
                break;
            } 
            break;
        case 'academic_agency_status':
            switch( $val ) 
            {
            case 'list': 
                $era_quarter = (new AjaxModel)->dbQuery('admin_academic_era_quarter_get', array('era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter']));
                $rs = (new AjaxModel)->dbQuery('admin_academic_agency_status', array('era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter']));
                $res = array();
                foreach ($rs as $r) {
                    switch( intval($r['state']) )
                    {
                    case 0:
                        if (intval($r['classes']) > 0) {
                            if (strtotime('now') < strtotime($r['regular_offline'] . ' 23:59:59')) {
                                $r['state'] = '填報中';
                            } else {
                                if ((intval($r['unlock']) == 1) && (sizeof($r['offline']) && (strtotime('now') < strtotime($r['offline'] . ' 23:59:59')))) {
                                    $r['state'] = '延長填報期限';
                                } else {
                                    $r['state'] = '填報截止'; 
                                }
                            }    
                        } else {
                            $r['state'] = '未填報';
                        }
                        break;
                    case 1:
                        if (intval($r['classes']) > 0) {
                            $r['state'] = '完成送件';
                        } else {
                            $r['state'] = '完成送件 無課程';
                        }
                        break;
                    }
/*
                    switch( intval($r['state']) ) 
                    {
                    case -1:
                        $r['state'] = '尚未填報';
                        break;
                    case 0:
                        if (strlen($r['offline'])) {
                            if (strtotime($r['offline'] . ' 23:59:59') - time() > 0) {
                                $r['state'] = '延長填報期限';
                            } else {
                                $r['state'] = '延長填報截止';
                            }
                        } else {
                            if (strtotime($era_quarter[0]['offline'] . ' 23:59:59') - time() > 0) {
                                if ($r['cnt']) {
                                    $r['state'] = '填報中'; 
                                } else {
                                    $r['state'] = '尚未填報';
                                }
                            } else {
                                $r['state'] = '填報截止';
                            }
                        }
                        break;
                    case 1:
                        $r['state'] = '完成送件';
                        break;
                    }
*/
                    array_push( $res, $r );
                }
                break;
            case 'agency':
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_status_byid', array('agency_id'=>$_POST['agency_id']));
                break;
            }
            $json = array("code"=>1, "data"=>$res);
            break;
        case 'academic_agency_unlock':
            switch( $val )
            {
            case 'yes':
                //$res = (new AjaxModel)->dbQuery('admin_academic_agency_unlock_yes', array('agency_id'=> $_POST['agency_id'], 'id'=> $_POST['id'], 'online'=>$_POST['online'], 'offline'=>$_POST['offline']));
                $data = array('agency_id'=> $_POST['agency_id'], 'id'=> $_POST['id'], 'online'=>$_POST['online'], 'offline'=>$_POST['offline']);
                $id = (new AjaxModel)->dbLogger('admin', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_unlock_yes', $data);
                $json = array("code"=>1, "data"=>$res);
                $mailer = (new AjaxModel)->dbQuery('admin_academic_agency_unlock_who', array('agency_id'=> $_POST['agency_id']));
                if (sizeof($mailer))
                    $this->mailer('unlocker', $mailer, 'unlock', 'yes');
                break;
            case 'no':
                //$res = (new AjaxModel)->dbQuery('admin_academic_agency_unlock_no', array('agency_id'=> $_POST['agency_id'], 'id'=> $_POST['id']));
                $data = array('agency_id'=> $_POST['agency_id'], 'id'=> $_POST['id']);
                $id = (new AjaxModel)->dbLogger('admin', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('admin_academic_agency_unlock_no', $data);
                $json = array("code"=>1, "data"=>$res);
                $mailer = (new AjaxModel)->dbQuery('admin_academic_agency_unlock_who', array('agency_id'=> $_POST['agency_id']));
                if (sizeof($mailer))
                    $this->mailer('unlocker', $mailer, 'unlock', 'yes');
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
                //$res = (new AjaxModel)->dbQuery('admin_academic_era_quarter_mod', array('id'=>$_POST['id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'], 'online'=>$_POST['online'], 'offline'=>$_POST['offline']));
                $data = array('id'=>$_POST['id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'], 'online'=>$_POST['online'], 'offline'=>$_POST['offline']);
                $id = (new AjaxModel)->dbLogger('admin', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('admin_academic_era_quarter_mod', $data);
                $json = array("code"=>1, "data"=>$res);
                break;
            }
            break;
        case 'academic_class':
            switch( $val )
            {
            case 'add':
                //$res = (new AjaxModel)->dbQuery('admin_academic_class_add', array('major_code'=>$_POST['major'], 'minor_code'=>$_POST['minor'], 'cname'=>$_POST['cname'], 'era_id'=>$_POST['era_id']));
                $data = array('major_code'=>$_POST['major'], 'minor_code'=>$_POST['minor'], 'cname'=>$_POST['cname'], 'era_id'=>$_POST['era_id']);
                $id = (new AjaxModel)->dbLogger('admin', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('admin_academic_class_add', $data);
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'sel':
                break;
            case 'mod':
                //$res = (new AjaxModel)->dbQuery('admin_academic_class_mod', array('checks'=>$_POST['checks'], 'era_id'=>$_POST['era_id'], 'taken'=>$_POST['taken']));
                $data = array('checks'=>$_POST['checks'], 'era_id'=>$_POST['era_id'], 'taken'=>$_POST['taken']);
                $id = (new AjaxModel)->dbLogger('admin', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('admin_academic_class_mod', $data);
                $json = array("code"=>1, "data"=>$res);
                break;
            }
            break;
        case 'academic_classes':
            $data = array('era_id'=>$val);
            $res = (new AjaxModel)->dbQuery('admin_academic_classes', $data);
            $json = array("code"=>1, "data"=>$res);
            break;
        case 'dashboard':
            switch( $val )
            {
            case 'update':
                $data = array('dashboard'=>$_POST['dashboard']);
                $id = (new AjaxModel)->dbLogger('admin', $key, $val, $data['dashboard']);
                $res = (new AjaxModel)->dbQuery('admin_dashboard_update', $data);
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
                        //$res = (new AjaxModel)->dbQuery('admin_profile_email_mod', array('username'=>$username, 'email'=>$_POST['email'],'session'=>$_SESSION['admin']['session']));
                        $data = array('username'=>$username, 'email'=>$_POST['email'],'session'=>$_SESSION['admin']['session']);
                        $id = (new AjaxModel)->dbLogger('admin', $key, $val, implode('@@@', $data));
                        $res = (new AjaxModel)->dbQuery('admin_profile_email_mod', $data);
                    }
                    if (isset($_POST['userpass'])) {
                        //$res = (new AjaxModel)->dbQuery('admin_profile_userpass_mod', array('username'=>$username, 'userpass'=>$_POST['userpass'],'session'=>$_SESSION['admin']['session']));
                        $data = array('username'=>$username, 'userpass'=>$_POST['userpass'],'session'=>$_SESSION['admin']['session']);
                        $id = (new AjaxModel)->dbLogger('admin', $key, $val, implode('@@@', $data));
                        $res = (new AjaxModel)->dbQuery('admin_profile_userpass_mod', $data);
                    }
                    $json = array("code"=>1, "data"=>$res);
                }
                break;
            }
            break;
        case 'message':
            switch($val)
            {
            case 'noReplyMsgQry':
                if (isset($_SESSION['admin'])) {
                    $res = (new AjaxModel)->dbQuery('admin_board_unreply_query');
                    $json = array("code"=>1, "data"=>$res);
                }
    
                break;
            case 'replyMsgSave':
                if (isset($_SESSION['admin'])) {
                    //$res = (new AjaxModel)->dbQuery('admin_board_save_reply',array('message_id'=>$_POST['msgid'],'admin_id'=>$_SESSION['admin']['id'],'reply_content'=>$_POST['replyContent']));
                    $data = array('message_id'=>$_POST['msgid'],'admin_id'=>$_SESSION['admin']['id'],'reply_content'=>$_POST['replyContent']);
                    $id = (new AjaxModel)->dbLogger('admin', $key, $val, implode('@@@', $data));
                    $res = (new AjaxModel)->dbQuery('admin_board_save_reply',$data);
                    $json = array("code"=>1, "data"=>$res);
                }
                break;
            }
            break;
        case 'postman':
            switch($val)
            {                                                                             
            case 'emailSend':
               if (isset($_SESSION['admin'])) {

                    mb_internal_encoding('UTF-8');
                    $receverCnt = 0;
                    switch($_POST['emailRcptTo'])
                    {
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                        $receverList = (new AjaxModel)->dbQuery('admin_postman_receverlist',array('rcpttotype'=>$_POST['emailRcptTo']));
                        debugger('postman_mhho',date('Y-m-d H:i:s',time())."\t".$_POST['emailSubject']."\tTotal mail count:".count($receverList));
                        foreach($receverList as $recever){
                            $receverCnt++;
                            $email = $recever['email'];
                            $emailName = $recever['cname'];
                            debugger('postman_mhho',date('Y-m-d H:i:s',time())."\t". $receverCnt."\t".$_POST['emailSubject']."\t".$email."\t".$emailName);
                            $subject = mb_encode_mimeheader($_POST['emailSubject'], 'UTF-8');
                            $message = $_POST['emailBody']."\n";
                            $from = 'enjouli82029@tea.ntue.edu.tw';
                            $headers = "Content-type: text/html; charset=UTF-8\r\n";
                            $headers .= 'From: 李恩柔<' . $from . "> \r\n".
                            'Reply-To: ' . $from . " \r\n".
                            'X-Mailer: PHP/'. phpversion();
                            mail( $email, $subject, $message, $headers );
                        }
                        // if cc

                        if (isset($_POST['emailCcTo']) && strlen($_POST['emailCcTo'])) {
                            $receverList = explode(';', $_POST['emailCcTo']);
                            foreach($receverList as $recever){
                                $receverCnt++;
                                $email = $recever;
                                $subject = $_POST['emailSubject'];
                                $message = $_POST['emailBody']."\n";
                                $from = 'enjouli82029@tea.ntue.edu.tw';
                                $headers = "Content-type: text/html; charset=UTF-8\r\n";
                                $headers .= 'From: '. mb_encode_mimeheader('李恩柔', 'UTF-8') .'<' . $from . "> \r\n".
                                'Reply-To: ' . $from . " \r\n".
                                'X-Mailer: PHP/'. phpversion();
                                //mail( $email, $subject, $message, $headers );
                            }
                        }
                        break;
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                        $receverList = (new AjaxModel)->dbQuery('admin_postman_receverlist',array('rcpttotype'=>$_POST['emailRcptTo']));
                        debugger('postman_mhho',date('Y-m-d H:i:s',time())."\t".$_POST['emailSubject']."\tTotal mail count:".count($receverList));
                        $message = "";
                        foreach($receverList as $recever){
                            $receverCnt++;
                            $email = $recever['email'];
                            $emailName = $recever['cname'];
                            debugger('postman_mhho',date('Y-m-d H:i:s',time())."\t". $receverCnt."\t".$_POST['emailSubject']."\t".$email."\t".$emailName);
                            $subject = mb_encode_mimeheader($_POST['emailSubject'], 'UTF-8');
                            //$message = $_POST['emailBody']."\n";
                            $from = 'enjouli82029@tea.ntue.edu.tw';
                            $headers = "Content-type: text/html; charset=UTF-8\r\n";
                            $headers .= 'From: 李恩柔<' . $from . "> \r\n".
                            'Reply-To: ' . $from . " \r\n".
                            'X-Mailer: PHP/'. phpversion();
                            //mail( $email, $subject, $message, $headers );
                            $message .= "<p>" . $email + "</p> \r\n";
                        }
                        // if cc
                        if (isset($_POST['emailCcTo']) && strlen($_POST['emailCcTo'])) {
                            $receverList = explode(';', $_POST['emailCcTo']);
                            foreach($receverList as $recever){
                                $receverCnt++;
                                $email = $recever;
                                $subject = $_POST['emailSubject'];
                                //$message = $_POST['emailBody']."\n";
                                $from = 'enjouli82029@tea.ntue.edu.tw';
                                $headers = "Content-type: text/html; charset=UTF-8\r\n";
                                $headers .= 'From: '. mb_encode_mimeheader('李恩柔', 'UTF-8') .'<' . $from . "> \r\n".
                                'Reply-To: ' . $from . " \r\n".
                                'X-Mailer: PHP/'. phpversion();
                                mail( $email, $subject, $message, $headers );
                            }
                        }
                        break;
                    case 9:
                        if (isset($_POST['emailCcTo']) && strlen($_POST['emailCcTo'])) {
                            $receverList = explode(';', $_POST['emailCcTo']);
                            foreach($receverList as $recever){
                                $receverCnt++;
                                $email = $recever;
                                $subject = mb_encode_mimeheader($_POST['emailSubject'], 'UTF-8');
                                $message = $_POST['emailBody']."\n";
                                $from = 'enjouli82029@tea.ntue.edu.tw';
                                $headers = "Content-type: text/html; charset=UTF-8\r\n";
                                $headers .= 'From: '. mb_encode_mimeheader('李恩柔', 'UTF-8') .'<' . $from . "> \r\n".
                                'Reply-To: ' . $from . " \r\n".
                                'X-Mailer: PHP/'. phpversion();
                                mail( $email, $subject, $message, $headers );
                            }
                        }
                        break;
                    }
                    $json = array("code"=>1, "data"=>$receverCnt, "recievers"=>$recievers, "ccs"=>$ccs);
                }
                    
                break;
            }
            break;
        } 
        echo json_encode($json);
    } 

    public function agent($key, $val) {
        if (!isset($_SESSION)) { exit; }
        $json = array("code"=>0);
        $data = array();
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
                //$res = (new AjaxModel)->dbQuery('agent_academic_agency_mod', array('id'=>$_POST['id'], 'cname'=>$_POST['cname'], 'zipcode'=>$_POST['zipcode'], 'address'=>$_POST['address'], 'established'=>$_POST['established'], 'approval'=>$_POST['approval'], 'note'=>$_POST['note']));
                $data = array('id'=>$_POST['id'], 'cname'=>$_POST['cname'], 'zipcode'=>$_POST['zipcode'], 'address'=>$_POST['address'], 'established'=>$_POST['established'], 'approval'=>$_POST['approval'], 'note'=>$_POST['note']);
                $id = (new AjaxModel)->dbLogger('agent', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_mod', $data);
                $json = array("code"=>1, "data"=>$res, "posts"=>$_POST);
                break;
            }
            break;
        case 'academic_agency_class':
            switch( $val )
            {
            case 'add':
                $country = (isset($_POST['country']))? $_POST['country'] : array();
                //$res = (new AjaxModel)->dbQuery('agent_academic_agency_class_add', array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'], 'major_code'=>$_POST['major_code'], 'minor_code'=>$_POST['minor_code'], 'cname'=>$_POST['cname'], 'weekly'=>$_POST['weekly'], 'weeks'=>$_POST['weeks'], 'adjust'=>$_POST['adjust'], 'content_code'=>$_POST['content_code'], 'target_code'=>$_POST['target_code'], 'new_people'=>$_POST['new_people'], 'people'=>$_POST['people'], 'hours'=>$_POST['hours'], 'total_hours'=>$_POST['total_hours'], 'revenue'=>$_POST['revenue'], 'subsidy'=>$_POST['subsidy'], 'turnover'=>$_POST['turnover'], 'note'=>$_POST['note'], 'country'=>$country));
                $data = array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'], 'major_code'=>$_POST['major_code'], 'minor_code'=>$_POST['minor_code'], 'cname'=>$_POST['cname'], 'weekly'=>$_POST['weekly'], 'weeks'=>$_POST['weeks'], 'adjust'=>$_POST['adjust'], 'content_code'=>$_POST['content_code'], 'target_code'=>$_POST['target_code'], 'new_people'=>$_POST['new_people'], 'people'=>$_POST['people'], 'hours'=>$_POST['hours'], 'total_hours'=>$_POST['total_hours'], 'revenue'=>$_POST['revenue'], 'subsidy'=>$_POST['subsidy'], 'turnover'=>$_POST['turnover'], 'note'=>$_POST['note'], 'country'=>$country);
                $rdata = $data;
                $rdata['country'] = "";
                $id = (new AjaxModel)->dbLogger('agent', $key, $val, implode('@@@', $rdata));
                $cdata = array();
                foreach( $country as $c ) {
                    array_push($cdata, implode('@@@', $c));
                }
                $id = (new AjaxModel)->dbLogger('agent', $key . '_country', $val, implode('@@@', $cdata));
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_class_add', $data);
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'del':
                //$res = (new AjaxModel)->dbQuery('agent_academic_agency_class_del', array('id'=>$_POST['id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'], 'agency_id'=>$_POST['agency_id']));
                $data = array('id'=>$_POST['id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'], 'agency_id'=>$_POST['agency_id']);
                $id = (new AjaxModel)->dbLogger('agent', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_class_del', $data);
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'get':
                break;
            case 'done':
                //$res = (new AjaxModel)->dbQuery('agent_academic_agency_class_done', array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'] ));
                $data = array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter']);
                $id = (new AjaxModel)->dbLogger('agent', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_class_done', $data);
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'import':
                //$res = (new AjaxModel)->dbQuery('agent_academic_agency_class_import', array('agency_id'=>$_POST['agency_id'], 'id'=>$_POST['id']));
                $data = array('agency_id'=>$_POST['agency_id'], 'id'=>$_POST['id']);
                $id = (new AjaxModel)->dbLogger('agent', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_class_import', $data);
                $json = array("code"=>1, "data"=>$res, 'post'=>$_POST);
                break;
            case 'mod':
                $country = (isset($_POST['country']))? $_POST['country'] : array();
                //$res = (new AjaxModel)->dbQuery('agent_academic_agency_class_mod', array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'], 'class_id'=>$_POST['class_id'], 'minor_code'=>$_POST['minor_code'], 'cname'=>$_POST['cname'], 'weekly'=>$_POST['weekly'], 'weeks'=>$_POST['weeks'], 'adjust'=>$_POST['adjust'], 'content_code'=>$_POST['content_code'], 'target_code'=>$_POST['target_code'], 'new_people'=>$_POST['new_people'], 'people'=>$_POST['people'], 'hours'=>$_POST['hours'], 'total_hours'=>$_POST['total_hours'], 'revenue'=>$_POST['revenue'], 'subsidy'=>$_POST['subsidy'], 'turnover'=>$_POST['turnover'], 'note'=>$_POST['note'], 'country'=>$country));
                $data = array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'], 'class_id'=>$_POST['class_id'], 'minor_code'=>$_POST['minor_code'], 'cname'=>$_POST['cname'], 'weekly'=>$_POST['weekly'], 'weeks'=>$_POST['weeks'], 'adjust'=>$_POST['adjust'], 'content_code'=>$_POST['content_code'], 'target_code'=>$_POST['target_code'], 'new_people'=>$_POST['new_people'], 'people'=>$_POST['people'], 'hours'=>$_POST['hours'], 'total_hours'=>$_POST['total_hours'], 'revenue'=>$_POST['revenue'], 'subsidy'=>$_POST['subsidy'], 'turnover'=>$_POST['turnover'], 'note'=>$_POST['note'], 'country'=>$country);
                $rdata = $data;
                $rdata['country'] = "";
                $id = (new AjaxModel)->dbLogger('agent', $key, $val, implode('@@@', $rdata));
                $cdata = array();
                foreach( $country as $c ) {
                    array_push($cdata, implode('@@@', $c));
                }
                $id = (new AjaxModel)->dbLogger('agent', $key . '_country', $val, implode('@@@', $cdata));
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_class_mod', $data);
                $json = array("code"=>1, "data"=>$res);
                break;
            }
            break;
        case 'academic_agency_contact':
            switch( $val ) 
            {
            case 'add':
                //$res = (new AjaxModel)->dbQuery('agent_academic_agency_contact_add', array('agency_id'=>$_POST['agency_id'], 'cname'=>$_POST['cname'], 'title'=>$_POST['title'], 'manager'=>$_POST['manager'], 'staff'=>$_POST['staff'], 'role'=>$_POST['role'], 'area_code'=>$_POST['area_code'], 'phone'=>$_POST['phone'], 'ext'=>$_POST['ext'], 'email'=>$_POST['email'], 'spare_email'=>$_POST['spare_email'], 'primary'=>$_POST['primary']));
                $data = array('agency_id'=>$_POST['agency_id'], 'cname'=>$_POST['cname'], 'title'=>$_POST['title'], 'manager'=>$_POST['manager'], 'staff'=>$_POST['staff'], 'role'=>$_POST['role'], 'area_code'=>$_POST['area_code'], 'phone'=>$_POST['phone'], 'ext'=>$_POST['ext'], 'email'=>$_POST['email'], 'spare_email'=>$_POST['spare_email'], 'primary'=>$_POST['primary']);
                $id = (new AjaxModel)->dbLogger('agent', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_contact_add', $data);
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'del':
                //$res = (new AjaxModel)->dbQuery('agent_academic_agency_contact_del', array('agency_id'=>$_POST['agency_id'], 'id'=>$_POST['id']));
                $data = array('agency_id'=>$_POST['agency_id'], 'id'=>$_POST['id']);
                $id = (new AjaxModel)->dbLogger('agent', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_contact_del', $data);
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'get':
                //$res = (new AjaxModel)->dbQuery('agent_cademic_agency_contact', array('agency_id'=>$_POST['agency_id']));
                $data = array('agency_id'=>$_POST['agency_id']);
                $id = (new AjaxModel)->dbLogger('agent', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('agent_cademic_agency_contact', $data);
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'mod':
                //$res = (new AjaxModel)->dbQuery('agent_academic_agency_contact_mod', array('agency_id'=>$_POST['agency_id'], 'id'=>$_POST['id'], 'cname'=>$_POST['cname'], 'title'=>$_POST['title'], 'manager'=>$_POST['manager'], 'staff'=>$_POST['staff'], 'role'=>$_POST['role'], 'area_code'=>$_POST['area_code'], 'phone'=>$_POST['phone'], 'ext'=>$_POST['ext'], 'email'=>$_POST['email'], 'spare_email'=>$_POST['spare_email'], 'primary'=>$_POST['primary']));
                $data = array('agency_id'=>$_POST['agency_id'], 'id'=>$_POST['id'], 'cname'=>$_POST['cname'], 'title'=>$_POST['title'], 'manager'=>$_POST['manager'], 'staff'=>$_POST['staff'], 'role'=>$_POST['role'], 'area_code'=>$_POST['area_code'], 'phone'=>$_POST['phone'], 'ext'=>$_POST['ext'], 'email'=>$_POST['email'], 'spare_email'=>$_POST['spare_email'], 'primary'=>$_POST['primary']);
                $id = (new AjaxModel)->dbLogger('agent', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_contact_mod', $data);
                $json = array("code"=>1, "data"=>$res);
                break;
            }
            break;
        case 'academic_agency_hr':
            switch( $val ) 
            {
            case 'add':
                //$res = (new AjaxModel)->dbQuery('agent_academic_agency_hr_add', array('agency_id'=>$_POST['agency_id']));
                $data = array('agency_id'=>$_POST['agency_id']);
                $id = (new AjaxModel)->dbLogger('agent', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_hr_add', $data);
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'get':
                //$res = (new AjaxModel)->dbQuery('agent_academic_agency_hr', array('agency_id'=>$_POST['agency_id']));
                $data = array('agency_id'=>$_POST['agency_id']);
                $id = (new AjaxModel)->dbLogger('agent', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_hr', $data);
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'mod':
                //$res = (new AjaxModel)->dbQuery('agent_academic_agency_hr_mod', array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'administration'=>$_POST['administration'], 'subject'=>$_POST['subject'], 'adjunct'=>$_POST['adjunct'], 'reserve'=>$_POST['reserve'], 'others'=>$_POST['others'], 'note'=>$_POST['note']));
                $data = array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'administration'=>$_POST['administration'], 'subject'=>$_POST['subject'], 'adjunct'=>$_POST['adjunct'], 'reserve'=>$_POST['reserve'], 'others'=>$_POST['others'], 'note'=>$_POST['note']);
                $id = (new AjaxModel)->dbLogger('agent', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_hr_mod', $data);
                $json = array("code"=>1, "data"=>$res);
                break;
            }
            break;
        case 'academic_agency_report':
            /* academic_agency_report_quarter */
            /* 0:1~4, 1:1, 2:2, 3:3, 4:4, 5:1~2, 6:2~3, 7:3~4, 8:1~3, 9:2~4 */
            $res = array();
            $res['summary'] = (new AjaxModel)->dbQuery('agent_academic_agency_report_era_summary', array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter']));
            $res['detail'] = (new AjaxModel)->dbQuery('agent_academic_agency_report_era_detail', array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter']));
            $res['pdf'] = (new AjaxModel)->dbQuery('agent_academic_agency_report_era_pdf', array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter']));
            $res['taken'] = (new AjaxModel)->dbQuery('agent_academic_agency_report_era_taken', array('era_id'=>$_POST['era_id']));
            $json = array("code"=>1, "data"=>$res);
            break;
        case 'academic_agency_unlock':
            switch( $val ) 
            {
            case 'mod':
                //$res = (new AjaxModel)->dbQuery('agent_academic_agency_unlock', array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'], 'note'=>$_POST['note'], 'work_days'=>$_POST['work_days'], 'minors'=>$_POST['minors']));
                $data = array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'], 'note'=>$_POST['note'], 'work_days'=>$_POST['work_days'], 'minors'=>$_POST['minors']);
                $id = (new AjaxModel)->dbLogger('agent', $key, $val, implode('@@@', $data));
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_unlock', $data);
                $json = array("code"=>1, "data"=>$res);
                $who = (new AjaxModel)->dbQuery('agent_academic_agency_unlock_who', $data);
                $this->mailer('unlock', $who[0]['academic_institution_cname'], $who[0]['academic_era_cname'], $_POST['quarter']);
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
                        //$res = (new AjaxModel)->dbQuery('agent_profile_email_mod', array('agency_id'=>$_POST['agency_id'], 'username'=>$username, 'email'=>$_POST['email']));
                        $data = array('agency_id'=>$_POST['agency_id'], 'username'=>$username, 'email'=>$_POST['email']);
                        $id = (new AjaxModel)->dbLogger('agent', $key, $val, implode('@@@', $data));
                        $res = (new AjaxModel)->dbQuery('agent_profile_email_mod', $data); 
                    }
                    if (isset($_POST['userpass'])) {
                        //$res = (new AjaxModel)->dbQuery('agent_profile_userpass_mod', array('agency_id'=>$_POST['agency_id'], 'username'=>$username, 'userpass'=>$_POST['userpass']));
                        $data = array('agency_id'=>$_POST['agency_id'], 'username'=>$username, 'userpass'=>$_POST['userpass']);
                        $id = (new AjaxModel)->dbLogger('agent', $key, $val, implode('@@@', $data));
                        $res = (new AjaxModel)->dbQuery('agent_profile_userpass_mod', $data);
                    }
                    $json = array("code"=>1, "data"=>$res);
                }
                break;
            }
            break;
        case 'message':
            switch( $val )
            {
            case 'histMsgQry':
                if (isset($_SESSION['agent'])) {
                    //$res = (new AjaxModel)->dbQuery('agent_board_reply_query', array('agent_id'=>$_SESSION['agent']['id']));
                    $data = array('agent_id'=>$_SESSION['agent']['id']);
                    $id = (new AjaxModel)->dbLogger('agent', $key, $val, implode('@@@', $data));
                    $res = (new AjaxModel)->dbQuery('agent_board_reply_query', $data);
                    $json = array("code"=>1, "data"=>$res);

                }
                break;
            case 'quesSave':
                if (isset($_SESSION['agent'])) {
                    //$res = (new AjaxModel)->dbQuery('agent_board_question_add', array('agent_id'=>$_SESSION['agent']['id'],'question_content'=>$_POST['questionContent']));
                    $data = array('agent_id'=>$_SESSION['agent']['id'],'question_content'=>$_POST['questionContent']);
                    $id = (new AjaxModel)->dbLogger('agent', $key, $val, implode('@@@', $data));
                    $res = (new AjaxModel)->dbQuery('agent_board_question_add', $data);
                    $json = array("code"=>1, "data"=>$res);
                    $who = (new AjaxModel)->dbQuery('agent_board_question_who', $data);
                    $this->mailer('message', $who[0]['academic_institution_cname'], '', '');
                }
    
                break;
            }
            break;
        }
        echo json_encode($json);
    }

    public function uploads($key, $val) {
        if (!isset($_SESSION)) { exit; }
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
        if (!isset($_SESSION)) { exit; }
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

    public function reporter($key, $val, $era_id, $quarter=1, $agency_id=0) {
        if (!(isset($_SESSION['admin']) || isset($_SESSION['agent']))) { exit; }
        switch($key) 
        {
        case 'academic_admin_report':
            error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);
            //date_default_timezone_set('Asia/Taipei');
            define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
            $objPHPExcel = new PHPExcel();
            $sharedStyle = new PHPExcel_Style(); 
            $sharedStyle->applyFromArray(
                array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'borders' => array(
                        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    )
                )
            );

            $sharedStyleT = new PHPExcel_Style(); 
            $sharedStyleT->applyFromArray(
                array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'borders' => array(
                        'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    )
                )
            );

            $targets = (new AjaxModel)->dbQuery('admin_academic_agency_report_targets');
            $majors = (new AjaxModel)->dbQuery('refs_major_list');

            $major_head = array();
            $major_foot = array('S'=>'合計');
            $major_sum = array();
            foreach( $majors as $major ) {
                switch( $major['code'] ) 
                {
                case 'A':
                    $major_head[ $major['code'] ] = '第一類研習類別';
                    $major_foot[ $major['code'] ] = '第一類研習類別小計';
                    break;
                case 'B':
                    $major_head[ $major['code'] ] = '第二類研習類別';
                    $major_foot[ $major['code'] ] = '第二類研習類別小計';
                    break;
                case 'C':
                    $major_head[ $major['code'] ] = '第三類研習類別';
                    $major_foot[ $major['code'] ] = '第三類研習類別小計';
                    break;
                }
            }

            switch( $val )
            {
            case 'era_detail':
                $quarter = 10; 
                $cnt = 0;
                foreach ($targets as $target) {
                    $major_cache = 'A';
                    $major_sum = array(
                        'A'=>array('countries'=>0, 'new_male'=>0, 'new_female'=>0, 'new_people'=>0, 'people'=>0, 'weekly'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0),
                        'B'=>array('countries'=>0, 'new_male'=>0, 'new_female'=>0, 'new_people'=>0, 'people'=>0, 'weekly'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0),
                        'C'=>array('countries'=>0, 'new_male'=>0, 'new_female'=>0, 'new_people'=>0, 'people'=>0, 'weekly'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0),
                        'S'=>array('countries'=>0, 'new_male'=>0, 'new_female'=>0, 'new_people'=>0, 'people'=>0, 'weekly'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0)
                    );
                    if ($cnt > 0) {
                        $objPHPExcel->createSheet();
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt);
                    $objPHPExcel->getActiveSheet()->setTitle( $target['institution_code'] . '-'. $target['institution_cname'] . $target['cname'] );
                    //$res = (new AjaxModel)->dbQuery('agent_academic_agency_report_detail', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'quarter'=>$quarter));
                    $res = (new AjaxModel)->dbQuery('agent_academic_agency_report_era_detail', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'quarter'=>$quarter));
                    $size = sizeof($res);
                    if ($size) {
                        $knt = 1;
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $target['institution_code'] . '-'. $target['institution_cname'] . $target['cname']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                        $knt++;
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '研習類別');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '國別(地區)');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '男新生人數');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, '女新生人數');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '總人數');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, '人次');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, '總人次');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, '每期上課時數');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, '每週平均上課時數');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, '總人時數');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, '營收額度');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, '小註(課程名稱)');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('M' . $knt, '備註');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('N' . $knt, '最後修改時間');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
    
                        $count = 0;
                        $knt++;
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_head[ $major_cache ]);
                        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                        foreach($res as $r) {
                            $count++;
                            $countries = sizeof($r['country']);
                            if ($major_cache != $r['major_code']) {
                                $knt++;
                                $kountry = (new AjaxModel)->dbQuery('agent_academic_agency_report_countries', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'quarter'=>$quarter));
                                $major_sum[ $major_cache ][ 'countries' ] = intval($kountry[0]['countries']);
                                $objPHPExcel->getActiveSheet()->mergeCells('A'. $knt .':B'. $knt);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ $major_cache ]);
/*
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($major_sum[ $major_cache ]['new_male']));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($major_sum[ $major_cache ]['new_female']));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($major_sum[ $major_cache ]['new_people']));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($major_sum[ $major_cache ]['people']));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($major_sum[ $major_cache ]['people']));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($major_sum[ $major_cache ]['hours'], 2));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($major_sum[ $major_cache ]['weekly'], 2));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, number_format($major_sum[ $major_cache ]['total_hours'], 2));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, number_format($major_sum[ $major_cache ]['turnover']));
*/

                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $major_sum[ $major_cache ]['new_male']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $major_sum[ $major_cache ]['new_female']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $major_sum[ $major_cache ]['new_people']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $major_sum[ $major_cache ]['people']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $major_sum[ $major_cache ]['people']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $major_sum[ $major_cache ]['hours']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $major_sum[ $major_cache ]['weekly']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $major_sum[ $major_cache ]['total_hours']);

                                $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                                $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                                $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                                $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                                $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                                $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                $objPHPExcel->getActiveSheet()->getStyle('J' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


                                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                                $knt++;
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_head[ $r['major_code'] ]);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                            }
                            $kount = 0;
                            if (!empty($r['country'])) {
                                foreach($r['country'] as $country) {
                                    $kount++;
                                    $knt++;
                                    if ($countries == $kount) {
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '第'. $r['quarter'] .'季: ' . $r['minor_code_cname']);

/*
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($r['new_people']));
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($r['people']));
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($r['hours'], 2));
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($r['weekly'], 2));
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, number_format($r['total_hours'], 2));
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, number_format($r['turnover']));
*/

                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $r['new_people']);
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $r['people']);
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $r['hours']);
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $r['weekly']);
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $r['total_hours']);
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $r['turnover']);

                                        $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                                        $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                                        $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                        $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                        $objPHPExcel->getActiveSheet()->getStyle('J' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                        $objPHPExcel->getActiveSheet()->getStyle('K' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, $r['info']);
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('M' . $knt, $r['note']);
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('N' . $knt, $r['latest']);
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                                    } else {
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, "");
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, "");
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, "");
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, "");
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, "");
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, "");
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, "");
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, "");
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('M' . $knt, "");
                                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('N' . $knt, "");
                                    }
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $country['country_code_cname']);
/*
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($country['new_male']));
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($country['new_female']));
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($country['people']));
*/
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $country['new_male']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $country['new_female']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $country['people']);
                                    $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                                    $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                                    $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                                    $major_sum[ $r['major_code'] ][ 'new_male' ] += intval( $country['new_male'] );
                                    $major_sum[ $r['major_code'] ][ 'new_female' ] += intval( $country['new_female'] );
                                    $major_sum[ 'S' ][ 'new_male' ] += intval( $country['new_male'] );
                                    $major_sum[ 'S' ][ 'new_female' ] += intval( $country['new_female'] );
                                } 
                            } 
    
                            $major_sum[ $r['major_code'] ][ 'new_people' ] += intval( $r['new_people'] );
                            $major_sum[ $r['major_code'] ][ 'people' ] += intval( $r['people'] );
                            $major_sum[ $r['major_code'] ][ 'hours' ] += floatval( $r['hours'] );
                            $major_sum[ $r['major_code'] ][ 'weekly' ] += floatval( $r['weekly'] );
                            $major_sum[ $r['major_code'] ][ 'total_hours' ] += floatval( $r['total_hours'] );
                            $major_sum[ $r['major_code'] ][ 'turnover' ] += intval( $r['turnover'] );
    
                            $major_sum[ 'S' ][ 'new_people' ] += intval( $r['new_people'] );
                            $major_sum[ 'S' ][ 'people' ] += intval( $r['people'] );
                            $major_sum[ 'S' ][ 'hours' ] += floatval( $r['hours'] );
                            $major_sum[ 'S' ][ 'weekly' ] += floatval( $r['weekly'] );
                            $major_sum[ 'S' ][ 'total_hours' ] += floatval( $r['total_hours'] );
                            $major_sum[ 'S' ][ 'turnover' ] += intval( $r['turnover'] );

                            $major_cache = $r['major_code'];
                        }

                        $knt++;
                        $objPHPExcel->getActiveSheet()->mergeCells('A'. $knt .':B'. $knt);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ $major_cache ]);
/*
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($major_sum[ $major_cache ]['new_male']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($major_sum[ $major_cache ]['new_female']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($major_sum[ $major_cache ]['new_people']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($major_sum[ $major_cache ]['people']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($major_sum[ $major_cache ]['people']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($major_sum[ $major_cache ]['hours'], 2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($major_sum[ $major_cache ]['weekly'], 2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, number_format($major_sum[ $major_cache ]['total_hours'], 2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, number_format($major_sum[ $major_cache ]['turnover']));
*/

                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $major_sum[ $major_cache ]['new_male']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $major_sum[ $major_cache ]['new_female']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $major_sum[ $major_cache ]['new_people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $major_sum[ $major_cache ]['people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $major_sum[ $major_cache ]['people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $major_sum[ $major_cache ]['hours']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $major_sum[ $major_cache ]['weekly']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $major_sum[ $major_cache ]['total_hours']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $major_sum[ $major_cache ]['turnover']);
                        $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');                                                                            
                        $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');                                                                            
                        $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');                                                                            
                        $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');                                                                            
                        $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');                                                                            
                        $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('J' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('K' . $knt)->getNumberFormat()->setFormatCode('#,##0');                                                                            


                        $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                        $knt++;
                        $objPHPExcel->getActiveSheet()->mergeCells('A'. $knt .':B'. $knt);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ 'S' ]);
/*
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($major_sum[ 'S' ]['new_male']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($major_sum[ 'S' ]['new_female']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($major_sum[ 'S' ]['new_people']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($major_sum[ 'S' ]['people']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($major_sum[ 'S' ]['people']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($major_sum[ 'S' ]['hours'], 2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($major_sum[ 'S' ]['weekly'], 2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, number_format($major_sum[ 'S' ]['total_hours'],2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, number_format($major_sum[ 'S' ]['turnover']));
*/

                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $major_sum[ 'S' ]['new_male']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $major_sum[ 'S' ]['new_female']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $major_sum[ 'S' ]['new_people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $major_sum[ 'S' ]['people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $major_sum[ 'S' ]['people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $major_sum[ 'S' ]['hours']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $major_sum[ 'S' ]['weekly']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $major_sum[ 'S' ]['total_hours']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $major_sum[ 'S' ]['turnover']);

                        $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');                                                                            
                        $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');                                                                            
                        $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');                                                                            
                        $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');                                                                            
                        $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');                                                                            
                        $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('J' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('K' . $knt)->getNumberFormat()->setFormatCode('#,##0');                                                                            

                        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                    }

                    $cnt++;
                }

                $filename = '年度機構詳表';
                break;
            case 'era_summary':
                $quarter = 10; 
                $cnt = 0;
                foreach ($targets as $target) {
                    $major_cache = 'A';
                    $major_sum = array(
                        'A'=>array('countries'=>0, 'new_male'=>0, 'new_female'=>0, 'new_people'=>0, 'people'=>0, 'weekly'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0),
                        'B'=>array('countries'=>0, 'new_male'=>0, 'new_female'=>0, 'new_people'=>0, 'people'=>0, 'weekly'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0),
                        'C'=>array('countries'=>0, 'new_male'=>0, 'new_female'=>0, 'new_people'=>0, 'people'=>0, 'weekly'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0),
                        'S'=>array('countries'=>0, 'new_male'=>0, 'new_female'=>0, 'new_people'=>0, 'people'=>0, 'weekly'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0)
                    );
                    if ($cnt > 0) {
                        $objPHPExcel->createSheet();
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt);
                    $objPHPExcel->getActiveSheet()->setTitle( $target['institution_code'] . '-'. $target['institution_cname'] . $target['cname'] );

                    //$res = (new AjaxModel)->dbQuery('agent_academic_agency_report_summary', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'quarter'=>$quarter));
                    $res = (new AjaxModel)->dbQuery('agent_academic_agency_report_era_summary', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'quarter'=>$quarter));
                    $knt = 1;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $target['institution_code'] . '-'. $target['institution_cname'] . $target['cname'] );
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '研習類別');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '總人數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '總人次');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, '每週平均上課時數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '每週平均上課時數(每班平均)');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, '每期上課時數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, '總人時數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, '營收額度');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, '已組合班數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, '小註(課程名稱)');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, '備註');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);

                    $count = 0;
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_head[ $major_cache ]);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);
                    foreach($res as $r) {
                        $knt++;
                        if ($major_cache != $r['major_code']) {
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ $major_cache ]);
/*
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, number_format($major_sum[ $major_cache ]['new_people']));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($major_sum[ $major_cache ]['people']));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($major_sum[ $major_cache ]['weekly'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($major_sum[ $major_cache ]['avg_weekly'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($major_sum[ $major_cache ]['hours'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($major_sum[ $major_cache ]['total_hours'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($major_sum[ $major_cache ]['turnover']));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($major_sum[ $major_cache ]['classes']));
*/

                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $major_sum[ $major_cache ]['new_people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $major_sum[ $major_cache ]['people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $major_sum[ $major_cache ]['weekly']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $major_sum[ $major_cache ]['avg_weekly']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $major_sum[ $major_cache ]['hours']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $major_sum[ $major_cache ]['total_hours']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $major_sum[ $major_cache ]['turnover']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $major_sum[ $major_cache ]['classes']);
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                            $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                            $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                            $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                            $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);

                            $knt++;
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_head[ $r['major_code'] ]);
                            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);
                            $knt++;
                        }
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '第' . $r['quarter'] .'季: ' . $r['minor_code_cname']);
/*
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, number_format($r['new_people']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($r['people']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($r['weekly'], 2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($r['avg_weekly'], 2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($r['hours'], 2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($r['total_hours'], 2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($r['turnover']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($r['classes']));
*/

                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $r['new_people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $r['people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $r['weekly']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $r['avg_weekly']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $r['hours']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $r['total_hours']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $r['turnover']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $r['classes']);

                        $objPHPExcel->getActiveSheet()->getStyle('B' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $r['info']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $r['note']);

                        $major_sum[ $r['major_code'] ][ 'new_people' ] += intval( $r['new_people'] );
                        $major_sum[ $r['major_code'] ][ 'people' ] += intval( $r['people'] );
                        $major_sum[ $r['major_code'] ][ 'weekly' ] += floatval( $r['weekly'] );
                        $major_sum[ $r['major_code'] ][ 'avg_weekly' ] += floatval( $r['avg_weekly'] );
                        $major_sum[ $r['major_code'] ][ 'hours' ] += floatval( $r['hours'] );
                        $major_sum[ $r['major_code'] ][ 'total_hours' ] += floatval( $r['total_hours'] );
                        $major_sum[ $r['major_code'] ][ 'turnover' ] += intval( $r['turnover'] );
                        $major_sum[ $r['major_code'] ][ 'classes' ] += intval( $r['classes'] );

                        $major_sum[ 'S' ][ 'new_people' ] += intval( $r['new_people'] );
                        $major_sum[ 'S' ][ 'people' ] += intval( $r['people'] );
                        $major_sum[ 'S' ][ 'weekly' ] += floatval( $r['weekly'] );
                        $major_sum[ 'S' ][ 'avg_weekly' ] += floatval( $r['avg_weekly'] );
                        $major_sum[ 'S' ][ 'hours' ] += floatval( $r['hours'] );
                        $major_sum[ 'S' ][ 'total_hours' ] += floatval( $r['total_hours'] );
                        $major_sum[ 'S' ][ 'turnover' ] += intval( $r['turnover'] );
                        $major_sum[ 'S' ][ 'classes' ] += intval( $r['classes'] );

                        $major_cache = $r['major_code'];
                    }
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ $major_cache ]);
/*
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, number_format($major_sum[ $major_cache ]['new_people']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($major_sum[ $major_cache ]['people']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($major_sum[ $major_cache ]['weekly'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($major_sum[ $major_cache ]['avg_weekly'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($major_sum[ $major_cache ]['hours'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($major_sum[ $major_cache ]['total_hours'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($major_sum[ $major_cache ]['turnover']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($major_sum[ $major_cache ]['classes']));
*/

                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $major_sum[ $major_cache ]['new_people']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $major_sum[ $major_cache ]['people']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $major_sum[ $major_cache ]['weekly']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $major_sum[ $major_cache ]['avg_weekly']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $major_sum[ $major_cache ]['hours']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $major_sum[ $major_cache ]['total_hours']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $major_sum[ $major_cache ]['turnover']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $major_sum[ $major_cache ]['classes']);

                    $objPHPExcel->getActiveSheet()->getStyle('B' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);

                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ 'S' ]);
/*
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, number_format($major_sum[ 'S' ]['new_people']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($major_sum[ 'S' ]['people']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($major_sum[ 'S' ]['weekly'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($major_sum[ 'S' ]['avg_weekly'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($major_sum[ 'S' ]['hours'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($major_sum[ 'S' ]['total_hours'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($major_sum[ 'S' ]['turnover']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($major_sum[ 'S' ]['classes']));
*/

                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $major_sum[ 'S' ]['new_people']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $major_sum[ 'S' ]['people']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $major_sum[ 'S' ]['weekly']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $major_sum[ 'S' ]['avg_weekly']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $major_sum[ 'S' ]['hours']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $major_sum[ 'S' ]['total_hours']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $major_sum[ 'S' ]['turnover']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $major_sum[ 'S' ]['classes']);

                    $objPHPExcel->getActiveSheet()->getStyle('B' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                    $cnt++;
                }

                $filename = '年度機構簡表';

                break;
            case 'quarter_detail':
                $cnt = 0;
                foreach ($targets as $target) {
                    $major_cache = 'A';
                    $major_sum = array(
                        'A'=>array('countries'=>0, 'new_male'=>0, 'new_female'=>0, 'new_people'=>0, 'people'=>0, 'weekly'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0),
                        'B'=>array('countries'=>0, 'new_male'=>0, 'new_female'=>0, 'new_people'=>0, 'people'=>0, 'weekly'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0),
                        'C'=>array('countries'=>0, 'new_male'=>0, 'new_female'=>0, 'new_people'=>0, 'people'=>0, 'weekly'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0),
                        'S'=>array('countries'=>0, 'new_male'=>0, 'new_female'=>0, 'new_people'=>0, 'people'=>0, 'weekly'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0)
                    );
                    if ($cnt > 0) {
                        $objPHPExcel->createSheet();
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt);
                    $objPHPExcel->getActiveSheet()->setTitle( $target['institution_code'] . '-'. $target['institution_cname'] . $target['cname'] );

                    //$res = (new AjaxModel)->dbQuery('agent_academic_agency_report_detail', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'quarter'=>$quarter));
                    $res = (new AjaxModel)->dbQuery('agent_academic_agency_report_era_detail', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'quarter'=>$quarter));
                    $knt = 1;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $target['institution_code'] . '-'. $target['institution_cname'] . $target['cname'] );
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '研習類別');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '國別(地區)');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '男新生人數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, '女新生人數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '總人數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, '人次');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, '總人次');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, '每期上課時數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, '每週平均上課時數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, '總人時數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, '營收額度');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, '小註(課程名稱)');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('M' . $knt, '備註');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('N' . $knt, '最後修改時間');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);

                    $size = sizeof($res);
                    if ($size) {
                        $knt = 1;
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $target['institution_code'] . '-'. $target['institution_cname'] . $target['cname']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                        $knt++;
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '研習類別');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '國別(地區)');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '男新生人數');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, '女新生人數');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '總人數');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, '人次');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, '總人次');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, '每期上課時數');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, '每週平均上課時數');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, '總人時數');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, '營收額度');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, '小註(課程名稱)');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('M' . $knt, '備註');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('N' . $knt, '最後修改時間');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
    
                        $count = 0;
                        $knt++;
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_head[ $major_cache ]);
                        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                        foreach($res as $r) {
                            $count++;
                            $countries = sizeof($r['country']);
                            if ($major_cache != $r['major_code']) {
                                $knt++;
                                $kountry = (new AjaxModel)->dbQuery('agent_academic_agency_report_countries', array('agency_id'=>$agency_id, 'era_id'=>$era_id, 'quarter'=>$quarter));
                                $major_sum[ $r['major_code'] ][ 'countries' ] = intval($kountry[0]['countries']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ $major_cache ]);
                                //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $major_sum[ $r['major_code'] ]['countries']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '');
/*
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($major_sum[ $major_cache ]['new_male']));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($major_sum[ $major_cache ]['new_female']));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($major_sum[ $major_cache ]['new_people']));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($major_sum[ $major_cache ]['people']));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($major_sum[ $major_cache ]['people']));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($major_sum[ $major_cache ]['hours'], 2));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($major_sum[ $major_cache ]['weekly'], 2));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, number_format($major_sum[ $major_cache ]['total_hours'], 2));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, number_format($major_sum[ $major_cache ]['turnover']));
*/

                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $major_sum[ $major_cache ]['new_male']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $major_sum[ $major_cache ]['new_female']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $major_sum[ $major_cache ]['new_people']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $major_sum[ $major_cache ]['people']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $major_sum[ $major_cache ]['people']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $major_sum[ $major_cache ]['hours']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $major_sum[ $major_cache ]['weekly']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $major_sum[ $major_cache ]['total_hours']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $major_sum[ $major_cache ]['turnover']);

                                $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                                $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                                $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                                $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                                $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                                $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                $objPHPExcel->getActiveSheet()->getStyle('J' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                $objPHPExcel->getActiveSheet()->getStyle('K' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);

                                $knt++;
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_head[ $r['major_code'] ]);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                            }
                            $kount = 0;
                            foreach($r['country'] as $country) {
                                $kount++;
                                $knt++;
                                if ($countries == $kount) {
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $r['minor_code_cname']);
/*
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($r['new_people']));
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($r['people']));
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($r['hours'], 2));
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($r['weekly'], 2));
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, number_format($r['total_hours'], 2));
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, number_format($r['turnover']));
*/

                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $r['new_people']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $r['people']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $r['hours']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $r['weekly']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $r['total_hours']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $r['turnover']);

                                    $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                                    $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                                    $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                    $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                    $objPHPExcel->getActiveSheet()->getStyle('J' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                                    $objPHPExcel->getActiveSheet()->getStyle('K' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, $r['info']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('M' . $knt, $r['note']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('N' . $knt, $r['latest']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                                } else {
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('M' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('N' . $knt, "");
                                }
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $country['country_code_cname']);
/*
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($country['new_male']));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($country['new_female']));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($country['people']));
*/
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $country['new_male']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $country['new_female']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $country['people']);

                                $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                                $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                                $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                                $major_sum[ $r['major_code'] ][ 'new_male' ] += intval( $country['new_male'] );
                                $major_sum[ $r['major_code'] ][ 'new_female' ] += intval( $country['new_female'] );
                                $major_sum[ 'S' ][ 'new_male' ] += intval( $country['new_male'] );
                                $major_sum[ 'S' ][ 'new_female' ] += intval( $country['new_female'] );
                            } 
    
                            $major_sum[ $r['major_code'] ][ 'new_people' ] += intval( $r['new_people'] );
                            $major_sum[ $r['major_code'] ][ 'people' ] += intval( $r['people'] );
                            $major_sum[ $r['major_code'] ][ 'hours' ] += floatval( $r['hours'] );
                            $major_sum[ $r['major_code'] ][ 'weekly' ] += floatval( $r['weekly'] );
                            $major_sum[ $r['major_code'] ][ 'total_hours' ] += floatval( $r['total_hours'] );
                            $major_sum[ $r['major_code'] ][ 'turnover' ] += intval( $r['turnover'] );
    
                            $major_sum[ 'S' ][ 'new_people' ] += intval( $r['new_people'] );
                            $major_sum[ 'S' ][ 'people' ] += intval( $r['people'] );
                            $major_sum[ 'S' ][ 'hours' ] += floatval( $r['hours'] );
                            $major_sum[ 'S' ][ 'weekly' ] += floatval( $r['weekly'] );
                            $major_sum[ 'S' ][ 'total_hours' ] += floatval( $r['total_hours'] );
                            $major_sum[ 'S' ][ 'turnover' ] += intval( $r['turnover'] );
                            $major_cache = $r['major_code'];
                        }
                        $knt++;
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_head[ $major_cache ]);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '');
/*
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($major_sum[ $major_cache ]['new_male']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($major_sum[ $major_cache ]['new_female']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($major_sum[ $major_cache ]['new_people']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($major_sum[ $major_cache ]['people']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($major_sum[ $major_cache ]['people']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($major_sum[ $major_cache ]['hours'], 2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($major_sum[ $major_cache ]['weekly'], 2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, number_format($major_sum[ $major_cache ]['total_hours'], 2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, number_format($major_sum[ $major_cache ]['turnover']));
*/

                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $major_sum[ $major_cache ]['new_male']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $major_sum[ $major_cache ]['new_female']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $major_sum[ $major_cache ]['new_people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $major_sum[ $major_cache ]['people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $major_sum[ $major_cache ]['people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $major_sum[ $major_cache ]['hours']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $major_sum[ $major_cache ]['weekly']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $major_sum[ $major_cache ]['total_hours']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $major_sum[ $major_cache ]['turnover']);

                        $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('J' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('K' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                        $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                        $knt++;
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ 'S' ]);
                        //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, number_format($major_sum[ 'S' ]['countries']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '');
/*
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($major_sum[ 'S' ]['new_male']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($major_sum[ 'S' ]['new_female']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($major_sum[ 'S' ]['new_people']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($major_sum[ 'S' ]['people']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($major_sum[ 'S' ]['people']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($major_sum[ 'S' ]['hours'], 2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($major_sum[ 'S' ]['weekly'], 2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, number_format($major_sum[ 'S' ]['total_hours'], 2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, number_format($major_sum[ 'S' ]['turnover']));
*/

                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $major_sum[ 'S' ]['new_male']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $major_sum[ 'S' ]['new_female']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $major_sum[ 'S' ]['new_people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $major_sum[ 'S' ]['people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $major_sum[ 'S' ]['people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $major_sum[ 'S' ]['hours']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $major_sum[ 'S' ]['weekly']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $major_sum[ 'S' ]['total_hours']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $major_sum[ 'S' ]['turnover']);

                        $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('J' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('K' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                    }

                    $cnt++;
                }
                $filename = '季度機構詳表';

                break;
            case 'quarter_summary':

                $cnt = 0;
                foreach ($targets as $target) {
                    $major_cache = 'A';
                    $major_sum = array(
                        'A'=>array('countries'=>0, 'new_male'=>0, 'new_female'=>0, 'new_people'=>0, 'people'=>0, 'weekly'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0),
                        'B'=>array('countries'=>0, 'new_male'=>0, 'new_female'=>0, 'new_people'=>0, 'people'=>0, 'weekly'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0),
                        'C'=>array('countries'=>0, 'new_male'=>0, 'new_female'=>0, 'new_people'=>0, 'people'=>0, 'weekly'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0),
                        'S'=>array('countries'=>0, 'new_male'=>0, 'new_female'=>0, 'new_people'=>0, 'people'=>0, 'weekly'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0)
                    );
                    if ($cnt > 0) {
                        $objPHPExcel->createSheet();
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt);
                    $objPHPExcel->getActiveSheet()->setTitle( $target['institution_code'] . '-'. $target['institution_cname'] . $target['cname'] );

                    //$res = (new AjaxModel)->dbQuery('agent_academic_agency_report_summary', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'quarter'=>$quarter));
                    $res = (new AjaxModel)->dbQuery('agent_academic_agency_report_era_summary', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'quarter'=>$quarter));
                    $knt = 1;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $target['institution_code'] . '-'. $target['institution_cname'] . $target['cname'] );
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '研習類別');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '總人數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '總人次');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, '每週平均上課時數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '每週平均上課時數(每班平均)');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, '每期上課時數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, '總人時數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, '營收額度');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, '已組合班數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, '小註(課程名稱)');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, '備註');

                    $count = 0;
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_head[ $major_cache ]);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);
                    foreach($res as $r) {
                        $knt++;
                        if ($major_cache != $r['major_code']) {
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ $major_cache ]);
/*
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, number_format($major_sum[ $major_cache ]['new_people']));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($major_sum[ $major_cache ]['people']));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($major_sum[ $major_cache ]['weekly'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($major_sum[ $major_cache ]['avg_weekly'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($major_sum[ $major_cache ]['hours'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($major_sum[ $major_cache ]['total_hours'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($major_sum[ $major_cache ]['turnover']));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($major_sum[ $major_cache ]['classes']));
*/
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $major_sum[ $major_cache ]['new_people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $major_sum[ $major_cache ]['people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $major_sum[ $major_cache ]['weekly']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $major_sum[ $major_cache ]['avg_weekly']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $major_sum[ $major_cache ]['hours']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $major_sum[ $major_cache ]['total_hours']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $major_sum[ $major_cache ]['turnover']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $major_sum[ $major_cache ]['classes']);

                            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);

                            $knt++;
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_head[ $r['major_code'] ]);
                            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);
                            $knt++;
                        }
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $r['minor_code_cname']);
/*
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, number_format($r['new_people']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($r['people']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($r['weekly']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($r['avg_weekly'], 2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($r['hours'], 2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($r['total_hours'], 2));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($r['turnover']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($r['classes']));
*/
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $r['new_people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $r['people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $r['weekly']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $r['avg_weekly']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $r['hours']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $r['total_hours']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $r['turnover']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $r['classes']);

                        $objPHPExcel->getActiveSheet()->getStyle('B' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $r['info']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $r['note']);

                        $major_sum[ $r['major_code'] ][ 'new_people' ] += intval( $r['new_people'] );
                        $major_sum[ $r['major_code'] ][ 'people' ] += intval( $r['people'] );
                        $major_sum[ $r['major_code'] ][ 'weekly' ] += floatval( $r['weekly'] );
                        $major_sum[ $r['major_code'] ][ 'avg_weekly' ] += floatval( $r['avg_weekly'] );
                        $major_sum[ $r['major_code'] ][ 'hours' ] += floatval( $r['hours'] );
                        $major_sum[ $r['major_code'] ][ 'total_hours' ] += floatval( $r['total_hours'] );
                        $major_sum[ $r['major_code'] ][ 'turnover' ] += intval( $r['turnover'] );
                        $major_sum[ $r['major_code'] ][ 'classes' ] += intval( $r['classes'] );

                        $major_sum[ 'S' ][ 'new_people' ] += intval( $r['new_people'] );
                        $major_sum[ 'S' ][ 'people' ] += intval( $r['people'] );
                        $major_sum[ 'S' ][ 'weekly' ] += floatval( $r['weekly'] );
                        $major_sum[ 'S' ][ 'avg_weekly' ] += floatval( $r['avg_weekly'] );
                        $major_sum[ 'S' ][ 'hours' ] += floatval( $r['hours'] );
                        $major_sum[ 'S' ][ 'total_hours' ] += floatval( $r['total_hours'] );
                        $major_sum[ 'S' ][ 'turnover' ] += intval( $r['turnover'] );
                        $major_sum[ 'S' ][ 'classes' ] += intval( $r['classes'] );

                        $major_cache = $r['major_code'];
                    }
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ $major_cache ]);
/*
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, number_format($major_sum[ $major_cache ]['new_people']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($major_sum[ $major_cache ]['people']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($major_sum[ $major_cache ]['weekly'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($major_sum[ $major_cache ]['avg_weekly'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($major_sum[ $major_cache ]['hours'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($major_sum[ $major_cache ]['total_hours'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($major_sum[ $major_cache ]['turnover']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($major_sum[ $major_cache ]['classes']));
*/
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $major_sum[ $major_cache ]['new_people']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $major_sum[ $major_cache ]['people']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $major_sum[ $major_cache ]['weekly']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $major_sum[ $major_cache ]['avg_weekly']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $major_sum[ $major_cache ]['hours']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $major_sum[ $major_cache ]['total_hours']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $major_sum[ $major_cache ]['turnover']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $major_sum[ $major_cache ]['classes']);

                    $objPHPExcel->getActiveSheet()->getStyle('B' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);

                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ 'S' ]);
/*
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, number_format($major_sum[ 'S' ]['new_people']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($major_sum[ 'S' ]['people']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($major_sum[ 'S' ]['weekly'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($major_sum[ 'S' ]['avg_weekly'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($major_sum[ 'S' ]['hours'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($major_sum[ 'S' ]['total_hours'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($major_sum[ 'S' ]['turnover']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($major_sum[ 'S' ]['classes']));
*/
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $major_sum[ 'S' ]['new_people']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $major_sum[ 'S' ]['people']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $major_sum[ 'S' ]['weekly']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $major_sum[ 'S' ]['avg_weekly']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $major_sum[ 'S' ]['hours']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $major_sum[ 'S' ]['total_hours']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $major_sum[ 'S' ]['turnover']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $major_sum[ 'S' ]['classes']);

                    $objPHPExcel->getActiveSheet()->getStyle('B' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                    $cnt++;
                }
                $filename = '季度機構簡表';

                break;
            case 'manager':
                $era = (new AjaxModel)->dbQuery('admin_academic_era', array('era_id'=>$era_id));
                $academic_classes = (new AjaxModel)->dbQuery('admin_academic_class', array('era_id'=>$era_id));
                $quarters = '';
/*
                switch($quarter)
                {
                case '2':
                case '3':
                case '4':
                case '5':
                    $quarters = '-Q' . ($quarter-1);
                    break;
                case '6':
                    $quarters = '-Q1,2';
                    break;
                case '7':
                    $quarters = '-Q2,3';
                    break;
                case '8':
                    $quarters = '-Q3,4';
                    break;
                case '9':
                    $quarters = '-Q1,2,3';
                    break;
                case '10':
                    $quarters = '-Q2,3,4';
                    break;
                }
*/
                /* summary */
                $cnt = 0;
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '人時數營收總簡表' . $quarters );

                $turnover_summary = (new AjaxModel)->dbQuery('admin_academic_agency_report_manager_summary', array('era_id'=>$era_id, 'quarters'=>$quarter));
                $counter = array('new_people'=>0, 'people'=>0, 'total_hours'=>0, 'turnover'=>0);

                $knt = 1;
                $qnt = 0;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $era[0]['cname'] . '華語中心(人時數營收總簡表)');
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":G" . $knt);
                $knt++;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '序號');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '學校代碼');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '單位機構名稱');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, '總人數');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '總人次');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, '總人時數');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, '營收額度');
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":G" . $knt);

                foreach( $turnover_summary as $summary ) {
                    $knt++;
                    $qnt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $qnt);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $summary['institution_code']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $summary['institution_cname'] . $summary['academic_agency_cname']);
/*
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($summary['new_people']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($summary['people']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($summary['total_hours'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($summary['turnover']));
*/
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $summary['new_people']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $summary['people']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $summary['total_hours']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $summary['turnover']);

                    $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');


                    $counter['new_people'] += intval($summary['new_people']);
                    $counter['people'] += intval($summary['people']);
                    $counter['total_hours'] += floatval($summary['total_hours']);
                    $counter['turnover'] += floatval($summary['turnover']);
                }

                $knt++;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '合計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '');
/*
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($counter['new_people']));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($counter['people']));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($counter['total_hours'], 2));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($counter['turnover']));
*/
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $counter['new_people']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $counter['people']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $counter['total_hours']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $counter['turnover']);

                $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":G" . $knt);

                /* new people detail */
                $cnt = 1;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '各類研習總人數詳表' . $quarters);

                $knt = 1;
                $qnt = 0;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $era[0]['cname'] . '各類研習總人數詳表' );
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":G" . $knt);
                $knt++;
                // columns array
                $cols = array();
                $col_min = 65;
                $col_ini = 68;
                $col_max = 90;
                $col_tag = 0;
                $col_idx = $col_ini;
                $sum_a = 0;
                $sum_b = 0;
                $sum_c = 0;
                $sum_t = 0;

                // academic_class array
                //$academic_classes = (new AjaxModel)->dbQuery('admin_academic_class', array('era_id'=>$era_id));
                $acs = array();
                $acn = array();
                $cols_a = array();
                $cols_b = array();
                $cols_c = array();
                $cols_sum = array();
                foreach ($academic_classes as $ac) {
                    switch( $ac['major_code'] )
                    {
                    case 'A':
                        array_push($cols_a, $ac['minor_code']);
                        break;
                    case 'B':
                        array_push($cols_b, $ac['minor_code']);
                        break;
                    case 'C':
                        array_push($cols_c, $ac['minor_code']);
                        break;
                    }
                    $acs[$ac['minor_code']] = $ac['cname'];
                }

                for ($i=0; $i<sizeof($cols_a); $i++) {
                    array_push($cols, $cols_a[$i]);
                    $cols_sum[$cols_a[$i]] = 0;
                }
                array_push($cols, 'ASUM');
                $cols_sum['ASUM'] = 0;

                for ($i=0; $i<sizeof($cols_b); $i++) {
                    array_push($cols, $cols_b[$i]);
                    $cols_sum[$cols_b[$i]] = 0;
                }
                array_push($cols, 'BSUM');
                $cols_sum['BSUM'] = 0;
                
                for ($i=0; $i<sizeof($cols_c); $i++) {
                    array_push($cols, $cols_c[$i]);
                    $cols_sum[$cols_c[$i]] = 0;
                }
                array_push($cols, 'CSUM');
                $cols_sum['CSUM'] = 0;
                array_push($cols, 'TSUM');
                $cols_sum['TSUM'] = 0;

                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '序號');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '學校代碼');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '單位機構名稱');
                foreach ($cols as $col) {
                    switch( $col_tag )
                    {
                    case 0:
                        $chr = chr($col_idx);
                        break;
                    case 1:
                        $chr = 'A' . chr($col_idx);
                        break;
                    }
                    switch( $col )
                    {
                    case 'ASUM':
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, '第一類(A類)小計');
                        break;
                    case 'BSUM':
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, '第二類(B類)小計');
                        break;
                    case 'CSUM':
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, '第三類(C類)小計');
                        break;
                    case 'TSUM':
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, '華語研習三大類合計');
                        break;
                    default:
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $acs[ $col ]);
                    }
                    if ($col_max > $col_idx) {
                        $col_idx++;
                    } else {
                        $col_tag = 1;
                        $col_idx = $col_min;
                    }
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":C" . $knt);

                $col_idx = $col_ini;
                $col_tag = 0;
                $qnt = 0;

                foreach ($targets as $target) {
                    $qnt++;
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $qnt);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $target['institution_code']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $target['institution_cname'] . $target['cname']);
                    $classes = (new AjaxModel)->dbQuery('admin_academic_agency_report_manager_new_people_detail', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'quarters'=>$quarter));

                    $cs = array();
                    $sum_a = 0;
                    $sum_b = 0;
                    $sum_c = 0;
                    foreach ($classes as $c) {
                        $cs[$c['minor_code']] = $c['new_people'];
                        if (strpos($c['minor_code'], 'A') !== false) {
                            $sum_a += $c['new_people'];
                        } else if (strpos($c['minor_code'], 'B') !== false) {
                            $sum_b += $c['new_people'];
                        } else if (strpos($c['minor_code'], 'C') !== false) {
                            $sum_c += $c['new_people'];
                        }
                        $cols_sum[$c['minor_code']] += $c['new_people'];
                    }

                    $sum_t = $sum_a + $sum_b + $sum_c;
                    $cols_sum['ASUM'] += $sum_a;
                    $cols_sum['BSUM'] += $sum_b;
                    $cols_sum['CSUM'] += $sum_c;
                    $cols_sum['TSUM'] += $sum_t;

                    $col_idx = $col_ini;
                    $col_tag = 0;
                    foreach ( $cols as $col ) {
                        switch($col_tag) 
                        {
                        case 0:
                            $chr = chr( $col_idx );
                            break;
                        case 1:
                            $chr = 'A' . chr( $col_idx );
                            break;
                        }

                        switch( $col )
                        {
                        case 'ASUM':
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, number_format($sum_a));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $sum_a);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');

                            break;
                        case 'BSUM':
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, number_format($sum_b));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $sum_b);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            break;
                        case 'CSUM':
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, number_format($sum_c));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $sum_c);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            break;
                        case 'TSUM':
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, number_format($sum_t));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $sum_t);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            break;
                        default:
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, (isset($cs[$col])? $cs[$col] : ""));
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $chr . $knt . $col_idx);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        }
                        if ($col_max > $col_idx) {
                            $col_idx++;
                        } else {
                            $col_tag = 1;
                            $col_idx = $col_min;
                        }
                    }
                }

                $knt++;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '合計');
                $col_idx = $col_ini;
                $col_tag = 0;
                foreach ($cols_sum as $cs) {
                    switch($col_tag) 
                    {
                    case 0:
                        $chr = chr( $col_idx );
                        break;
                    case 1:
                        $chr = 'A' . chr( $col_idx );
                        break;
                    }
                    //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, number_format($cs));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $cs);
                    $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');

                    if ($col_max > $col_idx) {
                        $col_idx++;
                    } else {
                        $col_tag = 1;
                        $col_idx = $col_min;
                    }
                }

                /* people detail */
                $cnt = 2;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '各類研習總人次詳表' . $quarters);

                $knt = 1;
                $qnt = 0;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $era[0]['cname'] . '各類研習總人次詳表' );
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":C" . $knt);
                $knt++;
                // columns array
                $cols = array();
                $col_min = 65;
                $col_ini = 68;
                $col_max = 90;
                $col_tag = 0;
                $col_idx = $col_ini;
                $sum_a = 0;
                $sum_b = 0;
                $sum_c = 0;
                $sum_t = 0;

                // academic_class array
                //$academic_classes = (new AjaxModel)->dbQuery('admin_academic_class', array('era_id'=>$era_id));
                $acs = array();
                $acn = array();
                $cols_a = array();
                $cols_b = array();
                $cols_c = array();
                $cols_sum = array();
                foreach ($academic_classes as $ac) {
                    switch( $ac['major_code'] )
                    {
                    case 'A':
                        array_push($cols_a, $ac['minor_code']);
                        break;
                    case 'B':
                        array_push($cols_b, $ac['minor_code']);
                        break;
                    case 'C':
                        array_push($cols_c, $ac['minor_code']);
                        break;
                    }
                    $acs[$ac['minor_code']] = $ac['cname'];
                }

                for ($i=0; $i<sizeof($cols_a); $i++) {
                    array_push($cols, $cols_a[$i]);
                    $cols_sum[$cols_a[$i]] = 0;
                }
                array_push($cols, 'ASUM');
                $cols_sum['ASUM'] = 0;
                for ($i=0; $i<sizeof($cols_b); $i++) {
                    array_push($cols, $cols_b[$i]);
                    $cols_sum[$cols_b[$i]] = 0;
                }
                array_push($cols, 'BSUM');
                $cols_sum['BSUM'] = 0;
                for ($i=0; $i<sizeof($cols_c); $i++) {
                    array_push($cols, $cols_c[$i]);
                    $cols_sum[$cols_c[$i]] = 0;
                }
                array_push($cols, 'CSUM');
                $cols_sum['CSUM'] = 0;
                array_push($cols, 'TSUM');
                $cols_sum['TSUM'] = 0;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '序號');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '學校代碼');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '單位機構名稱');
                foreach ($cols as $col) {
                    switch( $col_tag )
                    {
                    case 0:
                        $chr = chr($col_idx);
                        break;
                    case 1:
                        $chr = 'A' . chr($col_idx);
                        break;
                    }
                    switch( $col )
                    {
                    case 'ASUM':
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, '第一類(A類)小計');
                        break;
                    case 'BSUM':
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, '第二類(B類)小計');
                        break;
                    case 'CSUM':
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, '第三類(C類)小計');
                        break;
                    case 'TSUM':
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, '華語研習三大類合計');
                        break;
                    default:
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $acs[ $col ]);
                    }
                    if ($col_max > $col_idx) {
                        $col_idx++;
                    } else {
                        $col_tag = 1;
                        $col_idx = $col_min;
                    }
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":C" . $knt);

                $col_idx = $col_ini;
                $col_tag = 0;
                $qnt = 0;
                foreach ($targets as $target) {
                    $qnt++;
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $qnt);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $target['institution_code']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $target['institution_cname'] . $target['cname']);
                    $classes = (new AjaxModel)->dbQuery('admin_academic_agency_report_manager_people_detail', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'quarters'=>$quarter));

                    $cs = array();
                    $sum_a = 0;
                    $sum_b = 0;
                    $sum_c = 0;
                    foreach ($classes as $c) {
                        $cs[$c['minor_code']] = $c['people'];
                        if (strpos($c['minor_code'], 'A') !== false) {
                            $sum_a += $c['people'];
                        } else if (strpos($c['minor_code'], 'B') !== false) {
                            $sum_b += $c['people'];
                        } else if (strpos($c['minor_code'], 'C') !== false) {
                            $sum_c += $c['people'];
                        }
                        $cols_sum[$c['minor_code']] += $c['people'];
                    }

                    $sum_t = $sum_a + $sum_b + $sum_c;
                    $cols_sum['ASUM'] += $sum_a;
                    $cols_sum['BSUM'] += $sum_b;
                    $cols_sum['CSUM'] += $sum_c;
                    $cols_sum['TSUM'] += $sum_t;

                    $col_idx = $col_ini;
                    $col_tag = 0;
                    foreach ( $cols as $col ) {
                        switch($col_tag) 
                        {
                        case 0:
                            $chr = chr( $col_idx );
                            break;
                        case 1:
                            $chr = 'A' . chr( $col_idx );
                            break;
                        }

                        switch( $col )
                        {
                        case 'ASUM':
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, number_format($sum_a));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $sum_a);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            break;
                        case 'BSUM':
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, number_format($sum_b));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $sum_b);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            break;
                        case 'CSUM':
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, number_format($sum_c));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $sum_c);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            break;
                        case 'TSUM':
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, number_format($sum_t));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $sum_t);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            break;
                        default:
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, (isset($cs[$col])? $cs[$col] : ""));
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $chr . $knt . $col_idx);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        }
                        if ($col_max > $col_idx) {
                            $col_idx++;
                        } else {
                            $col_tag = 1;
                            $col_idx = $col_min;
                        }
                    }
                }

                $knt++;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '合計');
                $col_idx = $col_ini;
                $col_tag = 0;
                foreach ($cols_sum as $cs) {
                    switch($col_tag) 
                    {
                    case 0:
                        $chr = chr( $col_idx );
                        break;
                    case 1:
                        $chr = 'A' . chr( $col_idx );
                        break;
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $cs);

                    if ($col_max > $col_idx) {
                        $col_idx++;
                    } else {
                        $col_tag = 1;
                        $col_idx = $col_min;
                    }
                }
                /* total hours detail */
                $cnt = 3;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '各類研習總人時數詳表' . $quarters);

                $knt = 1;
                $qnt = 0;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $era[0]['cname'] . '各類研習總人時數詳表' );
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":C" . $knt);
                $knt++;
                // columns array
                $cols = array();
                $col_min = 65;
                $col_ini = 68;
                $col_max = 90;
                $col_tag = 0;
                $col_idx = $col_ini;
                $sum_a = 0;
                $sum_b = 0;
                $sum_c = 0;
                $sum_t = 0;

                // academic_class array
                //$academic_classes = (new AjaxModel)->dbQuery('admin_academic_class', array('era_id'=>$era_id));
                $acs = array();
                $acn = array();
                $cols_a = array();
                $cols_b = array();
                $cols_c = array();
                $cols_sum = array();
                foreach ($academic_classes as $ac) {
                    switch( $ac['major_code'] )
                    {
                    case 'A':
                        array_push($cols_a, $ac['minor_code']);
                        break;
                    case 'B':
                        array_push($cols_b, $ac['minor_code']);
                        break;
                    case 'C':
                        array_push($cols_c, $ac['minor_code']);
                        break;
                    }
                    $acs[$ac['minor_code']] = $ac['cname'];
                }

                for ($i=0; $i<sizeof($cols_a); $i++) {
                    array_push($cols, $cols_a[$i]);
                    $cols_sum[$cols_a[$i]] = 0;
                }
                array_push($cols, 'ASUM');
                $cols_sum['ASUM'] = 0;
                for ($i=0; $i<sizeof($cols_b); $i++) {
                    array_push($cols, $cols_b[$i]);
                    $cols_sum[$cols_b[$i]] = 0;
                }
                array_push($cols, 'BSUM');
                $cols_sum['BSUM'] = 0;
                for ($i=0; $i<sizeof($cols_c); $i++) {
                    array_push($cols, $cols_c[$i]);
                    $cols_sum[$cols_c[$i]] = 0;
                }
                array_push($cols, 'CSUM');
                $cols_sum['CSUM'] = 0;
                array_push($cols, 'TSUM');
                $cols_sum['TSUM'] = 0;

                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '序號');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '學校代碼');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '單位機構名稱');
                foreach ($cols as $col) {
                    switch( $col_tag )
                    {
                    case 0:
                        $chr = chr($col_idx);
                        break;
                    case 1:
                        $chr = 'A' . chr($col_idx);
                        break;
                    }
                    switch( $col )
                    {
                    case 'ASUM':
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, '第一類(A類)小計');
                        break;
                    case 'BSUM':
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, '第二類(B類)小計');
                        break;
                    case 'CSUM':
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, '第三類(C類)小計');
                        break;
                    case 'TSUM':
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, '華語研習三大類合計');
                        break;
                    default:
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $acs[ $col ]);
                    }
                    if ($col_max > $col_idx) {
                        $col_idx++;
                    } else {
                        $col_tag = 1;
                        $col_idx = $col_min;
                    }
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":C" . $knt);

                $col_idx = $col_ini;
                $col_tag = 0;
                $qnt = 0;
                foreach ($targets as $target) {
                    $qnt++;
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $qnt);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $target['institution_code']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $target['institution_cname'] . $target['cname']);
                    $classes = (new AjaxModel)->dbQuery('admin_academic_agency_report_manager_total_hours_detail', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'quarters'=>$quarter));

                    $cs = array();
                    $sum_a = 0;
                    $sum_b = 0;
                    $sum_c = 0;
                    foreach ($classes as $c) {
                        $cs[$c['minor_code']] = $c['total_hours'];
                        if (strpos($c['minor_code'], 'A') !== false) {
                            $sum_a += $c['total_hours'];
                        } else if (strpos($c['minor_code'], 'B') !== false) {
                            $sum_b += $c['total_hours'];
                        } else if (strpos($c['minor_code'], 'C') !== false) {
                            $sum_c += $c['total_hours'];
                        }
                        $cols_sum[$c['minor_code']] += $c['total_hours'];
                    }

                    $sum_t = $sum_a + $sum_b + $sum_c;
                    $cols_sum['ASUM'] += $sum_a;
                    $cols_sum['BSUM'] += $sum_b;
                    $cols_sum['CSUM'] += $sum_c;
                    $cols_sum['TSUM'] += $sum_t;

                    $col_idx = $col_ini;
                    $col_tag = 0;
                    foreach ( $cols as $col ) {
                        switch($col_tag) 
                        {
                        case 0:
                            $chr = chr( $col_idx );
                            break;
                        case 1:
                            $chr = 'A' . chr( $col_idx );
                            break;
                        }

                        switch( $col )
                        {
                        case 'ASUM':
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, number_format($sum_a));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $sum_a);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            break;
                        case 'BSUM':
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, number_format($sum_b));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $sum_b);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            break;
                        case 'CSUM':
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, number_format($sum_c));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $sum_c);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            break;
                        case 'TSUM':
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, number_format($sum_t));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $sum_t);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            break;
                        default:
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, (isset($cs[$col])? $cs[$col] : ""));
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $chr . $knt . $col_idx);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        }
                        if ($col_max > $col_idx) {
                            $col_idx++;
                        } else {
                            $col_tag = 1;
                            $col_idx = $col_min;
                        }
                    }
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":C" . $knt);

                $knt++;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '合計');
                $col_idx = $col_ini;
                $col_tag = 0;
                foreach ($cols_sum as $cs) {
                    switch($col_tag) 
                    {
                    case 0:
                        $chr = chr( $col_idx );
                        break;
                    case 1:
                        $chr = 'A' . chr( $col_idx );
                        break;
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $cs);

                    if ($col_max > $col_idx) {
                        $col_idx++;
                    } else {
                        $col_tag = 1;
                        $col_idx = $col_min;
                    }
                }

                /* turnover detail */
                $cnt = 4;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '各類研習總營收詳表' .$quarters);

                $knt = 1;
                $qnt = 0;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $era[0]['cname'] . '各類研習總營收詳表' );
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":C" . $knt);
                $knt++;
                // columns array
                $cols = array();
                $col_min = 65;
                $col_ini = 68;
                $col_max = 90;
                $col_tag = 0;
                $col_idx = $col_ini;
                $sum_a = 0;
                $sum_b = 0;
                $sum_c = 0;
                $sum_t = 0;

                // academic_class array
                //$academic_classes = (new AjaxModel)->dbQuery('admin_academic_class', array('era_id'=>$era_id));
                $acs = array();
                $acn = array();
                $cols_a = array();
                $cols_b = array();
                $cols_c = array();
                $cols_sum = array();
                foreach ($academic_classes as $ac) {
                    switch( $ac['major_code'] )
                    {
                    case 'A':
                        array_push($cols_a, $ac['minor_code']);
                        break;
                    case 'B':
                        array_push($cols_b, $ac['minor_code']);
                        break;
                    case 'C':
                        array_push($cols_c, $ac['minor_code']);
                        break;
                    }
                    $acs[$ac['minor_code']] = $ac['cname'];
                }

                for ($i=0; $i<sizeof($cols_a); $i++) {
                    array_push($cols, $cols_a[$i]);
                    $cols_sum[$cols_a[$i]] = 0;
                }
                array_push($cols, 'ASUM');
                $cols_sum['ASUM'] = 0;
                for ($i=0; $i<sizeof($cols_b); $i++) {
                    array_push($cols, $cols_b[$i]);
                    $cols_sum[$cols_b[$i]] = 0;
                }
                array_push($cols, 'BSUM');
                $cols_sum['BSUM'] = 0;
                for ($i=0; $i<sizeof($cols_c); $i++) {
                    array_push($cols, $cols_c[$i]);
                    $cols_sum[$cols_c[$i]] = 0;
                }
                array_push($cols, 'CSUM');
                $cols_sum['CSUM'] = 0;
                array_push($cols, 'TSUM');
                $cols_sum['TSUM'] = 0;

                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '序號');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '學校代碼');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '單位機構名稱');
                foreach ($cols as $col) {
                    switch( $col_tag )
                    {
                    case 0:
                        $chr = chr($col_idx);
                        break;
                    case 1:
                        $chr = 'A' . chr($col_idx);
                        break;
                    }
                    switch( $col )
                    {
                    case 'ASUM':
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, '第一類(A類)小計');
                        break;
                    case 'BSUM':
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, '第二類(B類)小計');
                        break;
                    case 'CSUM':
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, '第三類(C類)小計');
                        break;
                    case 'TSUM':
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, '華語研習三大類合計');
                        break;
                    default:
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $acs[ $col ]);
                    }
                    if ($col_max > $col_idx) {
                        $col_idx++;
                    } else {
                        $col_tag = 1;
                        $col_idx = $col_min;
                    }
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":C" . $knt);

                $col_idx = $col_ini;
                $col_tag = 0;
                $qnt = 0;
                foreach ($targets as $target) {
                    $qnt++;
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $qnt);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $target['institution_code']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $target['institution_cname'] . $target['cname']);
                    $classes = (new AjaxModel)->dbQuery('admin_academic_agency_report_manager_turnover_detail', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'quarters'=>$quarter));

                    $cs = array();
                    $sum_a = 0;
                    $sum_b = 0;
                    $sum_c = 0;
                    foreach ($classes as $c) {
                        $cs[$c['minor_code']] = $c['turnover'];
                        if (strpos($c['minor_code'], 'A') !== false) {
                            $sum_a += $c['turnover'];
                        } else if (strpos($c['minor_code'], 'B') !== false) {
                            $sum_b += $c['turnover'];
                        } else if (strpos($c['minor_code'], 'C') !== false) {
                            $sum_c += $c['turnover'];
                        }
                        $cols_sum[$c['minor_code']] += $c['turnover'];
                    }

                    $sum_t = $sum_a + $sum_b + $sum_c;
                    $cols_sum['ASUM'] += $sum_a;
                    $cols_sum['BSUM'] += $sum_b;
                    $cols_sum['CSUM'] += $sum_c;
                    $cols_sum['TSUM'] += $sum_t;

                    $col_idx = $col_ini;
                    $col_tag = 0;
                    foreach ( $cols as $col ) {
                        switch($col_tag) 
                        {
                        case 0:
                            $chr = chr( $col_idx );
                            break;
                        case 1:
                            $chr = 'A' . chr( $col_idx );
                            break;
                        }

                        switch( $col )
                        {
                        case 'ASUM':
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, number_format($sum_a));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $sum_a);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            break;
                        case 'BSUM':
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, number_format($sum_b));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $sum_b);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            break;
                        case 'CSUM':
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, number_format($sum_c));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $sum_c);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            break;
                        case 'TSUM':
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, number_format($sum_t));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $sum_t);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            break;
                        default:
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, (isset($cs[$col])? $cs[$col] : ""));
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $chr . $knt . $col_idx);
                            $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        }
                        if ($col_max > $col_idx) {
                            $col_idx++;
                        } else {
                            $col_tag = 1;
                            $col_idx = $col_min;
                        }
                    }
                }

                $knt++;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '合計');
                $col_idx = $col_ini;
                $col_tag = 0;
                foreach ($cols_sum as $cs) {
                    switch($col_tag) 
                    {
                    case 0:
                        $chr = chr( $col_idx );
                        break;
                    case 1:
                        $chr = 'A' . chr( $col_idx );
                        break;
                    }
                    //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, number_format($cs));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($chr . $knt, $cs);
                    $objPHPExcel->getActiveSheet()->getStyle($chr . $knt)->getNumberFormat()->setFormatCode('#,##0');

                    if ($col_max > $col_idx) {
                        $col_idx++;
                    } else {
                        $col_tag = 1;
                        $col_idx = $col_min;
                    }
                }
                /* new people summary */
                $cnt = 5;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '華語中心各類研習總人數簡表' . $quarters);

                $new_peoples = (new AjaxModel)->dbQuery('admin_academic_agency_report_manager_new_people_summary', array('era_id'=>$era_id, 'quarters'=>$quarter));
                $sum_a = 0;
                $sum_b = 0;
                $sum_c = 0;
                $sum_t = 0;
                $ratio_a = 0;
                $ratio_b = 0;
                $ratio_c = 0;
                $ratio_t = 0;

                $minor_a = array();
                $minor_b = array();
                $minor_c = array();

                foreach( $new_peoples as $new_people ) {
                    switch( $new_people['major_code'] )
                    {
                    case 'A':
                        $sum_a += $new_people['new_people'];
                        array_push($minor_a, $new_people);
                        break;
                    case 'B':
                        $sum_b += $new_people['new_people'];
                        array_push($minor_b, $new_people);
                        break;
                    case 'C':
                        $sum_c += $new_people['new_people'];
                        array_push($minor_c, $new_people);
                        break;
                    }
                    $sum_t += $new_people['new_people'];
                }

                $knt = 1;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $era[0]['cname'] . '華語文中心各類研習總人數簡表' );

                $knt = 2;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總人數');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '第一類(A)小計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '第二類(B)小計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, '第三類(C)小計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, '華語中心三類合計');

                $knt = 3;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總人數');
                //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($sum_a));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $sum_a);
                $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                //$objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 
                //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($sum_b));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $sum_b);
                $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                //$objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $sum_c);
                $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                //$objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $sum_t);
                $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                //$objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 

                $knt = 4;

                if ($sum_t > 0) {
/*
                    $ratio_a = round((($sum_a/$sum_t) * 100), 2) . '%';
                    $ratio_b = round((($sum_b/$sum_t) * 100), 2) . '%';
                    $ratio_c = round((($sum_c/$sum_t) * 100), 2) . '%';
                    $ratio_t = round((($sum_t/$sum_t) * 100), 2) . '%';
*/
                    $ratio_a = $sum_a/$sum_t;
                    $ratio_b = $sum_b/$sum_t;
                    $ratio_c = $sum_c/$sum_t;
                    $ratio_t = $sum_t/$sum_t;
                }

                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '佔全部百分比');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $ratio_a);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $ratio_b);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $ratio_c);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $ratio_t);
                $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));

                $col_ini = 66;
                $col_min = 65;
                $col_max = 90;
                
                /* A */
                $knt = 6;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $era[0]['cname'] . '華語文中心各類研習總人數詳表明細' );

                $knt = 7;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, 'A類總人數');
                foreach( $minor_a as $minor ) {
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $acs[$minor['minor_code']]);
                    $col_idx++;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, '第一類(A類)小計');

                $knt = 8;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總人數');
                foreach( $minor_a as $minor ) {
                    //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($minor['new_people']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $minor['new_people']);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $col_idx++;
                }
                //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($sum_a));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $sum_a);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');
                
                $knt = 9;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '各類佔百分比');
                foreach( $minor_a as $minor ) {
                    $ratio_a = 0;
                    if ($sum_a > 0) {
                        //$ratio_a = round(($minor['new_people']/$sum_a) * 100, 2) . '%';
                        $ratio_a = $minor['new_people']/$sum_a;
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_a);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                    $col_idx++;
                }
                $ratio_a = 0;
                if ($sum_a > 0) {
                    //$ratio_a = round(($sum_a/$sum_a) * 100, 2) . '%';
                    $ratio_a = $sum_a/$sum_a;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_a);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));

                /* B */
                $knt = 11;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, 'B類總人數');
                foreach( $minor_b as $minor ) {
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $acs[$minor['minor_code']]);
                    $col_idx++;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, '第二類(B類)小計');

                $knt = 12;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總人數');
                foreach( $minor_b as $minor ) {
                    //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($minor['new_people']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $minor['new_people']);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $col_idx++;
                }
                //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($sum_b));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $sum_b);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');

                $knt = 13;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '各類佔百分比');
                foreach( $minor_b as $minor ) {
                    $ratio_b = 0;
                    if ($sum_b > 0) {
                        //$ratio_b = round(($minor['new_people']/$sum_b) * 100, 2) .'%';
                        $ratio_b = $minor['new_people']/$sum_b;
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_b);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                    $col_idx++;
                }
                $ratio_b = 0;
                if ($sum_b > 0) {
                    //$ratio_b = round(($sum_b/$sum_b) * 100, 2) . '%';
                    $ratio_b = $sum_b/$sum_b;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_b);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));

                /* C */
                $knt = 15;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, 'C類總人數');
                foreach( $minor_c as $minor ) {
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $acs[$minor['minor_code']]);
                    $col_idx++;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, '第三類(C類)小計');

                $knt = 16;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總人數');
                foreach( $minor_c as $minor ) {
                    //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($minor['new_people']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $minor['new_people']);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $col_idx++;
                }
                //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($sum_c));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $sum_c);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');

                $knt = 17;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '各類佔百分比');
                foreach( $minor_c as $minor ) {
                    $ratio_c = 0;
                    if ($sum_c > 0) {
                        //$ratio_c = round(($minor['new_people']/$sum_c) * 100, 2) . '%';
                        $ratio_c = $minor['new_people']/$sum_c;
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_c);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                    $col_idx++;
                }
                $ratio_c = 0;
                if ($sum_c > 0) {
                    //$ratio_c = round(($sum_c/$sum_c) * 100, 2) .'%';
                    $ratio_c = $sum_c/$sum_c;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_c);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));

                /* people summary */
                $cnt = 6;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '華語中心各類研習總人次簡表' . $quarters);

                $peoples = (new AjaxModel)->dbQuery('admin_academic_agency_report_manager_people_summary', array('era_id'=>$era_id, 'quarters'=>$quarter));

                $sum_a = 0;
                $sum_b = 0;
                $sum_c = 0;
                $sum_t = 0;
                $ratio_a = 0;
                $ratio_b = 0;
                $ratio_c = 0;
                $ratio_t = 0;

                $minor_a = array();
                $minor_b = array();
                $minor_c = array();

                foreach( $peoples as $people ) {
                    switch( $people['major_code'] )
                    {
                    case 'A':
                        $sum_a += $people['people'];
                        array_push($minor_a, $people);
                        break;
                    case 'B':
                        $sum_b += $people['people'];
                        array_push($minor_b, $people);
                        break;
                    case 'C':
                        $sum_c += $people['people'];
                        array_push($minor_c, $people);
                        break;
                    }
                    $sum_t += $people['people'];
                }

                $knt = 1;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $era[0]['cname'] . '華語文中心各類研習總人次簡表' );

                $knt = 2;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總人次');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '第一類(A)小計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '第二類(B)小計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, '第三類(C)小計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, '華語中心三類合計');

                $knt = 3;
/*
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總人次');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($sum_a));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($sum_b));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($sum_c));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($sum_t));
*/

                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $sum_a);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $sum_b);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $sum_c);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $sum_t);
                $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                $knt = 4;
                if ($sum_t > 0) {
/*
                    $ratio_a = round((($sum_a/$sum_t) * 100), 2) . '%';
                    $ratio_b = round((($sum_b/$sum_t) * 100), 2) . '%';
                    $ratio_c = round((($sum_c/$sum_t) * 100), 2) . '%';
                    $ratio_t = round((($sum_t/$sum_t) * 100), 2) . '%';
*/
                    $ratio_a = $sum_a/$sum_t;
                    $ratio_b = $sum_b/$sum_t;
                    $ratio_c = $sum_c/$sum_t;
                    $ratio_t = $sum_t/$sum_t;
                }

                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '佔全部百分比');

                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $ratio_a);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $ratio_b);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $ratio_c);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $ratio_t);

                $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));

                $col_ini = 66;
                $col_min = 65;
                $col_max = 90;
                
                /* A */
                $knt = 6;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $era[0]['cname'] . '華語文中心各類研習總人次簡表明細' );

                $knt = 7;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, 'A類總人次');
                foreach( $minor_a as $minor ) {
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $acs[$minor['minor_code']]);
                    $col_idx++;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, '第一類(A類)小計');

                $knt = 8;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總人次');
                foreach( $minor_a as $minor ) {
                    //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($minor['people']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $minor['people']);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $col_idx++;
                }
                //$objPHPExce_->setActiveSheetIndex($cnt)->setCellValue(chr($cml_idx) . $knt, number_format($sum_a));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $sum_a);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');
                
                $knt = 9;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '各類佔百分比');
                foreach( $minor_a as $minor ) {
                    $ratio_a = 0;
                    if ($sum_a > 0) {
                        //$ratio_a = round(($minor['people']/$sum_a) * 100, 2) . '%';
                        $ratio_a = $minor['people']/$sum_a;
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_a);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                    $col_idx++;
                }
                $ratio_a = 0;
                if ($sum_a > 0) {
                    //$ratio_a = round(($sum_a/$sum_a) * 100, 2) . '%';
                    $ratio_a = $sum_a/$sum_a;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_a);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));

                /* B */
                $knt = 11;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, 'B類總人次');
                foreach( $minor_b as $minor ) {
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $acs[$minor['minor_code']]);
                    $col_idx++;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, '第二類(B類)小計');

                $knt = 12;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總人次');
                foreach( $minor_b as $minor ) {
                    //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($minor['people']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $minor['people']);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $col_idx++;
                }
                //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($sum_b));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $sum_b);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');

                $knt = 13;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '各類佔百分比');
                foreach( $minor_b as $minor ) {
                    $ratio_b = 0;
                    if ($sum_b > 0) {
                        //$ratio_b = round(($minor['people']/$sum_b) * 100, 2) . '%';
                        $ratio_b = $minor['people']/$sum_b;
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_b);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                    $col_idx++;
                }
                $ratio_b = 0;
                if ($sum_b > 0) {
                    $ratio_b = round(($sum_b/$sum_b) * 100, 2) . '%';
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_b);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));

                /* C */
                $knt = 15;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, 'C類總人次');
                foreach( $minor_c as $minor ) {
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $acs[$minor['minor_code']]);
                    $col_idx++;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, '第三類(C類)小計');

                $knt = 16;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總人次');
                foreach( $minor_c as $minor ) {
                    //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($minor['people']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $minor['people']);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $col_idx++;
                }
                //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($sum_c));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $sum_c);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');

                $knt = 17;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '各類佔百分比');
                foreach( $minor_c as $minor ) {
                    $ratio_c = 0;
                    if ($sum_c > 0) {
                        //$ratio_c = round(($minor['people']/$sum_c) * 100, 2) . '%';
                        $ratio_c = $minor['people']/$sum_c;
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_c);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                    $col_idx++;
                }
                $ratio_c = 0;
                if ($sum_c > 0) {
                    //$ratio_c = round(($sum_c/$sum_c) * 100, 2) . '%';
                    $ratio_c = $sum_c/$sum_c;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_c);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));

                /* total hours summary */
                $cnt = 7;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '華語中心各類研習總人時數簡表' . $quarters);

                $total_hours = (new AjaxModel)->dbQuery('admin_academic_agency_report_manager_total_hours_summary', array('era_id'=>$era_id, 'quarters'=>$quarter));
                $sum_a = 0;
                $sum_b = 0;
                $sum_c = 0;
                $sum_t = 0;
                $ratio_a = 0;
                $ratio_b = 0;
                $ratio_c = 0;
                $ratio_t = 0;

                $minor_a = array();
                $minor_b = array();
                $minor_c = array();

                foreach( $total_hours as $total_hour ) {
                    switch( $total_hour['major_code'] )
                    {
                    case 'A':
                        $sum_a += $total_hour['total_hours'];
                        array_push($minor_a, $total_hour);
                        break;
                    case 'B':
                        $sum_b += $total_hour['total_hours'];
                        array_push($minor_b, $total_hour);
                        break;
                    case 'C':
                        $sum_c += $total_hour['total_hours'];
                        array_push($minor_c, $total_hour);
                        break;
                    }
                    $sum_t += $total_hour['total_hours'];
                }
                $knt = 1;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $era[0]['cname'] . '華語文中心各類研習總人時數簡表' );

                $knt = 2;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總人時數');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '第一類(A)小計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '第二類(B)小計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, '第三類(C)小計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, '華語中心三類合計');

                $knt = 3;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總人時數');
/*
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($sum_a));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($sum_b));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($sum_c));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($sum_t));
*/

                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $sum_a);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $sum_b);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $sum_c);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $sum_t);

                $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                $knt = 4;
                if ($sum_t > 0) {
/*
                    $ratio_a = round((($sum_a/$sum_t) * 100), 2) . '%';
                    $ratio_b = round((($sum_b/$sum_t) * 100), 2) . '%';
                    $ratio_c = round((($sum_c/$sum_t) * 100), 2) . '%';
                    $ratio_t = round((($sum_t/$sum_t) * 100), 2) . '%';
*/
                    $ratio_a = $sum_a/$sum_t;
                    $ratio_b = $sum_b/$sum_t;
                    $ratio_c = $sum_c/$sum_t;
                    $ratio_t = $sum_t/$sum_t;
                }

                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '佔全部百分比');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $ratio_a);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $ratio_b);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $ratio_c);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $ratio_t);
                $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));

                $col_ini = 66;
                $col_min = 65;
                $col_max = 90;
                
                // A 
                $knt = 6;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $era[0]['cname'] . '華語文中心各類研習總人時數簡表明細' );

                $knt = 7;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, 'A類總人時數');
                foreach( $minor_a as $minor ) {
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $acs[$minor['minor_code']]);
                    $col_idx++;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, '第一類(A類)小計');

                $knt = 8;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總人時數');
                foreach( $minor_a as $minor ) {
                    //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($minor['total_hours']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $minor['total_hours']);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');

                    $col_idx++;
                }
                //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($sum_a));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $sum_a);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');
                
                $knt = 9;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '各類佔百分比');
                foreach( $minor_a as $minor ) {
                    $ratio_a = 0;
                    if ($sum_a > 0) {
                        //$ratio_a = round(($minor['total_hours']/$sum_a) * 100, 2) . '%';
                        $ratio_a = $minor['total_hours']/$sum_a;
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_a);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                    $col_idx++;
                }
                $ratio_a = 0;
                if ($sum_a > 0) {
                    //$ratio_a = round(($sum_a/$sum_a) * 100, 2) . '%';
                    $ratio_a = $sum_a/$sum_a;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_a);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                // B 
                $knt = 11;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, 'B類總人時數');
                foreach( $minor_b as $minor ) {
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $acs[$minor['minor_code']]);
                    $col_idx++;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, '第二類(B類)小計');

                $knt = 12;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總人時數');
                foreach( $minor_b as $minor ) {
                    //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($minor['total_hours'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $minor['total_hours']);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $col_idx++;
                }
                //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($sum_b, 2));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $sum_b);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');

                $knt = 13;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '各類佔百分比');
                foreach( $minor_b as $minor ) {
                    $ratio_b = 0;
                    if ($sum_b > 0) {
                        //$ratio_b = round(($minor['total_hours']/$sum_b) * 100, 2) . '%';
                        $ratio_b = $minor['total_hours']/$sum_b;
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_b);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $col_idx++;
                }
                $ratio_b = 0;
                if ($sum_b > 0) {
                    //$ratio_b = round(($sum_b/$sum_b) * 100, 2) . '%';
                    $ratio_b = $sum_b/$sum_b;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_a);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                // C 
                $knt = 15;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, 'C類總人時數');
                foreach( $minor_c as $minor ) {
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $acs[$minor['minor_code']]);
                    $col_idx++;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, '第三類(C類)小計');

                $knt = 16;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總人時數');
                foreach( $minor_c as $minor ) {
                    //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($minor['total_hours'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $minor['total_hours']);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');

                    $col_idx++;
                }
                //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($sum_c, 2));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $sum_c);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');

                $knt = 17;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '各類佔百分比');
                foreach( $minor_c as $minor ) {
                    $ratio_c = 0;
                    if ($sum_c > 0) {
                        //$ratio_c = round(($minor['total_hours']/$sum_c) * 100, 2) . '%';
                        $ratio_c = $minor['total_hours']/$sum_c;
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_c);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $col_idx++;
                }
                $ratio_c = 0;
                if ($sum_c > 0) {
                    //$ratio_c = round(($sum_c/$sum_c) * 100, 2) . '%';
                    $ratio_c = $sum_c/$sum_c;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_a);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                /* turnover summary */
                $cnt = 8;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '華語中心各類研習總營收簡表' . $quarters);

                $turnovers = (new AjaxModel)->dbQuery('admin_academic_agency_report_manager_turnover_summary', array('era_id'=>$era_id, 'quarters'=>$quarter));

                $sum_a = 0;
                $sum_b = 0;
                $sum_c = 0;
                $sum_t = 0;
                $ratio_a = 0;
                $ratio_b = 0;
                $ratio_c = 0;
                $ratio_t = 0;

                $minor_a = array();
                $minor_b = array();
                $minor_c = array();

                foreach( $turnovers as $turnover ) {
                    switch( $turnover['major_code'] )
                    {
                    case 'A':
                        $sum_a += $turnover['turnover'];
                        array_push($minor_a, $turnover);
                        break;
                    case 'B':
                        $sum_b += $turnover['turnover'];
                        array_push($minor_b, $turnover);
                        break;
                    case 'C':
                        $sum_c += $turnover['turnover'];
                        array_push($minor_c, $turnover);
                        break;
                    }
                    $sum_t += $turnover['turnover'];
                }

                $knt = 1;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $era[0]['cname'] . '華語文中心各類研習總營收簡表' );

                $knt = 2;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總營收');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '第一類(A)小計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '第二類(B)小計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, '第三類(C)小計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, '華語中心三類合計');

                $knt = 3;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總營收');
/*
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($sum_a));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($sum_b));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($sum_c));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($sum_t));
*/

                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $sum_a);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $sum_b);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $sum_c);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $sum_t);
                //$objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1));
                //$objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1));
                //$objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1));
                //$objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1));
                $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode('#,##0');


                $knt = 4;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '佔全部百分比');
                if ($sum_t > 0) {
/*
                    $ratio_a = round((($sum_a/$sum_t) * 100), 2) . '%';
                    $ratio_b = round((($sum_b/$sum_t) * 100), 2) . '%';
                    $ratio_c = round((($sum_c/$sum_t) * 100), 2) . '%';
                    $ratio_t = round((($sum_t/$sum_t) * 100), 2) . '%';
*/

                    $ratio_a = $sum_a/$sum_t;
                    $ratio_b = $sum_b/$sum_t;
                    $ratio_c = $sum_c/$sum_t;
                    $ratio_t = $sum_t/$sum_t;
                }

                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $ratio_a);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $ratio_b);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $ratio_c);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $ratio_t);
                $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));

                $col_ini = 66;
                $col_min = 65;
                $col_max = 90;
                
                // A 
                $knt = 6;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $era[0]['cname'] . '華語文中心各類研習總營收簡表明細' );

                $knt = 7;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, 'A類總營收');
                foreach( $minor_a as $minor ) {
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $acs[$minor['minor_code']]);
                    $col_idx++;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, '第一類(A類)小計');

                $knt = 8;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總營收');
                foreach( $minor_a as $minor ) {
                    //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($minor['turnover']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $minor['turnover']);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $col_idx++;
                }
                //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($sum_a));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $sum_a);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');
                
                $knt = 9;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '各類佔百分比');
                foreach( $minor_a as $minor ) {
                    $ratio_a = 0;
                    if ($sum_a > 0) {
                        //$ratio_a = round(($minor['turnover']/$sum_a) * 100, 2) . '%';
                        $ratio_a = $minor['turnover']/$sum_a;
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_a);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                    $col_idx++;
                }
                $ratio_a = 0;
                if ($sum_a > 0) {
                    //$ratio_a = round(($sum_a/$sum_a) * 100, 2) . '%';
                    $ratio_a = $sum_a/$sum_a;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_a);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));

                // B 
                $knt = 11;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, 'B類總營收');
                foreach( $minor_b as $minor ) {
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $acs[$minor['minor_code']]);
                    $col_idx++;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, '第二類(B類)小計');

                $knt = 12;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總營收');
                foreach( $minor_b as $minor ) {
                    //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($minor['turnover']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $minor['turnover']);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $col_idx++;
                }
                //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($sum_b));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $sum_b);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');

                $knt = 13;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '各類佔百分比');
                foreach( $minor_b as $minor ) {
                    $ratio_b = 0;
                    if ($sum_b > 0) {
                        //$ratio_b = round(($minor['turnover']/$sum_b) * 100, 2) . '%';
                        $ratio_b = $minor['turnover']/$sum_b;
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_b);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                    $col_idx++;
                }
                $ratio_b = 0;
                if ($sum_b > 0) {
                    //$ratio_b = round(($sum_b/$sum_b) * 100, 2) . '%';
                    $ratio_b = $sum_b/$sum_b;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_b);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));

                // C 
                $knt = 15;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, 'C類總營收');
                foreach( $minor_c as $minor ) {
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $acs[$minor['minor_code']]);
                    $col_idx++;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, '第三類(C類)小計');

                $knt = 16;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '總營收');
                foreach( $minor_c as $minor ) {
                    //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($minor['turnover']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $minor['turnover']);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $col_idx++;
                }
                //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, number_format($sum_c));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $sum_c);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->setFormatCode('#,##0');

                $knt = 17;
                $col_idx = $col_ini;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '各類佔百分比');
                foreach( $minor_c as $minor ) {
                    $ratio_c = 0;
                    if ($sum_c > 0) {
                        //$ratio_c = round(($minor['turnover']/$sum_c) * 100, 2) . '%';
                        $ratio_c = $minor['turnover']/$sum_c;
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_c);
                    $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));
                    $col_idx++;
                }
                $ratio_c = 0;
                if ($sum_c > 0) {
                    //$ratio_c = round(($sum_c/$sum_c) * 100, 2) . '%';
                    $ratio_c = $sum_c/$sum_c;
                }
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr($col_idx) . $knt, $ratio_c);
                $objPHPExcel->getActiveSheet()->getStyle(chr($col_idx) . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00));

                /* */
                $cnt = 9;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '大學附設華語中心人數(次)一覽表(交叉總表)' . $quarters);

                $details = (new AjaxModel)->dbQuery('admin_academic_agency_report_manager_detail', array('era_id'=>$era_id, 'quarters'=>$quarter));

                $new_people = 0;
                $people = 0;

                $knt = 1;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $era[0]['cname'] . '大學附設華語中心招生人數(次)一覽表(交叉總表)' );

                $knt = 2;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '學校代碼');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '機構名稱');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, 'A + B人數');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, 'A + B總人次');

                foreach( $details as $detail ) {
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $detail['institution_code']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $detail['institution_cname'] . $detail['academic_agency_cname']);
/*
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($detail['new_people']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($detail['people']));
*/
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $detail['new_people']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $detail['people']);
                    $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $new_people += $detail['new_people'];
                    $people += $detail['people'];
                }
                $knt++;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '合計');
/*
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($new_people));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($people));
*/

                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $new_people);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $people);
                $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                $filename = '管理報表';
                break;
            case 'statistics':
                $era = (new AjaxModel)->dbQuery('admin_academic_era', array('era_id'=>$era_id));
                $era_last = (new AjaxModel)->dbQuery('admin_academic_era_last', array('era_id'=>$era_id));
                
                $cnt = 0;
                $sum_n = 0;
                $sum_m = 0;
                $sum_f = 0;
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( '明細' );

                $knt = 1;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '語文中心代碼');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '語文中心名稱');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '國家代碼');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, '國家名稱');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '小計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, '男生');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, '女生');
                //$objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":G" . $knt);
                
                foreach ($targets as $target) {
                    $res = (new AjaxModel)->dbQuery('admin_academic_agency_report_statistics_detail', array('agency_id'=>$target['id'], 'era_id'=>$era_id));
                    if (sizeof($res)) {
                        $cache_code = '';
                        foreach($res as $r) {
                            $knt++;
                            if ($cache_code != $target['institution_code']) {
                                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyleT, "A". $knt .":G" . $knt);
                            }
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $target['institution_code']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $target['institution_cname'] . $target['cname']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $r['country_code']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $r['country_cname']);
/*
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($r['new_people']));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($r['new_male']));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($r['new_female']));
*/
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $r['new_people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $r['new_male']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $r['new_female']);
                            $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            //$objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":G" . $knt);
                            $sum_n += $r['new_people'];
                            $sum_m += $r['new_male'];
                            $sum_f += $r['new_female'];
                            $cache_code = $target['institution_code'];
                        }
                    }
                }
                $knt++;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '合計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, '');
/*
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($sum_n));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($sum_m));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($sum_f));
*/
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $sum_n);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $sum_m);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $sum_f);
                $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyleT, "A". $knt .":G" . $knt);
                // contact
                $cnt++;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex(1);
                $objPHPExcel->getActiveSheet()->setTitle( '聯絡人' );

                $knt = 1;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '語文中心代碼');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '語文中心名稱');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '統計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, '男生');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '女生');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, '主管姓名');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, '職稱');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, '主要聯絡人');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, '職稱');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, '聯絡電話');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, '電子郵件');
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);

                foreach ($targets as $target) {
                    $res = (new AjaxModel)->dbQuery('admin_academic_agency_report_statistics_contact', array('agency_id'=>$target['id'], 'era_id'=>$era_id));
                    if (sizeof($res)) {
                        $r = $res[0];
                        $knt++;
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $target['institution_code']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $target['institution_cname'] . $target['cname']);
/*
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($r['new_people']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($r['new_male']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($r['new_female']));
*/
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $r['new_people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $r['new_male']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $r['new_female']);
                        $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $r['manager_cname']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $r['manager_title']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $r['primary_cname']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $r['primary_title']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $r['primary_tel']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $r['primary_email']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);
                    }
                }

                // compare
                $cnt++;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex(2);
                $objPHPExcel->getActiveSheet()->setTitle( '差異' );

                $knt = 1;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '語文中心代碼');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '語文中心名稱');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $era[0]['cname']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, ($era[0]['common'] - 1911 - 1) . '年度');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '差距');
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":E" . $knt);
                $sum_curr = 0;
                $sum_last = 0;
                foreach ($targets as $target) {
                    $res = (new AjaxModel)->dbQuery('admin_academic_agency_report_statistics_compare', array('agency_id'=>$target['id'], 'era_id'=>$era_id));
                    if (sizeof($res)) {
                        $knt++;
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $target['institution_code']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $target['institution_cname'] . $target['cname']);
/*
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($res['curr']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($res['last']));
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format(($res['curr'] - $res['last'])));
*/
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $res['curr']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $res['last']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, ($res['curr'] - $res['last']));
                        $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                        $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                        $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":E" . $knt);
                        $sum_curr += intval($res['curr']);
                        $sum_last += intval($res['last']);
                    }
                }
                $knt++;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '合計');
/*
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($sum_curr));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($sum_last));
*/
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $sum_curr);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $sum_last);
                $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $filename = '統計處報表';
                $filename = '統計處報表';
                break;
            case 'major_b':
                $major_b = (new AjaxModel)->dbQuery('admin_academic_agency_report_major_b', array('era_id'=>$era_id));
                $cnt = 0;
                foreach ($major_b as $b) {
                    if ($cnt > 0) {
                        $objPHPExcel->createSheet();
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt);
                    $cname = preg_replace("/\(/", "|", $b['cname']);
                    $cname = preg_replace("/\)/", "|", $cname);
                    $cname = preg_replace("/\:/", "-", $cname);
                    $objPHPExcel->getActiveSheet()->setTitle( $cname );
                    $knt = 1;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $cname);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":H" . $knt);
                    $knt = 2;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '學校代碼');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '單位名稱');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '總人數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, '總人次');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '總人時數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, '總營收');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, '小註');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, '備註');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":H" . $knt);

                    $new_people = 0;
                    $people = 0;
                    $total_hours = 0;
                    $turnover = 0;
                    $info = '';
                    $note = '';
                    
                    $knt = 3;
                    foreach ($targets as $target) {
                        $minor_b = (new AjaxModel)->dbQuery('admin_academic_agency_report_minor_b', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'minor_code'=>$b['minor_code']));
                        if (sizeof($minor_b)) {
                            $minor = $minor_b[0];
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $target['institution_code']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $target['institution_cname'] . $target['cname']);
/*
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($minor['new_people']));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($minor['people']));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($minor['total_hours'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($minor['turnover']));
*/
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $minor['new_people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $minor['people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $minor['total_hours']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $minor['turnover']);

                            $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1));
                            $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $minor['info']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $minor['note']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":H" . $knt);
                            
                            $new_people += intval( $minor['new_people'] );
                            $people += intval( $minor['people'] );
                            $total_hours += floatval( $minor['total_hours'] );
                            $turnover += floatval( $minor['turnover'] );
                            $knt++;
                        }
                    }

                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '合計');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '');
/*
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($new_people));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($people));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($total_hours));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($turnover));
*/
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $new_people);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $people);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $total_hours);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $turnover);

                    $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1));
                    $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $info);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $note);

                    $cnt++;
                }

                $filename = 'B類年度總表';
                break;
            case 'states':
                $states = (new AjaxModel)->dbQuery('admin_academic_agency_report_states', array('era_id'=>$era_id));
                $cnt = 0;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $knt = 0;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '');
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":L" . $knt);
                $knt = 1;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '排序');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '洲別');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '國別');
                $objPHPExcel->getActiveSheet()->mergeCells('D'. $knt .':E'. $knt);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, 'A類課程');
                $objPHPExcel->getActiveSheet()->mergeCells('F'. $knt .':G'. $knt);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, 'B類課程');
                $objPHPExcel->getActiveSheet()->mergeCells('H'. $knt .':I'. $knt);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, 'C類課程');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, '男生人數');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, '女生人數');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, '總計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "D". $knt .":I" . $knt);
                $knt = 2;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '(地區)');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, '男');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '女');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, '男');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, '女');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, '男');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, '女');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, '');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, '');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, '');
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":L" . $knt);

                $sn = 0;
                $country_code = "";
                $male_a = 0;
                $male_b = 0;
                $male_c = 0;
                $female_a = 0;
                $female_b = 0;
                $female_c = 0;
                $male = 0;
                $female = 0;
                $people = 0;

                $roc = array( 
                    'state_name' => '',
                    'country_name' => '',
                    'male_a' => 0,
                    'male_b' => 0,
                    'male_c' => 0,
                    'female_a' => 0,
                    'female_b' => 0,
                    'female_c' => 0,
                    'male' => 0,
                    'female' => 0,
                    'people' => 0
                );

                foreach ($states as $s) {
                    if ('000' == $s['country_code']) {
                        $roc['state_name'] = $s['state_name'];
                        $roc['country_name'] = $s['country_name'];
                        $roc['male_a'] = $s['male_a'];
                        $roc['male_b'] = $s['male_b'];
                        $roc['male_c'] = $s['male_c'];
                        $roc['female_a'] = $s['female_a'];
                        $roc['female_b'] = $s['female_b'];
                        $roc['female_c'] = $s['female_c'];
                        $roc['male'] = $s['male'];
                        $roc['female'] = $s['female'];
                        $roc['people'] = $s['people'];
                        continue;
                    }
                    $sn++;
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $sn);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $s['state_name']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $s['country_name']);
/*
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($s['male_a']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($s['female_a']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($s['male_b']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($s['female_b']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($s['male_c']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($s['female_c']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, number_format($s['male']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, number_format($s['female']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, number_format($s['people']));
*/
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $s['male_a']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $s['female_a']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $s['male_b']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $s['female_b']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $s['male_c']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $s['female_c']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $s['male']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $s['female']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, $s['people']);

                    $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('J' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('K' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('L' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                    $male_a += $s['male_a'];
                    $male_b += $s['male_b'];
                    $male_c += $s['male_c'];
                    $female_a += $s['female_a'];
                    $female_b += $s['female_b'];
                    $female_c += $s['female_c'];
                    $male += $s['male'];
                    $female += $s['female'];
                    $people += $s['people'];
                }
                $knt++;
                $objPHPExcel->getActiveSheet()->mergeCells('A'. $knt .':B'. $knt);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '不含中華民國人數');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '總和');
/*
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($male_a));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($female_a));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($male_b));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($female_b));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($male_c));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($female_c));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, number_format($male));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, number_format($female));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, number_format($people));
*/
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $male_a);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $female_a);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $male_b);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $female_b);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $male_c);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $female_c);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $male);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $female);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, $people);

                $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('J' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('K' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('L' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                $knt++;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '*');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $roc['state_name']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $roc['country_name']);
/*
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($roc['male_a']));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($roc['female_a']));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($roc['male_b']));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($roc['female_b']));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($roc['male_c']));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($roc['female_c']));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, number_format($roc['male']));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, number_format($roc['female']));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, number_format($roc['people']));
*/

                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $roc['male_a']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $roc['female_a']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $roc['male_b']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $roc['female_b']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $roc['male_c']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $roc['female_c']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $roc['male']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $roc['female']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, $roc['people']);

                $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('J' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('K' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('L' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                $knt++;
                $objPHPExcel->getActiveSheet()->mergeCells('A'. $knt .':B'. $knt);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '含中華民國人數');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '總和');
/*
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($male_a + $roc['male_a']));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($female_a + $roc['female_a']));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($male_b + $roc['male_b']));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($female_b + $roc['female_b']));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($male_c + $roc['male_c']));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($female_c + $roc['female_c']));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, number_format($male + $roc['male']));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, number_format($female + $roc['female']));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, number_format($people + $roc['people']));
*/
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $male_a + $roc['male_a']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $female_a + $roc['female_a']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $male_b + $roc['male_b']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $female_b + $roc['female_b']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $male_c + $roc['male_c']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $female_c + $roc['female_c']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $male + $roc['male']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $female + $roc['female']);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, $people + $roc['people']);

                $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('G' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('H' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('I' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('J' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('K' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('L' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                $filename = '國籍人數排序報表';
                break;
            case 'classes':
                // pass minor_code as quarter in param
                $classes = (new AjaxModel)->dbQuery('admin_academic_agency_report_classes', array('era_id'=>$era_id, 'minor_code'=>$quarter));
                $class_name = '';
                if (sizeof($classes)) {
                    $class_name = $classes[0]['class_name'];
                }
                $cnt = 0;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $knt = 1;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '');
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":F" . $knt);
                $knt = 2;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '洲別');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '排序');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '國家名稱');
                $objPHPExcel->getActiveSheet()->mergeCells('D'. $knt .':E'. $knt);
                $objPHPExcel->getActiveSheet()->setCellValue('D'. $knt, $class_name);

                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, '合計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":F" . $knt);
                
                $knt = 3;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, '男性');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '女性');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, '');
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":F" . $knt);

                $state_code = '';
                $state_name = '';
                $male = 0;
                $female = 0;
                $people = 0;
                $sum_male = 0;
                $sum_female = 0;
                $sum_people = 0;
                $sn = 0;
                foreach ($classes as $c) {
                    $knt++;
                    if ($state_code != $c['code']) {
                        $sn = 0;
                        if ($state_code != "") {
                            $objPHPExcel->getActiveSheet()->mergeCells('B'. $knt .':C'. $knt);
                            $objPHPExcel->getActiveSheet()->setCellValue('B'. $knt, $state_name . '地區合計');
/*
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($male));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($female));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($people));
*/
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $male);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $female);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $people);

                            $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                            $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                            $sum_male += $male;  
                            $sum_female += $female;  
                            $sum_people += $people;  
                            $male = 0;
                            $female = 0;
                            $people = 0;
                            $knt++;
                        }
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $c['state_name']);
                    }
                    $sn++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $sn);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $c['country_name']);
/*
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($c['male']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($c['female']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($c['people']));
*/
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $c['male']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $c['female']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $c['people']);

                    $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                    $male += $c['male'];
                    $female += $c['female'];
                    $people += $c['people'];

                    $state_code = $c['code']; 
                    $state_name = $c['state_name']; 
                }

                $sum_male += $male;  
                $sum_female += $female;  
                $sum_people += $people;  

                $knt++;
                $objPHPExcel->getActiveSheet()->mergeCells('B'. $knt .':C'. $knt);
                $objPHPExcel->getActiveSheet()->setCellValue('B'. $knt, $state_name . '地區合計');
/*
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($male));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($female));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($people));
*/
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $male);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $female);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $people);

                $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');

                $knt++;
                $objPHPExcel->getActiveSheet()->mergeCells('A'. $knt .':E'. $knt);
                $objPHPExcel->getActiveSheet()->setCellValue('A'. $knt, '人數合計');
                //$objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($sum_people));
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $sum_people);
                $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->setFormatCode('#,##0');
                $filename = '五大洲及課程呈現報表';
                break;
            case 'people':
                $persons = (new AjaxModel)->dbQuery('admin_academic_agency_report_people', array('era_id'=>$era_id));
                $cnt = 0;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $knt = 0;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '');
                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":G" . $knt);
                $knt = 1;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '洲別');
                $knt = 2;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '國家數');
                $knt = 3;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '男性');
                $knt = 4;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '女性');
                $knt = 5;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '人數合計');
                $knt = 6;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '比例');
                $countries = 0;
                $male = 0;
                $female = 0;
                $people = 0;
                $AC = 0;
                $AF = 0;
                $AS = 0;
                $EU = 0;
                $OA = 0;
                foreach ($persons as $p) {
                    $countries += $p['countries'];
                    $male += $p['male'];
                    $female += $p['female'];
                    $people += $p['people'];
                    switch($p['state_code'])
                    {
                    case 'AC':
                        $c = 'C';
                        $AC = $p['people'];
                        break;
                    case 'AF':
                        $c = 'E';
                        $AF = $p['people'];
                        break;
                    case 'AS':
                        $c = 'B';
                        $AS = $p['people'];
                        break;
                    case 'EU':
                        $c = 'D';
                        $EU = $p['people'];
                        break;
                    case 'OA':
                        $c = 'F';
                        $OA = $p['people'];
                        break;
                    }    
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($c . '1', $p['state_name']);
/*
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($c . '2', number_format($p['countries']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($c . '3', number_format($p['male']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($c . '4', number_format($p['female']));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($c . '5', number_format($p['people']));
*/
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($c . '2', $p['countries']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($c . '3', $p['male']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($c . '4', $p['female']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue($c . '5', $p['people']);
                    $objPHPExcel->getActiveSheet()->getStyle($c . '2')->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle($c . '3')->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle($c . '4')->getNumberFormat()->setFormatCode('#,##0');
                    $objPHPExcel->getActiveSheet()->getStyle($c . '5')->getNumberFormat()->setFormatCode('#,##0');
                }

                $knt = 6;
/*
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, number_format($AS*100/$people, 2) . '%');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($AC*100/$people, 2) . '%');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($EU*100/$people, 2) . '%');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($AF*100/$people, 2) . '%');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($OA*100/$people, 2) . '%');
*/                
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $AS*100/$people);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $AC*100/$people);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $EU*100/$people);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $AF*100/$people);
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $OA*100/$people);

                $objPHPExcel->getActiveSheet()->getStyle('B' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1));
                $objPHPExcel->getActiveSheet()->getStyle('C' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1));
                $objPHPExcel->getActiveSheet()->getStyle('D' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1));
                $objPHPExcel->getActiveSheet()->getStyle('E' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1));
                $objPHPExcel->getActiveSheet()->getStyle('F' . $knt)->getNumberFormat()->applyFromArray( array( 'code' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1));

                $filename = '五大洲課程人數統計報表';
                break;

            case 'special':

                $special = (new AjaxModel)->dbQuery('admin_academic_agency_report_special', array('era_id'=>$era_id, 'sql'=>base64_decode( $quarter )));
                
                $cnt = 0;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);

                $knt = 0;
                foreach ($special as $s) {
                    $knt++;
                    if (1 == $knt) {
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '');
                        $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":". chr( 64 + sizeof($s) ) . $knt);
                        $knt++;
                        $c = 64; 
                        foreach( $s as $k=>$v ) {
                            $c++;
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr( $c ) . $knt, $k);
                        }
                        $knt++;
                    }
                    
                    $c = 64; 
                    foreach( $s as $k=>$v ) {
                        $c++;

                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue(chr( $c ) . $knt, $v);
                    }
                }

                $filename = '自訂查詢';
                break;

            }

            $objPHPExcel->setActiveSheetIndex(0);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
            break;
        case 'academic_agency_report':
            $agency = (new AjaxModel)->dbQuery('agent_academic_agency', array('id'=>$agency_id));
            $era = (new AjaxModel)->dbQuery('admin_academic_era', array('era_id'=>$era_id));
            $majors = (new AjaxModel)->dbQuery('refs_major_list');
            $major_head = array();
            $major_foot = array('S'=>'合計');
            $major_sum = array('S'=>array('countries'=>0, 'new_male'=>0, 'new_female'=>0, 'new_people'=>0, 'people'=>0, 'weekly'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0));
            $major_cache = 'A';
            foreach( $majors as $major ) {
                switch( $major['code'] ) 
                {
                case 'A':
                    $major_head[ $major['code'] ] = '第一類研習類別';
                    $major_foot[ $major['code'] ] = '第一類研習類別小計';
                    break;
                case 'B':
                    $major_head[ $major['code'] ] = '第二類研習類別';
                    $major_foot[ $major['code'] ] = '第二類研習類別小計';
                    break;
                case 'C':
                    $major_head[ $major['code'] ] = '第三類研習類別';
                    $major_foot[ $major['code'] ] = '第三類研習類別小計';
                    break;
                }
                $major_sum[ $major['code'] ] = array('countries'=>0, 'new_male'=>0, 'new_female'=>0, 'new_people'=>0, 'people'=>0, 'weekly'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0);
               
            }

            switch($val)
            {
            case 'summary':
                error_reporting(E_ALL);
                ini_set('display_errors', TRUE);
                ini_set('display_startup_errors', TRUE);
                //date_default_timezone_set('Asia/Taipei');
                define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
                $objPHPExcel = new PHPExcel();
                $sharedStyle = new PHPExcel_Style(); 
                $sharedStyle->applyFromArray(
                    array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'borders' => array(
                            'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                        )
                    )
                );
                $cnt = 0;
                $filename = $era[0]['cname'] . $agency[0]['institution_code'] . $agency[0]['academic_institution_cname'] . '課程明細簡表(四大類)';
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $filename );

                //$res = (new AjaxModel)->dbQuery('agent_academic_agency_report_summary', array('agency_id'=>$agency_id, 'era_id'=>$era_id, 'quarter'=>$quarter));
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_report_era_summary', array('agency_id'=>$agency_id, 'era_id'=>$era_id, 'quarter'=>$quarter));
                $size = sizeof($res);
                if ($size) {
                    $knt = 1;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $filename);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '研習類別');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '總人數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '總人次');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, '每週平均上課時數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '每週平均上課時數(每班平均)');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, '每期上課時數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, '總人時數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, '營收額度');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, '已組合班數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, '小註(課程名稱)');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, '備註');
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);
                    $count = 0;
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_head[ $major_cache ]);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);
                    foreach($res as $r) {
                        if ($major_cache != $r['major_code']) {
                            $knt++;
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ $major_cache ]);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, number_format($major_sum[ $major_cache ]['new_people'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($major_sum[ $major_cache ]['people'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($major_sum[ $major_cache ]['weekly'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($major_sum[ $major_cache ]['avg_weekly'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($major_sum[ $major_cache ]['hours'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($major_sum[ $major_cache ]['total_hours'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($major_sum[ $major_cache ]['turnover'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($major_sum[ $major_cache ]['classes'], 2));
                            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);

                            $knt++;
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_head[ $r['major_code'] ]);
                            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);
                        }
                        $knt++;
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '第'. $r['quarter'] .'季: '. $r['minor_code_cname']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, number_format($r['new_people']), 2);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($r['people']), 2);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($r['weekly']), 2);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($r['avg_weekly']), 2);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($r['hours']), 2);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($r['total_hours']), 2);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($r['turnover']), 2);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($r['classes']), 2);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $r['info']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $r['note']);

                        $major_sum[ $r['major_code'] ][ 'new_people' ] += intval( $r['new_people'] );
                        $major_sum[ $r['major_code'] ][ 'people' ] += intval( $r['people'] );
                        $major_sum[ $r['major_code'] ][ 'weekly' ] += floatval( $r['weekly'] );
                        $major_sum[ $r['major_code'] ][ 'avg_weekly' ] += floatval( $r['avg_weekly'] );
                        $major_sum[ $r['major_code'] ][ 'hours' ] += floatval( $r['hours'] );
                        $major_sum[ $r['major_code'] ][ 'total_hours' ] += floatval( $r['total_hours'] );
                        $major_sum[ $r['major_code'] ][ 'turnover' ] += intval( $r['turnover'] );
                        $major_sum[ $r['major_code'] ][ 'classes' ] += intval( $r['classes'] );

                        $major_cache = $r['major_code'];
                    }
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ $major_cache ]);

                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, number_format($major_sum[ $major_cache ]['new_people'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($major_sum[ $major_cache ]['people'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($major_sum[ $major_cache ]['weekly'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($major_sum[ $major_cache ]['avg_weekly'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($major_sum[ $major_cache ]['hours'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($major_sum[ $major_cache ]['total_hours'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($major_sum[ $major_cache ]['turnover'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($major_sum[ $major_cache ]['classes'], 2));
                            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);


                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ 'S' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, number_format($major_sum[ 'A' ]['new_people'], 2) + number_format($major_sum[ 'B' ][ 'new_people' ], 2) + number_format($major_sum[ 'C' ][ 'new_people' ], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($major_sum[ 'A' ]['people'], 2) + number_format($major_sum[ 'B' ][ 'people' ], 2) + number_format($major_sum[ 'C' ][ 'people' ], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($major_sum[ 'A' ]['weekly'], 2) + number_format($major_sum[ 'B' ][ 'weekly' ], 2) + number_format($major_sum[ 'C' ][ 'weekly' ], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($major_sum[ 'A' ]['avg_weekly'], 2) + number_format($major_sum[ 'B' ][ 'avg_weekly' ], 2) + number_format($major_sum[ 'C' ][ 'avg_weekly' ], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($major_sum[ 'A' ]['hours'], 2) + number_format($major_sum[ 'B' ][ 'hours' ], 2) + number_format($major_sum[ 'C' ][ 'hours' ], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($major_sum[ 'A' ]['total_hours'], 2) + number_format($major_sum[ 'B' ][ 'total_hours' ], 2) + number_format($major_sum[ 'C' ][ 'total_hours' ], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($major_sum[ 'A' ]['turnover'], 2) + number_format($major_sum[ 'B' ][ 'turnover' ], 2) + number_format($major_sum[ 'C' ][ 'turnover' ], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($major_sum[ 'A' ]['classes'], 2) + number_format($major_sum[ 'B' ][ 'classes' ], 2) + number_format($major_sum[ 'C' ][ 'classes' ], 2));

                }

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
                header('Cache-Control: max-age=0');
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('php://output');
                exit;
                break;
            case 'detail':
                error_reporting(E_ALL);
                ini_set('display_errors', TRUE);
                ini_set('display_startup_errors', TRUE);
                //date_default_timezone_set('Asia/Taipei');
                define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
                $objPHPExcel = new PHPExcel();
                $sharedStyle = new PHPExcel_Style(); 
                $sharedStyle->applyFromArray(
                    array(
                        'borders' => array(
                            'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                        )
                    )
                );

                $filename = $era[0]['cname'] . $agency[0]['institution_code'] . $agency[0]['academic_institution_cname'] . '課程明細詳表(含國別)';
                $cnt = 0;
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $filename );

                $res = (new AjaxModel)->dbQuery('agent_academic_agency_report_era_detail', array('agency_id'=>$agency_id, 'era_id'=>$era_id, 'quarter'=>$quarter));
                $size = sizeof($res);
                if ($size) {
                    $knt = 1;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $filename);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '研習類別');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '國別(地區)');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '男新生人數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, '女新生人數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '總人數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, '人次');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, '總人次');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, '每期上課時數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, '每週平均上課時數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, '總人時數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, '營收額度');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, '小註(課程名稱)');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('M' . $knt, '備註');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('N' . $knt, '最後修改時間');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);

                    $count = 0;
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_head[ $major_cache ]);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                    foreach($res as $r) {
                        $count++;
                        $countries = sizeof($r['country']);
                        if ($major_cache != $r['major_code']) {
                            $knt++;
                            $kountry = (new AjaxModel)->dbQuery('agent_academic_agency_report_countries', array('agency_id'=>$agency_id, 'era_id'=>$era_id, 'quarter'=>$quarter));
                            $major_sum[ $r['major_code'] ][ 'countries' ] = intval($kountry[0]['countries']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ $major_cache ]);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, number_format($major_sum[ $major_cache ]['countries'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($major_sum[ $major_cache ]['new_male'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($major_sum[ $major_cache ]['new_female'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($major_sum[ $major_cache ]['new_people'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($major_sum[ $major_cache ]['people'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($major_sum[ $major_cache ]['people'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($major_sum[ $major_cache ]['hours'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($major_sum[ $major_cache ]['weekly'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, number_format($major_sum[ $major_cache ]['total_hours'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, number_format($major_sum[ $major_cache ]['turnover'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                            $knt++;
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_head[ $r['major_code'] ]);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                        }
                        $kount = 0;
                        foreach($r['country'] as $country) {
                            $kount++;
                            $knt++;
                            if ($countries == $kount) {
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '第'. $r['quarter'] . '季: ' . $r['minor_code_cname']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($r['new_people'], 2));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($r['people'], 2));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($r['hours'], 2));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($r['weekly'], 2));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, number_format($r['total_hours'], 2));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, number_format($r['turnover'], 2));
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, $r['info']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('M' . $knt, $r['note']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('N' . $knt, $r['latest']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                            } else {
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, "");
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, "");
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, "");
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, "");
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, "");
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, "");
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, "");
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('L' . $knt, "");
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('M' . $knt, "");
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('N' . $knt, "");
                            }
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $country['country_code_cname']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($country['new_male'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($country['new_female'], 2));
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($country['people'], 2));
                            $major_sum[ $r['major_code'] ][ 'new_male' ] += intval( $country['new_male'] );
                            $major_sum[ $r['major_code'] ][ 'new_female' ] += intval( $country['new_female'] );
                        } 

                        $major_sum[ $r['major_code'] ][ 'new_people' ] += intval( $r['new_people'] );
                        $major_sum[ $r['major_code'] ][ 'people' ] += intval( $r['people'] );
                        $major_sum[ $r['major_code'] ][ 'hours' ] += floatval( $r['hours'] );
                        $major_sum[ $r['major_code'] ][ 'weekly' ] += floatval( $r['weekly'] );
                        $major_sum[ $r['major_code'] ][ 'total_hours' ] += floatval( $r['total_hours'] );
                        $major_sum[ $r['major_code'] ][ 'turnover' ] += intval( $r['turnover'] );

                        $major_cache = $r['major_code'];
                    }

                    $knt++;
                    $kountry = (new AjaxModel)->dbQuery('agent_academic_agency_report_countries', array('agency_id'=>$agency_id, 'era_id'=>$era_id, 'quarter'=>$quarter));
                    $major_sum[ $r['major_code'] ][ 'countries' ] = intval($kountry[0]['countries']);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ $major_cache ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, number_format($major_sum[ $major_cache ]['countries'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($major_sum[ $major_cache ]['new_male'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($major_sum[ $major_cache ]['new_female'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($major_sum[ $major_cache ]['new_people'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($major_sum[ $major_cache ]['people'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($major_sum[ $major_cache ]['people'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($major_sum[ $major_cache ]['hours'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($major_sum[ $major_cache ]['weekly'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, number_format($major_sum[ $major_cache ]['total_hours'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, number_format($major_sum[ $major_cache ]['turnover'], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);

                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ 'S' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, number_format($major_sum[ 'A' ]['countries'], 2) + number_format($major_sum[ 'B' ][ 'countries' ], 2) + number_format($major_sum[ 'C' ][ 'countries' ], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, number_format($major_sum[ 'A' ]['new_male'], 2) + number_format($major_sum[ 'B' ][ 'new_male' ], 2) + number_format($major_sum[ 'C' ][ 'new_male' ], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, number_format($major_sum[ 'A' ]['new_female'], 2) + number_format($major_sum[ 'B' ][ 'new_female' ], 2) + number_format($major_sum[ 'C' ][ 'new_female' ], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, number_format($major_sum[ 'A' ]['new_people'], 2) + number_format($major_sum[ 'B' ][ 'new_people' ], 2) + number_format($major_sum[ 'C' ][ 'new_people' ], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, number_format($major_sum[ 'A' ]['people'], 2) + number_format($major_sum[ 'B' ][ 'people' ], 2) + number_format($major_sum[ 'C' ][ 'people' ], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, number_format($major_sum[ 'A' ]['people'], 2) + number_format($major_sum[ 'B' ][ 'people' ], 2) + number_format($major_sum[ 'C' ][ 'people' ], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, number_format($major_sum[ 'A' ]['hours'], 2) + number_format($major_sum[ 'B' ][ 'hours' ], 2) + number_format($major_sum[ 'C' ][ 'hours' ], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, number_format($major_sum[ 'A' ]['weekly'], 2) + number_format($major_sum[ 'B' ][ 'weekly' ], 2) + number_format($major_sum[ 'C' ][ 'weekly' ], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, number_format($major_sum[ 'A' ]['total_hours'], 2) + number_format($major_sum[ 'B' ][ 'total_hours' ], 2) + number_format($major_sum[ 'C' ][ 'total_hours' ], 2));
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, number_format($major_sum[ 'A' ]['turnover'], 2) + number_format($major_sum[ 'B' ][ 'turnover' ], 2) + number_format($major_sum[ 'C' ][ 'turnover' ], 2));
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                }
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
                header('Cache-Control: max-age=0');
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save('php://output');
                exit;

                break;
            case 'pdf':
                $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                // set document information
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('華語文機構績效報表');
                $pdf->SetTitle('績效報表');
                $pdf->SetSubject('績效報表');
                $pdf->SetKeywords('績效報表');
                // set default header data
                //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
                $pdf->SetHeaderData('', 0, $era[0]['cname'] . ' 績效報表', $agency[0]['academic_institution_cname'] . ' ' . $agency[0]['cname'], '', array(0,64,255), array(0,64,128));
                $pdf->setFooterData(array(0,64,0), array(0,64,128));
                // set header and footer fonts
                $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
                // set default monospaced font
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
                // set margins
                $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
                // set auto page breaks
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
                // set image scale factor
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                // set some language-dependent strings (optional)
                if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
                    require_once(dirname(__FILE__).'/lang/eng.php');
                    $pdf->setLanguageArray($l);
                }
                // ---------------------------------------------------------
                // set default font subsetting mode
                $pdf->setFontSubsetting(true);
                // Set font
                // dejavusans is a UTF-8 Unicode font, if you only need to
                // print standard ASCII chars, you can use core fonts like
                // helvetica or times to reduce file size.
                //$pdf->SetFont('dejavusans', '', 14, '', true);
                $pdf->SetFont('msungstdlight', '', 12);
                // Add a page
                // This method has several options, check the source code documentation for more information.
                $pdf->AddPage();
                // set text shadow effect
                $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
                // Set some content to print
                //$res = (new AjaxModel)->dbQuery('agent_academic_agency_report_era_summary', array('agency_id'=>$agency_id, 'era_id'=>$era_id, 'quarter'=>$quarter));
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_report_era_pdf', array('agency_id'=>$agency_id, 'era_id'=>$era_id, 'quarter'=>$quarter));

                $size = sizeof($res);
                if ($size) {
                    $html  = '<table border="1" align="center" cellpadding="2">';
                    $html .=   '<thead>';
                    $html .=     '<tr>';
                    $html .=       '<th>研習類別</th>';
                    $html .=       '<th>總人數</th>';
                    $html .=       '<th>總人次</th>';
                    $html .=       '<th>每週平均上課時數(每班平均)</th>';
                    $html .=       '<th>每期上課時數</th>';
                    $html .=       '<th>總人時數</th>';
                    $html .=       '<th>營收額度</th>';
                    $html .=       '<th>已組合班數</th>';
                    $html .=       '<th>備註</th>';
                    $html .=     '</tr>';
                    $html .=   '</thead>';
                    $html .=   '<tbody>';
                    
                    $html_a = '<tr><th colspan="9">'. $major_head[ 'A' ] .'</th></tr>'; 
                    $html_b = '<tr><th colspan="9">'. $major_head[ 'B' ] .'</th></tr>'; 
                    $html_c = '<tr><th colspan="9">'. $major_head[ 'C' ] .'</th></tr>'; 
                    
                    foreach($res as $r) {
                        switch( $r['major_code'] )
                        {
                        case 'A':
                            $html_a .= '<tr>';
                            $html_a .=  '<td class="">' . $r['minor_code_cname'] . '</td>';
                            $html_a .=  '<td class="">' . $r['new_people'] . '</td>';
                            $html_a .=  '<td class="">' . $r['people'] . '</td>';
                            $html_a .=  '<td class="">' . $r['avg_weekly'] . '</td>';
                            $html_a .=  '<td class="">' . $r['hours'] . '</td>';
                            $html_a .=  '<td class="">' . $r['total_hours'] . '</td>';
                            $html_a .=  '<td class="">' . $r['turnover'] . '</td>';
                            $html_a .=  '<td class="">' . $r['classes'] . '</td>';
                            $html_a .=  '<td class=""></td>';
                            $html_a .= '</tr>';
                            break;
                        case 'B':
                            $html_b .= '<tr>';
                            $html_b .=  '<td class="">' . $r['minor_code_cname'] . '</td>';
                            $html_b .=  '<td class="">' . $r['new_people'] . '</td>';
                            $html_b .=  '<td class="">' . $r['people'] . '</td>';
                            $html_b .=  '<td class="">' . $r['avg_weekly'] . '</td>';
                            $html_b .=  '<td class="">' . $r['hours'] . '</td>';
                            $html_b .=  '<td class="">' . $r['total_hours'] . '</td>';
                            $html_b .=  '<td class="">' . $r['turnover'] . '</td>';
                            $html_b .=  '<td class="">' . $r['classes'] . '</td>';
                            $html_b .=  '<td class=""></td>';
                            $html_b .= '</tr>';
                            break;
                        case 'C':
                            $html_c .= '<tr>';
                            $html_c .=  '<td class="">' . $r['minor_code_cname'] . '</td>';
                            $html_c .=  '<td class="">' . $r['new_people'] . '</td>';
                            $html_c .=  '<td class="">' . $r['people'] . '</td>';
                            $html_c .=  '<td class="">' . $r['avg_weekly'] . '</td>';
                            $html_c .=  '<td class="">' . $r['hours'] . '</td>';
                            $html_c .=  '<td class="">' . $r['total_hours'] . '</td>';
                            $html_c .=  '<td class="">' . $r['turnover'] . '</td>';
                            $html_c .=  '<td class="">' . $r['classes'] . '</td>';
                            $html_c .=  '<td class=""></td>';
                            $html_c .= '</tr>';
                            break;
                        }

                        $major_sum[ $r['major_code'] ][ 'new_people' ] += intval( $r['new_people'] );
                        $major_sum[ $r['major_code'] ][ 'people' ] += intval( $r['people'] );
                        $major_sum[ $r['major_code'] ][ 'avg_weekly' ] += floatval( $r['avg_weekly'] );
                        $major_sum[ $r['major_code'] ][ 'hours' ] += floatval( $r['hours'] );
                        $major_sum[ $r['major_code'] ][ 'total_hours' ] += floatval( $r['total_hours'] );
                        $major_sum[ $r['major_code'] ][ 'turnover' ] += intval( $r['turnover'] );
                        $major_sum[ $r['major_code'] ][ 'classes' ] += intval( $r['classes'] );
                    }

                    $html_a .= '<tr>';
                    $html_a .=   '<th>'. $major_foot[ 'A' ] .'</th>';
                    $html_a .=   '<th>'. $major_sum[ 'A' ][ 'new_people' ] .'</th>';
                    $html_a .=   '<th>'. $major_sum[ 'A' ][ 'people' ] .'</th>';
                    $html_a .=   '<th>'. $major_sum[ 'A' ][ 'avg_weekly' ] .'</th>';
                    $html_a .=   '<th>'. $major_sum[ 'A' ][ 'hours' ] .'</th>';
                    $html_a .=   '<th>'. $major_sum[ 'A' ][ 'total_hours' ] .'</th>';
                    $html_a .=   '<th>'. $major_sum[ 'A' ][ 'turnover' ] .'</th>';
                    $html_a .=   '<th>'. $major_sum[ 'A' ][ 'classes' ] .'</th>';
                    $html_a .=   '<th></th>';
                    $html_a .= '</tr>';

                    $html_b .= '<tr>';
                    $html_b .=   '<th>'. $major_foot[ 'B' ] .'</th>';
                    $html_b .=   '<th>'. $major_sum[ 'B' ][ 'new_people' ] .'</th>';
                    $html_b .=   '<th>'. $major_sum[ 'B' ][ 'people' ] .'</th>';
                    $html_b .=   '<th>'. $major_sum[ 'B' ][ 'avg_weekly' ] .'</th>';
                    $html_b .=   '<th>'. $major_sum[ 'B' ][ 'hours' ] .'</th>';
                    $html_b .=   '<th>'. $major_sum[ 'B' ][ 'total_hours' ] .'</th>';
                    $html_b .=   '<th>'. $major_sum[ 'B' ][ 'turnover' ] .'</th>';
                    $html_b .=   '<th>'. $major_sum[ 'B' ][ 'classes' ] .'</th>';
                    $html_b .=   '<th></th>';
                    $html_b .= '</tr>';

                    $html_c .= '<tr>';
                    $html_c .=   '<th>'. $major_foot[ 'C' ] .'</th>';
                    $html_c .=   '<th>'. $major_sum[ 'C' ][ 'new_people' ] .'</th>';
                    $html_c .=   '<th>'. $major_sum[ 'C' ][ 'people' ] .'</th>';
                    $html_c .=   '<th>'. $major_sum[ 'C' ][ 'avg_weekly' ] .'</th>';
                    $html_c .=   '<th>'. $major_sum[ 'C' ][ 'hours' ] .'</th>';
                    $html_c .=   '<th>'. $major_sum[ 'C' ][ 'total_hours' ] .'</th>';
                    $html_c .=   '<th>'. $major_sum[ 'C' ][ 'turnover' ] .'</th>';
                    $html_c .=   '<th>'. $major_sum[ 'C' ][ 'classes' ] .'</th>';
                    $html_c .=   '<th></th>';
                    $html_c .= '</tr>';

                    $html .= $html_a . $html_b . $html_c;

                    $html .= '<tr>';
                    $html .=   '<th>合計</th>';
                    $html .=   '<th>'. ( $major_sum[ 'A' ][ 'new_people' ] + $major_sum[ 'B' ][ 'new_people' ] + $major_sum[ 'C' ][ 'new_people' ] ) .'</th>';
                    $html .=   '<th>'. ( $major_sum[ 'A' ][ 'people' ] + $major_sum[ 'B' ][ 'people' ] + $major_sum[ 'C' ][ 'people' ] ) .'</th>';
                    $html .=   '<th>'. ( $major_sum[ 'A' ][ 'avg_weekly' ] + $major_sum[ 'B' ][ 'avg_weekly' ] + $major_sum[ 'C' ][ 'avg_weekly' ] ) .'</th>';
                    $html .=   '<th>'. ( $major_sum[ 'A' ][ 'hours' ] + $major_sum[ 'B' ][ 'hours' ] + $major_sum[ 'C' ][ 'hours' ] ) .'</th>';
                    $html .=   '<th>'. ( $major_sum[ 'A' ][ 'total_hours' ] + $major_sum[ 'B' ][ 'total_hours' ] + $major_sum[ 'C' ][ 'total_hours' ] ) .'</th>';
                    $html .=   '<th>'. ( $major_sum[ 'A' ][ 'turnover' ] + $major_sum[ 'B' ][ 'turnover' ] + $major_sum[ 'C' ][ 'turnover' ] ) .'</th>';
                    $html .=   '<th>'. ( $major_sum[ 'A' ][ 'classes' ] + $major_sum[ 'B' ][ 'classes' ] + $major_sum[ 'C' ][ 'classes' ] ) .'</th>';
                    $html .=   '<th></th>';
                    $html .= '</tr>';


                    $html .=   '</tbody>';
                    $html .= '</table>';
                    $html .= '<div height="100"></div>';
                    $html .= '<table>';
                    $html .=   '<thead>';
                    $html .=     '<tr width="200"><th>註冊並繳費人數:</th><th></th></tr>';
                    $html .=     '<tr width="200"><th>免費人數:</th><th></th></tr>';
                    $html .=     '<tr width="200"><th>填報人簽章:</th><th>申請單位主管簽章:</th></tr>';
                    $html .=   '</thead>';
                    $html .= '</table>';
                }

                // Print text using writeHTMLCell()
                //$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
                $pdf->writeHTML($html, true, false, false, false, '');
                // ---------------------------------------------------------
                // Close and output PDF document
                // This method has several options, check the source code documentation for more information.
                $pdf->SetHeaderData('', 0, $era[0]['cname'] . ' 績效報表', $agency[0]['academic_institution_cname'] . ' ' . $agency[0]['cname'], '', array(0,64,255), array(0,64,128));
                //$pdf->Output( $era[0]['cname'] . '績效報表-' . $agency[0]['academic_institution_cname'] . '-' . $agency[0]['aka'] . '.pdf', 'D');
                $pdf->Output( $era[0]['cname'] . '-' . $agency[0]['institution_code'] . '-' . trim($agency[0]['aka'], " ") . '.pdf', 'D');
                break;
            }
            break;
        }
    }

    public function mailer($key, $username, $email, $msg) {
        if (!isset($_SESSION)) { exit; }
        $official = (new AjaxModel)->dbQuery('mailer_official_get')[0];

        if ($official) {
            switch($key)
            {
            case 'add':
                $subject = $official['subject_agent_add'];
                $message = str_ireplace("@@@url@@@", $msg, str_ireplace("@@@username@@@", $username, $official['message_agent_add']));
                break;
            case 'mod':
                $subject = $official['subject_agent_mod'];
                $message = str_ireplace("@@@url@@@", $msg, str_ireplace("@@@username@@@", $username, $official['message_agent_mod']));
                break;
            case 'message':
                $subject = str_ireplace("@@@academic_institution@@@", $username, $official['subject_agent_board']);
                $message = str_ireplace("@@@academic_institution@@@", $username, $official['message_agent_board']);
                $email = $official['email_from'];
                break;
            case 'unlock':
                $subject = str_ireplace("@@@academic_institution@@@", $username, str_ireplace("@@@academic_era@@@", $email, str_ireplace("@@@academic_quarter@@@", $msg, $official['subject_agent_unlock'])));
                $message = str_ireplace("@@@academic_institution@@@", $username, str_ireplace("@@@academic_era@@@", $email, str_ireplace("@@@academic_quarter@@@", $msg, $official['message_agent_unlock'])));
                $email = $official['email_from'];
                break;
            case 'unlocker':
                $subject = '資料修改申請';
                $message = '資料修改申請';
                switch( $msg )
                {
                case 'yes':
                    $subject .= ' 同意'; 
                    $message .= ' 同意'; 
                    break;
                case 'no':
                    $subject .= ' 不同意';
                    $message .= ' 不同意';
                    break;
                }
                $email = $mailer[0]['email'];
                $cc = '';
                if ((isset($mailer[1])) && (!empty($mailer[1]['email']))) {
                    $cc = 'Cc: '. $mailer[1]['email'] . "\r\n";
                }
                break;
            }

            $headers = "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= 'From: 華語文教育機構績效系統 <' . $official['email_from'] . "> \r\n".
                       $cc .
                       'Reply-To: '. $official['email_reply'] . "\r\n".
                       'X-Mailer: PHP/' . phpversion();
        } else {
            $subject = '華語文教育機構績效系統通知信';
            $message = '您好，您在華語文教育機構招生填報系統的使用者帳號為['. $username .']，請透過以下連結網址設定登入密碼：['. $msg .']';
            $from = 'enjouli82029@tea.ntue.edu.tw';

            $headers = "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= 'From: 李恩柔<' . $from . "> \r\n".
            'Reply-To: ' . $from . " \r\n".
            'X-Mailer: PHP/'. phpversion();
        }
        mail( $email, $subject, $message, $headers );
    }

    public function downloader( $key, $val, $param="" ) {
        if (!isset($_SESSION)) { exit; }
        //ignore_user_abort(true);
        //set_time_limit(0); // disable the time limit for this script
         
        $path = "/var/www/html/ntue/public/template/"; // change the path to fit your websites document structure
         
        switch( $key ) 
        {
        case 'pdf':
            switch( $val )
            {
            case 'admin_manual':
                $dl_file = $val . '.pdf';
                $full_path = $path . $dl_file;
                if (file_exists($full_path)) {
                    $fs = filesize($full_path);
                    $path_info = pathinfo($full_path);
                    if ($fd = fopen($full_path, "r")) {
                        header("Content-type: application/pdf");
                        header("Content-Disposition: attachment; filename=\"".$path_info["basename"]."\""); // use 'attachment' to force a file download
                        header("Content-length: $fs");
                        header("Cache-control: private"); //use this to open files directly

                        while(!feof($fd)) {
                            $bf = fread($fd, 2048);
                            echo $bf;
                        }

                    }
                }
                fclose($fd);
                break;
            case 'user_manual':
                $dl_file = $val . '.pdf';
                $full_path = $path . $dl_file;
                if (file_exists($full_path)) {
                    $fs = filesize($full_path);
                    $path_info = pathinfo($full_path);
                    if ($fd = fopen($full_path, "r")) {
                        header("Content-type: application/pdf");
                        header("Content-Disposition: attachment; filename=\"".$path_info["basename"]."\""); // use 'attachment' to force a file download
                        header("Content-length: $fs");
                        header("Cache-control: private"); //use this to open files directly

                        while(!feof($fd)) {
                            $bf = fread($fd, 2048);
                            echo $bf;
                        }

                    }
                }
                fclose($fd);
                break;
            case 'qa':
                $dl_file = $val . '.pdf';
                $full_path = $path . $dl_file;
                if (file_exists($full_path)) {
                    $fs = filesize($full_path);
                    $path_info = pathinfo($full_path);
                    if ($fd = fopen($full_path, "r")) {
                        header("Content-type: application/pdf");
                        header("Content-Disposition: attachment; filename=\"".$path_info["basename"]."\""); // use 'attachment' to force a file download
                        header("Content-length: $fs");
                        header("Cache-control: private"); //use this to open files directly

                        while(!feof($fd)) {
                            $bf = fread($fd, 2048);
                            echo $bf;
                        }

                    }
                }
                fclose($fd);
                break;
            }
            break;
        case 'y105':

            if (preg_match("/^(\w){2,7}$/", $param)) {
                $path .= preg_replace("/y/", "", $key) . '_' . $val . '/';
                $aka = (new AjaxModel)->dbQuery('agent_academic_institution_aka', array('code'=>$param));
                if (sizeof($aka)) {
                    $dl_file = $param .'-'. $aka[0]['aka'] . '.xls';
                    $full_path = $path . $dl_file;
                    if (file_exists($full_path)) {

                        if ($fd = fopen($full_path, "r")) {
                            $fs = filesize($full_path);
                            $path_info = pathinfo($full_path);
                            header("Content-type: application/octet-stream");
                            header("Content-Disposition: filename=\"".$path_info["basename"]."\"");
                            header("Content-length: $fs");
                            header("Cache-control: private"); //use this to open files directly
                            while(!feof($fd)) {
                                $bf = fread($fd, 2048);
                                echo $bf;
                            }
                        }
                        fclose($fd);

                    }
                }
            }
            break;
        }
        exit;
    }

}
