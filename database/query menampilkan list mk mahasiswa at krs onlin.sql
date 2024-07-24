select * from sia_jadwal_kuliah as jdk
left join sia_mk_kurikulum as mkur on mkur.id = jdk.id_mkur
left join sia_kurikulum as kur on mkur.id_kurikulum = kur.id
where jdk.id_smt=20171
and jdk.id_prodi=61201
and mkur.smt=1

-- left join sia_matakuliah as mk on jdk.id_mk = mk.id