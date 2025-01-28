saya ingin mengambil duk_pelayanan by id, 
kemudian duk_pelayanan join dengan duk_jenis_pelayanan by duk_pelayanan id, 
lalu untuk duk_jenis_pelayanan join dengan duk_identitas_pemohon, juga join dengan duk_syarat_dokumen, 
untuk tiap data duk_identitas_pemohon pemohon, saya ingin mengambil data dari duk_data_identitas_pemohon
untuk tiap data duk_syarat_dokumen, saya ingin mengambil data dari duk_dokumen_pengajuan
buatkan query untuk mengambil data tersebut


buatkan migration, model, dan controller dengan kebutuhan data kolom : id, nama, user_id (refer to table users), jumlah_kk, jumlah_pr, jumlah_lk. tiru pembuatan model dan controller dengan cara meniru dari KategoriSuratController

sekarang buatkan model berdasarkan migration yang sudah dibuat, dan buatkan controller berdasarkan model yang sudah dibuat. buatkan view untuk (index, create, edit) data dari controller yang sudah dibuat (copy dari view/admin/dusun). dengan mengacu pada metode (index, create, edit) logic yang sudah pernah dibuat sebelumnya, contoh dusuncontroller. buatkan sesuai kebutuhan fitur update progres proyek pembangunan.
