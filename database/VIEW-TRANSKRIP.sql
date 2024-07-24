CREATE OR REPLACE VIEW view_transkrip AS 
	(
	select sia_nt.id_mhs_reg, sia_nt.nilai_huruf_diakui as nilai_huruf, sia_nt.nilai_indeks, sia_mk.kode_mk, sia_mk.nm_mk, sia_mk.sks_mk, sia_mkur.smt
	from sia_nilai_transfer as sia_nt
	left join sia_matakuliah as sia_mk on sia_nt.id_mk = sia_mk.id
	left join sia_mk_kurikulum as sia_mkur on sia_mk.id = sia_mkur.id_mk
	)
	union
	(
	select sia_nil.id_mhs_reg, sia_nil.nilai_huruf, sia_nil.nilai_indeks, sia_mk.kode_mk, sia_mk.nm_mk, sia_mk.sks_mk, sia_nil.semester_mk as smt
	from sia_nilai as sia_nil 
	left join sia_jadwal_kuliah as sia_jdk on sia_jdk.id = sia_nil.id_jdk
	left join sia_matakuliah as sia_mk on sia_jdk.id_mk = sia_mk.id
	)