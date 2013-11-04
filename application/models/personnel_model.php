<?php

class Personnel_model extends CI_Model {

    var $tbl = 'userinfo';

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    function get_all_personnel_name() {
        $this->load->database('default');
        $col_user_id = 'user_id';
        $col_name = 'name';
        $sql = "SELECT $col_user_id,
            $col_name
            FROM $this->tbl 
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
}

?>
