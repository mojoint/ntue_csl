<?php

class IndexModel extends Model {
    public function dbQuery( $key, $data = array() ) {
        $timestamp = time();
        $session = '';
        switch($key)
        {
        case 'verify':
            $sql = 'SELECT `username` FROM `academic_agency_agent` WHERE `email` = :email AND `timestamp` = :timestamp AND `userpass` = MD5(:userpass) AND `state` = 0';
            return $this->dbSelect($sql, array(':email'=>$data['email'], ':timestamp'=>$data['timestamp'], ':userpass'=>$data['userpass']));
            break;
        case 'activate':
            $sql = 'UPDATE `academic_agency_agent` SET `userpass` = MD5(:userpass), `state` = 1 WHERE `email` = :email AND `timestamp` = :timestamp';
            return $this->dbUpdate($sql, array(':email'=>$data['email'], ':timestamp'=>$data['timestamp'], ':userpass'=>MD5Prefix . $data['userpass'] . MD5Suffix));
            break;
        case 'admin':
            $sql = 'SELECT `username`, `ip_address` FROM `admin` WHERE username = :username AND userpass = MD5(:userpass) AND state = 1';
            $res = $this->dbSelect($sql, array(':username'=>$data['username'], ':userpass'=>MD5Prefix . $data['userpass'] . MD5Suffix));
            if (1 == sizeof($res)) {
                if ($res[0]['ip_address'] != '0.0.0.0') {
                    if ($res[0]['ip_address'] != $_REMOTE['IP_ADDR']) {
                        $sql = 'UPDATE `admin` SET session = :session, timestamp = :timestamp WHERE username = :username';
                        $session = "1" . base64_encode(MD5Prefix . $res[0]['username'] . '@@@' . $timestamp);
                        $cnt = $this->dbUpdate($sql, array(':timestamp'=>$timestamp, ':session'=>$session, ':username'=>$res[0]['username']));
                        $sql = 'SELECT `username`, `email`, `session`, `timestamp` ,`id` FROM `admin` WHERE username = :username AND userpass = MD5(:userpass) AND state = 1';
                        return $this->dbSelect($sql, array(':username'=>$data['username'], ':userpass'=>MD5Prefix . $data['userpass'] . MD5Suffix));
                    } else {
                        return $res;
                    }
                } else {
                    $sql = 'UPDATE `admin` SET session = :session, timestamp = :timestamp WHERE username = :username';
                    $session = "1" . base64_encode(MD5Prefix . $res[0]['username'] . '@@@' . $timestamp);
                    $cnt = $this->dbUpdate($sql, array(':timestamp'=>$timestamp, ':session'=>$session, ':username'=>$res[0]['username']));
                    $sql = 'SELECT `username`, `email`, `session`, `timestamp` ,`id` FROM `admin` WHERE username = :username AND userpass = MD5(:userpass) AND state = 1';
                    return $this->dbSelect($sql, array(':username'=>$data['username'], ':userpass'=>MD5Prefix . $data['userpass'] . MD5Suffix));
                }
            } else {
                return $res;
            }
            break;
        case 'agent':
            $sql = 'SELECT t1.`agency_id`, t1.`username`, IFNULL(t2.`state`, 0) `agency_state` FROM `academic_agency_agent` t1 INNER JOIN `academic_agency` t2 ON t1.`agency_id` = t2.`id` WHERE t1.username = :username AND t1.userpass = MD5(:userpass) AND t1.state = 1';
            $res = $this->dbSelect($sql, array(':username'=>$data['username'], ':userpass'=>MD5Prefix . $data['userpass'] . MD5Suffix));
            if (1 == sizeof($res)) {
                $sql = 'UPDATE `academic_agency_agent` SET session = :session, timestamp = :timestamp WHERE username = :username';
                $session = "0" . base64_encode(MD5Prefix . $res[0]['username'] . '@@@' . $timestamp . '@@@' . $res[0]['agency_id'] . '@@@' . $res[0]['agency_state']);
                $cnt = $this->dbUpdate($sql, array(':username'=>$res[0]['username'], ':timestamp'=>$timestamp, ':session'=>$session));
                $sql  = 'SELECT t1.`agency_id`, t1.`username`, t1.`email`, t1.`session`, t1.`timestamp`, t3.`cname` `academic_institution_cname`, t2.`institution_code`, t1.`id`, t2.`state` `agency_state` ';
                $sql .= '  FROM `academic_agency_agent` t1';
                $sql .= ' INNER JOIN `academic_agency` t2 ON t2.`id` = t1.`agency_id`';
                $sql .= ' INNER JOIN `academic_institution` t3 ON t3.`code` = t2.`institution_code`';
                $sql .= ' WHERE t1.`username` = :username AND t1.`userpass` = MD5(:userpass)';
debugger('mhho',$sql);
                
                return $this->dbSelect($sql, array(':username'=>$data['username'], ':userpass'=>MD5Prefix . $data['userpass'] . MD5Suffix));
            } else {
                return $res;
            }
            break;
        }
    }
}
