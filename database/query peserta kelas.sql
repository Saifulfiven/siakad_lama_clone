select jdk.*, jk.jam_masuk, mk.nm_mk,m1.nim,m2.nm_mhs from nilai as n
left join mahasiswa_reg as m1 on n.id_mhs_reg = m1.id
left join mahasiswa as m2 on m1.id_mhs = m2.id
left join jadwal_kuliah as jdk on n.id_jdk = jdk.id
left join jam_kuliah as jk on jk.id = jdk.id_jam
left join mk_kurikulum as mkur on mkur.id = jdk.id_mkur
left join matakuliah as mk on mk.id = mkur.id_mk
where jdk.id_smt=20181
and jdk.jenis = 1