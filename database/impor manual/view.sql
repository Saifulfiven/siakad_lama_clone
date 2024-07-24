-- View Mahasiswa transfer sks diakui 0 --
CREATE VIEW v_konversi_0 as
	select m2.id, m1.nim,m2.nm_mhs,m1.id_prodi,pr.jenjang,pr.nm_prodi
	from mahasiswa_reg as m1 
	left join mahasiswa as m2 on m1.id_mhs = m2.id
	left join prodi as pr on m1.id_prodi = pr.id_prodi
	left join nilai_transfer as tr on m1.id = tr.id_mhs_reg
		where m1.jenis_daftar = 2 and m1.id_jenis_keluar = 0
			and tr.id_mk is null;

-- End view mahasiswa transfer --


-- View umur mahasiswa
CREATE VIEW v_umur_mhs as
	SELECT id,nm_mhs,tgl_lahir,DATEDIFF(CURRENT_DATE, STR_TO_DATE(t.tgl_lahir, '%Y-%m-%d'))/365 AS umur
	  FROM mahasiswa t;

-- View Mahasiswa yang melakukan KRS
CREATE VIEW v_mhs_krsan as
	select m2.id as id_mhs_reg, m2.id_prodi, m2.nim, m1.nm_mhs, p.nm_prodi, p.jenjang,
		ks.id_smt
		from krs_status as ks 
		left join mahasiswa_reg as m2 on ks.id_mhs_reg = m2.id 
		left join mahasiswa as m1 on m1.id = m2.id_mhs  
		left join prodi as p on p.id_prodi = m2.id_prodi 
		where m2.id_jenis_keluar = 0 
		and ks.status_krs = '1'

-- jumlah matakuliah diambil
SELECT count(*) as jml FROM nilai as nil
	left join jadwal_kuliah as jdk on nil.id_jdk = jdk.id
	where jdk.id_smt = 20181
	and nil.id_mhs_reg='0009910c-d286-437e-a918-4d6f1132acf5'

-- Jumlah Kehadiran
SELECT 
SUM(a_1)+
SUM(a_2)+
SUM(a_3)+
SUM(a_4)+
SUM(a_5)+
SUM(a_6)+
SUM(a_7)+
SUM(a_8)+
SUM(a_9)+
SUM(a_10)+
SUM(a_11)+
SUM(a_12)+
SUM(a_13)+
SUM(a_14)

FROM nilai as nil
left join mahasiswa_reg as m1 on nil.id_mhs_reg = m1.id
left join jadwal_kuliah as jdk on nil.id_jdk = jdk.id
where jdk.id_smt = 20181
and nil.id_mhs_reg='0009910c-d286-437e-a918-4d6f1132acf5'
