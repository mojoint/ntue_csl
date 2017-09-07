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
                $id = $this->dbInsert($sql, array(':agency_id'=>$data['agency_id']));
                $sql = 'SELECT * FROM `academic_agency` WHERE agency_id = :agency_id';
                $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            }
            return $res;
            break;
        case 'academic_agency_fill':
            $sql = 'SELECT `era_id`, `quarter` FROM `academic_agency_unlock` WHERE `agency_id` = :agency_id AND `state` = 1 AND NOW() BETWEEN CONCAT(`online`, " 00:00:00")  AND CONCAT(`offline`, " 23:59:59")';
            $res = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id']));
            if (1 == sizeof($res)) {
                $sql  = 'SELECT * FROM `academic_era_quarter` WHERE `era_id` = :era_id AND `quarter` = :quarter';
                return $this->dbSelect($sql, array(':era_id'=>$res[0]['era_id'], 'quarter'=>$res[0]['quarter']));
            } else {
                $sql  = 'SELECT t1.* ';
                $sql .= '  FROM `academic_era_quarter` t1';
                $sql .= ' WHERE CURDATE() BETWEEN t1.`online` AND t1.`offline` AND "NTUE" = :ntue ORDER BY t1.`id` ASC LIMIT 1';
                $res = $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
                if (sizeof($res)) {
                    $sql = 'SELECT * FROM `academic_agency_class` WHERE `agency_id` = :agency_id AND `state` = 1 AND `era_id` = :era_id AND `quarter` = :quarter';
                    $result = $this->dbSelect($sql, array(':agency_id'=>$data['agency_id'], ':era_id'=>$res[0]['era_id'], ':quarter'=>$res[0]['quarter']));
                    if (sizeof($result)) {
                        return array();
                    } else {
                        return $res;
                    }
                } else {
                    return $res;
                }
            }
            break;
        case 'academic_agency_class':
            $sql  = 'SELECT t1.`id`, t1.`agency_id`, t1.`era_id`, t1.`quarter`, t1.`major_code`, t1.`minor_code`, t1.`cname`, t1.`people`, t1.`total_hours`, t1.`turnover`, t2.`cname` `major_cname`, t3.`cname` `minor_cname`';
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
            $sql = 'SELECT * FROM `academic_era` WHERE state = :state';
            return $this->dbSelect($sql, array(':state'=>1));
            break;
        case 'academic_class':
            $sql = 'SELECT * FROM `academic_class` WHERE "NTUE" = :ntue ORDER BY `era_id`, `major_code`, `minor_code`';
            return $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
            break;
        case 'academic_agency_unlock':
            $sql  = 'SELECT *, (now() between concat(`online`, " 00:00:00") and concat(`offline`, " 23:59:59") ) `status`';
            $sql .= ' FROM `academic_agency_unlock` WHERE `agency_id` = :agency_id';
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
        case 'refs_target_list':
            $sql = 'SELECT * FROM `target_list` WHERE "NTUE" = :ntue ORDER BY `code`';
            return $this->dbSelect($sql, array(':ntue'=>MD5Prefix));
            break;
        }
    }
}
