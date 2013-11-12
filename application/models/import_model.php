<?php

class Import_model extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }
    
    function set_mdb_connect() {
        $file_path = $this->Parameter->get_value('FILE_ON_SERVER_FOR_MDB');
        $config['hostname'] = "Driver={Microsoft Access Driver (*.mdb)}; DBQ=".$file_path;
        $config['username'] = "";
        $config['password'] = "";
        $config['database'] = "Driver={Microsoft Access Driver (*.mdb)}; DBQ=".$file_path;
        $config['dbdriver'] = "odbc";
        $config['dbprefix'] = "";
        $config['pconnect'] = FALSE;
        $config['db_debug'] = TRUE;
        $config['cache_on'] = FALSE;
        $config['cachedir'] = "";
        $config['char_set'] = "utf8";
        $config['dbcollat'] = "utf8_general_ci";
        
        return $config;
    }
    
    function get_checkinout_mdb() {
        $db_mdb = $this->load->database($this->set_mdb_connect(), TRUE);
        
        $sql_mdb = "SELECT USERID AS user_id,
            CHECKTIME AS check_time,
            CHECKTYPE AS check_type,
            VERIFYCODE AS verify_code,
            SENSORID AS sensor_id, 
            WORKCODE AS work_code,
            sn 
            FROM CHECKINOUT";
        
        $qry_mdb = $db_mdb->query($sql_mdb);
        
        $db_mdb->close();
        
        return $qry_mdb->result();
    }
    
    function insert_into_checkinout_temp($result_mdb) {
        $db_mysql = $this->load->database('temporary', TRUE);
        
        $db_mysql->trans_start();
        
        $db_mysql->truncate('mdb_checkinout');
        
        $i = 0;
        
        foreach ($result_mdb->result() as $row_mdb) {
            $data_mysql = array(
                'user_id' => $row_mdb->user_id,
                'check_time' => $row_mdb->check_time,
                'check_type' => $row_mdb->check_type,
                'verify_code' => $row_mdb->verify_code,
                'sensor_id' => $row_mdb->sensor_id,
                'work_code' => $row_mdb->work_code,
                'sn' => $row_mdb->sn
            );
            $db_mysql->insert('mdb_checkinout', $data_mysql);
            $i++;
        }
        
        //echo $i;
        
        $db_mysql->trans_complete();
        
        $db_mysql->close();
    }
    
    function get_checkinout() {
        $result_checkinout = $this->get_checkinout_mdb();
        $this->insert_into_checkinout_temp($result_checkinout);
    }
    
    function process_checkinout() {
    
    }
    
    function get_userinfo_mdb() {
        
    }
    
    function get_department_mdb() {
        
    }
    
    
    function get_all_personnel_name($arr_initial = NULL) {
        $this->load->database('default');
        $col_user_id = 'user_id';
        $col_name = 'name';
        $sql = "SELECT $col_user_id,
            $col_name
            FROM $this->tbl 
            ORDER BY $col_name";
        $query = $this->db->query($sql);
        if ($arr_initial == NULL) {
            $arr_personnel_name = array();
        } else {
            $arr_personnel_name = $arr_initial;
        }
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $obj) {
                $arr_personnel_name[$obj->$col_user_id] = $obj->$col_name;
            }
        }
        $this->db->close();
        return $arr_personnel_name;
    }
    
    function get_all_personnel_name_by_dept_id($dept_id) {
        $this->load->database('default');
        $col_user_id = 'user_id';
        $col_name = 'name';
        $col_dept_id = 'default_dept_id';
        $sql = "SELECT $col_user_id,
            $col_name,
            $col_dept_id
            FROM $this->tbl
            WHERE $col_dept_id = $dept_id
            ORDER BY $col_name";
        $query = $this->db->query($sql);
        $arr_personnel_name = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $obj) {
                $arr_personnel_name[$obj->$col_user_id] = $obj->$col_name;
            }
        }
        $this->db->close();
        return $arr_personnel_name;
    }
    
    function get_personnel_name($user_id) {
        $this->load->database('default');
        $col_user_id = 'user_id';
        $col_name = 'name';
        $sql = "SELECT $col_user_id,
            $col_name
            FROM $this->tbl 
            WHERE $col_user_id = $user_id
            LIMIT 1";
        $query = $this->db->query($sql);
        $personnel_name = NULL;
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $personnel_name = $row->$col_name;
        }
        $this->db->close();
        return $personnel_name;
    }
    
    function get_dept_id($user_id) {
        $this->load->database('default');
        $col_user_id = 'user_id';
        $col_dept_id = 'default_dept_id';
        $sql = "SELECT $col_user_id,
            $col_dept_id
            FROM $this->tbl 
            WHERE $col_user_id = $user_id
            LIMIT 1";
        $query = $this->db->query($sql);
        $dept_id = NULL;
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $dept_id = $row->$col_dept_id;
        }
        $this->db->close();
        return $dept_id;
    }
}

?>
