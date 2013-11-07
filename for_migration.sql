ALTER TABLE `keterangan` CHANGE `tgl` `tanggal` DATE NOT NULL;

ALTER TABLE `keterangan` CHANGE `tanggal` `tgl` DATE NOT NULL;

ALTER TABLE `opt_keterangan` ADD `counter_hadir` TINYINT NOT NULL DEFAULT '1' COMMENT 'perhitungan dalam kehadiran (dihitung hadir atau tidak)' AFTER `content`;

UPDATE `hris_att`.`opt_keterangan` SET `counter_hadir` = '0' WHERE `opt_keterangan`.`opt_keterangan_id` =10;