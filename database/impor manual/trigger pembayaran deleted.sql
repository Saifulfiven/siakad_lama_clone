DELIMITER $$
CREATE TRIGGER `pembayaran_deleted_moved` AFTER DELETE ON `pembayaran` FOR EACH ROW INSERT INTO pembayaran_deleted(id_smt, id_mhs_reg, id_jns_pembayaran, tgl_bayar, jml_bayar, ket, created_at) VALUES (OLD.id_smt, OLD.id_mhs_reg, OLD.id_jns_pembayaran, OLD.tgl_bayar, OLD.jml_bayar, OLD.ket, OLD.created_at)
$$
DELIMITER ;