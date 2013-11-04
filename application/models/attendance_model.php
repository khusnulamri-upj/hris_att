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
    
    function get_attendance_data_personnel_monthly($user_id,$tahun,$bulan) {
        if (empty($user_id) || empty($tahun) || empty($bulan)) {
            return NULL;
        }

        $fmt_date = '%d/%m/%Y';
        
        $this->load->database('default');
        
        /*$sql = "SELECT gen_lbr2.tanggal,
            gen_lbr2.hari,
            att2.jam_masuk,
            att2.jam_keluar,
            att2.opt_keterangan,
            opt.content AS keterangan,
            att2.detik_telat_masuk,
            att2.is_late,
            att2.is_early,
            gen_lbr2.is_holiday,
            gen_lbr2.desc_holiday
            FROM (
              SELECT gen_lbr.*
              FROM (  
                SELECT gen.*,
                lbr.deskripsi AS desc_holiday,
                IF(lbr.deskripsi IS NULL,0,1) AS is_holiday
                FROM (  
                    SELECT DATE_FORMAT(DATE_ADD(MAKEDATE(z.tahun, z.gen_date), INTERVAL (z.bulan-1) MONTH),?) AS tanggal,
                    DATE_FORMAT(DATE_ADD(MAKEDATE(z.tahun, z.gen_date), INTERVAL (z.bulan-1) MONTH),'%a') AS hari
                    FROM (
                      SELECT gen_date,
                      ? AS tahun,
                      ? AS bulan
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
                ON ((UPPER(gen.hari) = UPPER(lbr.hari)) AND (lbr.tgl IS NULL))
                OR ((gen.tanggal = DATE_FORMAT(lbr.tgl,'%d/%m/%Y')) AND (lbr.hari IS NULL))
                AND lbr.expired_time IS NULL
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
                DATE_FORMAT(aa.enter_time,'%H:%i') AS jam_masuk,
                DATE_FORMAT(aa.leave_time,'%H:%i') AS jam_keluar,
                IF(aa.enter_time > '07:40', 1, 0) AS is_late,
                TIME_TO_SEC(IF(TIMEDIFF(DATE_FORMAT(aa.enter_time,'%H:%i'),'07:40') > 0, TIMEDIFF(DATE_FORMAT(aa.enter_time,'%H:%i'),'07:40'), NULL)) AS detik_telat_masuk,
                IF(aa.leave_time < '16:30', 1, 0) AS is_early,
                NULL AS opt_keterangan
                FROM (
                  SELECT a.user_id,
                  DATE_FORMAT(a.date,'%d/%m/%Y') AS tanggal,
                  IF(a.max_time = a.min_time,IF(TIMEDIFF(DATE_FORMAT(a.min_time,'%H:%i'),'12:00') >= 0,NULL,a.min_time),a.min_time) AS enter_time,
                  IF(a.max_time = a.min_time,IF(TIMEDIFF('12:00',DATE_FORMAT(a.max_time,'%H:%i')) > 0,NULL,a.max_time),a.max_time) AS leave_time,
                  IF(a.max_time = a.min_time,1,0) AS is_same
                  FROM attendance a
                  WHERE a.user_id = ?
                ) aa
                UNION
                SELECT k.user_id,
                DATE_FORMAT(k.tgl,'%d/%m/%Y') AS tanggal,
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
                AND k.user_id = ?
              ) att
              GROUP BY att.tanggal
            ) att2
            ON gen_lbr2.tanggal = att2.tanggal
            LEFT OUTER JOIN opt_keterangan opt
            ON att2.opt_keterangan = opt.opt_keterangan_id";*/
        
        $sql = "SELECT gen_lbr2.tanggal,
            gen_lbr2.hari,
            att2.jam_masuk,
            att2.jam_keluar,
            att2.is_same,
            att2.opt_keterangan,
            opt.content AS keterangan,
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
                ON ((UPPER(gen.hari) = UPPER(lbr.hari)) AND (lbr.tgl IS NULL))
                OR ((gen.tanggal = DATE_FORMAT(lbr.tgl,'%d/%m/%Y')) AND (lbr.hari IS NULL))
                AND lbr.expired_time IS NULL
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
                DATE_FORMAT(aa.enter_time,'%H:%i') AS jam_masuk,
                DATE_FORMAT(aa.leave_time,'%H:%i') AS jam_keluar,
                IF(aa.enter_time > '07:40', 1, 0) AS is_late,
                TIME_TO_SEC(IF(TIMEDIFF(DATE_FORMAT(aa.enter_time,'%H:%i'),'07:40') > 0, TIMEDIFF(DATE_FORMAT(aa.enter_time,'%H:%i'),'07:40'), NULL)) AS detik_telat_masuk,
                IF(aa.leave_time < '16:30', 1, 0) AS is_early,
                NULL AS opt_keterangan
                FROM (
                  SELECT a.user_id,
                  DATE_FORMAT(a.date,'%d/%m/%Y') AS tanggal,
                  IF(a.max_time = a.min_time,IF(TIMEDIFF(DATE_FORMAT(a.min_time,'%H:%i'),'12:00') >= 0,NULL,a.min_time),a.min_time) AS enter_time,
                  IF(a.max_time = a.min_time,IF(TIMEDIFF('12:00',DATE_FORMAT(a.max_time,'%H:%i')) > 0,NULL,a.max_time),a.max_time) AS leave_time,
                  IF(a.max_time = a.min_time,1,0) AS is_same
                  FROM attendance a
                  WHERE a.user_id = $user_id
                ) aa
                UNION
                SELECT k.user_id,
                DATE_FORMAT(k.tgl,'%d/%m/%Y') AS tanggal,
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
    
    function insert_keterangan($user_id,$tahun,$bulan,$arr_ket) {
        $tbl = 'attendance';
        $col_year = 'date';
        $col_year_alias = 'year';
        
        $this->load->database('default');
        $this->db->trans_start();
                
        $row_inserted = -1;
        foreach ($arr_ket as $key => $value) {
            if ((isset($value)) && ($value > 0)) {
                $strcek = "SELECT * FROM keterangan WHERE expired_time IS NULL
                    AND user_id = $user_id
                    AND DATE_FORMAT(tgl,'$fmt_date') LIKE DATE_FORMAT(DATE_ADD(MAKEDATE(z.tahun, z.gen_date), INTERVAL (z.bulan-1) MONTH),'$fmt_date')
                    AND opt_keterangan = $value";

                $querycek = $this->db->query($strcek);

                if ($querycek->num_rows == 0) {

                    $str = "UPDATE keterangan SET expired_time = CURRENT_TIMESTAMP, modified_by = " . $this->session->userdata('credentials') . " WHERE expired_time IS NULL AND user_id = $user_id AND DATE_FORMAT(tgl,'%d/%m/%Y') LIKE '$key_f/$month/$year'";

                    $query = $this->db->query($str);

                    $data_mysql = array(
                        'user_id' => $user_id,
                        'tgl' => $tanggal,
                        'opt_keterangan' => $value,
                        'created_by' => $this->session->userdata('credentials')
                    );

                    $this->db->insert('keterangan', $data_mysql);
                }
            } else {
                $key_f = ($key < 10) ? "0" . $key : $key;

                $str = "UPDATE keterangan SET expired_time = CURRENT_TIMESTAMP, modified_by = ".$this->session->userdata('credentials')." WHERE expired_time IS NULL AND user_id = $user_id AND DATE_FORMAT(tgl,'%d/%m/%Y') LIKE '$key_f/$month/$year'";

                $query = $this->db->query($str);
            }
        }
        $this->db->trans_complete();        
    }
}

?>
