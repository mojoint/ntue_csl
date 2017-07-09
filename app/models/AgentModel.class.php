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
        case 'academic_agency_set':
            $sql = 'SELECT * FROM `academic_agency` WHERE agency_id = :agency_id';
            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            if (0 == sizeof($res)) {
                $sql = 'INSERT INTO `academic_agency` (agency_id) VALUES (:agency_id)';
                $res = $this->dbUpdate($sql, array(':agency_id'=>$data['agency_id']));
                $sql = 'SELECT * FROM `academic_agency` WHERE agency_id = :agency_id';
                $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            }
            return $res;
            break;
        case 'academic_agency_fill':
            //$sql  = 'SELECT * FROM `academic_era_quarter` WHERE CURDATE() BETWEEN `online` AND `offline`';
            $sql = 'SELECT `era_id`, `quarter` FROM `academic_agency_unlock` WHERE `agency_id` = :agency_id AND `state` = 1 AND CURDATE() BETWEEN `online` AND `offline`';
            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            if (sizeof($res)) {
                $sql  = 'SELECT * FROM `academic_era_qurater` WHERE `era_id` = :era_id AND `quarter` = :quarter';
                return $this->dbSelect($sql, array(':era_id'=>$res[0]['era_id'], 'quarter'=>$res[0]['quarter']));
            } else {
                $sql  = 'SELECT t1.* ';
                $sql .= '  FROM `academic_era_quarter` t1';
                $sql .= ' INNER JOIN `academic_agency_class` t2 ON t2.`era_id` = t1.`era_id` AND t2.`quarter` = t1.`quarter` AND t2.`state` = 0 AND t2.`agency_id` = :agency_id';
                $sql .= ' WHERE CURDATE() BETWEEN t1.`online` AND t1.`offline` ORDER BY t1.`id` ASC LIMIT 1';
                return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            }
            break;
        case 'academic_agency_class':
            $sql  = 'SELECT t1.*, t2.`cname` `major_cname`, t3.`cname` `minor_cname`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= ' INNER JOIN `major_list` t2 ON t1.`major_code` = t2.`code`';
            $sql .= ' INNER JOIN `minor_list` t3 ON t1.`minor_code` = t3.`code`';
            $sql .= ' WHERE t1.`agency_id` = :agency_id AND t1.`era_id` = :era_id AND t1.`quarter` = :quarter';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$data['era_id'], ':quarter'=>$data['quarter']));
            break;
        case 'academic_agency_class_query':
            $sql  = 'SELECT t1.*, t2.`cname` `major_cname`, t3.`cname` `minor_cname`';
            $sql .= '  FROM `academic_agency_class` t1';
            $sql .= ' INNER JOIN `major_list` t2 ON t1.`major_code` = t2.`code`';
            $sql .= ' INNER JOIN `minor_list` t3 ON t1.`minor_code` = t3.`code`';
            $sql .= ' WHERE t1.id = :id ';
            return $this->dbSelect($sql, array(':id'=>$data['class_id']));
            break;
        case 'academic_agency_class_country_query':
            $sql  = 'SELECT t1.*, t3.`cname` `country_cname`';
            $sql .= '  FROM `academic_agency_class_country` t1';
            $sql .= ' INNER JOIN `academic_agency_class` t2 ON t2.`id` = t1.`class_id`';
            $sql .= ' INNER JOIN `country_list` t3 on t1.`country_code` = t3.`code`';
            $sql .= ' WHERE t1.`class_id` = :class_id';
            return $this->dbSelect($sql, array(':class_id'=>$data['class_id']));
            break;
        case 'academic_agency_contact':
            $sql = 'SELECT * FROM `academic_agency_contact` WHERE `agency_id` = :agency_id ORDER by `id` DESC';
            return $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            break;
        case 'academic_era':
            $sql = 'SELECT * FROM `academic_era` WHERE state = 1';
            return $this->dbSelect($sql);
            break;
        case 'academic_class':
            $sql = 'SELECT * FROM `academic_class` ORDER BY `era_id`, `major_code`, `minor_code`';
            return $this->dbSelect($sql);
            break;
        case 'academic_agency_unlock':
            $sql = 'SELECT * FROM `academic_agency_unlock` WHERE `agency_id` = :agency_id';
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
            $sql = 'SELECT * FROM `academic_institution` WHERE `code` != null';
            return $this->dbSelect($sql);
            break;
        }
    }
}
