<?php

class AjaxModel extends Model {
    public function dbQuery( $key, $data = array() ) {
        switch($key)
        {
        /* admin */
        case 'admin_academic_agency_add':
            $sql  = 'INSERT INTO `academic_agency`';
            $sql .= ' (`id`, `institution_code`, `cname`, `zipcode`, `address`, `established`, `approval`, `note`, `agent`, `state`)';
            $sql .= ' VALUES (0, :institution_code, :cname, "", "", "", "", "", 0, 0)';
            $id = $this->dbInsert($sql, array(':institution_code'=>$data['institution_code'], ':cname'=>$data['cname']));
            return $this->dbQuery('admin_academic_agency_get');
            break;
        case 'admin_academic_agency_del':
            $sql  = 'DELETE FROM `academic_agency_agent`';
            $sql .= ' WHERE `agency_id` = :agency_id';
            $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['id']));
            $sql  = 'DELETE FROM `academic_agency`';
            $sql .= ' WHERE `id` = :id';
            $cnt = $this->dbUpdate($sql, array(':id'=>$data['id']));
            return $this->dbQuery('admin_academic_agency_get');
            break;
        case 'admin_academic_agency_get':
            $sql  = 'SELECT t1.*, t2.`cname` AS `institution_cname`, t2.`aka` AS `institution_aka`, IFNULL(t3.`administration`, 0) AS `academic_agency_hr_administration`, IFNULL(t3.`subject`, 0) AS `academic_agency_hr_subject`, IFNULL(t3.`adjunct`, 0) AS `academic_agency_hr_adjunct`, IFNULL(t3.`reserve`, 0) AS `academic_agency_hr_reserve` ';
            $sql .= '  FROM `academic_agency` t1 ';
            $sql .= ' INNER JOIN `academic_institution` t2 ON t1.`institution_code` = t2.`code` ';
            $sql .= '  LEFT JOIN ( SELECT t4.`agency_id`, t4.`administration`, t4.`subject`, t4.`adjunct`, t4.`reserve` FROM `academic_agency_hr` t4 INNER JOIN `academic_era` t5 ON t4.`era_id` = t5.id AND t5.`state` = 1 ) t3 ON t1.`id` = t3.`agency_id` ';
            $sql .= ' WHERE "NTUE" = :ntue';
            return $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
            break;
        case 'admin_academic_agency_mod':
            $sql  = 'UPDATE `academic_agency`';
            $sql .= '   SET `cname` = :cname';
            $sql .= '      ,`institution_code` = :institution_code';
            $sql .= '      ,`state` = :state';
            $sql .= ' WHERE `id` = :id';
            $cnt = $this->dbUpdate($sql, array(':institution_code'=>$data['institution_code'], ':cname'=>$data['cname'], ':state'=>$data['agency_state'], ':id'=>$data['id']));
            return $this->dbQuery('admin_academic_agency_get');
            break;
        case 'admin_academic_agency_agent_add':
            $sql = 'SELECT * FROM `academic_agency_agent` WHERE username = :username';
            $res = $this->dbQuery($sql, array(':username'=>$data['username']));
            if (0 == sizeof($res)) {
                $sql = 'SELECT `agent` FROM `academic_agency` WHERE `id` = :id';
                $res = $this->dbQuery($sql, array(':id'=>$data['agency_id']));
                $agent = intval($res[0]['agent']);
                if ($agent < 2) {
                    $sql  = 'INSERT INTO `academic_agency_agent`';
                    $sql .= ' (`id`, `agency_id`, `username`, `userpass`, `email`, `session`, `timestamp`, `state`)';
                    $sql .= ' VALUES (0, :agency_id, :username, MD5(:userpass), :email, "", :timestamp, 0)';
                    $id = $this->dbInsert($sql, array(':agency_id'=>$data['agency_id'], ':username'=>$data['username'], ':userpass'=>$data['userpass'], ':email'=>$data['email'], ':timestamp'=>$data['timestamp']));
                    $sql = 'UPDATE `academic_agency` SET `agent` = `agent` + 1 WHERE `id` = :id';
                    $cnt = $this->dbUpdate($sql, array(':id'=>$data['agency_id']));
                    return array('code'=>1, 'res'=>$this->dbQuery('admin_academic_agency_agent_get'));
                } else {
                    return array('code'=>2);
                }
            } else {
                return array('code'=>0);
            }
            break;
        case 'admin_academic_agency_agent_del':
            $sql  = 'DELETE FROM `academic_agency_agent`';
            $sql .= ' WHERE `id` = :id';
            $cnt = $this->dbUpdate($sql, array(':id'=>$data['id']));
            $sql  = 'UPDATE `academic_agency`';
            $sql .= '   SET `agent` = `agent` -1';
            $sql .= ' WHERE `id` = :id';
            $cnt = $this->dbUpdate($sql, array(':id'=>$data['agency_id']));
            return $this->dbQuery('admin_academic_agency_agent_get');
            break;
        case 'admin_academic_agency_agent_get':
            $sql  = 'SELECT t1.*, t2.`cname` `academic_agency_cname`';
            $sql .= '  FROM `academic_agency_agent` t1';
            $sql .= ' INNER JOIN `academic_agency` t2 ON t1.`agency_id` = t2.`id`';
            $sql .= ' WHERE "NTUE" = :ntue';
            return $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
            break;
        case 'admin_academic_agency_agent_get_byid':
            $sql  = 'SELECT t1.*, t2.`cname` `academic_agency_cname`';
            $sql .= '  FROM `academic_agency_agent` t1';
            $sql .= ' INNER JOIN `academic_agency` t2 ON t1.`agency_id` = t2.`id`';
            $sql .= ' WHERE t1.`id` = :id';
            return $this->dbSelect($sql,array(':id'=>$data['id'])); 
            break;
        case 'admin_academic_agency_agent_mod':
            $sql  = 'UPDATE `academic_agency_agent`';
            $sql .= '   SET `email` = :email,';
            $sql .= '       `userpass` = MD5(:userpass),';
            $sql .= '       `session` = "",';
            $sql .= '       `timestamp` = :timestamp,';
            $sql .= '       `state` = 0';
            $sql .= ' WHERE `id` = :id';
            $cnt = $this->dbUpdate($sql, array(':userpass'=>$data['userpass'], ':email'=>$data['email'], ':timestamp'=>$data['timestamp'], ':id'=>$data['id']));
            return $this->dbQuery('admin_academic_agency_agent_get_byid',array('id'=>$data['id']));
            break;
        case 'admin_academic_agency_status':
            $sql  = 'SELECT t1.*, t2.`cname` `academic_agency_cname`, t2.`institution_code`, t3.`cname` `institution_cname`, t4.`id` `era_id`, t4.`cname` `era_cname`, IFNULL(t5.`online`, "") `regular_online`, IFNULL(t5.`offline`, "") `regular_offline`';
            $sql .= '  FROM `academic_agency_class_status` t1';
            $sql .= ' INNER JOIN `academic_agency` t2 ON t1.`agency_id` = t2.`id`';
            $sql .= ' INNER JOIN `academic_institution` t3 ON t2.`institution_code` = t3.`code`';
            $sql .= ' INNER JOIN `academic_era` t4 ON t4.`id` = t1.`era_id`';
            $sql .= ' INNER JOIN `academic_era_quarter` t5 ON t1.`era_id` = t5.`era_id` AND t1.`quarter` = t5.`quarter`';
            $sql .= ' WHERE t1.`agency_id` != :agency_id';
            $sql .= '   AND t1.`era_id` = :era_id';
            $sql .= '   AND t1.`quarter` = :quarter';
            $sql .= ' GROUP BY t2.`id`';
            $sql .= ' ORDER BY t2.`institution_code`';

            return $this->dbSelect($sql, array(':agency_id'=>999, ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            break;
        case 'admin_academic_agency_status_byid':
            $sql  = 'SELECT count(*) `cnt`, t1.`agency_id`, t2.`cname` `academic_agency_cname`, t2.`institution_code`, t3.`cname` `institution_cname`, t1.`era_id`, t4.`cname` `era_cname`, t1.`quarter`, t1.`state`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= ' INNER JOIN `academic_agency` t2 ON t1.`agency_id` = t2.`id`';
            $sql .= ' INNER JOIN `academic_institution` t2 ON t2.`institution_code` = t3.`code`';
            $sql .= ' INNER JOIN `academic_era` t4 ON t1.`era_id` = t4.`id`';
            $sql .= ' WHERE t1.`agency_id` = :agency_id';
            $sql .= ' GROUP BY t1.`era_id`, t1.`quarter`';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            break;
        case 'admin_academic_agency_unlock_yes':
            $sql = 'UPDATE `academic_agency_unlock` SET `state` = 1, `online` = :online, `offline` = :offline WHERE `agency_id` = :agency_id AND `id` = :id';
            $cnt = $this->dbUpdate($sql, array(':online'=>$data['online'], ':offline'=>$data['offline'], ':agency_id'=>$data['agency_id'], ':id'=>$data['id']));
            $sql = 'SELECT * FROM `academic_agency_unlock` WHERE `id` = :id';
            $unlock = $this->dbSelect($sql, array(':id'=>$data['id']));
            if (sizeof($unlock)) {
                $sql = 'SELECT * FROM `academic_agency_class_status` WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter';
                $status =  $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$unlock[0]['era_id'], ':quarter'=>$unlock[0]['quarter']));
                if (sizeof($status)) {
                    $sql = 'UPDATE `academic_agency_class_status` SET `state` = 0, `unlock` = 1, `online` = :online, `offline` = :offline WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter';
                    return $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$unlock[0]['era_id'], ':quarter'=>$unlock[0]['quarter'], ':online'=>$unlock[0]['online'], ':offline'=>$unlock[0]['offline']));
                } else {
                    $sql = 'INSERT INTO `academic_agency_class_status` (`id`, `agency_id`, `era_id`, `quarter`, `classes`, `unlock`, `minors`, `work_days`, `online`, `offline`, `note`, `state`) VALUES (0, :agency_id, :era_id, :quarter, 0, 1, :minors, :work_days, :online, :offline, :note, 0)';
                    return $this->dbInsert($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$unlock[0]['era_id'], ':quarter'=>$unlock[0]['quarter'], ':minors'=>$unlock[0]['minors'], ':work_days'=>$unlock[0]['work_days'], ':online'=>$unlock[0]['online'], ':offline'=>$unlock[0]['offline'], ':note'=>$unlock[0]['note']));
                }
            } else {
                return array();
            }
            break;
        case 'admin_academic_agency_unlock_no':
            $sql = 'SELECT * FROM `academic_agency_unlock` WHERE `agency_id` = :agency_id AND `id` = :id';
            $unlock = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':id'=>$data['id']));
            $sql = 'UPDATE `academic_agency_class_status` SET `unlock` = 0 WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter';
            $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$unlock[0]['era_id'], ':quarter'=>$unlock[0]['quarter']));
            $sql = 'DELETE FROM `academic_agency_unlock` WHERE `agency_id` = :agency_id AND `id` = :id';
            return $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id'], ':id'=>$data['id']));
            break;
        case 'admin_academic_agency_unlock_who':
            $sql  = 'SELECT aaa.`username`, aaa.`email`';
            $sql .= '  FROM `academic_agency_agent` aaa';
            $sql .= ' WHERE aaa.`agency_id` = :agency_id';
            $sql .= '   AND aaa.`email` is not null';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            break;
        case 'admin_academic_era':
            $sql = 'SELECT * FROM `academic_era` WHERE `id` = :era_id';
            return $this->dbSelect($sql, array(':era_id'=>$data['era_id']));
            break;
        case 'admin_academic_era_add':
            $sql = 'SELECT * FROM `academic_era` WHERE "NTUE" = :ntue ORDER BY `id` DESC LIMIT 1';
            $res = $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
            $sql  = 'INSERT INTO `academic_era` (`id`, `common`, `roc`, `code`, `cname`, `state`, `taken`)';
            $sql .= ' VALUES (0, :common, :roc, :code, :cname, 0, 0)';
            $common = intval($res[0]['common']) + 1;
            $roc = intval($res[0]['roc']) + 1;
            $code = "Y". $roc;
            $cname = $roc . "年度";
            // add academic_era
            $id = $this->dbInsert($sql, array(':common'=>$common, ':roc'=>$roc, ':code'=>$code, ':cname'=>$cname));
            $sql = 'SELECT * FROM `academic_era` WHERE id = :id';
            $res = $this->dbSelect($sql, array(':id'=>$id));
            // add academic_era_quarter
            $sql  = 'INSERT INTO `academic_era_quarter` (`id`, `era_id`, `quarter`, `cname`, `online`, `offline`, `state`)';
            $sql .= ' VALUES (0, :era_id, :quarter, :cname, "", "", 0)';
            $cnt = $this->dbUpdate($sql, array(':era_id'=>$res[0]['id'], ':quarter'=>1, ':cname'=>$res[0]['code'] . " 第一季(1~3月)"));
            $sql  = 'INSERT INTO `academic_era_quarter` (`id`, `era_id`, `quarter`, `cname`, `online`, `offline`, `state`)';
            $sql .= ' VALUES (0, :era_id, :quarter, :cname, "", "", 0)';
            $cnt = $this->dbUpdate($sql, array(':era_id'=>$res[0]['id'], ':quarter'=>2, ':cname'=>$res[0]['code'] . " 第二季(4~6月)"));
            $sql  = 'INSERT INTO `academic_era_quarter` (`id`, `era_id`, `quarter`, `cname`, `online`, `offline`, `state`)';
            $sql .= ' VALUES (0, :era_id, :quarter, :cname, "", "", 0)';
            $cnt = $this->dbUpdate($sql, array(':era_id'=>$res[0]['id'], ':quarter'=>3, ':cname'=>$res[0]['code'] . " 第三季(7~9月)"));
            $sql  = 'INSERT INTO `academic_era_quarter` (`id`, `era_id`, `quarter`, `cname`, `online`, `offline`, `state`)';
            $sql .= ' VALUES (0, :era_id, :quarter, :cname, "", "", 0)';
            $cnt = $this->dbUpdate($sql, array(':era_id'=>$res[0]['id'], ':quarter'=>4, ':cname'=>$res[0]['code'] . " 第四季(10~12月)"));
            // add academic_class
            $sql = 'INSERT INTO `academic_class` SELECT 0, '. $res[0]['id'] .', `major_code`, `code`, `cname`, 0 FROM `minor_list` WHERE `code` != :code';
            $cnt = $this->dbInsert($sql, array(':code'=>""));
            return $this->dbQuery('admin_academic_era_quarter');
            break;
        case 'admin_academic_era_quarter':
            $sql  = 'SELECT t1.*';
            $sql .= '  FROM `academic_era_quarter` t1';
            $sql .= ' INNER JOIN `academic_era` t2 ON t1.`era_id` = t2.`id` AND t2.`state` < :state';
            $sql .= ' ORDER BY t1.`era_id` DESC, t1.`id` ASC';
            return $this->dbSelect($sql, array(':state'=>3));
            break;
        case 'admin_academic_era_quarter_get':
            $sql  = 'SELECT *';
            $sql .= '  FROM `academic_era_quarter`';
            $sql .= ' WHERE `era_id` = :era_id';
            $sql .= '   AND `quarter` = :quarter';
            return $this->dbSelect($sql, array(':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            break;
        case 'admin_academic_era_quarter_mod':
            // update previous quarter to be offline
            $sql = 'UPDATE `academic_era_quarter` SET `state` = :state WHERE `state` = :state_orig';
            $cnt = $this->dbUpdate($sql, array(':state'=>2, ':state_orig'=>1));
            // update the setting one to be online
            $sql = 'UPDATE `academic_era_quarter` SET `online` = :online, `offline` = :offline, `state` = :state WHERE `id` = :id';
            $cnt = $this->dbUpdate($sql, array(':online'=>$data['online'], ':offline'=>$data['offline'], ':state'=>1, ':id'=>$data['id']));

            $sql = 'SELECT * FROM `academic_era_quarter` WHERE `id` = :id';
            $qs = $this->dbSelect($sql, array(':id'=>$data['id']));
            if (sizeof($qs)) {
                $sql = 'SELECT * FROM `academic_agency` WHERE `agent` > :agent';
                $res = $this->dbSelect($sql, array(':agent'=>0));
                foreach($res as $rs) {
                    $sql = 'SELECT * FROM `academic_agency_class_status` WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter';
                    $r = $this->dbSelect($sql, array(':agency_id'=>$rs['id'], ':era_id'=>$qs[0]['era_id'], ':quarter'=>$qs[0]['quarter']));
                    if (!sizeof($r)) {
                        $sql = 'INSERT INTO `academic_agency_class_status` (`id`, `agency_id`, `era_id`, `quarter`, `classes`, `unlock`, `minors`, `work_days`, `online`, `offline`, `note`, `state`) VALUES (0, :agency_id, :era_id, :quarter, 0, 0, "", 0, "", "", "", 0)';
                        $id = $this->dbInsert($sql, array(':agency_id'=>$rs['id'], ':era_id'=>$qs[0]['era_id'], ':quarter'=>$qs[0]['quarter']));
                    }
                }
            }

            return $this->dbQuery('admin_academic_era_quarter');
            break;
        case 'admin_academic_class':
            $sql = 'SELECT * FROM `academic_class` WHERE `era_id` = :era_id';
            return $this->dbSelect($sql, array(':era_id'=>$data['era_id']));
            break;
        case 'admin_academic_class_add':
            $sql = 'INSERT INTO `academic_class` (`id`, `era_id`, `major_code`, `minor_code`, `cname`, `state`) VALUES (0, :era_id, :major_code, :minor_code, :cname, 0)';
            $id = $this->dbInsert($sql, array(':era_id'=>$data['era_id'], ':major_code'=>$data['major_code'], ':minor_code'=>$data['minor_code'], ':cname'=>$data['cname']));
            return $this->dbQuery('admin_academic_class', array('era_id'=>$data['era_id']));
            break;
        case 'admin_academic_class_mod':
            for ($i=0; $i<sizeof($data['checks']); $i++) {
              $sql = 'UPDATE `academic_class` SET `state` = 1 WHERE `id` = :id';
              $cnt = $this->dbUpdate($sql, array(':id'=>$data['checks'][$i]));
            }

            $sql = 'UPDATE `academic_era` SET `taken` = :taken WHERE `id` = :era_id';
            return $this->dbUpdate($sql, array(':taken'=>$data['taken'], ':era_id'=>$data['era_id']));
            break;
        case 'admin_academic_classes':
            $sql = 'SELECT * FROM `academic_class` WHERE `era_id` = :era_id';
            return $this->dbSelect($sql, array(':era_id'=>$data['era_id']));
            break;
        case 'admin_check_new_user_add':
            $sql = 'SELECT count(*) `cnt` FROM `academic_agency_agent` where `username` = :username ';
            return $this->dbSelect($sql, array(':username'=>$data['username']));
            break;
        // stand alone admin report start
        case 'admin_academic_agency_report':
            
            break;
        case 'admin_academic_agency_report_era_detail':

            break;
        case 'admin_academic_agency_report_quarter_detail':
    
            break;
        case 'admin_academic_agency_report_era_summary':

            break;
        case 'admin_academic_agency_report_quarter_summary':

            break;
        case 'admin_academic_agency_report_manager':

            break;
        case 'admin_academic_agency_report_statistics':

            break;
        case 'admin_academic_agency_report_major':

            break;
        // stand alone admin report end
        case 'admin_academic_agency_report_targets':
            $sql  = 'SELECT t1.`institution_code`, t1.`cname`, t2.`cname` `institution_cname`, t1.`id`';
            $sql .= '  FROM `academic_agency` t1';
            $sql .= ' INNER JOIN `academic_institution` t2 ON t1.`institution_code` = t2.`code`';
            $sql .= ' WHERE t1.`agent` > :agent';
            $sql .= '   AND t1.`id` != :id '; // exclue test 
            return $this->dbSelect($sql, array(':agent'=>0, ':id'=>999));
            break;
        case 'admin_academic_agency_report_manager_summary':
            $sql  = 'SELECT t1.`id`, t1.`agency_id`, t2.`institution_code`, t2.`cname` `academic_agency_cname`, t3.`cname` `institution_cname`, SUM(t1.`total_hours`) `total_hours`, SUM(t1.`turnover`) `turnover`, 0 `new_people`, 0 `people`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= ' INNER JOIN `academic_agency` t2 ON t1.`agency_id` = t2.`id`';
            $sql .= ' INNER JOIN `academic_institution` t3 ON t2.`institution_code` = t3.`code`';
            $sql .= ' WHERE t1.`era_id` = :era_id';
            $sql .= '   AND t2.`id` != 999';
            $sql .= ' GROUP BY t1.`agency_id`';
            $sql .= ' ORDER BY t2.`institution_code`';

            $res = $this->dbSelect($sql, array(':era_id'=>$data['era_id']));

            foreach( $res as $key=>$val ) {
                $sql  = 'SELECT SUM(IFNULL(t2.`new_male`,0) + IFNULL(t2.`new_female`,0)) `new_people`, SUM(IFNULL(t2.`male`,0) + IFNULL(t2.`female`,0) + IFNULL(t2.`new_male`,0) + IFNULL(t2.`new_female`,0)) `people`';
                $sql .= '  FROM `academic_agency_class` t1';
                $sql .= '  LEFT JOIN `academic_agency_class_country` t2 ON t1.`id` = t2.`class_id`';
                $sql .= ' WHERE t1.`agency_id` = :agency_id';
                $sql .= '   AND t1.`era_id` = :era_id';
                $sql .= ' GROUP BY t1.`agency_id`';
                
                $new_people = 0;
                $people = 0;
                
                $rs = $this->dbSelect($sql, array(':agency_id'=>$val['agency_id'], ':era_id'=>$data['era_id']));
                if (sizeof($rs)) {
                    foreach( $rs as $r ) {
                        $new_people = $r['new_people'];
                        $people = $r['people'];
                    } 
                }
                $res[$key]['new_people'] = $new_people;
                $res[$key]['people'] = $people;
            }

            return $res;
            break;
        case 'admin_academic_agency_report_manager_new_people_summary':
            $sql  = 'SELECT t1.`agency_id`, t1.`major_code`, t1.`minor_code`, SUM(IFNULL(t2.`new_male`,0) + IFNULL(t2.`new_female`, 0)) `new_people`, SUM(IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0) + IFNULL(t2.`male`, 0) + IFNULL(t2.`female`, 0)) `people`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= '  LEFT JOIN `academic_agency_class_country` t2 ON t2.`class_id` = t1.`id`';
            $sql .= ' WHERE `era_id` = :era_id';
            $sql .= '   AND t1.`agency_id` != 999';
            $sql .= ' GROUP BY t1.`minor_code`';
            return $this->dbSelect($sql, array(':era_id'=>$data['era_id']));
            break;
        case 'admin_academic_agency_report_manager_people_summary':
            $sql  = 'SELECT t1.`agency_id`, t1.`major_code`, t1.`minor_code`, SUM(IFNULL(t2.`new_male`,0) + IFNULL(t2.`new_female`, 0)) `new_people`, SUM(IFNULL(t2.`new_male`,0) + IFNULL(t2.`new_female`,0) + IFNULL(t2.`male`,0) + IFNULL(t2.`female`, 0)) `people`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= '  LEFT JOIN `academic_agency_class_country` t2 ON t2.`class_id` = t1.`id`';
            $sql .= ' WHERE `era_id` = :era_id';
            $sql .= '   AND t1.`agency_id` != 999';
            $sql .= ' GROUP BY t1.`minor_code`';
            return $this->dbSelect($sql, array(':era_id'=>$data['era_id']));
            break;
        case 'admin_academic_agency_report_manager_total_hours_summary':
            $sql  = 'SELECT `agency_id`, `major_code`, `minor_code`, SUM(`total_hours`) `total_hours`';
            $sql .= '  FROM `academic_agency_class`';
            $sql .= ' WHERE `era_id` = :era_id';
            $sql .= '   AND `agency_id` != 999';
            $sql .= ' GROUP BY `minor_code`';
            return $this->dbSelect($sql, array(':era_id'=>$data['era_id']));
            break;
        case 'admin_academic_agency_report_manager_turnover_summary':
            $sql  = 'SELECT `agency_id`, `major_code`, `minor_code`, SUM(`turnover`) `turnover`';
            $sql .= '  FROM `academic_agency_class`';
            $sql .= ' WHERE `era_id` = :era_id';
            $sql .= '   AND `agency_id` != 999';
            $sql .= ' GROUP BY `minor_code`';
            return $this->dbSelect($sql, array(':era_id'=>$data['era_id']));
            break;
        case 'admin_academic_agency_report_manager_detail':
            $sql  = 'SELECT t2.`institution_code`, t2.`cname` `academic_agency_cname`, t3.`cname` `institution_cname`, SUM(IFNULL(t4.`new_male`, 0) + IFNULL(t4.`new_female`, 0)) `new_people`, SUM(IFNULL(t4.`male`, 0) + IFNULL(t4.`female`, 0) + IFNULL(t4.`new_male`, 0) + IFNULL(t4.`new_female`, 0)) `people`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= ' INNER JOIN `academic_agency` t2 ON t1.`agency_id` = t2.`id`';
            $sql .= ' INNER JOIN `academic_institution` t3 ON t2.`institution_code` = t3.`code`';
            $sql .= '  LEFT JOIN `academic_agency_class_country` t4 ON t1.`id` = t4.`class_id`';
            $sql .= ' WHERE t1.`era_id` = :era_id';
            $sql .= '   AND t1.`major_code` IN ("A", "B")';
            $sql .= '   AND t1.`agency_id` != 999';
            $sql .= ' GROUP BY t1.`agency_id`';
            $sql .= ' ORDER BY t2.`institution_code`';
            return $this->dbSelect($sql, array(':era_id'=>$data['era_id']));
            break;
        case 'admin_academic_agency_report_manager_new_people_detail':
            $sql  = 'SELECT t1.`agency_id`, t1.`major_code`, t1.`minor_code`, SUM(IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0)) `new_people`, SUM(IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0) + IFNULL(t2.`male`, 0) + IFNULL(t2.`female`, 0)) `people`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= '  LEFT JOIN `academic_agency_class_country` t2 ON t2.`class_id` = t1.`id`';
            $sql .= ' WHERE t1.`era_id` = :era_id';
            $sql .= '   AND t1.`agency_id` = :agency_id';
            $sql .= ' GROUP BY t1.`minor_code`';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id']));
            break;
        case 'admin_academic_agency_report_manager_people_detail':
            $sql  = 'SELECT t1.`agency_id`, t1.`major_code`, t1.`minor_code`, SUM(IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0)) `new_people`, SUM(IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0) + IFNULL(t2.`male`, 0) + IFNULL(t2.`female`, 0)) `people`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= '  LEFT JOIN `academic_agency_class_country` t2 ON t2.`class_id` = t1.`id`';
            $sql .= ' WHERE t1.`era_id` = :era_id';
            $sql .= '   AND t1.`agency_id` = :agency_id';
            $sql .= ' GROUP BY t1.`minor_code`';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id']));
            break;
        case 'admin_academic_agency_report_manager_total_hours_detail':
            $sql  = 'SELECT `agency_id`, `major_code`, `minor_code`, SUM(`total_hours`) `total_hours`';
            $sql .= '  FROM `academic_agency_class`';
            $sql .= ' WHERE `era_id` = :era_id';
            $sql .= '   AND `agency_id` = :agency_id';
            $sql .= ' GROUP BY `minor_code`';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id']));
            break;
        case 'admin_academic_agency_report_manager_turnover_detail':
            $sql  = 'SELECT `agency_id`, `major_code`, `minor_code`, SUM(`turnover`) `turnover`';
            $sql .= '  FROM `academic_agency_class`';
            $sql .= ' WHERE `era_id` = :era_id';
            $sql .= '   AND `agency_id` = :agency_id';
            $sql .= ' GROUP BY `minor_code`';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id']));
            break;
        case 'admin_academic_agency_report_statistics_detail':
            #$sql  = 'SELECT t2.`country_code`, t3.`cname` `country_cname`, SUM(t2.`new_male` + t2.`new_female`) `new_people`, SUM(t2.`new_male`) `new_male`, SUM(t2.`new_female`) `new_female`';
            $sql  = 'SELECT t2.`country_code`, t3.`cname` `country_cname`, SUM(IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0)) `new_people`, SUM(IFNULL(t2.`new_male`, 0)) `new_male`, SUM(IFNULL(t2.`new_female`, 0)) `new_female`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= ' INNER JOIN `academic_agency_class_country` t2 ON t1.`id` = t2.`class_id`';
            $sql .= ' INNER JOIN `country_list` t3 ON t3.`code` = t2.`country_code`';
            $sql .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id';
            $sql .= '   AND t1.`agency_id` != 999';
            $sql .= ' GROUP BY t2.`country_code`';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id']));
            break;
        case 'admin_academic_agency_report_statistics_contact':
            $sql  = 'SELECT SUM(t2.`new_male` + t2.`new_female`) `new_people`, SUM(t2.`new_male`) `new_male`, SUM(t2.`new_female`) `new_female`, IFNULL(t3.`cname`, "") `manager_cname`, IFNULL(t3.`title`, "") `manager_title`, IFNULL(t4.`cname`, "") `primary_cname`, IFNULL(t4.`title`, "") `primary_title`, IFNULL(concat(t4.`area_code`, " ", t4.`phone`, " ", t4.`ext`), "") `primary_tel`, IFNULL(t4.`email`, "") `primary_email`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= ' INNER JOIN `academic_agency_class_country` t2 ON t1.`id` = t2.`class_id`';
            $sql .= '  LEFT JOIN `academic_agency_contact` t3 ON t1.`agency_id` = t3.`agency_id` AND t3.`era_id` = t1.`era_id` AND t3.`manager` = 1';
            $sql .= '  LEFT JOIN `academic_agency_contact` t4 ON t1.`agency_id` = t4.`agency_id` AND t4.`era_id` = t1.`era_id` AND t4.`primary` = 1';
            $sql .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id';
            $sql .= ' GROUP BY t1.`agency_id`';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id']));
            break;
        case 'admin_academic_agency_report_statistics_compare':
            if ($data['era_id'] == 1) {
                $sql  = 'SELECT SUM(IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0)) `new_people`';
                $sql .= '  FROM `academic_agency_class` t1';
                $sql .= '  LEFT JOIN `academic_agency_class_country` t2 ON t2.`class_id` = t1.`id`';
                $sql .= ' WHERE t1.`agency_id` = :agency_id';
                $sql .= '   AND t1.`era_id` = :era_id';
                $curr = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'])); 
                $sql  = 'SELECT IFNULL(`new_people`, 0) `new_people` FROM `academic_agency_class_y105` WHERE `agency_id` = :agency_id';
                $last = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));

                if (!sizeof($last)) {
                    return array('curr'=>$curr[0]['new_people'], 'last'=>0, 'agency_id'=>$data['agency_id']);
                }
                return array('curr'=>$curr[0]['new_people'], 'last'=>$last[0]['new_people'], 'agency_id'=>$data['agency_id']);
            } else {
                $era = $this->dbQuery('admin_academic_era', array('era_id'=>$data['era_id']));
                $common = intval($era[0]['common']) - 1;
                $sql = 'SELECT * FROM `academic_era` WHERE `common` = :common';
                $era_last = $this->dbSelect($sql, array(':common'=>$common));

                $sql  = 'SELECT SUM(IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0)) `new_people`';
                $sql .= '  FROM `academic_agency_class` t1';
                $sql .= '  LEFT JOIN `academic_agency_class_country` t2 ON t2.`class_id` = t1.`id`';
                $sql .= ' WHERE t1.`agency_id` = :agency_id';
                $sql .= '   AND t1.`era_id` = :era_id';

                $curr = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'])); 

                $curr_new_people = (sizeof($curr) > 0)? $curr[0]['new_people'] : 0;

                $sql  = 'SELECT SUM(IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0)) `new_people`';
                $sql .= '  FROM `academic_agency_class` t1';
                $sql .= '  LEFT JOIN `academic_agency_class_country` t2 ON t2.`class_id` = t1.`id`';
                $sql .= ' WHERE t1.`agency_id` = :agency_id';
                $sql .= '   AND t1.`era_id` = :era_id';
                $last = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$era_last[0]['id'])); 

                $last_new_people = (sizeof($last) > 0)? $last[0]['new_people'] : 0;

                return array('curr'=>$curr_new_people, 'last'=>$last_new_people, 'agency_id'=>$data['agency_id']);
            }
            break;
        case 'admin_academic_agency_report_major_b':
            $sql = 'SELECT * FROM `academic_class` WHERE `era_id` = :era_id AND `major_code` = :major_b';
            return $this->dbSelect($sql, array(':era_id'=>$data['era_id'], ':major_b'=>'B'));
            break;
        case 'admin_academic_agency_report_minor_b':
            $sql  = 'SELECT 0 `new_people`, 0 `people`, SUM(`hours`) `hours`, SUM(`total_hours`) `total_hours`, SUM(`turnover`) `turnover`, "" `info`, GROUP_CONCAT(`note` SEPARATOR " ") `note`'; 
            $sql .= '  FROM `academic_agency_class` ';
            $sql .= ' WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `minor_code` = :minor_code'; 
            $sql .= '   AND `agency_id` != 999';
            $sql .= ' GROUP BY `minor_code`';

            $res =  $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], 'minor_code'=>$data['minor_code']));
            if (sizeof($res)) {
                foreach($res as $key=>$val) {

                    $sql  = 'SELECT SUM(IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0)) `new_people`, SUM(IFNULL(t2.`male`, 0) + IFNULL(t2.`female`, 0) + IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0)) `people`';
                    $sql .= '  FROM `academic_agency_class` t1';
                    $sql .= '  LEFT JOIN `academic_agency_class_country` t2 ON t2.`class_id` = t1.`id`';
                    $sql .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`minor_code` = :minor_code';
                    $sql .= ' GROUP BY t1.`minor_code`';
                    $r = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':minor_code'=>$data['minor_code']));
                    $res[$key]['new_people'] = $r[0]['new_people'];
                    $res[$key]['people'] = $r[0]['people'];

                    $sql  = 'SELECT GROUP_CONCAT(CONCAT(t1.`cname`, "-", t2.`cname`, "-", t3.`cname`)) `cname`';
                    $sql .= '  FROM `academic_agency_class` t1';
                    $sql .= ' INNER JOIN `target_list` t2 ON t1.`target_code` = t2.`code`';               
                    $sql .= ' INNER JOIN `content_list` t3 ON t1.`content_code` = t3.`code`';               
                    $sql .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`minor_code` = :minor_code';
                    $sql .= ' GROUP BY t1.`minor_code`';
                    $r = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':minor_code'=>$data['minor_code']));
                    $res[$key]['info'] = $r[0]['cname'];
                }
            }

            return $res;
            break;
        case 'admin_academic_agency_report_states':
            $sql  = 'SELECT aacc.`country_code`, cl.`cname` country_name, sl.`cname` state_name,';
            #$sql .= ' SUM(aacc.`male` + aacc.`new_male`) male, SUM(aacc.`female` + aacc.`new_female`) female, SUM(aacc.`male` + aacc.`new_male` + aacc.`female` + aacc.`new_female`) people, ';
            $sql .= ' SUM(aacc.`new_male`) male, SUM(aacc.`new_female`) female, SUM(aacc.`new_male` + aacc.`new_female`) people, ';
            #$sql .= ' 0 male, 0 female, 0 people, ';
            $sql .= ' 0 male_a, 0 female_a, 0 male_b, 0 female_b, 0 male_c, 0 female_c';
            $sql .= '  FROM `academic_agency_class` aac';
            $sql .= ' INNER JOIN academic_agency_class_country aacc on aacc.class_id = aac.id';
            $sql .= ' INNER JOIN country_list cl on aacc.country_code = cl.code';
            $sql .= ' INNER JOIN state_list sl on cl.state_code = sl.code';
            $sql .= ' WHERE aac.`era_id` = :era_id';
            $sql .= '   AND aac.`agency_id` != 999';
            $sql .= ' GROUP BY aacc.country_code';
            $sql .= ' ORDER BY people DESC';
            $states =  $this->dbSelect($sql, array(':era_id'=>$data['era_id']));

            foreach($states as $key=>$val) {
                #$sql = 'SELECT aac.`major_code`, SUM(aacc.`male` + aacc.`new_male`) male, SUM(aacc.`female` + aacc.`new_female`) female, SUM(aacc.`male` + aacc.`new_male` + aacc.`female` + aacc.`new_female`) people';
                $sql = 'SELECT aac.`major_code`, SUM(IFNULL(aacc.`new_male`, 0)) male, SUM(IFNULL(aacc.`new_female`, 0)) female, SUM(IFNULL(aacc.`new_male`, 0) + IFNULL(aacc.`new_female`, 0)) people';
                $sql .= '  FROM `academic_agency_class` aac';
                $sql .= ' INNER JOIN `academic_agency_class_country` aacc on aac.id = aacc.class_id';
                $sql .= ' WHERE aac.`era_id` = :era_id';
                $sql .= '   AND aacc.`country_code` = :country_code';
                $sql .= '   AND aac.`agency_id` != 999';
                $sql .= ' GROUP BY aac.major_code';
                $res = $this->dbSelect($sql, array(':era_id'=>$data['era_id'], ':country_code'=>$states[$key]['country_code']));
                if (sizeof($res)) {
                    $male = 0;
                    $female = 0;
                    foreach($res as $r) {
                        switch($r['major_code'])
                        {
                        case 'A':
                            $states[$key]['male_a'] = $r['male'];
                            $states[$key]['female_a'] = $r['female'];
                            break;
                        case 'B':
                            $states[$key]['male_b'] = $r['male'];
                            $states[$key]['female_b'] = $r['female'];
                            break;
                        case 'C':
                            $states[$key]['male_c'] = $r['male'];
                            $states[$key]['female_c'] = $r['female'];
                            break;
                        }
                        $male += $r['male'];
                        $female += $r['female'];
                    }
                    #$states[$key]['male'] = $male;
                    #$states[$key]['female'] = $female;
                    #$states[$key]['people'] = $male + $female;
                }
            }

            return $states;
            break;
        case 'admin_academic_agency_report_classes':
            $sql  = 'SELECT ac.cname class_name, aac.minor_code, aacc.country_code, cl.cname country_name, sl.sno, sl.code, sl.cname state_name, ';
            $sql .= ' SUM(aacc.male + aacc.new_male) male, SUM(aacc.female + aacc.new_female) female, SUM(aacc.male + aacc.new_male + aacc.female + aacc.new_female) people';
            $sql .= '  FROM academic_agency_class aac';
            $sql .= ' INNER JOIN academic_agency_class_country aacc on aacc.class_id = aac.id';
            $sql .= ' INNER JOIN country_list cl on aacc.country_code = cl.code';
            $sql .= ' INNER JOIN state_list sl on cl.state_code = sl.code';
            $sql .= ' INNER JOIN academic_class ac on ac.era_id = aac.era_id AND ac.minor_code = aac.minor_code';
            $sql .= ' WHERE aac.era_id = :era_id';
            $sql .= '   AND aac.minor_code = :minor_code';
            $sql .= ' GROUP BY sl.code, aacc.country_code';
            $sql .= ' ORDER BY sl.sno, people DESC';
            return $this->dbSelect($sql, array(':era_id'=>$data['era_id'], ':minor_code'=>$data['minor_code']));
            break;
        case 'admin_academic_agency_report_people':
            $sql  = 'SELECT cl.state_code, sl.cname state_name, Count(Distinct(aacc.country_code)) countries, SUM(aacc.male + aacc.new_male) male, SUM(aacc.female + aacc.new_female) female, SUM(aacc.male + aacc.new_male + aacc.female + aacc.new_female) people';
            $sql .= '  FROM academic_agency_class aac';
            $sql .= ' INNER JOIN academic_agency_class_country aacc on aacc.class_id = aac.id';
            $sql .= ' INNER JOIN country_list cl on aacc.country_code = cl.code';
            $sql .= ' INNER JOIN state_list sl on cl.state_code = sl.code';
            $sql .= ' WHERE aac.era_id = :era_id';
            $sql .= '   AND aac.`agency_id` != 999';
            $sql .= ' GROUP BY cl.state_code';
            $sql .= ' ORDER BY people DESC';
            return $this->dbSelect($sql, array(':era_id'=>$data['era_id']));
            break;
        case 'admin_board_unreply_query_bk':
            $sql  = 'SELECT t.* ';
            $sql .= '  FROM `academic_board` t';
            return $this->dbSelect('SELECT count(*) `cnt` FROM `academic_board` ',array(':ntue'=>'NTUE'));
            break;
        case 'admin_board_unreply_query':
            $sql  = 'SELECT t.* , a.`username`,g.`cname`, n.`cname` as ins_cname ';
            $sql .= '  FROM `academic_board` t INNER JOIN `academic_agency_agent` a ';
            $sql .= '       ON t.`agent_id` = a.`id` INNER JOIN `academic_agency` g ';
            $sql .= '       ON a.`agency_id` = g.`id` INNER JOIN `academic_institution` n ';
            $sql .= '       ON g.`institution_code` = n.`code` ';
            $sql .= ' WHERE "NTUE" = :ntue ';
            $sql .= '   AND t.`insert_date`  > now() - 3600*24*60';
            $sql .= '   AND t.`reply_yn` = "0"'; 
            $sql .= ' ORDER BY t.`insert_date` ';
            return $this->dbSelect($sql,array(':ntue'=>'NTUE'));
            break;
        case 'admin_board_save_reply':
            $sql = 'UPDATE `academic_board` SET reply_content = :reply_content ,admin_id = :admin_id,reply_date = now(), reply_yn = "1" WHERE message_id = :message_id ';
            $cnt = $this->dbUpdate($sql, array(':reply_content'=>$data['reply_content'],':admin_id'=>$data['admin_id'],':message_id'=>$data['message_id']));
            return ['cnt'=>$cnt];
            break;						
        case 'admin_dashboard_update':
            $sql = 'UPDATE `official` SET `dashboard` = :dashboard';
            $cnt = $this->dbUpdate($sql, array(':dashboard'=>$data['dashboard']));
            return ['cnt'=>$cnt];
            break;
        case 'admin_postman_receverlist':
            $sql = 'SELECT * FROM `academic_era` WHERE `common` = :common';
            $era = $this->dbSelect($sql, array(':common'=>date('Y')));
            $era_id = $era[0]['id'];
            $quarter = 0;
            $sql  = 'SELECT cname,email ';
            $sql .= '  FROM `academic_agency_contact` ';
            $sql .= ' WHERE "NTUE" = :ntue ';
            $sql .= '   AND `email` IS NOT NULL ';
            $sql .= '   AND `email` != "" ';
            switch($data['rcpttotype']){
                /* 所有單位人員 */
                case '1':
                    return $this->dbSelect($sql,array(':ntue'=>MD5Prefix));
                    break;
                /* 所有單位主管 */
                case '2':
                    $sql .= 'and `manager` = :manager';
                    return $this->dbSelect($sql,array(':ntue'=>MD5Prefix, ':manager'=>1));
                    break;
                /* 所有單位職員 */
                case '3':
                    $sql .= 'and `staff` = :staff';
                    return $this->dbSelect($sql,array(':ntue'=>MD5Prefix, ':staff'=>1));
                    break;
                /* 所有未填報單位聯絡人 */
                /*
                case '4':
                    $sql .= 'AND `agency_id` IN ( ';
					$sql .= 'SELECT DISTINCT t6.`id` ';
					$sql .= '  FROM `academic_agency` t6 ';
					$sql .= ' WHERE t6.id NOT IN ( ';
					$sql .= '       SELECT DISTINCT t5.`agency_id` ';
					$sql .= '         FROM ( ';
					$sql .= '              SELECT t3.* ';
					$sql .= '                FROM `academic_era_quarter` t3 ';
					$sql .= '               WHERE t3.`offline` in ( ';
					$sql .= '                     SELECT max(t2.`offline`) era_quarter_max_offline ';
					$sql .= '                       FROM `academic_era` t1 INNER JOIN `academic_era_quarter` t2 ';
					$sql .= '                             ON t2.`era_id` = t1.`id` ';
					$sql .= '                      WHERE t1.`state` = "1" ';
					$sql .= '                    ) ';
					$sql .= '              ) t4 INNER JOIN `academic_agency_class` t5 ';
					$sql .= '                   ON t5.`era_id` = t4.`era_id` AND t5.`quarter` = t4.`quarter` ';
					$sql .= '     ) ';   
                    $sql .= ')' ;
                    break;
                */
                case 5:
                    $quarter = 1;
                    $sql .= '   AND `agency_id` IN (SELECT t1.`agency_id` FROM `academic_agency_class_status` t1 WHERE t1.`classes` = 0 AND t1.`era_id` = :era_id AND t1.`quarter` = :quarter)';
                    return $this->dbSelect($sql,array(':ntue'=>MD5Prefix, ':era_id'=>$era_id, ':quarter'=>$quarter));
                    break;
                case 6:
                    $quarter = 2;
                    $sql .= '   AND `agency_id` IN (SELECT t1.`agency_id` FROM `academic_agency_class_status` t1 WHERE t1.`classes` = 0 AND t1.`era_id` = :era_id AND t1.`quarter` = :quarter)';
                    return $this->dbSelect($sql,array(':ntue'=>MD5Prefix, ':era_id'=>$era_id, ':quarter'=>$quarter));
                    break;
                case 7:
                    $quarter = 3;
                    $sql .= '   AND `agency_id` IN (SELECT t1.`agency_id` FROM `academic_agency_class_status` t1 WHERE t1.`classes` = 0 AND t1.`era_id` = :era_id AND t1.`quarter` = :quarter)';
                    return $this->dbSelect($sql,array(':ntue'=>MD5Prefix, ':era_id'=>$era_id, ':quarter'=>$quarter));
                    break;
                case 8:
                    $quarter = 4;
                    $sql .= '   AND `agency_id` IN (SELECT t1.`agency_id` FROM `academic_agency_class_status` t1 WHERE t1.`classes` = 0 AND t1.`era_id` = :era_id AND t1.`quarter` = :quarter)';
                    return $this->dbSelect($sql,array(':ntue'=>MD5Prefix, ':era_id'=>$era_id, ':quarter'=>$quarter));
                    break;
            }
            break;
        /* agent */
        case 'agent_academic_agency':
            $sql  = 'SELECT t1.*, t2.`cname` `academic_institution_cname`, t2.`aka`';
            $sql .= '  FROM `academic_agency` t1';
            $sql .= ' INNER JOIN `academic_institution` t2 ON t2.`code` = t1.`institution_code`';
            $sql .= ' WHERE t1.`id` = :id';
            return $this->dbSelect($sql, array(':id'=>$data['id']));
            break;
        case 'agent_academic_agency_mod':
            $sql = 'UPDATE `academic_agency` SET cname = :cname, zipcode = :zipcode, address = :address, established = :established, approval = :approval, note = :note WHERE id = :id';
            $cnt = $this->dbUpdate($sql, array(':cname'=>$data['cname'], ':zipcode'=>$data['zipcode'], ':address'=>$data['address'], ':established'=>$data['established'], ':approval'=>$data['approval'], ':note'=>$data['note'], ':id'=>$data['id']));
            return $this->dbQuery('agent_academic_agency', array('id'=>$data['id']));
            break;
        case 'agent_academic_agency_class':
            $sql  = 'SELECT t1.*, t2.`cname` `major_cname`, t3.`cname` `minor_cname`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= ' INNER JOIN `major_list` t2 ON t1.`major_code` = t2.`code`';
            $sql .= ' INNER JOIN `minor_list` t3 ON t1.`minor_code` = t3.`code`';
            $sql .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`quarter` = :quarter AND t1.`state` = 0';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            break;
        case 'agent_academic_agency_class_add':
            $sql = 'SELECT * FROM `academic_agency_class` WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter ORDER BY `id` DESC';
            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            $classed_id = (sizeof($res))? $res[0]['id'] : 0;
            $sql  = 'INSERT INTO `academic_agency_class`';
            $sql .= ' (`id`, `agency_id`, `era_id`, `quarter`, `major_code`, `minor_code`, `cname`, `content_code`, `target_code`, `new_people`, `people`, `weekly`, `weeks`, `hours`, `adjust`, `total_hours`, `revenue`, `subsidy`, `turnover`, `note`, `latest`, `state`)';
            $sql .= ' VALUES (0, :agency_id, :era_id, :quarter, :major_code, :minor_code, :cname, :content_code, :target_code, :new_people, :people, :weekly, :weeks, :hours, :adjust, :total_hours, :revenue, :subsidy, :turnover, :note, NOW(), 0)';
            $class_id = $this->dbInsert($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter'], ':major_code'=>$data['major_code'], ':minor_code'=>$data['minor_code'], ':cname'=>$data['cname'], ':content_code'=>$data['content_code'], ':target_code'=>$data['target_code'], ':new_people'=>$data['new_people'], ':people'=>$data['people'], ':weekly'=>$data['weekly'], ':weeks'=>$data['weeks'], 'hours'=>$data['hours'], ':adjust'=>$data['adjust'], ':total_hours'=>$data['total_hours'], ':revenue'=>$data['revenue'], ':subsidy'=>$data['subsidy'], ':turnover'=>$data['turnover'], ':note'=>base64_decode($data['note'])));
            if ($class_id && $class_id != $classed_id) {
                $sql = 'SELECT * FROM `academic_agency_class` WHERE `id` = :id';
                $res = $this->dbSelect($sql, array(':id'=>$class_id));
                $sql = 'DELETE FROM `academic_agency_class_country` WHERE `class_id` = :class_id';
                $cnt = $this->dbUpdate($sql, array(':class_id'=>$class_id));
                for ($i=0; $i<sizeof($data['country']); $i++) {
                    $sql = 'INSERT INTO `academic_agency_class_country` (`id`, `class_id`, `country_code`, `male`, `female`, `new_male`, `new_female`, `note`, `state`) VALUES (0, :class_id, :country_code, :male, :female, :new_male, :new_female, :note, 0)';
                    $id = $this->dbInsert($sql, array(':class_id'=>$class_id, ':country_code'=>$data['country'][$i]['country_code'], ':male'=>$data['country'][$i]['male'], ':new_male'=>$data['country'][$i]['new_male'], ':female'=>$data['country'][$i]['female'], ':new_female'=>$data['country'][$i]['new_female'], ':note'=>base64_decode($data['country'][$i]['note'])));
                } 
            } 

            // update academic_ageny_status
            $sql = 'SELECT * FROM `academic_agency_class` WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter ORDER BY `id` DESC';
            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            $classes = sizeof($res);
            $sql = 'SELECT * FROM `academic_agency_class_status` WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter';
            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            if (sizeof($res)) {
                $sql = 'UPDATE `academic_agency_class_status` t1 SET t1.`classes` = :classes WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`quarter` = :quarter';
                $cnt = $this->dbUpdate($sql, array(':classes'=>$classes, ':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            } else {
                $sql = 'SELECT * FROM `academic_agency_unlock` WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter';
                $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
                $unlock = 0;
                if (sizeof($res)) {
                    $unlock = $res[0]['unlock'];
                    $sql = 'INSERT INTO `academic_agency_class_status` (`id`, `agency_id`, `era_id`, `quarter`, `classes`, `unlock`, `minors`, `work_days`, `online`, `offline`, `note`, `state`) VALUES (0, :agency_id, :era_id, :quarter, :classes, :unlock, :minors, :work_days, :online, :offline, :note, 0)';
                    $id = $this->dbInsert($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter'], ':classes'=>$classes, ':unlock'=>$unlock, ':minors'=>$res[0]['minors'], ':work_days'=>$res[0]['work_days'], ':online'=>$res[0]['online'], ':offline'=>$res[0]['offline'], ':note'=>$res[0]['note']));
                } else {
                    $sql = 'INSERT INTO `academic_agency_class_status` (`id`, `agency_id`, `era_id`, `quarter`, `classes`, `unlock`, `minors`, `work_days`, `online`, `offline`, `note`, `state`) VALUES (0, :agency_id, :era_id, :quarter, :classes, 0, "", 0, "", "", "", 0)';
                    $id = $this->dbInsert($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter'], ':classes'=>$classes));
                }
            }

            return $this->dbQuery('agent_academic_agency_class', array('agency_id'=>$data['agency_id'], 'era_id'=>$data['era_id'], 'quarter'=>$data['quarter']));
            break;
        case 'agent_academic_agency_class_del':
            $sql = 'SELECT * FROM `academic_agency_class` WHERE `id` = :id';
            $res = $this->dbSelect($sql, array(':id'=>$data['id']));
            $sql = 'DELETE FROM `academic_agency_class` WHERE `id` = :id';
            $cnt = $this->dbUpdate($sql, array(':id'=>$data['id']));
            $sql = 'DELETE FROM `academic_agency_class_country` WHERE `class_id` = :class_id';
            $cnt = $this->dbUpdate($sql, array(':class_id'=>$data['id']));
            $sql = 'UPDATE `academic_agency_class_status` t1 SET t1.`classes` = (SELECT COUNT(*) FROM `academic_agency_class` t2 WHERE t1.`agency_id` = t2.`agency_id` AND t1.`era_id` = t2.`era_id` AND t1.`quarter` = t2.`quarter`) WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`quarter` = :quarter';
            $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            return $this->dbQuery('agent_academic_agency_class', array('agency_id'=>$data['agency_id'], 'era_id'=>$data['era_id'], 'quarter'=>$data['quarter']));
            break;
        case 'agent_academic_agency_class_done':
            $sql = 'UPDATE `academic_agency_class` SET `state` = 1 WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter';
            $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            
            $sql = 'SELECT * FROM `academic_agency_class` WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter';
            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            $classes = sizeof($res);            

            $sql = 'SELECT * FROM `academic_agency_class_status` WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter';
            $status = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            
            if (sizeof($status)) {
                $sql = 'UPDATE `academic_agency_class_status` SET `state` = 1 WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter';
                $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            } else {
                $sql = 'INSERT INTO `academic_agency_class_status` (`id`, `agency_id`, `era_id`, `quarter`, `classes`, `unlock`, `minors`, `work_days`, `online`, `offline`, `note`, `state`) VALUES (0, :agency_id, :era_id, :quarter, :classes, 0, "", 0, "", "", "", 0)';
                $sid = $this->dbInsert($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter'], ':classes'=>$classes));
            }

            return $this->dbQuery('agent_academic_agency_class', array('agency_id'=>$data['agency_id'], 'era_id'=>$data['era_id'], 'quarter'=>$data['quarter']));
            break;
        case 'agent_academic_agency_class_import':
            // classes
            $sql  = 'INSERT INTO `academic_agency_class` ';
            $sql .= 'SELECT 0 `id`, `agency_id`, `era_id`, (`quarter` + 1) `quarter`, `major_code`, `minor_code`, `cname`, `content_code`, `target_code`, `new_people`, `people`, `weekly`, `weeks`, `hours`, `adjust`, `total_hours`, `revenue`, `subsidy`, `turnover`, `note`, now() `latest`, 0 `state`';
            $sql .= '  FROM `academic_agency_class` ';
            $sql .= ' WHERE `id` = :id';

            $id = $this->dbInsert($sql, array(':id'=>$data['id']));

            // country 
            $sql  = 'INSERT INTO `academic_agency_class_country`';
            $sql .= 'SELECT 0 `id`, :class_id `class_id`, `country_code`, `male`, `female`, `new_male`, `new_female`, `note`, `state`';
            $sql .= '  FROM `academic_agency_class_country`';
            $sql .= ' WHERE `class_id` = :id';
            $cnt = $this->dbInsert($sql, array(':class_id'=>$id, ':id'=>$data['id']));

            $sql = 'SELECT * FROM `academic_agency_class` WHERE `id` = :id';
            $res = $this->dbSelect($sql, array(':id'=>$id));

            $sql = 'SELECT * FROM `academic_agency_class` WHERE `era_id` = :era_id AND `quarter` = :quarter AND `agency_id` = :agency_id';
            $class = $this->dbSelect($sql, array(':era_id'=>$res[0]['era_id'], ':quarter'=>$res[0]['quarter'], ':agency_id'=>$res[0]['agency_id']));
            $classes = sizeof($class);

            $sql = 'SELECT * FROM `academic_agency_class_status` WHERE `era_id` = :era_id AND `quarter` = :quarter AND `agency_id` = :agency_id';
            $status = $this->dbSelect($sql, array(':era_id'=>$res[0]['era_id'], ':quarter'=>$res[0]['quarter'], ':agency_id'=>$res[0]['agency_id']));

            if (sizeof($status)) {
                $sql = 'UPDATE `academic_agency_class_status` t1 SET t1.`classes` = :classes WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`quarter` = :quarter';
                $cnt = $this->dbUpdate($sql, array(':classes'=>$classes, ':agency_id'=>$res[0]['agency_id'], ':era_id'=>$res[0]['era_id'], ':quarter'=>$res[0]['quarter']));
            } else {
                $sql = 'INSERT INTO `academic_agency_class_status` (`id`, `agency_id`, `era_id`, `quarter`, `classes`, `unlock`, `minors`, `work_days`, `online`, `offline`, `note`, `state`) VALUES (0, :agency_id, :era_id, :quarter, :classes, 0, "", 0, "", "", "", 0)';
                $sid = $this->dbInsert($sql, array(':agency_id'=>$res[0]['agency_id'], ':era_id'=>$res[0]['era_id'], ':quarter'=>$res[0]['quarter'], ':classes'=>$classes));
            }

            return $id;
            break;
        case 'agent_academic_agency_class_mod':
            $sql = 'UPDATE `academic_agency_class` SET `minor_code` = :minor_code, `cname` = :cname, `content_code` = :content_code, `target_code` = :target_code, `new_people` = :new_people, `people` = :people, `weekly` = :weekly, `weeks` = :weeks, `hours` = :hours, `adjust` = :adjust, `total_hours` = :total_hours, `revenue` = :revenue, `subsidy` = :subsidy, `turnover` = :turnover, `note` = :note, `latest` = NOW() WHERE `id` = :class_id';
            $cnt = $this->dbUpdate($sql, array(':minor_code'=>$data['minor_code'], ':cname'=>$data['cname'], ':content_code'=>$data['content_code'], ':target_code'=>$data['target_code'], ':new_people'=>$data['new_people'], ':people'=>$data['people'], ':weekly'=>$data['weekly'], ':weeks'=>$data['weeks'], 'hours'=>$data['hours'], ':adjust'=>$data['adjust'], ':total_hours'=>$data['total_hours'], ':revenue'=>$data['revenue'], ':subsidy'=>$data['subsidy'], ':turnover'=>$data['turnover'], ':note'=>base64_decode($data['note']), ':class_id'=>$data['class_id']));
            $sql = 'DELETE FROM `academic_agency_class_country` WHERE `class_id` = :class_id';
            $cnt = $this->dbUpdate($sql, array(':class_id'=>$data['class_id']));
            for ($i=0; $i<sizeof($data['country']); $i++) {
                $sql = 'INSERT INTO `academic_agency_class_country` (`id`, `class_id`, `country_code`, `male`, `female`, `new_male`, `new_female`, `note`, `state`) VALUES (0, :class_id, :country_code, :male, :female, :new_male, :new_female, :note, 0)';
                    $id = $this->dbInsert($sql, array(':class_id'=>$data['class_id'], ':country_code'=>$data['country'][$i]['country_code'], ':male'=>$data['country'][$i]['male'], ':new_male'=>$data['country'][$i]['new_male'], ':female'=>$data['country'][$i]['female'], ':new_female'=>$data['country'][$i]['new_female'], ':note'=>base64_decode($data['country'][$i]['note'])));
            } 
            return $this->dbQuery('agent_academic_agency_class', array('agency_id'=>$data['agency_id'], 'era_id'=>$data['era_id'], 'quarter'=>$data['quarter']));
            break;
        case 'agent_academic_agency_contact':
            $sql = 'SELECT `id` FROM `academic_era` WHERE `state` = :state';
            $era = $this->dbSelect($sql, array(':state'=>1));
            $sql = 'SELECT * FROM `academic_agency_contact` WHERE `agency_id` = :agency_id AND `era_id` = :era_id ORDER by `manager` DESC, `id` DESC';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$era[0]['id']));
            break;
        case 'agent_academic_agency_contact_add':
            $sql = 'SELECT * FROM `academic_era` WHERE `state` = :state';
            $era = $this->dbSelect($sql, array(':state'=>1));
            
            if (1 == $data['primary']) {
                $sql = 'UPDATE `academic_agency_contact` SET `primary` = 0 WHERE `agency_id` = :agency_id AND `era_id` = :era_id';
                $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id'], 'era_id'=>$era[0]['id']));
            }
            $manager = 0;
            $staff = 1;
            if (1 == $data['manager']) {
                $sql = 'UPDATE `academic_agency_contact` SET `manager` = 0, `staff` = 1 WHERE `agency_id` = :agency_id AND `era_id` = :era_id';
                $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$era[0]['id']));
                $manager = 1;
                $staff = $data['staff'];
            }
            $sql = 'INSERT INTO `academic_agency_contact` (`id`, `agency_id`, `era_id`, `cname`, `title`, `manager`, `staff`, `role`, `area_code`, `phone`, `ext`, `email`, `spare_email`, `primary`) VALUES (0, :agency_id, :era_id, :cname, :title, :manager, :staff, :role, :area_code, :phone, :ext, :email, :spare_email, :primary)';
            $id = $this->dbInsert($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$era[0]['id'], ':cname'=>$data['cname'], ':title'=>$data['title'], ':manager'=>$manager, ':staff'=>$staff, ':role'=>$data['role'], ':area_code'=>$data['area_code'], ':phone'=>$data['phone'], ':ext'=>$data['ext'], ':email'=>$data['email'], ':spare_email'=>$data['spare_email'], ':primary'=>$data['primary']));
            return $this->dbQuery('agent_academic_agency_contact', array('agency_id'=>$data['agency_id']));
            break;
        case 'agent_academic_agency_contact_del':
            $sql = 'DELETE FROM `academic_agency_contact` WHERE `agency_id` = :agency_id AND `id` = :id';
            $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id'], ':id'=>$data['id']));
            return $this->dbQuery('agent_academic_agency_contact', array('agency_id'=>$data['agency_id']));
            break;
        case 'agent_academic_agency_contact_mod':
            $sql = 'SELECT `id` FROM `academic_era` WHERE `state` = :state';
            $era = $this->dbSelect($sql, array(':state'=>1));

            if (1 == $data['primary']) {
                $sql = 'UPDATE `academic_agency_contact` SET `primary` = 0 WHERE `agency_id` = :agency_id AND `era_id` = :era_id';
                $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$era[0]['id']));
            }
            $manager = 0;
            $staff = 1;
            if (1 == $data['manager']) {
                $sql = 'UPDATE `academic_agency_contact` SET `manager` = 0, `staff` = 1 WHERE `agency_id` = :agency_id AND era_id = :era_id';
                $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$era[0]['id']));
                $manager = 1;
                $staff = $data['staff'];
            }
            $sql = 'UPDATE `academic_agency_contact` SET `cname` = :cname, `title` = :title, `manager` = :manager, `staff` = :staff, `role` = :role, `area_code` = :area_code, `phone` = :phone, `ext` = :ext, `email` = :email, `spare_email` = :spare_email, `primary` = :primary WHERE `agency_id` = :agency_id AND `id` = :id';
            $cnt = $this->dbUpdate($sql, array(':cname'=>$data['cname'], ':title'=>$data['title'], ':manager'=>$manager, ':staff'=>$staff, ':role'=>$data['role'], ':area_code'=>$data['area_code'], ':phone'=>$data['phone'], ':ext'=>$data['ext'], ':email'=>$data['email'], ':spare_email'=>$data['spare_email'], ':primary'=>$data['primary'], ':agency_id'=>$data['agency_id'], ':id'=>$data['id']));
            return $this->dbQuery('agent_academic_agency_contact', array('agency_id'=>$data['agency_id']));
            break;
        case 'agent_academic_agency_class_country':
            $sql  = 'SELECT t1.*';
            $sql .= '  FROM `academic_agency_class_country` t1';
            $sql .= ' INNER JOIN `academic_agency_class` t2 ON t2.`id` = t1.`class_id`';
            $sql .= ' WHERE t2.`id` = :id';
            return $this->dbSelect($sql, array(':id'=>$data['id']));
            break;
        case 'agent_academic_agency_class_country_add':

            break;
        case 'agent_academic_agency_class_country_del':

            break;
        case 'agent_academic_agency_class_country_mod':

            break;
        case 'agent_academic_agency_hr':
            $sql  = 'SELECT t1.*, t2.`code` `academic_era_code`';
            $sql .= '  FROM `academic_agency_hr` t1';
            $sql .= ' INNER JOIN `academic_era` t2 ON t2.`id` = t1.`era_id`';
            $sql .= ' WHERE t1.`agency_id` = :agency_id ORDER by t1.`era_id` DESC';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            break;
        case 'agent_academic_agency_hr_add':
            $sql = 'SELECT `era_id` FROM `academic_agency_hr` WHERE agency_id = :agency_id ORDER by `agency_id` DESC LIMIT 1';
            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            if (1 == sizeof($res)) {
                $sql = 'SELECT * FROM `academic_era` WHERE `id` > :id ORDER BY `id` ASC LIMIT 1';
                $res = $this->dbSelect($sql, array(':id'=>$res[0]['era_id']));
                if (1 == sizeof($res)) {
                    $sql = 'INSERT INTO `academic_agency_hr` (`agency_id`, `era_id`, `administration`, `subject`, `adjunct`, `reserve`, `others`, `note`, `state`) VALUES (:agency_id, :era_id, 0, 0, 0, 0, 0, "", 0)';
                    $id = $this->dbInsert($sql, array(":agency_id"=>$data['agency_id'], ":era_id"=>$res[0]['id']));
                }
            } else {
                $sql = 'SELECT * FROM `academic_era` WHERE state = :state ORDER BY `id` ASC LIMIT 1';
                $res = $this->dbSelect($sql, array(':state'=>1));
                if (sizeof($res) > 0) {
                    $sql = 'INSERT INTO `academic_agency_hr` (`agency_id`, `era_id`, `administration`, `subject`, `adjunct`, `reserve`, `others`, `note`, `state`) VALUES (:agency_id, :era_id, 0, 0, 0, 0, 0, "", 0)';
                    $id = $this->dbInsert($sql, array(":agency_id"=>$data['agency_id'], ":era_id"=>$res[0]['id']));
                }
            }
            
            return $this->dbQuery('agent_academic_agency_hr', array('agency_id'=>$data['agency_id']));
            break;
        case 'agent_academic_agency_hr_mod':
            $sql  = 'UPDATE `academic_agency_hr` SET `administration` = :administration, `subject` = :subject, `adjunct` = :adjunct, `reserve` = :reserve, `others` = :others, `note` = :note, `state` = 1 WHERE `agency_id` = :agency_id AND `era_id` = :era_id';
            $cnt = $this->dbUpdate($sql, array(':administration'=>$data['administration'], ':subject'=>$data['subject'], ':adjunct'=>$data['adjunct'], ':reserve'=>$data['reserve'], ':others'=>$data['others'], ':note'=>$data['note'], ':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id']));
            return $this->dbQuery('agent_academic_agency_hr', array('agency_id'=>$data['agency_id']));
            break;
        case 'agent_academic_agency_report_countries':
            $sql  = 'SELECT IFNULL(COUNT(DISTINCT t2.`country_code`), 0) `countries`, t1.*';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= ' INNER JOIN `academic_agency_class_country` t2 ON t2.`class_id` = t1.`id`';
            $sql .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`quarter` = :quarter';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            break;

        // stand alone agency report start
        case 'agent_academic_agency_report_era_detail':
            $sql  = 'SELECT t1.`era_id`, t1.`quarter`, t1.`major_code`, t1.`minor_code`, t2.`cname` `minor_code_cname`, t3.`cname` `major_code_cname`, 0 `new_people`, 0 `people`, SUM(t1.`weekly`) `weekly`, SUM(t1.`hours`) `hours`,';
            //$sql .= 'TRUNCATE(SUM(t1.`weekly`)/(SELECT COUNT(*) FROM `academic_agency_class` t5 WHERE t5.`agency_id` = t1.`agency_id` AND t5.`era_id` = t1.`era_id` AND t5.`quarter` = t1.`quarter` AND t5.`minor_code` = t1.`minor_code`), 2) `avg_weekly`, ';
            $sql .= '0 `avg_weekly`, ';
            $sql .= 'SUM(t1.`hours`) `hours`, SUM(t1.`total_hours`) `total_hours`, SUM(t1.`turnover`) `turnover`, ';
            //$sql .= '(SELECT COUNT(*) FROM `academic_agency_class` t5 WHERE t5.`agency_id` = t1.`agency_id` AND t5.`era_id` = t1.`era_id` AND t5.`quarter` = t1.`quarter` AND t5.`minor_code` = t1.`minor_code`) `classes`, ';
            $sql .= 'COUNT(*) `classes`, ';
            $sql .= 'GROUP_CONCAT(t1.`note` SEPARATOR " ") `note`,';
            $sql .= 'MAX(t1.`latest`) `latest`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= ' INNER JOIN `academic_class` t2 ON t1.`era_id` = t2.`era_id` AND t1.`minor_code` = t2.`minor_code`';
            $sql .= ' INNER JOIN `major_list` t3 ON t1.`major_code` = t3.`code`';
            $sql .= ' WHERE t1.`agency_id` = :agency_id';
            $sql .= '   AND t1.`era_id` = :era_id';

            switch($data['quarter'])
            {
            case 5:
                $quarters = '1,2';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 6:
                $quarters = '2,3';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 7:
                $quarters = '3,4';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 8:
                $quarters = '1,2,3';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 9:
                $quarters = '2,3,4';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 10:
                $quarters = '1,2,3,4';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 11:
                break;
            default:
                $quarters = $data['quarter'];
                $sql .= '   AND t1.`quarter` = '. $quarters ;
            }
            $sql .= ' GROUP BY t1.`major_code`, t1.`quarter`, t1.`minor_code`';
            $sql .= ' ORDER BY t1.`major_code`, t1.`quarter`, t1.`minor_code`';

            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id']));

            if (sizeof($res)) {
                foreach($res as $key=>$val) {
                    $res[$key]['avg_weekly'] = number_format(floatval($val['weekly']/$val['classes']), 2);

                    $sql  = 'SELECT SUM(IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0)) `new_people`, SUM(IFNULL(t2.`male`, 0) + IFNULL(t2.`female`, 0) + IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0)) `people`';
                    $sql .= '  FROM `academic_agency_class` t1';
                    $sql .= '  LEFT JOIN `academic_agency_class_country` t2 ON t2.`class_id` = t1.`id`';
                    $sql .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`quarter` = :quarter AND t1.`minor_code` = :minor_code';
                    $sql .= ' GROUP BY t1.`minor_code`';
                    $r = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$val['quarter'], ':minor_code'=>$val['minor_code']));
                    $res[$key]['new_people'] = $r[0]['new_people'];
                    $res[$key]['people'] = $r[0]['people'];

                    $sql  = 'SELECT GROUP_CONCAT(CONCAT(t1.`cname`, "-", t2.`cname`, "-", t3.`cname`) SEPARATOR " ") `cname`';
                    $sql .= '  FROM `academic_agency_class` t1';
                    $sql .= ' INNER JOIN `target_list` t2 ON t1.`target_code` = t2.`code`';               
                    $sql .= ' INNER JOIN `content_list` t3 ON t1.`content_code` = t3.`code`';               
                    $sql .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`quarter` = :quarter AND t1.`minor_code` = :minor_code';
                    $sql .= ' GROUP BY t1.`minor_code`';
                    $r = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$val['quarter'], ':minor_code'=>$val['minor_code']));
                    $res[$key]['info'] = $r[0]['cname'];

                    $sql  = 'SELECT t1.`country_code`, t2.`cname` `country_code_cname`, SUM(t1.`new_male`) `new_male`, SUM(t1.`new_female`) `new_female`, (SUM(t1.`new_male`) + SUM(t1.`new_female`) + SUM(t1.`male`) + SUM(t1.`female`)) `people`';
                    $sql .= '  FROM `academic_agency_class_country` t1';
                    $sql .= ' INNER JOIN `country_list` t2 ON t1.`country_code` = t2.`code`';
                    $sql .= ' WHERE t1.`class_id` in (SELECT `id` FROM `academic_agency_class` WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter AND `minor_code` = :minor_code)';
                    $sql .= ' GROUP by t1.`country_code`';
                    $r = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$val['quarter'], ':minor_code'=>$val['minor_code']));
                    $res[$key]['country'] = $r;
                }
            }
            return $res;
            break;
        case 'agent_academic_agency_report_era_summary':
            // get data from class
            $sql  = 'SELECT t1.`era_id`, t1.`quarter`, t1.`major_code`, t1.`minor_code`, 0 `new_people`, 0 `people`, COUNT(*) `classes`, SUM(t1.`weekly`) `weekly`, SUM(t1.`hours`) `hours`, SUM(t1.`total_hours`) `total_hours`, SUM(t1.`turnover`) `turnover`, GROUP_CONCAT(t1.`note` SEPARATOR " ") `note`, IFNULL(MAX(t1.`latest`), "") `latest`, ';
            //$sql .= 'TRUNCATE(SUM(t1.`weekly`)/(SELECT COUNT(*) FROM `academic_agency_class` t2 WHERE t2.`agency_id` = t1.`agency_id` AND t2.`era_id` = t1.`era_id` AND t2.`quarter` = t1.`quarter` AND t2.`minor_code` = t1.`minor_code`),2) `avg_weekly`, t3.`cname` `minor_code_cname` ';
            //$sql .= 'TRUNCATE(SUM(t1.`weekly`)/COUNT(*)), 2) `avg_weekly`, ';
            $sql .= 't3.`cname` `minor_code_cname`, ';
            $sql .= '0 `avg_weekly` ';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= ' INNER JOIN `academic_class` t3 ON t1.`era_id` = t3.`era_id` AND t1.`minor_code` = t3.`minor_code`';
            $sql .= ' WHERE t1.`agency_id` = :agency_id';
            $sql .= '   AND t1.`era_id` = :era_id';

            switch($data['quarter'])
            {
            case 10:
                $quarters = '1,2,3,4';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 5:
                $quarters = '1,2';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 6:
                $quarters = '2,3';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 7:
                $quarters = '3,4';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 8:
                $quarters = '1,2,3';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 9:
                $quarters = '2,3,4';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            default:
                $sql .= '   AND t1.`quarter` = ' . $data['quarter'];
            }

            $sql .= ' GROUP BY t1.`major_code`, t1.`quarter`, t1.`minor_code`';
            $sql .= ' ORDER BY t1.`major_code`, t1.`quarter`, t1.`minor_code`';
            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id']));
            if (sizeof($res)) {
                foreach($res as $key=>$val) {
                    $res[$key]['avg_weekly'] = number_format(floatval($val['weekly']/$val['classes']), 2);
                    // country
                    $sql  = 'SELECT SUM(IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0)) `new_people`, SUM(IFNULL(t2.`male`, 0) + IFNULL(t2.`female`, 0) + IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0)) `people`';
                    $sql .= '  FROM `academic_agency_class` t1';
                    $sql .= '  LEFT JOIN `academic_agency_class_country` t2 ON t2.`class_id` = t1.`id`';
                    $sql .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`quarter` = :quarter AND t1.`minor_code` = :minor_code';
                    $sql .= ' GROUP BY t1.`minor_code`';
                    $r = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$val['quarter'], ':minor_code'=>$val['minor_code']));
                    $res[$key]['new_people'] = $r[0]['new_people'];
                    $res[$key]['people'] = $r[0]['people'];
                    // info 
                    $sql  = 'SELECT GROUP_CONCAT(CONCAT(t1.`cname`, "-", t2.`cname`, "-", t3.`cname`) SEPARATOR " ") `cname`';
                    $sql .= '  FROM `academic_agency_class` t1';
                    $sql .= ' INNER JOIN `target_list` t2 ON t1.`target_code` = t2.`code`';               
                    $sql .= ' INNER JOIN `content_list` t3 ON t1.`content_code` = t3.`code`';               
                    $sql .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`quarter` = :quarter AND t1.`minor_code` = :minor_code';
                    $sql .= ' GROUP BY t1.`minor_code`';
                    $r = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$val['quarter'], ':minor_code'=>$val['minor_code']));
                    $res[$key]['info'] = $r[0]['cname'];
                }
            }

            return $res;
            // get data from country

            break;

        case 'agent_academic_agency_report_era_pdf':

            /* academic_agency_report_era */
            $sql  = 'SELECT t1.`era_id`, t1.`quarter`, t1.`major_code`, t1.`minor_code`, t2.`cname` `minor_code_cname`, 0 `new_people`, 0 `people`, SUM(t1.`weekly`) `weekly`, SUM(t1.`hours`) `hours`,';
            //$sql .= 'TRUNCATE(SUM(t1.`weekly`)/(SELECT COUNT(*) FROM `academic_agency_class` t5 WHERE t5.`agency_id` = t1.`agency_id` AND t5.`era_id` = t1.`era_id` AND t5.`quarter` = t1.`quarter` AND t5.`minor_code` = t1.`minor_code`),2) `avg_weekly`, ';
            //$sql .= 'TRUNCATE(SUM(t1.`weekly`)/COUNT(*)), 2) `avg_weekly`, ';
            $sql .= '0 `avg_weekly`, ';
            $sql .= 'SUM(t1.`hours`) `hours`, SUM(t1.`total_hours`) `total_hours`, SUM(t1.`turnover`) `turnover`, ';
            //$sql .= '(SELECT COUNT(*) FROM `academic_agency_class` t5 WHERE t5.`agency_id` = t1.`agency_id` AND t5.`era_id` = t1.`era_id` AND t5.`quarter` = t1.`quarter` AND t5.`minor_code` = t1.`minor_code`) `classes`, ';
            $sql .= 'COUNT(*) `classes`, ';
            $sql .= 'GROUP_CONCAT(t1.`note` SEPARATOR " ") `note`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= ' INNER JOIN `academic_class` t2 ON t1.`era_id` = t2.`era_id` AND t1.`minor_code` = t2.`minor_code`';
            $sql .= ' WHERE t1.`agency_id` = :agency_id';
            $sql .= '   AND t1.`era_id` = :era_id';
            $sql .= ' GROUP BY t1.`minor_code`';

            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id']));

            if (sizeof($res)) {
                foreach($res as $key=>$val) {
                    $res[$key]['avg_weekly'] = number_format(floatval($val['weekly']/$val['classes']), 2);
                    $res[$key]['new_people'] = 0;
                    $res[$key]['people'] = 0;
                    // country
                    $sql  = 'SELECT SUM(IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0)) `new_people`, SUM(IFNULL(t2.`male`, 0) + IFNULL(t2.`female`, 0) + IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0)) `people`';
                    $sql .= '  FROM `academic_agency_class` t1';
                    $sql .= ' INNER JOIN `academic_agency_class_country` t2 ON t2.`class_id` = t1.`id`';
                    $sql .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`minor_code` = :minor_code';
                    $sql .= ' GROUP BY t1.`minor_code`';
                    $r = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':minor_code'=>$val['minor_code']));
                    if (sizeof($r)) {
                        $res[$key]['new_people'] = $r[0]['new_people'];
                        $res[$key]['people'] = $r[0]['people'];
                    }
                    // info 
                    $str  = 'SELECT GROUP_CONCAT(CONCAT(t1.`cname`, "-", t2.`cname`, "-", t3.`cname`) SEPARATOR " ") `cname`';
                    $str .= '  FROM `academic_agency_class` t1';
                    $str .= ' INNER JOIN `target_list` t2 ON t1.`target_code` = t2.`code`';               
                    $str .= ' INNER JOIN `content_list` t3 ON t1.`content_code` = t3.`code`';               
                    $str .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`minor_code` = :minor_code';
                    $str .= ' GROUP BY t1.`minor_code`';
                    $r = $this->dbSelect($str, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':minor_code'=>$val['minor_code']));
                    $res[$key]['info'] = $r[0]['cname'];
                }
            }

            return $res; 
            break;
        case 'agent_academic_agency_report_era_taken':
            $sql  = 'SELECT t1.*, t2.`taken`';
            $sql .= '  FROM `academic_class` t1';
            $sql .= ' INNER JOIN `academic_era` t2 ON t1.`era_id` = t2.`id`';
            $sql .= ' WHERE t1.`era_id` = :era_id AND t1.`state` = 0';
            return $this->dbSelect($sql, array(':era_id'=>$data['era_id']));
            break;
        // stand alone agency report end
        case 'agent_academic_agency_report_summary':
            /* academic_agency_report_quarter */
            /* 0:1~4, 1:1, 2:2, 3:3, 4:4, 5:1~2, 6:2~3, 7:3~4, 8:1~3, 9:2~4 */
            //$sql  = 'SELECT t1.`era_id`, t1.`quarter`, t1.`major_code`, t1.`minor_code`, t2.`cname` `minor_code_cname`, SUM(t1.`new_people`) `new_people`, SUM(t1.`people`) `people`, SUM(t1.`weekly`) `weekly`, ';
            $sql  = 'SELECT t1.`era_id`, t1.`quarter`, t1.`major_code`, t1.`minor_code`, t2.`cname` `minor_code_cname`, SUM(IFNULL(t3.`new_male`, 0) + IFNULL(t3.`new_female`, 0)) `new_people`, SUM(IFNULL(t3.`male`, 0) + IFNULL(t3.`female`, 0) + IFNULL(t3.`new_male`, 0) + IFNULL(t3.`new_female`, 0)) `people`, SUM(IFNULL(t1.`weekly`, 0)) `weekly`,';
            //$sql .= 'TRUNCATE(SUM(t1.`weekly`)/(SELECT COUNT(*) FROM `academic_agency_class` t5 WHERE t5.`agency_id` = t1.`agency_id` AND t5.`era_id` = t1.`era_id` AND t5.`quarter` = t1.`quarter` AND t5.`minor_code` = t1.`minor_code`),2) `avg_weekly`, ';
            //$sql .= 'TRUNCATE(SUM(t1.`weekly`)/COUNT(*)), 2) `avg_weekly`, ';
            $sql .= '0 `avg_weekly`, ';
            $sql .= 'SUM(t1.`hours`) `hours`, SUM(t1.`total_hours`) `total_hours`, SUM(t1.`turnover`) `turnover`, ';
            //$sql .= '(SELECT COUNT(*) FROM `academic_agency_class` t5 WHERE t5.`agency_id` = t1.`agency_id` AND t5.`era_id` = t1.`era_id` AND t5.`quarter` = t1.`quarter` AND t5.`minor_code` = t1.`minor_code`) `classes`, ';
            $sql .= 'COUNT(*) `classes`, ';
            $sql .= 'GROUP_CONCAT(t1.`note` SEPARATOR " ") `note`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= ' INNER JOIN `academic_class` t2 ON t1.`era_id` = t2.`era_id` AND t1.`minor_code` = t2.`minor_code`';
            $sql .= '  LEFT JOIN `academic_agency_class_country` t3 ON t3.`class_id` = t1.`id`';
            $sql .= ' WHERE t1.`agency_id` = :agency_id';
            $sql .= '   AND t1.`era_id` = :era_id';
            
            switch($data['quarter'])
            {
            case 10:
                $quarters = '1,2,3,4';
                break;
            case 5:
                $quarters = '1,2';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 6:
                $quarters = '2,3';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 7:
                $quarters = '3,4';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 8:
                $quarters = '1,2,3';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 9:
                $quarters = '2,3,4';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            default:
                $quarters = $data['quarter'];
                $sql .= '   AND t1.`quarter` = ' . $data['quarter'];
            }
            $sql .= ' GROUP BY t1.`major_code`, t1.`minor_code`';
            $sql .= ' ORDER BY t1.`major_code`, t1.`minor_code`';

            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id']));

            if (sizeof($res)) {
                foreach($res as $key=>$val) {
                    $res[$key]['avg_weekly'] = number_format(floatval($val['weekly']/$val['classes']), 2);
                    $str  = 'SELECT GROUP_CONCAT(CONCAT(t1.`cname`, "-", t2.`cname`, "-", t3.`cname`) SEPARATOR " ") `cname`';
                    $str .= '  FROM `academic_agency_class` t1';
                    $str .= ' INNER JOIN `target_list` t2 ON t1.`target_code` = t2.`code`';               
                    $str .= ' INNER JOIN `content_list` t3 ON t1.`content_code` = t3.`code`';               
                    $str .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`quarter` = :quarter AND t1.`minor_code` = :minor_code';
                    $str .= ' GROUP BY t1.`minor_code`';
                    $r = $this->dbSelect($str, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$val['quarter'], ':minor_code'=>$val['minor_code']));
                    $res[$key]['info'] = $r[0]['cname'];
                }
            }

            return $res; 
            break;
        case 'agent_academic_agency_report_detail':
            /* academic_agency_report_quarter */
            /* 0:1~4, 1:1, 2:2, 3:3, 4:4, 5:1~2, 6:2~3, 7:3~4, 8:1~3, 9:2~4 */

            //$sql  = 'SELECT t1.`era_id`, t1.`quarter`, t1.`major_code`, t1.`minor_code`, t2.`cname` `minor_code_cname`, t3.`cname` `major_code_cname`, SUM(t1.`new_people`) `new_people`, SUM(t1.`people`) `people`, SUM(t1.`weekly`) `weekly`, ';
            $sql  = 'SELECT t1.`era_id`, t1.`quarter`, t1.`major_code`, t1.`minor_code`, t2.`cname` `minor_code_cname`, t3.`cname` `major_code_cname`, SUM(IFNULL(t4.`new_male`, 0) + IFNULL(t4.`new_female`, 0)) `new_people`, SUM(IFNULL(t4.`male`, 0) + IFNULL(t4.`female`, 0) + IFNULL(t4.`new_male`, 0) + IFNULL(t4.`new_female`, 0)) `people`, SUM(IFNULL(t1.`weekly`, 0)) `weekly`, SUM(IFNULL(t1.`hours`, 0)) `hours`,';
            //$sql .= 'TRUNCATE(SUM(t1.`weekly`)/(SELECT COUNT(*) FROM `academic_agency_class` t5 WHERE t5.`agency_id` = t1.`agency_id` AND t5.`era_id` = t1.`era_id` AND t5.`quarter` = t1.`quarter` AND t5.`minor_code` = t1.`minor_code`), 2) `avg_weekly`, ';
            //$sql .= 'TRUNCATE(SUM(t1.`weekly`)/COUNT(*)), 2) `avg_weekly`, ';
            $sql .= '0 `avg_weekly`, ';
            $sql .= 'SUM(IFNULL(t1.`hours`, 0)) `hours`, SUM(IFNULL(t1.`total_hours`, 0)) `total_hours`, SUM(IFNULL(t1.`turnover`, 0)) `turnover`, ';
            //$sql .= '(SELECT COUNT(*) FROM `academic_agency_class` t5 WHERE t5.`agency_id` = t1.`agency_id` AND t5.`era_id` = t1.`era_id` AND t5.`quarter` = t1.`quarter` AND t5.`minor_code` = t1.`minor_code`) `classes`, ';
            $sql .= 'COUNT(*) `classes`, ';
            $sql .= 'GROUP_CONCAT(t1.`note` SEPARATOR " ") `note`,';
            $sql .= 'MAX(t1.`latest`) `latest`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= ' INNER JOIN `academic_class` t2 ON t1.`era_id` = t2.`era_id` AND t1.`minor_code` = t2.`minor_code`';
            $sql .= ' INNER JOIN `major_list` t3 ON t1.`major_code` = t3.`code`';
            $sql .= '  LEFT JOIN `academic_agency_class_country` t4 ON t4.`class_id` = t1.`id`';
            $sql .= ' WHERE t1.`agency_id` = :agency_id';
            $sql .= '   AND t1.`era_id` = :era_id';

            switch($data['quarter'])
            {
            case 5:
                $quarters = '1,2';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 6:
                $quarters = '2,3';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 7:
                $quarters = '3,4';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 8:
                $quarters = '1,2,3';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 9:
                $quarters = '2,3,4';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 10:
                $quarters = '1,2,3,4';
                $sql .= '   AND t1.`quarter` IN ('. $quarters .')';
                break;
            case 11:
                break;
            default:
                $quarters = $data['quarter'];
                $sql .= '   AND t1.`quarter` = '. $quarters ;
            }
            $sql .= ' GROUP BY t1.`major_code`, t1.`quarter`, t1.`minor_code`';
            $sql .= ' ORDER BY t1.`major_code`, t1.`quarter`, t1.`minor_code`';

            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id']));

            if (sizeof($res)) {
                foreach($res as $key=>$val) {
                    $res[$key]['avg_weekly'] = number_format(floatval($val['weekly']/$val['classes']), 2);
                    $str  = 'SELECT GROUP_CONCAT(CONCAT(t1.`cname`, "-", t2.`cname`, "-", t3.`cname`) SEPARATOR " ") `cname`';
                    $str .= '  FROM `academic_agency_class` t1';
                    $str .= ' INNER JOIN `target_list` t2 ON t1.`target_code` = t2.`code`';               
                    $str .= ' INNER JOIN `content_list` t3 ON t1.`content_code` = t3.`code`';               
                    $str .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`quarter` = :quarter AND t1.`minor_code` = :minor_code';
                    $str .= ' GROUP BY t1.`minor_code`';
                    $r = $this->dbSelect($str, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$val['quarter'], ':minor_code'=>$val['minor_code']));
                    $res[$key]['info'] = $r[0]['cname'];

                    $str  = 'SELECT t1.`country_code`, t2.`cname` `country_code_cname`, SUM(t1.`new_male`) `new_male`, SUM(t1.`new_female`) `new_female`, (SUM(t1.`new_male`) + SUM(t1.`new_female`) + SUM(t1.`male`) + SUM(t1.`female`)) `people`';
                    $str .= '  FROM `academic_agency_class_country` t1';
                    $str .= ' INNER JOIN `country_list` t2 ON t1.`country_code` = t2.`code`';
                    $str .= ' WHERE t1.`class_id` in (SELECT `id` FROM `academic_agency_class` WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter AND `minor_code` = :minor_code)';
                    $str .= ' GROUP by t1.`country_code`';
                    $r = $this->dbSelect($str, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$val['quarter'], ':minor_code'=>$val['minor_code']));
                    $res[$key]['country'] = $r;
                }
            }
            return $res;
            break;
        case 'agent_academic_agency_report_pdf':

            /* academic_agency_report_era */
            $sql  = 'SELECT t1.`era_id`, t1.`quarter`, t1.`major_code`, t1.`minor_code`, t2.`cname` `minor_code_cname`, 0 `new_people`, 0 `people`, SUM(t1.`weekly`) `weekly`, SUM(t1.`hours`) `hours`,';
            //$sql .= 'TRUNCATE(SUM(t1.`weekly`)/(SELECT COUNT(*) FROM `academic_agency_class` t5 WHERE t5.`agency_id` = t1.`agency_id` AND t5.`era_id` = t1.`era_id` AND t5.`quarter` = t1.`quarter` AND t5.`minor_code` = t1.`minor_code`),2) `avg_weekly`, ';
            //$sql .= 'TRUNCATE(SUM(t1.`weekly`)/COUNT(*)), 2) `avg_weekly`, ';
            $sql .= '0 `avg_weekly`, ';
            $sql .= 'SUM(t1.`hours`) `hours`, SUM(t1.`total_hours`) `total_hours`, SUM(t1.`turnover`) `turnover`, ';
            //$sql .= '(SELECT COUNT(*) FROM `academic_agency_class` t5 WHERE t5.`agency_id` = t1.`agency_id` AND t5.`era_id` = t1.`era_id` AND t5.`quarter` = t1.`quarter` AND t5.`minor_code` = t1.`minor_code`) `classes`, ';
            $sql .= 'COUNT(*) `classes`, ';
            $sql .= 'GROUP_CONCAT(t1.`note` SEPARATOR " ") `note`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= ' INNER JOIN `academic_class` t2 ON t1.`era_id` = t2.`era_id` AND t1.`minor_code` = t2.`minor_code`';
            $sql .= ' WHERE t1.`agency_id` = :agency_id';
            $sql .= '   AND t1.`era_id` = :era_id';
            $sql .= ' GROUP BY t1.`minor_code`';

            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id']));

            if (sizeof($res)) {
                foreach($res as $key=>$val) {
                    $res[$key]['avg_weekly'] = number_format(floatval($val['weekly']/$val['classes']), 2);
                    // country
                    $sql  = 'SELECT SUM(IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0)) `new_people`, SUM(IFNULL(t2.`male`, 0) + IFNULL(t2.`female`, 0) + IFNULL(t2.`new_male`, 0) + IFNULL(t2.`new_female`, 0)) `people`';
                    $sql .= '  FROM `academic_agency_class` t1';
                    $sql .= ' INNER JOIN `academic_agency_class_country` t2 ON t2.`class_id` = t1.`id`';
                    $sql .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`minor_code` = :minor_code';
                    //$sql .= ' GROUP BY t1.`minor_code`';
                    $r = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':minor_code'=>$val['minor_code']));
                    $res[$key]['new_people'] = $r[0]['new_people'];
                    $res[$key]['people'] = $r[0]['people'];
                    // info 
                    $sql  = 'SELECT GROUP_CONCAT(CONCAT(t1.`cname`, "-", t2.`cname`, "-", t3.`cname`) SEPARATOR " ") `cname`';
                    $sql .= '  FROM `academic_agency_class` t1';
                    $sql .= ' INNER JOIN `target_list` t2 ON t1.`target_code` = t2.`code`';               
                    $sql .= ' INNER JOIN `content_list` t3 ON t1.`content_code` = t3.`code`';               
                    $sql .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`minor_code` = :minor_code';
                    $sql .= ' GROUP BY t1.`minor_code`';
                    $r = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':minor_code'=>$val['minor_code']));
                    $res[$key]['info'] = $r[0]['cname'];
                }
            }

            return $res; 
            break;
        case 'agent_academic_agency_report_taken':
            $sql  = 'SELECT t1.*, t2.`taken`';
            $sql .= '  FROM `academic_class` t1';
            $sql .= ' INNER JOIN `academic_era` t2 ON t1.`era_id` = t2.`id`';
            $sql .= ' WHERE t1.`era_id` = :era_id AND t1.`state` = 0';
            return $this->dbSelect($sql, array(':era_id'=>$data['era_id']));
            break;
        case 'agent_academic_agency_unlock':
            $sql = 'DELETE FROM `academic_agency_unlock` WHERE `agency_id` = :agency_id';
            $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id']));
            $sql = 'SELECT * FROM `academic_agency_class_status` WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter';
            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            $sql = 'SELECT * FROM `academic_agency_class` WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter';
            $classes = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            if (sizeof($res)) {
                $sql = 'UPDATE `academic_agency_class_status` SET `classes` = :classes, `unlock` = 1, `minors` = :minors, `work_days` = :work_days, `online` = "", `offline` = "", `note` = :note WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter';  
                $cnt = $this->dbUpdate($sql, array(':classes'=>sizeof($classes), ':minors'=>$data['minors'], ':work_days'=>$data['work_days'], ':note'=>$data['note'], ':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            } else {
                $sql = 'INSERT INTO `academic_agency_class_status` (`id`, `agency_id`, `era_id`, `quarter`, `classes`, `unlock`, `minors`, `work_days`, `online`, `offline`, `note`, `state`) VALUES (0, :agency_id, :era_id, :quarter, :classes, 1, :minors, :work_days, "", "", :note, 0)';

                $id = $this->dbInsert($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter'], ':classes'=>sizeof($classes), ':minors'=>$data['minors'], ':work_days'=>$data['work_days'], ':note'=>$data['note']));
            }
            $sql = 'INSERT INTO `academic_agency_unlock` (`id`, `agency_id`, `era_id`, `quarter`, `minors`, `work_days`, `online`, `offline`, `note`, `state`) VALUES (0, :agency_id, :era_id, :quarter, :minors, :work_days, "", "", :note, 0)';
            return $this->dbInsert($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter'], ':minors'=>$data['minors'], ':work_days'=>$data['work_days'], ':note'=>$data['note']));
            break;
        case 'agent_academic_agency_unlock_who':
            $sql  = 'SELECT ai.`cname` academic_institution_cname, ae.`cname` academic_era_cname';
            $sql .= '  FROM `academic_agency` aa ';
            $sql .= ' INNER JOIN `academic_institution` ai ON aa.`institution_code` = ai.`code`';
            $sql .= ' INNER JOIN `academic_era` ae ON ae.`era_id` = :era_id';
            $sql .= ' WHERE aa.`id` = :agency_id';
            return $this->dbSelect($sql, array(':era_id'=>$data['era_id'], ':agency_id'=>$data['agency_id']));
            break;
        case 'agent_academic_institution_aka':
            $sql = 'SELECT `aka` FROM `academic_institution` WHERE `code` = :code';
            return $this->dbSelect($sql, array(':code'=>$data['code']));
            break;
        case 'admin_profile_email_mod':
            $sql = 'UPDATE `admin` SET `email` = :email WHERE username = :username AND session= :session ';
            return $this->dbUpdate($sql, array(':email'=>$data['email'], ':username'=>$data['username'],':session'=>$data['session']));
            break;
        case 'admin_profile_userpass_mod':
            $sql = 'UPDATE `admin` SET `userpass` = MD5(:userpass) WHERE username = :username AND session = :session ';
            return $this->dbUpdate($sql, array(':userpass'=>MD5Prefix . $data['userpass'] . MD5Suffix, ':username'=>$data['username'], ':session'=>$data['session']));
            break;
        case 'agent_profile_email_mod':
            $sql = 'UPDATE `academic_agency_agent` SET `email` = :email WHERE agency_id = :agency_id AND username = :username';
            return $this->dbUpdate($sql, array(':email'=>$data['email'], ':agency_id'=>$data['agency_id'], ':username'=>$data['username']));
            break;
        case 'agent_profile_userpass_mod':
            $sql = 'UPDATE `academic_agency_agent` SET `userpass` = MD5(:userpass) WHERE agency_id = :agency_id AND username = :username';
            return $this->dbUpdate($sql, array(':userpass'=>MD5Prefix . $data['userpass'] . MD5Suffix, ':agency_id'=>$data['agency_id'], ':username'=>$data['username']));
            break;
        case 'agent_board_question_add':
            $sql = 'INSERT INTO `academic_board` (`agent_id`,`question_content`,`insert_date`) values(:agent_id,:question_content,NOW() ) ';
            return $this->dbInsert($sql, array(':agent_id'=>$data['agent_id'], ':question_content'=>$data['question_content']));
            break;
        case 'agent_board_reply_query':
            $sql = 'SELECT t.*, a.`username` FROM `academic_board` t LEFT JOIN `admin` a on t.`admin_id` = a.`id` where t.`agent_id` = :agent_id ORDER BY t.`insert_date` DESC';
            /*
            $res = $this->dbSelect($sql, array(':agent_id'=>$data['agent_id']));
            echo json_encode($res);
            */
            return $this->dbSelect($sql, array(':agent_id'=>$data['agent_id']));
            break;
        case 'agent_board_question_who':
            $sql  = 'SELECT ai.`cname` academic_institution_cname';
            $sql .= '  FROM `academic_agency_agent` aaa ';
            $sql .= ' INNER JOIN `academic_agency` aa ON aa.`id` = aaa.`agency_id`';
            $sql .= ' INNER JOIN `academic_institution` ai ON aa.`institution_code` = ai.`code`';
            $sql .= ' WHERE aaa.`id` = :agent_id';
            return $this->dbSelect($sql, array(':agent_id'=>$data['agent_id']));
            break;
        /* mailer */
        case 'mailer_official_get':
            $sql = 'SELECT * FROM `official` WHERE "NTUE" = :ntue ';
            return $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
            break;
        /* references */
        case 'refs_academic_institution':
            $sql = 'SELECT * FROM `academic_institution` WHERE "NTUE" = :ntue ORDER BY `code`';
            return $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
            break;
        case 'refs_academic_agency':
            $sql = 'SELECT * FROM `academic_agency` WHERE "NTUE" = :ntue ORDER BY `id`';
            return $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
            break;
        case 'refs_area_list':
            $sql = 'SELECT * FROM `area_list` WHERE "NTUE" = :ntue ORDER BY `code`';
            return $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
            break;
        case 'refs_content_list':
            $sql = 'SELECT * FROM `content_list` WHERE "NTUE" = :ntue ORDER BY `code`';
            return $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
            break;
        case 'refs_country_list':
            $sql = 'SELECT * FROM `country_list` WHERE "NTUE" = :ntue ORDER BY `code`';
            return $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
            break;
        case 'refs_major_list':
            $sql = 'SELECT * FROM `major_list` WHERE "NTUE" = :ntue ORDER BY `code`';
            return $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
            break;
        case 'refs_minor_list':
            $sql = 'SELECT * FROM `minor_list` WHERE "NTUE" = :ntue ORDER BY `code`';
            return $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
            break;
        case 'refs_target_list':
            $sql = 'SELECT * FROM `target_list` WHERE "NTUE" = :ntue ORDER BY `code`';
            return $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
            break;
        }
    }
}
