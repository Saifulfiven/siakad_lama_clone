2f0811d3-dbb1-4fa0-9f78-1ffa5619b86f

/* Krs Mahasiswa */
select * from sia_nilai as nil
left join sia_jadwal_kuliah as jdk on nil.id_jdk=jdk.id
where jdk.id_smt BETWEEN '20152' and '20161'
and nil.id_mhs_reg='2f0811d3-dbb1-4fa0-9f78-1ffa5619b86f'

or 
/* Mendapatkan ipk mhs */
select sum(mk.sks_mk * nil.nilai_indeks), sum(mk.sks_mk) from sia_nilai as nil
left join sia_jadwal_kuliah as jdk on nil.id_jdk=jdk.id
left join sia_matakuliah as mk on mk.id=jdk.id_mk
where jdk.id_smt BETWEEN 20152 and 20161
and nil.id_mhs_reg='2f0811d3-dbb1-4fa0-9f78-1ffa5619b86f'



CREATE VIEW transkrip as

SELECT id_mhs_reg, min(nilai_huruf) , max(nilai_indeks), kode_mk, nm_mk, sks_mk,smt from
(

select id_mhs_reg,nilai_huruf, nilai_indeks, kode_mk, nm_mk, sks_mk,smt

from

(
select sia_nt.id_mhs_reg, sia_nt.nilai_huruf_diakui as nilai_huruf, sia_nt.nilai_indeks, sia_mk.kode_mk, sia_mk.nm_mk, sia_mk.sks_mk, sia_mkur.smt
from sia_nilai_transfer as sia_nt
left join sia_matakuliah as sia_mk on sia_nt.id_mk = sia_mk.id
left join sia_mk_kurikulum as sia_mkur on sia_mk.id = sia_mkur.id_mk 
where sia_nt.id_mhs_reg = 'f4356b3b-7243-4727-811b-e3cbdfb9f1c0'

union

select sia_nil.id_mhs_reg, sia_nil.nilai_huruf, sia_nil.nilai_indeks, sia_mk.kode_mk, sia_mk.nm_mk, sia_mk.sks_mk, sia_nil.semester_mk as smt
from sia_nilai as sia_nil 
left join sia_jadwal_kuliah as sia_jdk on sia_jdk.id = sia_nil.id_jdk
left join sia_matakuliah as sia_mk on sia_jdk.id_mk = sia_mk.id
where sia_nil.id_mhs_reg = 'f4356b3b-7243-4727-811b-e3cbdfb9f1c0'

) as result

) as result2
group by kode_mk



-- IPK on Hitung akm

SELECT SUM(mutu)/SUM(sks_mk) as ipk from 
(
	SELECT max(nilai_indeks) as ni, mutu, sks_mk, kode_mk from
		(

			select nilai_indeks * sks_mk as mutu, nilai_indeks, sks_mk, kode_mk from

			(
			select sia_nt.nilai_indeks, sia_mk.sks_mk, sia_mk.kode_mk
			from sia_nilai_transfer as sia_nt
			left join sia_matakuliah as sia_mk on sia_nt.id_mk = sia_mk.id
			left join sia_mk_kurikulum as sia_mkur on sia_mk.id = sia_mkur.id_mk 
			where sia_nt.id_mhs_reg = 'cd59f037-b557-4b0d-89de-beb28d5f0a65'

			union

			select sia_nil.nilai_indeks, sia_mk.sks_mk, sia_mk.kode_mk
			from sia_nilai as sia_nil 
			left join sia_jadwal_kuliah as sia_jdk on sia_jdk.id = sia_nil.id_jdk
			left join sia_matakuliah as sia_mk on sia_jdk.id_mk = sia_mk.id
			where sia_nil.id_mhs_reg = 'cd59f037-b557-4b0d-89de-beb28d5f0a65'
			and sia_nil.nilai_huruf is not null

			) as r1

		) as r2
	group by kode_mk
) as r3









select `sia_m1`.`id`, `sia_m1`.`nim`, `sia_m2`.`nm_mhs`, 
(SELECT count(mk.sks_mk) from sia_nilai as nil 
	left join sia_jadwal_kuliah as jdk on jdk.id = nil.id_jdk
	left join sia_matakuliah as mk on jdk.id_mk = mk.id
	where nil.id_mhs_reg=sia_m1.id
	and jdk.id_smt = 2016) 
as sks_smt, 
	(SELECT count(mk.sks_mk) from sia_nilai as nil 
	left join sia_jadwal_kuliah as jdk on jdk.id = nil.id_jdk
	left join sia_matakuliah as mk on jdk.id_mk = mk.id
	where nil.id_mhs_reg=sia_m1.id)
as sks_total, 
	(select sum(n.nilai_indeks)/sum(mk.sks_mk) as ips from sia_nilai as n
	left join sia_jadwal_kuliah as jdk on jdk.id=n.id_jdk
	left join sia_matakuliah as mk on jdk.id_mk=mk.id
	where n.id_mhs_reg=sia_m1.id and jdk.id_smt=2016)
as ips, 
(SELECT SUM(mutu)/SUM(sks_mk) as ipk from 
(
SELECT max(nilai_indeks) as ni, mutu, sks_mk, kode_mk from
(
select nilai_indeks * sks_mk as mutu, nilai_indeks, sks_mk, kode_mk from
(
select nt.nilai_indeks, mk.sks_mk, mk.kode_mk
from sia_nilai_transfer as nt
left join sia_matakuliah as mk on nt.id_mk = mk.id
left join sia_mk_kurikulum as mkur on mk.id = mkur.id_mk 
where nt.id_mhs_reg = sia_m1.id

union

select nil.nilai_indeks, mk.sks_mk, mk.kode_mk
from sia_nilai as nil 
left join sia_jadwal_kuliah as jdk on jdk.id = nil.id_jdk
left join sia_matakuliah as mk on jdk.id_mk = mk.id
where nil.id_mhs_reg = sia_m1.id
and nil.nilai_huruf is not null

) as r1

) as r2
group by kode_mk
) as r3) as ipk from `sia_mahasiswa_reg` as `sia_m1` left join `sia_mahasiswa` as `sia_m2` on `sia_m1`.`id_mhs` = `sia_m2`.`id` where left(semester_mulai,4)=2005 and `sia_m1`.`id_prodi` in (61201, 62201))




-- Tunggakn pembayaran
select total_bayar - potongan,id_smt,biaya_1,biaya_2,semester_mulai, 
if(id_smt-semester_mulai = 0,  biaya_1 - (total_bayar - potongan), biaya_2 - (total_bayar - potongan) ) as tunggakan

from (
	SELECT sum(pmb.jml_bayar) as total_bayar,IFNULL(pbk.potongan, 0) AS potongan, pmb.id_smt,m1.semester_mulai,
	(select bpp+spp+seragam+lainnya as biaya from sia_biaya_kuliah where tahun=left(m1.semester_mulai,4)) as biaya_1,
	(select bpp from sia_biaya_kuliah where tahun=left(m1.semester_mulai,4)) as biaya_2

	FROM sia_pembayaran as pmb

	left join sia_potongan_biaya_kuliah as pbk on pmb.id_mhs_reg=pbk.id_mhs_reg
	left join sia_mahasiswa_reg as m1 on pmb.id_mhs_reg = m1.id
	where pmb.id_mhs_reg='9f91aeb0-0079-412f-b737-33dac9802c2a'
	-- and pmb.id_smt=20171
	group by pmb.id_smt
) as result





-- DATA MAHASISWA LULUS
select m2.nm_mhs, m1.nim, m2.tempat_lahir, m2.tgl_lahir, m2.alamat, m2.hp, m2.nm_ayah, m2.nm_ibu, m1.ipk, k.nm_konsentrasi,m1.judul_skripsi,pr.nm_prodi from mahasiswa_reg as m1 left join mahasiswa as m2 on m1.id_mhs = m2.id left join prodi as pr on m1.id_prodi = pr.id_prodi left join konsentrasi as k on m1.id_konsentrasi = k.id_konsentrasi where m1.semester_keluar=20191 and m1.id_jenis_keluar = 1