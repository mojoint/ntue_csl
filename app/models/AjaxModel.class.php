<?php

class AjaxModel extends Model {
    public function dbQuery( $key, $data = array() ) {
        switch($key)
        {
        /* admin */
        case 'admin_academic_agency_add':
            $sql  = 'INSERT INTO `academic_agency`';
            $sql .= ' (`id`, `institution_code`, `cname`, `zipcode`, `address`, `established`, `approval`, `note`, `agent`)';
            $sql .= ' VALUES (0, :institution_code, :cname, "", "", "", "", "", 0)';
            $res = $this->dbUpdate($sql, array(':institution_code'=>$data['institution_code'], ':cname'=>$data['cname']));
            return $this->dbQuery('admin_academic_agency_get');
            break;
        case 'admin_academic_agency_del':
            $sql  = 'DELETE FROM `academic_agency_agent`';
            $sql .= ' WHERE `agency_id` = :agency_id';
            $res = $this->dbUpdate($sql, array(':agency_id'=>$data['id']));
            $sql  = 'DELETE FROM `academic_agency`';
            $sql .= ' WHERE `id` = :id';
            $res = $this->dbUpdate($sql, array(':id'=>$data['id']));
            return $this->dbQuery('admin_academic_agency_get');
            break;
        case 'admin_academic_agency_get':
            $sql  = 'SELECT t1.*, t2.`cname` AS `institution_cname`, t2.`aka` AS `institution_aka`, IFNULL(t3.`administration`, 0) AS `academic_agency_hr_administration`, IFNULL(t3.`subject`, 0) AS `academic_agency_hr_subject`, IFNULL(t3.`adjunct`, 0) AS `academic_agency_hr_adjunct`, IFNULL(t3.`reserve`, 0) AS `academic_agency_hr_reserve` ';
            $sql .= '  FROM `academic_agency` t1 ';
            $sql .= ' INNER JOIN `academic_institution` t2 ON t1.`institution_code` = t2.`code` ';
            $sql .= '  LEFT JOIN ( SELECT t4.`agency_id`, t4.`administration`, t4.`subject`, t4.`adjunct`, t4.`reserve` FROM `academic_agency_hr` t4 INNER JOIN `academic_era` t5 ON t4.`era_id` = t5.id AND t5.`state` = 1 ) t3 ON t1.`id` = t3.`agency_id` ';
            return $this->dbSelect($sql);
            break;
        case 'admin_academic_agency_mod':
            $sql  = 'UPDATE `academic_agency`';
            $sql .= '   SET `cname` = :cname,';
            $sql .= '       `institution_code` = :institution_code';
            $sql .= ' WHERE `id` = :id';
            $res = $this->dbUpdate($sql, array(':institution_code'=>$data['institution_code'], ':cname'=>$data['cname'], ':id'=>$data['id']));
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
                    $res = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id'], ':username'=>$data['username'], ':userpass'=>$data['userpass'], ':email'=>$data['email'], ':timestamp'=>$data['timestamp']));
                    $sql = 'UPDATE `academic_agency` SET `agent` = `agent` + 1 WHERE `id` = :id';
                    $res = $this->dbUpdate($sql, array(':id'=>$data['agency_id']));
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
            $res = $this->dbUpdate($sql, array(':id'=>$data['id']));
            $sql  = 'UPDATE `academic_agency`';
            $sql .= '   SET `agent` = `agent` -1';
            $sql .= ' WHERE `id` = :id';
            $res = $this->dbUpdate($sql, array(':id'=>$data['agency_id']));
            return $this->dbQuery('admin_academic_agency_agent_get');
            break;
        case 'admin_academic_agency_agent_get':
            $sql  = 'SELECT t1.*, t2.`cname` `academic_agency_cname`';
            $sql .= '  FROM `academic_agency_agent` t1';
            $sql .= ' INNER JOIN `academic_agency` t2 ON t1.`agency_id` = t2.`id`';
            return $this->dbSelect($sql);
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
            $res = $this->dbUpdate($sql, array(':userpass'=>$data['userpass'], ':email'=>$data['email'], ':timestamp'=>$data['timestamp'], ':id'=>$data['id']));
            return $this->dbQuery('admin_academic_agency_agent_get_byid',array('id'=>$data['id']));
            break;
        case 'admin_academic_agency_unlock_yes':
            $sql = 'UPDATE `academic_agency_unlock` SET `state` = 1, `online` = :online, `offline` = :offline WHERE `agency_id` = :agency_id AND `id` = :id';
            $cnt = $this->dbUpdate($sql, array(':online'=>$data['online'], ':offline'=>$data['offline'], ':agency_id'=>$data['agency_id'], ':id'=>$data['id']));
            return $sql;
            break;
        case 'admin_academic_agency_unlock_no':
            $sql = 'DELETE FROM `academic_agency_unlock` WHERE `agency_id` = :agency_id AND `id` = :id';
            $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id'], ':id'=>$data['id']));
            return $sql;
            break;
        case 'admin_academic_era_add':
            $sql = 'SELECT * FROM `academic_era` ORDER BY `id` DESC LIMIT 1';
            $res = $this->dbSelect($sql);
            $sql  = 'INSERT INTO `academic_era` (`id`, `common`, `roc`, `code`, `cname`, `state`)';
            $sql .= ' VALUES (0, :common, :roc, :code, :cname, 0)';
            $common = intval($res[0]['common']) + 1;
            $roc = intval($res[0]['roc']) + 1;
            $code = "Y". $roc;
            $cname = $roc . "年度";
            // add academic_era
            $cnt = $this->dbUpdate($sql, array(':common'=>$common, ':roc'=>$roc, ':code'=>$code, ':cname'=>$cname));
            $sql = 'SELECT * FROM `academic_era` ORDER BY `id` DESC LIMIT 1';
            $res = $this->dbSelect($sql);
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
            $sql = 'INSERT INTO `academic_class` SELECT 0, '. $res[0]['id'] .', `major_code`, `code`, `cname`, 0 FROM `minor_list` WHERE `code` != ""';
            $cnt = $this->dbUpdate($sql);
            return $this->dbQuery('admin_academic_era_quarter');
            break;
        case 'admin_academic_era_quarter':
            $sql  = 'SELECT t1.*';
            $sql .= '  FROM `academic_era_quarter` t1';
            $sql .= ' INNER JOIN `academic_era` t2 ON t1.`era_id` = t2.`id` AND t2.`state` < 2';
            $sql .= ' ORDER BY t1.`era_id` DESC, t1.`id` ASC';
            return $this->dbSelect($sql);
            break;
        case 'admin_academic_era_quarter_mod':
            $sql = 'UPDATE `academic_era_quarter` SET `online` = :online, `offline` = :offline WHERE `id` = :id';
            $cnt = $this->dbUpdate($sql, array(':id'=>$data['id'], ':online'=>$data['online'], ':offline'=>$data['offline']));
            return $this->dbQuery('admin_academic_era_quarter');
            break;
        case 'admin_academic_class_mod':
            for ($i=0; $i<sizeof($data['checks']); $i++) {
              $sql = 'UPDATE `academic_class` SET `state` = 1 WHERE `id` = :id';
              $cnt = $this->dbUpdate($sql, array(':id'=>$data['checks'][$i]));
            }
            break;
        case 'admin_check_new_user_add':
            $sql = 'SELECT count(*) `cnt` FROM `academic_agency_agent` where `username` = :username ';
            return $this->dbSelect($sql, array(':username'=>$data['username']));
            break;
        /* agent */
        case 'agent_academic_agency':
            $sql  = 'SELECT t1.*, t2.`cname` `academic_institution_cname`';
            $sql .= '  FROM `academic_agency` t1';
            $sql .= ' INNER JOIN `academic_institution` t2 ON t2.`code` = t1.`institution_code`';
            $sql .= ' WHERE t1.`id` = :id';
            return $this->dbSelect($sql, array(':id'=>$data['id']));
            break;

        case 'agent_academic_agency_mod':
            $sql = 'UPDATE `academic_agency` SET cname = :cname, zipcode = :zipcode, address = :address, established = :established, approval = :approval, note = :note WHERE id = :id';
            $cnt = $this->dbUpdate($sql, array(':cname'=>$data['cname'], ':zipcode'=>$data['zipcode'], ':address'=>$data['address'], ':established'=>$data['established'], ':approval'=>$data['approval'], ':note'=>$data['note'], ':id'=>$data['id']));
            $res = $this->dbSelect($sql, array(":id"=>$data['id']));
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
            $sql  = 'INSERT INTO `academic_agency_class`';
            $sql .= ' (`id`, `agency_id`, `era_id`, `quarter`, `major_code`, `minor_code`, `cname`, `content_code`, `target_code`, `people`, `weekly`, `weeks`, `hours`, `adjust`, `total_hours`, `revenue`, `subsidy`, `turnover`, `note`, `state`)';
            $sql .= ' VALUES (0, :agency_id, :era_id, :quarter, :major_code, :minor_code, :cname, :content_code, :target_code, :people, :weekly, :weeks, :hours, :adjust, :total_hours, :revenue, :subsidy, :turnover, :note, 0)';
            $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter'], ':major_code'=>$data['major_code'], ':minor_code'=>$data['minor_code'], ':cname'=>$data['cname'], ':content_code'=>$data['content_code'], ':target_code'=>$data['target_code'], ':people'=>$data['people'], ':weekly'=>$data['weekly'], ':weeks'=>$data['weeks'], 'hours'=>$data['hours'], ':adjust'=>$data['adjust'], ':total_hours'=>$data['hours'], ':revenue'=>$data['revenue'], ':subsidy'=>$data['subsidy'], ':turnover'=>$data['turnover'], ':note'=>$data['note']));
            $sql = 'SELECT * FROM `academic_agency_class` WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter ORDER BY `id` DESC LIMIT 1';
            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            $class_id = $res[0]['id'];
            $sql = 'DELETE FROM `academic_agency_class_country` WHERE `class_id` = :class_id';
            $cnt = $this->dbUpdate($sql, array(':class_id'=>$class_id));
            for ($i=0; $i<sizeof($data['country']); $i++) {
                $sql = 'INSERT INTO `academic_agency_class_country` (`id`, `class_id`, `country_code`, `male`, `new_male`, `female`, `new_female`, `note`, `state`) VALUES (0, :class_id, :country_code, :male, :new_male, :female, :new_female, :note, 0)';
                $cnt = $this->dbUpdate($sql, array(':class_id'=>$class_id, ':country_code'=>$data['country'][$i]['country_code'], ':male'=>$data['country'][$i]['male'], ':new_male'=>$data['country'][$i]['new_male'], ':female'=>$data['country'][$i]['female'], ':new_female'=>$data['country'][$i]['new_female'], ':note'=>$data['country'][$i]['note']));
            } 
            return $this->dbQuery('agent_academic_agency_class', array('agency_id'=>$data['agency_id'], 'era_id'=>$data['era_id'], 'quarter'=>$data['quarter']));
            break;
        case 'agent_academic_agency_class_del':
            $sql = 'SELECT * FROM `academic_agenc_class` WHERE `id` = :id';
            $res = $this->dbSelect($sql, array(':id'=>$data['id']));
            $sql = 'DELETE FROM `academic_agency_class` WHERE `id` = :id';
            $cnt = $this->dbUpdate($sql, array(':id'=>$data['id']));
            $sql = 'DELETE FROM `academic_agency_class_country` WHERE `class_id` = :class_id';
            $cnt = $this->dbUpdate($sql, array(':class_id'=>$data['id']));
            return $this->dbQuery('agent_academic_agency_class', array('agency_id'=>$data['agency_id'], 'era_id'=>$data['era_id'], 'quarter'=>$data['quarter']));
            break;
        case 'agent_academic_agency_class_done':
            $sql = 'UPDATE `academic_agency_class` SET `state` = 1 WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter';
            $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            return $this->dbQuery('agent_academic_agency_class', array('agency_id'=>$data['agency_id'], 'era_id'=>$data['era_id'], 'quarter'=>$data['quarter']));
            break;
        case 'agent_academic_agency_class_mod':
            $sql = 'UPDATE `academic_agency_class` SET `cname` = :cname, `content_code` = :content_code, `target_code` = :target_code, `people` = :people, `weekly` = :weekly, `weeks` = :weeks, `hours` = :hours, `adjust` = :adjust, `total_hours` = :total_hours, `revenue` = :revenue, `subsidy` = :subsidy, `turnover` = :turnover, `note` = :note WHERE `id` = :class_id';
            $cnt = $this->dbUpdate($sql, array(':cname'=>$data['cname'], ':content_code'=>$data['content_code'], ':target_code'=>$data['target_code'], ':people'=>$data['people'], ':weekly'=>$data['weekly'], ':weeks'=>$data['weeks'], 'hours'=>$data['hours'], ':adjust'=>$data['adjust'], ':total_hours'=>$data['total_hours'], ':revenue'=>$data['revenue'], ':subsidy'=>$data['subsidy'], ':turnover'=>$data['turnover'], ':note'=>$data['note'], ':class_id'=>$data['class_id']));
            $sql = 'DELETE FROM `academic_agency_class_country` WHERE `class_id` = :class_id';
            $cnt = $this->dbUpdate($sql, array(':class_id'=>$data['class_id']));
            for ($i=0; $i<sizeof($data['country']); $i++) {
                $sql = 'INSERT INTO `academic_agency_class_country` (`id`, `class_id`, `country_code`, `male`, `new_male`, `female`, `new_female`, `note`, `state`) VALUES (0, :class_id, :country_code, :male, :new_male, :female, :new_female, :note, 0)';
                    $cnt = $this->dbUpdate($sql, array(':class_id'=>$data['class_id'], ':country_code'=>$data['country'][$i]['country_code'], ':male'=>$data['country'][$i]['male'], ':new_male'=>$data['country'][$i]['new_male'], ':female'=>$data['country'][$i]['female'], ':new_female'=>$data['country'][$i]['new_female'], ':note'=>$data['country'][$i]['note']));
            } 
            return $this->dbQuery('agent_academic_agency_class', array('agency_id'=>$data['agency_id'], 'era_id'=>$data['era_id'], 'quarter'=>$data['quarter']));
            break;
        case 'agent_academic_agency_contact':
            $sql = 'SELECT * FROM `academic_agency_contact` WHERE `agency_id` = :agency_id ORDER by `id` DESC';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            break;
        case 'agent_academic_agency_contact_add':
            if (1 == $data['primary']) {
                $sql = 'UPDATE `academic_agency_contact` SET primary = 0 WHERE `agency_id` = :agency_id';
                $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id']));
            }
            $staff = ($data['manager'] == 1)? $data['staff'] : 1;
            $sql = 'INSERT INTO `academic_agency_contact` (`id`, `agency_id`, `cname`, `title`, `manager`, `staff`, `role`, `area_code`, `phone`, `ext`, `email`, `spare_email`, `primary`) VALUES (0, :agency_id, :cname, :title, :manager, :staff, :role, :area_code, :phone, :ext, :email, :spare_email, :primary)';
            $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id'], ':cname'=>$data['cname'], ':title'=>$data['title'], ':manager'=>$data['manager'], ':staff'=>$staff, ':role'=>$data['role'], ':area_code'=>$data['area_code'], ':phone'=>$data['phone'], ':ext'=>$data['ext'], ':email'=>$data['email'], ':spare_email'=>$data['spare_email'], ':primary'=>$data['primary']));
            return $this->dbQuery('agent_academic_agency_contact', array('agency_id'=>$data['agency_id']));
            break;
        case 'agent_academic_agency_contact_del':
            $sql = 'DELETE FROM `academic_agency_contact` WHERE `agency_id` = :agency_id AND `id` = :id';
            $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id'], ':id'=>$data['id']));
            return $this->dbQuery('agent_academic_agency_contact', array('agency_id'=>$data['agency_id']));
            break;
        case 'agent_academic_agency_contact_mod':
            if (1 == $data['primary']) {
                $sql = 'UPDATE `academic_agency_contact` SET primary = 0 WHERE `agency_id` = :agency_id';
                $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id']));
            }
            $staff = ($data['manager'] == 1)? $data['staff'] : 1;
            $sql = 'UPDATE `academic_agency_contact` SET `cname` = :cname, `title` = :title, `manager` = :manager, `staff` = :staff, `role` = :role, `area_code` = :area_code, `phone` = :phone, `ext` = :ext, `email` = :email, `spare_email` = :spare_email, `primary` = :primary WHERE `agency_id` = :agency_id AND `id` = :id';
            $cnt = $this->dbUpdate($sql, array(':cname'=>$data['cname'], ':title'=>$data['title'], ':manager'=>$data['manager'], ':staff'=>$staff, ':role'=>$data['role'], ':area_code'=>$data['area_code'], ':phone'=>$data['phone'], ':ext'=>$data['ext'], ':email'=>$data['email'], ':spare_email'=>$data['spare_email'], ':primary'=>$data['primary'], ':agency_id'=>$data['agency_id'], ':id'=>$data['id']));
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
            $sql = 'SELECT `era_id` FROM `academic_agency_hr` WHERE agency_id = :agency_id ORDER by `id` DESC LIMIT 1';
            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            if (1 == sizeof($res)) {
                $sql = 'SELECT * FROM `academic_era` WHERE `id` > :id ORDER BY `id` ASC LIMIT 1';
                $res = $this->dbSelect($sql, array(':id'=>$res[0]['era_id']));
                if (1 == sizeof($res)) {
                    $sql = 'INSERT INTO `academic_agency_hr` (`agency_id`, `era_id`, `administration`, `subject`, `adjunct`, `reserve`, `others`, `note`, `state`) VALUES (:agency_id, :era_id, 0, 0, 0, 0, 0, "", 0)';
                    $cnt = $this->dbUpdate($sql, array(":agency_id"=>$data['agency_id'], ":era_id"=>$res[0]['id']));
                }
            } else {
                $sql = 'SELECT * FROM `academic_era` WHERE state = 1 ORDER BY `id` ASC LIMIT 1';
                $res = $this->dbSelect($sql);
                if (sizeof($res) > 0) {
                    $sql = 'INSERT INTO `academic_agency_hr` (`agency_id`, `era_id`, `administration`, `subject`, `adjunct`, `reserve`, `others`, `note`, `state`) VALUES (:agency_id, :era_id, 0, 0, 0, 0, 0, "", 0)';
                    $cnt = $this->dbUpdate($sql, array(":agency_id"=>$data['agency_id'], ":era_id"=>$res[0]['id']));
                }
            }
            
            return $this->dbQuery('agent_academic_agency_hr', array('agency_id'=>$data['agency_id']));
            break;
        case 'agent_academic_agency_hr_mod':
            $sql  = 'UPDATE `academic_agency_hr` SET `administration` = :administration, `subject` = :subject, `adjunct` = :adjunct, `reserve` = :reserve, `others` = :others, `note` = :note, `state` = 1 WHERE `agency_id` = :agency_id AND `era_id` = :era_id';
            $res = $this->dbUpdate($sql, array(':administration'=>$data['administration'], ':subject'=>$data['subject'], ':adjunct'=>$data['adjunct'], ':reserve'=>$data['reserve'], ':others'=>$data['others'], ':note'=>$data['note'], ':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id']));
            return $this->dbQuery('agent_academic_agency_hr', array('agency_id'=>$data['agency_id']));
            break;
        case 'agent_academic_agency_unlock':
            $sql = 'DELETE FROM `academic_agency_unlock` WHERE `agency_id` = :agency_id';
            $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id']));
            $sql = 'INSERT INTO `academic_agency_unlock` (`id`, `agency_id`, `era_id`, `quarter`, `minors`, `work_days`, `online`, `offline`, `note`, `state`) VALUES (0, :agency_id, :era_id, :quarter, :minors, :work_days, "", "", :note, 0)';
            $cnt = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter'], ':minors'=>$data['minors'], ':work_days'=>$data['work_days'], ':note'=>$data['note']));
            return $cnt;
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

        /* mailer */
        case 'mailer_official_get':
            $sql = 'SELECT * FROM `official`';
            return $this->dbSelect($sql);
            break;
        /* references */
        case 'refs_academic_institution':
            $sql = 'SELECT * FROM `academic_institution` ORDER BY `code`';
            return $this->dbSelect($sql);
            break;
        case 'refs_academic_agency':
            $sql = 'SELECT * FROM `academic_agency` ORDER BY `id`';
            return $this->dbSelect($sql);
            break;
        case 'refs_area_list':
            $sql = 'SELECT * FROM `area_list` ORDER BY `code`';
            return $this->dbSelect($sql);
            break;
        case 'refs_content_list':
            $sql = 'SELECT * FROM `content_list` ORDER BY `code`';
            return $this->dbSelect($sql);
            break;
        case 'refs_country_list':
            $sql = 'SELECT * FROM `country_list` ORDER BY `code`';
            return $this->dbSelect($sql);
            break;
        case 'refs_major_list':
            $sql = 'SELECT * FROM `major_list` ORDER BY `code`';
            return $this->dbSelect($sql);
            break;
        case 'refs_minor_list':
            $sql = 'SELECT * FROM `minor_list` ORDER BY `code`';
            return $this->dbSelect($sql);
            break;
        case 'refs_target_list':
            $sql = 'SELECT * FROM `target_list` ORDER BY `code`';
            return $this->dbSelect($sql);
            break;
        }
    }
}
