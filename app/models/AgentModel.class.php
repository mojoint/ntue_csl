<?php

class AgentModel extends Model {
    public function dbQuery( $key, $data = array() ) {
        switch($key)
        {
        case 'academic_agency':
            $sql  = 'SELECT t1.*, t2.`cname` `academic_institution_cname`';
            $sql .= '  FROM `academic_agency` t1';
            $sql .= ' INNER JOIN `academic_institution` t2 ON t2.`code` = t1.`institution_code`';
            $sql .= ' WHERE t1.`id` = :id';
            return $this->dbSelect($sql, array(':id'=>$data['agency_id']));
            break;
        case 'academic_agency_get':
            $sql = 'SELECT a.*, b.cname AS agency_name, b.aka, b.state AS agency_state FROM `academic_agency` a, `academic_institution` b WHERE a.id = :agency_id AND a.code = b.code';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            break;
        case 'academic_agency_fill':
            $sql = 'SELECT * FROM `academic_agency_class_status` WHERE `agency_id` = :agency_id';
            $status = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            $quarter = array();
            $unlock = false;
            if (sizeof($status)) {
                foreach($status as $state) {
                    $quarter['quarter' . $state['quarter']] = $state['state'];
                    if (!$state['state']) {
                        $now = time();
                        if ((preg_match('/(\d){4}-(\d){1,2}-(\d){1,2}/', $state['online'])) && (preg_match('/(\d){4}-(\d){1,2}-(\d){1,2}/', $state['offline'])) && ($now > strtotime($state['online'] . ' 00:00:00')) && ($now < strtotime($state['offline'] . ' 23:59:59'))) {
                            $unlock = true;
                            $sql = 'SELECT * FROM `academic_era_quarter` WHERE `era_id` = :era_id AND `quarter` = :quarter';
                            $res = $this->dbSelect($sql, array(':era_id'=>$state['era_id'], ':quarter'=>$state['quarter']));
                        }
                    }
                }
            }


            if (!$unlock) {
                $sql  = 'SELECT t1.* ';
                $sql .= '  FROM `academic_era_quarter` t1';
                $sql .= ' WHERE state > 0 AND NOW() BETWEEN CONCAT(t1.`online`, " 00:00:00") AND CONCAT(t1.`offline`, " 23:59:59") AND "NTUE" = :ntue ORDER BY t1.`id` ASC LIMIT 1';
                $res = $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
                if (sizeof($res)) {
                    if ($quarter['quarter' . $res[0]['quarter']]) {
                        $res = array();
                    }
                }       
            }

            return $res;
            break;
        case 'academic_agency_class':
            $sql  = 'SELECT t1.`id`, t1.`agency_id`, t1.`era_id`, t1.`quarter`, t1.`major_code`, t1.`minor_code`, t1.`cname`, 0 `new_people`, 0 `people`, t1.`total_hours`, t1.`turnover`, t2.`cname` `major_cname`, t3.`cname` `minor_cname`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= ' INNER JOIN `major_list` t2 ON t1.`major_code` = t2.`code`';
            $sql .= ' INNER JOIN `academic_class` t3 ON t1.`era_id` = t3.`era_id` AND t1.`minor_code` = t3.`minor_code`';
            $sql .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`quarter` = :quarter';
            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            if (sizeof($res)) {
                foreach( $res as $key=>$val ) {
                    $sql  = 'SELECT SUM(t1.`new_male` + t1.`new_female`) `new_people`, SUM(t1.`male` + t1.`female` + t1.`new_male` + t1.`new_female`) `people`';
                    $sql .= '  FROM `academic_agency_class_country` t1';
                    $sql .= ' WHERE t1.`class_id` = :class_id';
                    $sql .= ' GROUP BY t1.`class_id`';
                    $rs = $this->dbSelect($sql, array(':class_id'=>$val['id']));
                    if (sizeof($rs)) {
                        $res[$key]['people'] = $rs[0]['people'];
                        $res[$key]['new_people'] = $rs[0]['new_people'];
                    }
                }
            }
            return $res;
            break;
        case 'academic_agency_class_last':
            $sql  = 'SELECT t1.`id`, t1.`agency_id`, t1.`era_id`, t1.`quarter`, t1.`major_code`, t1.`minor_code`, t1.`cname`, t1.`new_people`, t1.`people`, t1.`total_hours`, t1.`turnover`, t2.`cname` `major_cname`, t3.`cname` `minor_cname`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= ' INNER JOIN `major_list` t2 ON t1.`major_code` = t2.`code`';
            $sql .= ' INNER JOIN `academic_class` t3 ON t1.`era_id` = t3.`era_id` AND t1.`minor_code` = t3.`minor_code`';
            $sql .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`quarter` = :quarter AND t1.`major_code` = :major_code';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter'], ':major_code'=>$data['major_code']));
            break;
        case 'academic_agency_class_query':
            $sql  = 'SELECT t1.*, t2.`cname` `major_cname`, t3.`cname` `minor_cname`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= ' INNER JOIN `major_list` t2 ON t1.`major_code` = t2.`code`';
            $sql .= ' INNER JOIN `academic_class` t3 ON t1.`era_id` = t3.`era_id` AND t1.`minor_code` = t3.`minor_code`';
            $sql .= ' WHERE t1.id = :id ';
            return $this->dbSelect($sql, array(':id'=>$data['class_id']));
            break;
        case 'academic_agency_class_country_query':
            $sql  = 'SELECT t1.*, (t1.`male` + t1.`female` + t1.`new_male` + t1.`new_female`) `people`, t3.`cname` `country_cname`';
            $sql .= '  FROM `academic_agency_class_country` t1';
            $sql .= ' INNER JOIN `academic_agency_class` t2 ON t2.`id` = t1.`class_id`';
            $sql .= ' INNER JOIN `country_list` t3 on t1.`country_code` = t3.`code`';
            $sql .= ' WHERE t1.`class_id` = :class_id';
            return $this->dbSelect($sql, array(':class_id'=>$data['class_id']));
            break;
        case 'academic_agency_class_status':
            $sql = 'SELECT * FROM `academic_agency_class_status` WHERE `agency_id` = :agency_id';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            break;
        case 'academic_agency_class_status_query':
            $sql = 'SELECT * FROM `academic_agency_class_status` WHERE `agency_id` = :agency_id AND `era_id` = :era_id AND `quarter` = :quarter';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            break;
        case 'academic_agency_contact':
            $sql = 'SELECT * FROM `academic_agency_contact` WHERE `agency_id` = :agency_id ORDER by `id` DESC';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            break;
        case 'academic_era':
            $sql = 'SELECT * FROM `academic_era` WHERE state > :state';
            return $this->dbSelect($sql, array(':state'=>0));
            break;
        case 'academic_era_unlock':
            $sql = 'SELECT * FROM `academic_era` WHERE state = :state';
            return $this->dbSelect($sql, array(':state'=>1));
            break;
        case 'academic_class':
            $sql = 'SELECT * FROM `academic_class` WHERE "NTUE" = :ntue ORDER BY `era_id`, `major_code`, `minor_code`';
            return $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
            break;
        case 'academic_agency_unlock':
            $sql  = 'SELECT t1.*, (now() between concat(t1.`online`, " 00:00:00") and concat(t1.`offline`, " 23:59:59") ) `status`, t2.`classes`, t2.`unlock`, t2.`state` `status_state`';
            $sql .= '  FROM `academic_agency_unlock` t1';
            $sql .= ' INNER JOIN `academic_agency_class_status` t2 ON t1.`agency_id` = t2.`agency_id` AND t1.`era_id` = t2.`era_id` AND t1.`quarter` = t2.`quarter`';
            $sql .= '  WHERE t1.`agency_id` = :agency_id';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            break;
        case 'academic_agency_hr':
            $sql  = 'SELECT t1.*, t2.`code` `academic_era_code`';
            $sql .= '  FROM `academic_agency_hr` t1';
            $sql .= ' INNER JOIN `academic_era` t2 ON t2.`id` = t1.`era_id`';
            $sql .= ' WHERE t1.`agency_id` = :agency_id ORDER by t1.`era_id` DESC';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            break;
        case 'academic_institution':
            $sql = 'SELECT * FROM `academic_institution` WHERE `code` != :code';
            return $this->dbSelect($sql, array(':code'=>""));
            break;
            break;
        case 'dashboard':
            $sql = 'SELECT `dashboard` FROM `official` WHERE "NTUE" = :ntue';
            return $this->dbSelect($sql, array(':ntue'=>"NTUE"));
            break;
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
            $sql = 'SELECT `code`, `cname` FROM `major_list` WHERE "NTUE" = :ntue ORDER BY `code`';
            return $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
            break;
        case 'refs_minor_list':
            $sql = 'SELECT * FROM `minor_list` WHERE "NTUE" = :ntue ORDER BY `code`';
            return $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
            break;
        case 'refs_minor_list_current':
            $sql = 'SELECT * FROM `academic_class` WHERE `era_id` = :era_id ORDER BY `minor_code`';
            return $this->dbSelect($sql, array(':era_id'=>$data['era_id']));
            break;
        case 'refs_target_list':
            $sql = 'SELECT * FROM `target_list` WHERE "NTUE" = :ntue ORDER BY `code`';
            return $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
            break;
        case 'agent_contract_count':
            $sql = 'SELECT count(*) as cnt FROM `academic_agency_contact` WHERE `agency_id` = :agency_id ';
            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            return $res[0]['cnt'];
            break;
        }
    }
}
