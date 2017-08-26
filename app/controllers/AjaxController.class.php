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
            case 'chk':
                $res =  (new AjaxModel)->dbQuery('admin_check_new_user_add',array('username'=>$_POST['username']));
                $json = array('code'=>1,'data'=>$res);
                break;
            } 
            break;
        case 'academic_agency_status':
            switch( $val ) 
            {
            case 0: 
                $res = (new AjaxModel)->dbQuery('academic_agency_status_all', array('agency_id'=>$val));
                break;
            default:
                $res = (new AjaxModel)->dbQuery('academic_agency_status', array('agency_id'=>$val));
            }
            $json = array("code"=>1, "data"=>$res);
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
        case 'message':
            switch($val)
            {
            case 'noReplyMsgQry':
                if (isset($_SESSION['admin'])) {
                    $res = (new AjaxModel)->dbQuery('admin_board_unreply_query');
                    $json = array("code"=>1, "data"=>$res);
                    //$json = array("code"=>1, "data"=>"GOOD");
                }
    
                break;
            case 'replyMsgSave':
                if (isset($_SESSION['admin'])) {
                    $res = (new AjaxModel)->dbQuery('admin_board_save_reply',array('message_id'=>$_POST['msgid'],'admin_id'=>$_SESSION['admin']['id'],'reply_content'=>$_POST['replyContent']));
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
 
                     $receverList = (new AjaxModel)->dbQuery('admin_postman_receverlist',array('rcpttotype'=>$_POST['emailRcptTo']));
                     $receverCnt = 0;
                     foreach($receverList as $recever){
                         $receverCnt++;
                         $email = $recever['email'];
                         $emailName = $recever['cname'];
                         /* $email = 'thucop@gmail.com'; */
                         $subject = $_POST['emailSubject'];
                         $message = $_POST['emailBody']."\n";
                         $from = 'wenyu0421@tea.ntue.edu.tw';
                         $headers = 'From: 許文諭<' . $from . "> \r\n".
                         'Reply-To: ' . $from . " \r\n".
                         'X-Mailer: PHP/'. phpversion();
                         mail( $email, $subject, $message, $headers );
                     }
                     $json = array("code"=>1, "data"=>$receverCnt);
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
                $country = (isset($_POST['country']))? $_POST['country'] : array();
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_class_add', array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'], 'major_code'=>$_POST['major_code'], 'minor_code'=>$_POST['minor_code'], 'cname'=>$_POST['cname'], 'weekly'=>$_POST['weekly'], 'weeks'=>$_POST['weeks'], 'adjust'=>$_POST['adjust'], 'content_code'=>$_POST['content_code'], 'target_code'=>$_POST['target_code'], 'new_people'=>$_POST['new_people'], 'people'=>$_POST['people'], 'hours'=>$_POST['hours'], 'total_hours'=>$_POST['total_hours'], 'revenue'=>$_POST['revenue'], 'subsidy'=>$_POST['subsidy'], 'turnover'=>$_POST['turnover'], 'note'=>$_POST['note'], 'country'=>$country));
                $json = array("code"=>1, "data"=>$res, 'hours'=>$_POST['hours'], 'total_hours'=>$_POST['total_hours']);
                break;
            case 'del':
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_class_del', array('id'=>$_POST['id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'], 'agency_id'=>$_POST['agency_id']));
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'get':
                break;
            case 'done':
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_class_done', array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'] ));
                $json = array("code"=>1, "data"=>$res);
                break;
            case 'mod':
                $country = (isset($_POST['country']))? $_POST['country'] : array();
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_class_mod', array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter'], 'class_id'=>$_POST['class_id'], 'minor_code'=>$_POST['minor_code'], 'cname'=>$_POST['cname'], 'weekly'=>$_POST['weekly'], 'weeks'=>$_POST['weeks'], 'adjust'=>$_POST['adjust'], 'content_code'=>$_POST['content_code'], 'target_code'=>$_POST['target_code'], 'new_people'=>$_POST['new_people'], 'people'=>$_POST['people'], 'hours'=>$_POST['hours'], 'total_hours'=>$_POST['total_hours'], 'revenue'=>$_POST['revenue'], 'subsidy'=>$_POST['subsidy'], 'turnover'=>$_POST['turnover'], 'note'=>$_POST['note'], 'country'=>$country));
                $json = array("code"=>1, "data"=>$res, 'hours'=>$_POST['hours'], 'total_hours'=>$_POST['total_hours']);
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
                $res = (new AjaxModel)->dbQuery('agent_cademic_agency_contact', array('agency_id'=>$_POST['agency_id']));
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
        case 'academic_agency_report':
            /* academic_agency_report_quarter */
            /* 0:1~4, 1:1, 2:2, 3:3, 4:4, 5:1~2, 6:2~3, 7:3~4, 8:1~3, 9:2~4 */
            $res = array();
            $res['summary'] = (new AjaxModel)->dbQuery('agent_academic_agency_report_summary', array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter']));
            $res['detail'] = (new AjaxModel)->dbQuery('agent_academic_agency_report_detail', array('agency_id'=>$_POST['agency_id'], 'era_id'=>$_POST['era_id'], 'quarter'=>$_POST['quarter']));
            $json = array("code"=>1, "data"=>$res);
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
        case 'message':
            switch( $val )
            {
            case 'histMsgQry':
                if (isset($_SESSION['agent'])) {
                    $res = (new AjaxModel)->dbQuery('agent_board_reply_query', array('agent_id'=>$_SESSION['agent']['id']));
                    $json = array("code"=>1, "data"=>$res);

                }
                break;
            case 'quesSave':
                if (isset($_SESSION['agent'])) {
                    $res = (new AjaxModel)->dbQuery('agent_board_question_add', array('agent_id'=>$_SESSION['agent']['id'],'question_content'=>$_POST['questionContent']));
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

    public function reporter($key, $val, $era_id, $quarter=1, $agency_id=0) {
        switch($key) 
        {
        case 'academic_admin_report':
            error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);
            date_default_timezone_set('Asia/Taipei');
            define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
            $objPHPExcel = new PHPExcel();
            $sharedStyle = new PHPExcel_Style(); 
            $targets = (new AjaxModel)->dbQuery('admin_academic_agency_report_targets');
            switch( $val )
            {
            case 'era_detail':
                $cnt = 0;
                foreach ($targets as $target) {
                    if ($cnt > 0) {
                        $objPHPExcel->createSheet();
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt);
                    $objPHPExcel->getActiveSheet()->setTitle( $target['institution_code'] . '-'. $target['cname'] );
                    $res = (new AjaxModel)->dbQuery('agent_academic_agency_report_detail', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'quarter'=>$quarter));
                    if (sizeof($res)) {
                        $knt = 0;
                        foreach($res as $r) {
                            $count = 0;
                            $size = sizeof($r['country']);
                            foreach($r['country'] as $country) {
                                $knt++;
                                $count++;
                                if ($size == $count) {
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $r['minor_code_cname']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $r['new_people']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $r['people']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $r['weekly']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $r['avg_weekly']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $r['total_hours']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $r['turnover']);
                                } else {
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, "");
                                }
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $country['country_code_cname']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $country['new_male']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $country['new_female']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $country['people']);
                            } 
                        }
                    }

                    $cnt++;
                }
                $filename = '年度機構詳表';
                break;
            case 'era_summary':

                $cnt = 0;
                foreach ($targets as $target) {
                    if ($cnt > 0) {
                        $objPHPExcel->createSheet();
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt);
                    $objPHPExcel->getActiveSheet()->setTitle( $target['institution_code'] . '-' .$target['cname'] );

                    $res = (new AjaxModel)->dbQuery('agent_academic_agency_report_summary', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'quarter'=>$quarter));
                    if (sizeof($res)) {
                        $knt = 0;
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

                        foreach($res as $r) {
                            $knt++;
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $r['minor_code_cname']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $r['new_people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $r['people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $r['weekly']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $r['avg_weekly']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $r['hours']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $r['total_hours']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $r['turnover']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $r['classes']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $r['info']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $r['note']);
                        }
                    }

                    $cnt++;
                }
                $filename = '年度機構簡表';

                break;
            case 'quarter_detail':

                $cnt = 0;
                foreach ($targets as $target) {
                    if ($cnt > 0) {
                        $objPHPExcel->createSheet();
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt);
                    $objPHPExcel->getActiveSheet()->setTitle( $target['institution_code'] . '-' .$target['cname'] );

                    $res = (new AjaxModel)->dbQuery('agent_academic_agency_report_detail', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'quarter'=>$quarter));
                    if (sizeof($res)) {
                        $knt = 0;
                        foreach($res as $r) {
                            $count = 0;
                            $size = sizeof($r['country']);
                            foreach($r['country'] as $country) {
                                $knt++;
                                $count++;
                                if ($size == $count) {
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $r['minor_code_cname']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $r['new_people']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $r['people']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $r['weekly']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $r['avg_weekly']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $r['total_hours']);
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $r['turnover']);
                                } else {
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, "");
                                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, "");
                                }
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $country['country_code_cname']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $country['new_male']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $country['new_female']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $country['people']);
                            } 
                        }
                    }

                    $cnt++;
                }
                $filename = '季度機構詳表';

                break;
            case 'quarter_summary':

                $cnt = 0;
                foreach ($targets as $target) {
                    if ($cnt > 0) {
                        $objPHPExcel->createSheet();
                    }
                    $objPHPExcel->setActiveSheetIndex($cnt);
                    $objPHPExcel->getActiveSheet()->setTitle( $target['institution_code'] . '-' .$target['cname'] );

                    $res = (new AjaxModel)->dbQuery('agent_academic_agency_report_summary', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'quarter'=>$quarter));
                    if (sizeof($res)) {
                        $knt = 0;
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

                        foreach($res as $r) {
                            $knt++;
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $r['minor_code_cname']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $r['new_people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $r['people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $r['weekly']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $r['avg_weekly']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $r['hours']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $r['total_hours']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $r['turnover']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $r['classes']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $r['info']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $r['note']);
                        }
                    }

                    $cnt++;
                }
                $filename = '季度機構簡表';

                break;
            case 'manager':
                $era = (new AjaxModel)->dbQuery('admin_academic_era', array('era_id'=>$era_id));
                $cnt = 0;
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '人時數營收總簡表' );

                $cnt = 1;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '各類研習總人數簡表' );

                $cnt = 2;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '各類研習總人次簡表' );

                $cnt = 3;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '各類研習總人時數簡表' );

                $cnt = 4;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '各類研習總營收簡表' );

                $cnt = 5;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '華語中心各類研習總人數詳表' );

                $cnt = 6;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '華語中心各類研習總人次詳表' );

                $cnt = 7;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '華語中心各類研習總時數詳表' );

                $cnt = 8;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '華語中心各類研習總營收詳表' );

                $cnt = 9;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $era[0]['cname'] . '大學附設華語中心人數(次)一覽表(交叉總表)' );

                $filename = '管理報表';
                break;
            case 'statistics':
                $cnt = 0;
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( '明細' );

                $knt = 0;
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, '語文中心代碼');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, '語文中心名稱');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, '國家代碼');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, '國家名稱');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, '小計');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, '男生');
                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, '女生');
                foreach ($targets as $target) {
                    $res = (new AjaxModel)->dbQuery('admin_academic_agency_report_statistics', array('agency_id'=>$target['id'], 'era_id'=>$era_id));
                    if (sizeof($res)) {
                        foreach($res as $r) {
                            $knt++;
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $target['institution_code']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $target['institution_cname']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $r['country_code']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $r['country_cname']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $r['new_people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $r['new_male']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $r['new_female']);
                        }
                    }
                }

                $cnt++;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex(1);
                $objPHPExcel->getActiveSheet()->setTitle( '聯絡人' );




                $cnt++;
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex(2);
                $objPHPExcel->getActiveSheet()->setTitle( '差異' );

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
                    
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A0', '學校代碼');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B0', '單位名稱');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C0', '總人數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D0', '總人次');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E0', '總時數');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F0', '總營收');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G0', '小註');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H0', '備註');

                    $new_people = 0;
                    $people = 0;
                    $hours = 0;
                    $turnover = 0;
                    $info = '';
                    $note = '';
                    
                    $knt = 2;
                    foreach ($targets as $target) {
                        $minor_b = (new AjaxModel)->dbQuery('admin_academic_agency_report_minor_b', array('agency_id'=>$target['id'], 'era_id'=>$era_id, 'minor_code'=>$b['minor_code']));
                        if (sizeof($minor_b)) {
                            $minor = $minor_b[0];
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $target['institution_code']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $target['institution_cname'] . $target['cname']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $minor['new_people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $minor['people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $minor['hours']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $minor['turnover']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $minor['info']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $minor['note']);
                            
                            $new_people += intval( $minor['new_people'] );
                            $people += intval( $minor['people'] );
                            $hours += floatval( $minor['hours'] );
                            $turnover += floatval( $minor['turnover'] );
                        }
                        $knt++;
                    }

                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A1', '合計');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B1', '');
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C1', $new_people);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D1', $people);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E1', $hours);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F1', $turnover);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G1', $info);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H1', $note);

                    $cnt++;
                }


                $filename = 'B類年度總表';
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
            $major_tag = array();
            $major_sum = array('S'=>array('new_people'=>0, 'people'=>0, 'avg_weekly'=>0, 'hours'=>0, 'total_hours'=>0, 'turnover'=>0, 'classes'=>0));
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
               
                $major_tag[ $major['code'] ] = true;
            }

            $sharedStyle = new PHPExcel_Style(); 
            $sharedStyle->applyFromArray(
                array(
                    'borders' => array(
                        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                    )
                )
            );
            switch($val)
            {
            case 'summary':
                error_reporting(E_ALL);
                ini_set('display_errors', TRUE);
                ini_set('display_startup_errors', TRUE);
                date_default_timezone_set('Asia/Taipei');
                define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
                $objPHPExcel = new PHPExcel();
                $cnt = 0;
                $filename = $era[0]['cname'] . $agency[0]['institution_code'] . $agency[0]['academic_institution_cname'] . '課程明細簡表(四大類)';
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $filename );

                $res = (new AjaxModel)->dbQuery('agent_academic_agency_report_summary', array('agency_id'=>$agency_id, 'era_id'=>$era_id, 'quarter'=>$quarter));
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
                        $knt++;
                        if ($major_cache != $r['major_code']) {
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ $major_cache ]);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $major_sum[ $r['major_code'] ]['new_people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $major_sum[ $r['major_code'] ]['people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $major_sum[ $r['major_code'] ]['weekly']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $major_sum[ $r['major_code'] ]['avg_weekly']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $major_sum[ $r['major_code'] ]['hours']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $major_sum[ $r['major_code'] ]['total_hours']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $major_sum[ $r['major_code'] ]['turnover']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $major_sum[ $r['major_code'] ]['classes']);
                            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);

                            $knt++;
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_head[ $r['major_code'] ]);
                            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);
                            $knt++;
                        }
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $r['minor_code_cname']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $r['new_people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $r['people']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $r['weekly']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $r['avg_weekly']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $r['hours']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $r['total_hours']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $r['turnover']);
                        $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $r['classes']);
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
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, "A". $knt .":K" . $knt);

                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ 'S' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $major_sum[ 'A' ]['new_people'] + $major_sum[ 'B' ][ 'new_people' ] + $major_sum[ 'C' ][ 'new_people' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $major_sum[ 'A' ]['people'] + $major_sum[ 'B' ][ 'people' ] + $major_sum[ 'C' ][ 'people' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $major_sum[ 'A' ]['weekly'] + $major_sum[ 'B' ][ 'weekly' ] + $major_sum[ 'C' ][ 'weekly' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $major_sum[ 'A' ]['avg_weekly'] + $major_sum[ 'B' ][ 'avg_weekly' ] + $major_sum[ 'C' ][ 'avg_weekly' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $major_sum[ 'A' ]['hours'] + $major_sum[ 'B' ][ 'hours' ] + $major_sum[ 'C' ][ 'hours' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $major_sum[ 'A' ]['total_hours'] + $major_sum[ 'B' ][ 'total_hours' ] + $major_sum[ 'C' ][ 'total_hours' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $major_sum[ 'A' ]['turnover'] + $major_sum[ 'B' ][ 'turnover' ] + $major_sum[ 'C' ][ 'turnover' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $major_sum[ 'A' ]['classes'] + $major_sum[ 'B' ][ 'classes' ] + $major_sum[ 'C' ][ 'classes' ]);

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
                date_default_timezone_set('Asia/Taipei');
                define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
                $objPHPExcel = new PHPExcel();

                $filename = $era[0]['cname'] . $agency[0]['institution_code'] . $agency[0]['academic_institution_cname'] . '課程明細詳表(含國別)';
                $cnt = 0;
                $objPHPExcel->setActiveSheetIndex($cnt);
                $objPHPExcel->getActiveSheet()->setTitle( $filename );

                $res = (new AjaxModel)->dbQuery('agent_academic_agency_report_detail', array('agency_id'=>$agency_id, 'era_id'=>$era_id, 'quarter'=>$quarter));
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
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $major_sum[ $r['major_code'] ]['countries']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $major_sum[ $r['major_code'] ]['new_male']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $major_sum[ $r['major_code'] ]['new_female']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $major_sum[ $r['major_code'] ]['new_people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $major_sum[ $r['major_code'] ]['people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $major_sum[ $r['major_code'] ]['people']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $major_sum[ $r['major_code'] ]['weekly']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $major_sum[ $r['major_code'] ]['avg_weekly']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $major_sum[ $r['major_code'] ]['total_hours']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $major_sum[ $r['major_code'] ]['turnover']);
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
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $r['new_people']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $r['people']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $r['weekly']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $r['avg_weekly']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $r['total_hours']);
                                $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $r['turnover']);
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
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $country['new_male']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $country['new_female']);
                            $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $country['people']);
                            $major_sum[ $r['major_code'] ][ 'new_male' ] += intval( $country['new_male'] );
                            $major_sum[ $r['major_code'] ][ 'new_female' ] += intval( $country['new_female'] );
                        } 

                        $major_sum[ $r['major_code'] ][ 'new_people' ] += intval( $r['new_people'] );
                        $major_sum[ $r['major_code'] ][ 'people' ] += intval( $r['people'] );
                        $major_sum[ $r['major_code'] ][ 'weekly' ] += floatval( $r['weekly'] );
                        $major_sum[ $r['major_code'] ][ 'avg_weekly' ] += floatval( $r['avg_weekly'] );
                        $major_sum[ $r['major_code'] ][ 'total_hours' ] += floatval( $r['total_hours'] );
                        $major_sum[ $r['major_code'] ][ 'turnover' ] += intval( $r['turnover'] );

                        $major_cache = $r['major_code'];
                    }
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_head[ $major_cache ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setSharedStyle($sharedStyle, "A". $knt .":N" . $knt);
                    $knt++;
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('A' . $knt, $major_foot[ 'S' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('B' . $knt, $major_sum[ 'A' ]['countries'] + $major_sum[ 'B' ][ 'countries' ] + $major_sum[ 'C' ][ 'countries' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('C' . $knt, $major_sum[ 'A' ]['new_male'] + $major_sum[ 'B' ][ 'new_male' ] + $major_sum[ 'C' ][ 'new_male' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('D' . $knt, $major_sum[ 'A' ]['new_female'] + $major_sum[ 'B' ][ 'new_female' ] + $major_sum[ 'C' ][ 'new_female' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('E' . $knt, $major_sum[ 'A' ]['new_people'] + $major_sum[ 'B' ][ 'new_people' ] + $major_sum[ 'C' ][ 'new_people' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('F' . $knt, $major_sum[ 'A' ]['people'] + $major_sum[ 'B' ][ 'people' ] + $major_sum[ 'C' ][ 'people' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('G' . $knt, $major_sum[ 'A' ]['people'] + $major_sum[ 'B' ][ 'people' ] + $major_sum[ 'C' ][ 'people' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('H' . $knt, $major_sum[ 'A' ]['weekly'] + $major_sum[ 'B' ][ 'weekly' ] + $major_sum[ 'C' ][ 'weekly' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('I' . $knt, $major_sum[ 'A' ]['avg_weekly'] + $major_sum[ 'B' ][ 'avg_weekly' ] + $major_sum[ 'C' ][ 'avg_weekly' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('J' . $knt, $major_sum[ 'A' ]['total_hours'] + $major_sum[ 'B' ][ 'total_hours' ] + $major_sum[ 'C' ][ 'total_hours' ]);
                    $objPHPExcel->setActiveSheetIndex($cnt)->setCellValue('K' . $knt, $major_sum[ 'A' ]['turnover'] + $major_sum[ 'B' ][ 'turnover' ] + $major_sum[ 'C' ][ 'turnover' ]);
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
                $res = (new AjaxModel)->dbQuery('agent_academic_agency_report_summary', array('agency_id'=>$agency_id, 'era_id'=>$era_id, 'quarter'=>$quarter));

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

    public function mailer($key, $username, $email, $url) {
        $official = (new AjaxModel)->dbQuery('mailer_official_get')[0];

        if ($official) {
            switch($key)
            {
            case 'add':
                $subject = $official['subject_agent_add'];
                $message = str_ireplace("@@@url@@@", $url, str_ireplace("@@@username@@@", $username, $official['message_agent_add']));
                break;
            case 'mod':
                $subject = $official['subject_agent_mod'];
                $message = str_ireplace("@@@url@@@", $url, str_ireplace("@@@username@@@", $username, $official['message_agent_mod']));
                break;
            }
            $headers = 'From: '. $official['cname'] . '<' . $official['email_from'] . "> \r\n".
                       'Reply-To: '. $official['email_reply'] . "\r\n".
                       'X-Mailer: PHP/' . phpversion();
        } else {
            $subject = '華語文教育機構績效系統通知信';
            $message = '您好，您在華語文教育機構招生填報系統的使用者帳號為['. $username .']，請透過以下連結網址設定登入密碼：['. $url .']';
            $from = 'wenyu0421@tea.ntue.edu.tw';

            $headers = 'From: 許文諭<' . $from . "> \r\n".
            'Reply-To: ' . $from . " \r\n".
            'X-Mailer: PHP/'. phpversion();
        }
        mail( $email, $subject, $message, $headers );
    }

    public function downloader( $key, $val, $param="" ) {
        ignore_user_abort(true);
        set_time_limit(0); // disable the time limit for this script
         
        $path = "/var/www/html/ntue/public/template/"; // change the path to fit your websites document structure
         
        switch( $key ) 
        {
        case 'pdf':
            switch( $val )
            {
            case 'user_manual':
                $dl_file = $val . '.pdf';
                $full_path = $path . $dl_file;
//echo $full_path;
                if (file_exists($full_path)) {
                    $fs = filesize($fullPath);
                    $path_info = pathinfo($fullPath);
                    header("Content-type: application/pdf");
                    header("Content-Disposition: attachment; filename=\"".$path_info["basename"]."\""); // use 'attachment' to force a file download
                    header("Content-length: $fs");
                    header("Cache-control: private"); //use this to open files directly
                    while(!feof($fd)) {
                        $bf = fread($fd, 2048);
                        echo $bf;
                    }
                }
                fclose($fd);
                break;
            }
            break;
        case 'y105':
            if (preg_match("/^(\w){2,7}$/", $param)) {
                $path .= $key . '_' . $val;
                $aka = (new AjaxModel)->dbQuery('agent_academic_institution_aka', $param);
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

    public function uploader() {

    }

}
