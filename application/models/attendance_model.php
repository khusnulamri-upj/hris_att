<?php

class Attendance_model extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    function get_all_year() {
        $tbl = 'attendance';
        $col_year = 'date';
        $col_year_alias = 'year';
        
        $this->load->database('default');
        $sql = "SELECT DATE_FORMAT($col_year,'%Y') AS $col_year_alias 
            FROM $tbl 
            GROUP BY DATE_FORMAT($col_year,'%Y')
            ORDER BY $col_year";
        $query = $this->db->query($sql);
        $arr_year = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $obj) {
                $arr_year[$obj->$col_year_alias] = $obj->$col_year_alias;
            }
        }
        $this->db->close();
        return $arr_year;
    }
    
    function get_all_keterangan($arr_init = array()) {
        $tbl = 'opt_keterangan';
        $col_ket_id = 'opt_keterangan_id';
        $col_ket_desc = 'content';
        $col_order_by = 'order_no';
        
        $this->load->database('default');
        $sql = "SELECT $col_ket_id,
            $col_ket_desc
            FROM $tbl
            WHERE expired_time IS NULL
            ORDER BY $col_order_by";
        $query = $this->db->query($sql);
        //$arr_ket = array();
        $arr_ket = $arr_init;
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $obj) {
                $arr_ket[$obj->$col_ket_id] = $obj->$col_ket_desc;
            }
        }
        $this->db->close();
        return $arr_ket;
    }
    
    function is_attendance_data_exist($user_id,$tahun,$bulan) {
        if (empty($user_id) || empty($tahun) || empty($bulan)) {
            return FALSE;
        }
        
        $tbl = 'attendance';
        $col_user_id = 'user_id';
        $col_date = 'date';
        
        $this->load->database('default');
        $sql = "SELECT $col_user_id 
            FROM $tbl
            WHERE $col_date > DATE_ADD(MAKEDATE($tahun, 31), INTERVAL ($bulan-2) MONTH)
            AND $col_date < DATE_ADD(MAKEDATE($tahun, 1), INTERVAL ($bulan) MONTH)
            AND $col_user_id = $user_id";
        $query = $this->db->query($sql);
        $result = FALSE;
        if ($query->num_rows() > 0) {
            $result = TRUE;
        }
        $this->db->close();
        return $result;
    }
    
    function get_attendance_data_personnel_monthly($user_id,$tahun,$bulan) {
        if (empty($user_id) || empty($tahun) || empty($bulan)) {
            return NULL;
        }
        
        if (!$this->is_attendance_data_exist($user_id, $tahun, $bulan)) {
            return NULL;
        }
        
        $fmt_date = '%d/%m/%Y';
        $fmt_time = '%H:%i';
        $late_limit = '07:40';
        $early_limit = '16:30';
        $time_divider = '12:00';
        
        $this->load->database('default');
        
        $sql = "SELECT gen_lbr2.tanggal,
            gen_lbr2.hari,
            att2.jam_masuk,
            att2.jam_keluar,
            att2.is_same,
            att2.opt_keterangan,
            opt.content AS keterangan,
            TIME_FORMAT(SEC_TO_TIME(att2.detik_telat_masuk),'$fmt_time') AS waktu_telat_masuk,
            att2.detik_telat_masuk,
            att2.is_late,
            att2.is_early,
            gen_lbr2.is_holiday,
            gen_lbr2.desc_holiday,
            if(gen_lbr2.is_holiday,0,if(is_late IS NULL,1,0)) AS is_blank,
            gen_lbr2.tgl
            FROM (
              SELECT gen_lbr.*
              FROM (  
                SELECT gen.*,
                lbr.deskripsi AS desc_holiday,
                IF(lbr.deskripsi IS NULL,0,1) AS is_holiday
                FROM (  
                    SELECT DATE_FORMAT(DATE_ADD(MAKEDATE(z.tahun, z.gen_date), INTERVAL (z.bulan-1) MONTH),'$fmt_date') AS tanggal,
                    DATE_FORMAT(DATE_ADD(MAKEDATE(z.tahun, z.gen_date), INTERVAL (z.bulan-1) MONTH),'%a') AS hari,
                    gen_date AS tgl
                    FROM (
                      SELECT gen_date,
                      $tahun AS tahun,
                      $bulan AS bulan
                      FROM tabel_helper
                    ) z
                    GROUP BY z.gen_date
                    ORDER BY z.gen_date
                ) gen
                LEFT OUTER JOIN (
                  SELECT *
                  FROM libur
                  ORDER BY libur.tgl DESC
                ) lbr
                ON ((UPPER(gen.hari) = UPPER(lbr.hari)) AND (lbr.tgl IS NULL) AND lbr.expired_time IS NULL)
                OR ((gen.tanggal = DATE_FORMAT(lbr.tgl,'$fmt_date')) AND (lbr.hari IS NULL) AND lbr.expired_time IS NULL)
              ) gen_lbr
              GROUP BY gen_lbr.tanggal
            ) gen_lbr2
            LEFT OUTER JOIN (
              SELECT att.user_id,
              att.tanggal,
              att.is_same,
              att.jam_masuk,
              att.jam_keluar,
              att.is_late,
              att.detik_telat_masuk,
              att.is_early,
              MAX(att.opt_keterangan) AS opt_keterangan
              FROM (
                SELECT aa.user_id,
                aa.tanggal,
                aa.is_same,
                DATE_FORMAT(aa.enter_time,'$fmt_time') AS jam_masuk,
                DATE_FORMAT(aa.leave_time,'$fmt_time') AS jam_keluar,
                IF(aa.enter_time > '$late_limit', 1, 0) AS is_late,
                TIME_TO_SEC(IF(TIMEDIFF(DATE_FORMAT(aa.enter_time,'$fmt_time'),'$late_limit') > 0, TIMEDIFF(DATE_FORMAT(aa.enter_time,'$fmt_time'),'$late_limit'), NULL)) AS detik_telat_masuk,
                IF(aa.leave_time < '$early_limit', 1, 0) AS is_early,
                NULL AS opt_keterangan
                FROM (
                  SELECT a.user_id,
                  DATE_FORMAT(a.date,'$fmt_date') AS tanggal,
                  IF(a.max_time = a.min_time,IF(TIMEDIFF(DATE_FORMAT(a.min_time,'$fmt_time'),'$time_divider') >= 0,NULL,a.min_time),a.min_time) AS enter_time,
                  IF(a.max_time = a.min_time,IF(TIMEDIFF('$time_divider',DATE_FORMAT(a.max_time,'$fmt_time')) > 0,NULL,a.max_time),a.max_time) AS leave_time,
                  IF(a.max_time = a.min_time,1,0) AS is_same
                  FROM attendance a
                  WHERE a.user_id = $user_id
                ) aa
                UNION
                SELECT k.user_id,
                DATE_FORMAT(k.tgl,'$fmt_date') AS tanggal,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                k.opt_keterangan
                FROM keterangan k
                LEFT OUTER JOIN opt_keterangan o
                ON k.opt_keterangan = o.opt_keterangan_id
                WHERE k.expired_time IS NULL
                AND k.user_id = $user_id
              ) att
              GROUP BY att.tanggal
            ) att2
            ON gen_lbr2.tanggal = att2.tanggal
            LEFT OUTER JOIN opt_keterangan opt
            ON att2.opt_keterangan = opt.opt_keterangan_id";
        
        //$query = $this->db->query($sql, array($fmt_date, (integer)$bulan, (integer)$tahun, (integer)$user_id, (integer)$user_id));
        $query = $this->db->query($sql);
        $return = NULL;
        if ($query->num_rows() > 0) {
            $return = $query->result();
        }
        $this->db->close();
        return $return;
    }
    
    function get_summary_attendance_data_personnel_monthly($user_id,$tahun,$bulan) {
        if (empty($user_id) || empty($tahun) || empty($bulan)) {
            return NULL;
        }
        
        if (!$this->is_attendance_data_exist($user_id, $tahun, $bulan)) {
            return NULL;
        }
        
        $fmt_date = '%d/%m/%Y';
        $fmt_time = '%H:%i';
        $late_limit = '07:40';
        $early_limit = '16:30';
        $time_divider = '12:00';
        
        $this->load->database('default');
        
        $sql = "SELECT TIME_FORMAT(SEC_TO_TIME(SUM(summ.detik_telat_masuk)),'$fmt_time') AS sum_waktu_telat_masuk, SUM(summ.detik_telat_masuk) AS sum_detik_telat_masuk, SUM(summ.is_late) AS sum_is_late, SUM(summ.counter_hadir) AS sum_counter_hadir FROM (
          SELECT gen_lbr2.tanggal,
            att2.opt_keterangan,
            opt.content AS keterangan,
            if(att2.detik_telat_masuk,att2.detik_telat_masuk,0) AS detik_telat_masuk,
            if(att2.is_late,att2.is_late,0) AS is_late,
            if(att2.is_early,att2.is_early,0) AS is_early,
            if(att2.counter_hadir,att2.counter_hadir,0) AS counter_hadir
            FROM (
              SELECT gen_lbr.*
              FROM (  
                SELECT gen.*
                FROM (  
                    SELECT DATE_FORMAT(DATE_ADD(MAKEDATE(z.tahun, z.gen_date), INTERVAL (z.bulan-1) MONTH),'$fmt_date') AS tanggal
                    FROM (
                      SELECT gen_date,
                      $tahun AS tahun,
                      $bulan AS bulan
                      FROM tabel_helper
                    ) z
                    GROUP BY z.gen_date
                    ORDER BY z.gen_date
                ) gen
              ) gen_lbr
              GROUP BY gen_lbr.tanggal
            ) gen_lbr2
            LEFT OUTER JOIN (
              SELECT att.user_id,
              att.tanggal,
              att.is_late,
              att.detik_telat_masuk,
              att.is_early,
              MAX(att.opt_keterangan) AS opt_keterangan,
              MAX(att.counter_hadir) AS counter_hadir
              FROM (
                SELECT aa.user_id,
                aa.tanggal,
                IF(aa.enter_time > '$late_limit', 1, 0) AS is_late,
                TIME_TO_SEC(IF(TIMEDIFF(DATE_FORMAT(aa.enter_time,'$fmt_time'),'$late_limit') > 0, TIMEDIFF(DATE_FORMAT(aa.enter_time,'$fmt_time'),'$late_limit'), NULL)) AS detik_telat_masuk,
                IF(aa.leave_time < '$early_limit', 1, 0) AS is_early,
                NULL AS opt_keterangan,
                1 AS counter_hadir
                FROM (
                  SELECT a.user_id,
                  DATE_FORMAT(a.date,'$fmt_date') AS tanggal,
                  IF(a.max_time = a.min_time,IF(TIMEDIFF(DATE_FORMAT(a.min_time,'$fmt_time'),'$time_divider') >= 0,NULL,a.min_time),a.min_time) AS enter_time,
                  IF(a.max_time = a.min_time,IF(TIMEDIFF('$time_divider',DATE_FORMAT(a.max_time,'$fmt_time')) > 0,NULL,a.max_time),a.max_time) AS leave_time
                  FROM attendance a
                  WHERE a.user_id = $user_id
                ) aa
                UNION
                SELECT k.user_id,
                DATE_FORMAT(k.tgl,'$fmt_date') AS tanggal,
                NULL,
                NULL,
                NULL,
                k.opt_keterangan,
                o.counter_hadir
                FROM keterangan k
                LEFT OUTER JOIN opt_keterangan o
                ON k.opt_keterangan = o.opt_keterangan_id
                WHERE k.expired_time IS NULL
                AND k.user_id = $user_id
              ) att
              GROUP BY att.tanggal
            ) att2
            ON gen_lbr2.tanggal = att2.tanggal
            LEFT OUTER JOIN opt_keterangan opt
            ON att2.opt_keterangan = opt.opt_keterangan_id
          ) summ";
        
        //$query = $this->db->query($sql, array($fmt_date, (integer)$bulan, (integer)$tahun, (integer)$user_id, (integer)$user_id));
        $query = $this->db->query($sql);
        $return = NULL;
        if ($query->num_rows() == 1) {
            $return = $query->row();
        }
        $this->db->close();
        return $return;
    }
    
    
    function insert_keterangan($user_id,$tahun,$bulan,$arr_ket) {
        $current_user_id = $this->flexi_auth->get_user_id();
        
        $tbl = 'keterangan';
        $col_user_id = 'user_id';
        $col_tanggal = 'tgl';
        $col_opt_keterangan = 'opt_keterangan';
        
        $this->load->database('default');
        $this->db->trans_start();
                
        $row_inserted = 1;
        $row_deleted = 0;
        foreach ($arr_ket as $key => $value) {
            if ((isset($value)) && ($value > 0)) {
                $strcek = "SELECT * FROM $tbl WHERE expired_time IS NULL
                    AND $col_user_id = $user_id
                    AND 
                    $col_tanggal = DATE_ADD(MAKEDATE($tahun, $key), INTERVAL ($bulan-1) MONTH)
                    AND $col_opt_keterangan = $value";

                $querycek = $this->db->query($strcek);

                if ($querycek->num_rows == 0) {

                    $str = "UPDATE $tbl
                        SET expired_time = CURRENT_TIMESTAMP,
                        modified_by = $current_user_id
                        WHERE expired_time IS NULL AND $col_user_id = $user_id
                        AND $col_tanggal = DATE_ADD(MAKEDATE($tahun, $key), INTERVAL ($bulan-1) MONTH)";

                    $query = $this->db->query($str);

                    $data_mysql = array(
                        $col_user_id => $user_id,
                        $col_opt_keterangan => $value,
                        'created_by' => $current_user_id
                    );

                    $this->db->set($col_tanggal, 'DATE_ADD(MAKEDATE('.$tahun.', '.$key.'), INTERVAL ('.$bulan.'-1) MONTH)', FALSE);
                    $this->db->insert($tbl, $data_mysql);
                    $row_inserted++;
                }
            } else {
                $strcek = "SELECT * FROM $tbl WHERE expired_time IS NULL
                    AND $col_user_id = $user_id
                    AND 
                    $col_tanggal = DATE_ADD(MAKEDATE($tahun, $key), INTERVAL ($bulan-1) MONTH)";

                $querycek = $this->db->query($strcek);

                if ($querycek->num_rows > 0) {
                    $str = "UPDATE $tbl
                        SET expired_time = CURRENT_TIMESTAMP,
                        modified_by = $current_user_id
                        WHERE expired_time IS NULL AND $col_user_id = $user_id
                        AND $col_tanggal = DATE_ADD(MAKEDATE($tahun, $key), INTERVAL ($bulan-1) MONTH)";

                    $query = $this->db->query($str);
                    $row_deleted++;
                }
            }
        }
        $this->db->trans_complete();
        return ($row_inserted*100+$row_deleted); //AMRNOTE: FALSE == 100
    }
    
    function get_summary_of_keterangan($user_id,$tahun,$bulan) {
        if (empty($user_id) || empty($tahun) || empty($bulan)) {
            return NULL;
        }
        
        if (!$this->is_attendance_data_exist($user_id, $tahun, $bulan)) {
            return NULL;
        }
    
        $sql = "SELECT o.opt_keterangan_id AS id,
            o.content AS keterangan,
            count(a.user_id) AS jumlah
            FROM opt_keterangan o
            LEFT OUTER JOIN (
            SELECT k.*
            FROM keterangan k
            WHERE k.user_id = $user_id
            AND k.tgl > DATE_ADD(MAKEDATE($tahun, 31), INTERVAL ($bulan-2) MONTH)
            AND k.tgl < DATE_ADD(MAKEDATE($tahun, 1), INTERVAL ($bulan) MONTH)
            AND k.expired_time IS NULL
            ) a ON o.opt_keterangan_id = a.opt_keterangan
            GROUP BY o.opt_keterangan_id";
         
        $query = $this->db->query($sql);
        $return = NULL;
        if ($query->num_rows() > 0) {
            $return = $query->result();
        }
        $this->db->close();
        return $return;
    }
    
    function get_summary_of_keterangan_with_group($user_id,$tahun,$bulan,$empty_counter = FALSE,$digit_order_no = 1) {
        if (!$empty_counter && (empty($user_id) || empty($tahun) || empty($bulan))) {
            return NULL;
        }
        
        if (!$empty_counter && !$this->is_attendance_data_exist($user_id, $tahun, $bulan)) {
            return NULL;
        }
        
        if ($empty_counter && ($user_id == 'NOTUSE') && ($tahun == 'NOTUSE') && ($bulan == 'NOTUSE')) {
            $sql = "SELECT o.opt_keterangan_id AS id,
                o.reff AS keterangan,
                0 AS jumlah
                FROM opt_keterangan o
                GROUP BY SUBSTRING(o.order_no,1,$digit_order_no)";
        } else {
            $sql = "SELECT o.opt_keterangan_id AS id,
                o.reff AS keterangan,
                count(a.user_id) AS jumlah
                FROM opt_keterangan o
                LEFT OUTER JOIN (
                    SELECT k.*
                    FROM keterangan k
                    WHERE k.user_id = $user_id
                    AND k.tgl > DATE_ADD(MAKEDATE($tahun, 31), INTERVAL ($bulan-2) MONTH)
                    AND k.tgl < DATE_ADD(MAKEDATE($tahun, 1), INTERVAL ($bulan) MONTH)
                    AND k.expired_time IS NULL
                ) a ON o.opt_keterangan_id = a.opt_keterangan
                GROUP BY SUBSTRING(o.order_no,1,$digit_order_no)";
        }
        
        $query = $this->db->query($sql);
        $return = NULL;
        if ($query->num_rows() > 0) {
            $return = $query->result();
        }
        $this->db->close();
        return $return;
    }
    
}

?>
