<?php

class AdminModel extends Model {
    public function dbQuery( $key, $data = array() ) {
        switch($key)
        {
        case 'dashboard':

            break;
        case 'academic_agency':
            $sql  = 'SELECT t1.*, t2.`cname` AS `institution_cname`, t2.`aka` AS `institution_aka`, IFNULL(t3.`administration`, 0) AS `academic_agency_hr_administration`, IFNULL(t3.`subject`, 0) AS `academic_agency_hr_subject`, IFNULL(t3.`adjunct`, 0) AS `academic_agency_hr_adjunct`, IFNULL(t3.`reserve`, 0) AS `academic_agency_hr_reserve` ';
            $sql .= '  FROM `academic_agency` t1 ';
            $sql .= ' INNER JOIN `academic_institution` t2 ON t1.`institution_code` = t2.`code` ';
            $sql .= '  LEFT JOIN ( SELECT t4.`agency_id`, t4.`administration`, t4.`subject`, t4.`adjunct`, t4.`reserve` FROM `academic_agency_hr` t4 INNER JOIN `academic_era` t5 ON t4.`era_id` = t5.id AND t5.`state` = 1 ) t3 ON t1.`id` = t3.`agency_id` ';
            return $this->dbSelect($sql);
            break;
        case 'academic_agency_agent':
            $sql  = 'SELECT t1.*, t2.`cname` AS `academic_agency_cname` ';
            $sql .= '  FROM `academic_agency_agent` t1 ';
            $sql .= ' INNER JOIN `academic_agency` t2 ON t1.`agency_id` = t2.`id` ';
            return $this->dbSelect($sql);
            break;
        case 'academic_class':
            $sql = 'SELECT * FROM `academic_class` WHERE `era_id` = :era_id';
            return $this->dbSelect($sql, array(':era_id'=>$data['era_id']));
            break;
        case 'academic_era':
            $sql = 'SELECT * FROM `academic_era` ORDER BY `id` DESC';
            return $this->dbSelect($sql);
            break;
        case 'academic_era_add':
            $sql = 'SELECT * FROM `academic_era` ORDER BY `id` DESC LIMIT 1';
            $res = $this->dbSelect($sql);
            $sql  = 'INSERT INTO `academic_era` (`id`, `common`, `roc`, `code`, `cname`, `state`)';
            $sql .= ' VALUES (0, :common, :roc, :code, :cname, 0)';
            $common = intval($res[0]['common']) + 1;
            $roc = intval($res[0]['roc']) + 1;
            $code = "Y". $roc;
            $cname = $roc . "學年度";
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
            break;
        case 'academic_era_quarter':
            $sql  = 'SELECT t1.*';
            $sql .= '  FROM `academic_era_quarter` t1';
            $sql .= ' INNER JOIN `academic_era` t2 ON t1.`era_id` = t2.`id` AND t2.`state` < 2';
            $sql .= ' ORDER BY t1.`era_id` DESC, t1.`id` ASC';
            return $this->dbSelect($sql);
            break;
        case 'academic_agency_unlock':
            $sql  = 'SELECT t1.*, t2.`cname` `academic_agency_cname`, t3.`cname` `academic_institution_cname`, t4.`cname` `academic_era_cname`';
            $sql .= '  FROM `academic_agency_unlock` t1';
            $sql .= ' INNER JOIN `academic_agency` t2 ON t1.`agency_id` = t2.`id`';
            $sql .= ' INNER JOIN `academic_institution` t3 ON t2.`institution_code` = t3.`code`';
            $sql .= ' INNER JOIN `academic_era` t4 ON t1.`era_id` = t4.`id`';
            $sql .= ' ORDER BY t1.`state` ASC, t1.`id` DESC';
            return $this->dbSelect($sql);
            break;
        }
    }
}
