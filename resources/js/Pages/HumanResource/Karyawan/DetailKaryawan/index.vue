<template>
  <div class="uk-container uk-container-large" style="padding:1em;">
    <div class="uk-grid uk-grid-small">
      <router-link :to = "{ name:'listKaryawan' }" class="uk-width-auto">
        <a href="#" style="background-color:white;padding:0.3em;display:inline-block;">
            <span uk-icon="icon:chevron-left;ratio:1"></span>
        </a>
      </router-link>  
      <h4 style="font-weight:500;margin:0.2em 0 0 0;padding:0;" class="uk-text-uppercase">{{ pageTitle }}</h4>
    </div>
    <div>
        <div class="uk-card uk-card-default" style="padding:1em;margin-top:1em;border-top: 10px solid rgb(204, 2, 2);min-height:300px;">
          <!-- <div class="uk-width-1-1@s" style="padding:0 0.5em 1em 0.5em;">
              <h5 style="padding:0;margin:0;font-size:16px;font-weight:bold;color:#333;">DATA PROFIL</h5>
          </div> -->
          <div style="padding:0.5em;margin:0;">
            <div class="uk-grid-small uk-width-1-1@l 1-1@m" uk-grid style="padding:0;margin:0;">
              <div class="uk-width-1-4@m">
                <img src="https://picsum.photos/210/280" alt="Foto Profil" style="max-width: 100%; height: auto;">
              </div>
              <div class="uk-width-3-4@m">
                <div class="uk-width-1-1@s" style="padding:0 0.5em 1em 0;">
                    <h5 style="padding:0;margin:0;font-size:18px;font-weight:bold;color:#333;">DATA PROFIL</h5>
                </div>
                <div class="uk-grid-small" uk-grid style="height: 100%;">
                  <div class="uk-width-1-2@m">
                    <dl class="uk-description-list karyawan-description-list">
                      <dt>NIP | ID Karyawan</dt>
                      <dd>{{ karyawan.nip }} | {{ karyawan.karyawan_id }}</dd>
                      <dt>Nama</dt>
                      <dd>{{ karyawan.nama }}</dd>
                      <dt>Tempat Tanggal Lahir</dt>
                      <dd>{{ karyawan.tempat_lahir }}, {{ karyawan.tanggal_lahir }}</dd>
                      <dt>Jenis Kelamin</dt>
                      <dd v-if="karyawan.jns_kelamin == 'L'">Laki-laki</dd>
                      <dd v-else-if="karyawan.jns_kelamin == 'P'">Perempuan</dd>
                      <dd v-else>tidak ada info</dd>
                    </dl>
                  </div>
                  <div class="uk-width-1-2@m">
                    <dl class="uk-description-list karyawan-description-list">
                      <dt>Jabatan</dt>
                      <dd>{{ karyawan.jabatan_nama }}</dd>
                      <dt>Unit</dt>
                      <dd>{{ karyawan.unit_nama }}</dd>
                      <dt>Mulai Bekerja</dt>
                      <dd>{{ karyawan.tgl_masuk }}</dd>
                      <dt>Status</dt>
                      <dd>{{ karyawan.status }}</dd>
                    </dl>
                  </div>
                  <!-- <div class="baris-tambah">
                    <div class="baris-tambah-inner">
                      <button class="tombol-tambah" @click.prevent="ubahDataKaryawan">Ubah Data Karyawan</button>
                    </div>
                  </div> -->
                </div>
                
              </div>
            </div>
          </div>
        </div>
    </div>
    <div style="padding:1em 0 1em 0;" class="uk-container">
        <div style="padding:0; margin-top:1em; border-radius:10px;min-height:400px;">
            <!-- <div> -->
                <ul uk-accordion="multiple:false" class="ref-accordion" style="width:100%;padding:0;margin:0;background-color:whitesmoke;">
                    <li class="uk-open" style="margin:0;padding:0;">
                        <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#" >Data Pelengkap</a>
                        <div class="uk-accordion-content ref-accordion-content">                                            
                          <div style="padding:0.5em;margin:0;">
                            <div class="uk-grid-small uk-width-1-1@l 1-1@m" uk-grid style="padding:0;margin:0;">
                              <div class="uk-width-1-3@m">
                                <dl class="uk-description-list karyawan-description-list">
                                  <dt>ID Karyawan</dt>
                                  <dd>{{ karyawan.karyawan_id }}</dd>
                                  <dt>NIP</dt>
                                  <dd>{{ karyawan.nip }}</dd>
                                  <dt>Nama</dt>
                                  <dd>{{ karyawan.nama }}</dd>
                                  <dt>Tempat Tanggal Lahir</dt>
                                  <dd>{{ karyawan.tempat_lahir }}, {{ karyawan.tanggal_lahir }}</dd>
                                  <dt>Jenis Kelamin</dt>
                                  <dd v-if="karyawan.jns_kelamin == 'L'">Laki-laki</dd>
                                  <dd v-else-if="karyawan.jns_kelamin == 'P'">Perempuan</dd>
                                  <dd v-else>tidak ada info</dd>
                                </dl>
                              </div>
                              <div class="uk-width-1-3@m">
                                <dl class="uk-description-list karyawan-description-list">
                                  <dt>Jabatan</dt>
                                  <dd>{{ karyawan.jabatan_nama }}</dd>
                                  <dt>Unit</dt>
                                  <dd>{{ karyawan.unit_nama }}</dd>
                                  <dt>Mulai Bekerja</dt>
                                  <dd>{{ karyawan.tgl_masuk }}</dd>
                                  <dt>Status</dt>
                                  <dd>{{ karyawan.status }}</dd>
                                  <dt>Rekening Gaji</dt>
                                  <dd>{{ karyawan.rekening_gaji }}</dd>
                                </dl>
                              </div>
                              <div class="uk-width-1-3@m">
                                <dl class="uk-description-list karyawan-description-list">
                                  <dt>No. Kependudukan</dt>
                                  <dd>{{ karyawan.nik }}</dd>
                                  <dt>Telepon</dt>
                                  <dd>{{ karyawan.no_telepon }}</dd>
                                  <dt>Email</dt>
                                  <dd>{{ karyawan.email }}</dd>
                                  <dt>Alamat</dt>
                                  <dd>{{ karyawan.keahlian }}</dd>
                                  <!-- <dt>Alamat</dt>
                                  <dd>{{ karyawan.alamat }}</dd> -->
                                  <dt>Status Perkawinan</dt>
                                  <dd>{{ karyawan.status_perkawinan }}</dd>
                                </dl>
                              </div>
                            </div>
                          </div>
                          <div class="baris-tambah">
                            <div class="baris-tambah-inner">
                              <span class="teks">Ubah informasi data pelengkap</span>
                              <button class="tombol-tambah" @click.prevent="ubahDataKaryawan">Ubah</button>
                            </div>
                          </div>
                        </div>
                    </li>
                    <li style="margin:0;padding:0;">
                      <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#">Daftar Keluarga</a>
                      <div class="uk-accordion-content ref-accordion-content" style="position: relative;">
                        <div style="padding: 0.5em; margin: 0;">
                          <table class="uk-table uk-table-divider tabel-keluarga" style="margin-right: 20px;">
                            <thead>
                              <tr>
                                <th>No.</th>
                                <th>Nama</th>
                                <th>Hubungan</th>
                                <th>Tanggal Lahir</th>
                                <th>Jenis Kelamin</th>
                                <th>Pendidikan Terakhir</th>
                                <th>Catatan</th>
                                <th></th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(hrkeluarga, index) in dataKeluarga" :key="index">
                                <td>{{ index + 1 }}</td>
                                <td>{{ hrkeluarga.nama }}</td>
                                <td>{{ hrkeluarga.hubungan }}</td>
                                <td>{{ hrkeluarga.tanggal_lahir }}</td>
                                <td>{{ hrkeluarga.jns_kelamin }}</td>
                                <td>{{ hrkeluarga.pendidikan_terakhir }}</td>
                                <td>{{ hrkeluarga.catatan }}</td>
                                <td>
                                  <button class="tombol-hapus" @click.prevent="hapusItem(index)">
                                    <span class="uk-icon uk-icon-small" uk-icon="trash"></span>
                                  </button>
                                </td>
                              </tr>
                              <!-- <tr v-for="(keluarga, keluargaId) in dataItems" :key="keluargaId">
                                <td>{{ keluargaId }}</td>
                                <td>{{ keluarga.nama }}</td>
                                <td>{{ keluarga.hubungan }}</td>
                                <td>{{ keluarga.tanggal_lahir }}</td>
                                <td>{{ keluarga.jns_kelamin }}</td>
                                <td>{{ keluarga.pendidikan_terakhir }}</td>
                                <td>{{ keluarga.catatan }}</td>
                                <td>
                                  <button class="tombol-hapus" @click.prevent="hapusItem(index)">
                                    <span class="uk-icon uk-icon-small" uk-icon="trash"></span>
                                  </button>
                                </td>
                              </tr> -->
                            </tbody>
                          </table>
                          <div style="margin-top:1em;background-color:#fff;">
                              <form-keluarga ref="formKeluarga"></form-keluarga>
                          </div>
                        </div>
                        <div class="baris-tambah">
                          <div class="baris-tambah-inner">
                            <span class="teks">Tambahkan Anggota Keluarga Baru</span>
                            <button class="tombol-tambah" @click.prevent="tambahKeluarga">+ Tambah</button>
                          </div>
                        </div>
                        <!-- <button class="tombol-edit" style="position: absolute; top: 10px; right: 10px;" @click.prevent="editKeluarga">Edit</button> -->
                      </div>
                    </li>
                    <li style="margin:0;padding:0;">
                        <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#">Daftar Riwayat Pendidikan</a>
                        <div class="uk-accordion-content ref-accordion-content">                                            
                          <div class="timeline-container uk-grid" style="margin-left: 10px;">
                            <div class="timeline uk-width-1-2">
                              <h3>Formal</h3>
                              <ul>
                                <li v-for="(info, index) in sortedTimelineData" :key="index">
                                  <span></span>
                                  <div>{{ info.institusi }}</div>
                                  <div>{{ info.jenjang }} - {{ info.nama_pendidikan }}</div>
                                  <div>{{ info.ipk }}</div>
                                  <div class="year">
                                    <span>{{ info.yearEnd }}</span>
                                    <span>{{ info.yearStart }}</span>
                                  </div>
                                </li>
                              </ul>
                            </div>  
                            <div class="timeline uk-width-1-2">
                              <h3>Non Formal</h3>
                              <ul>
                                <li v-for="(info, index) in sortedTimelineData" :key="index">
                                  <span></span>
                                  <div>{{ info.institusi }}</div>
                                  <div>{{ info.jenjang }} - {{ info.nama_pendidikan }}</div>
                                  <div>{{ info.ipk }}</div>
                                  <div class="year">
                                    <span>{{ info.yearEnd }}</span>
                                    <span>{{ info.yearStart }}</span>
                                  </div>
                                </li>
                                <!-- <li v-for="(pendidikan, pendidikanId) in sortedTimelineData" :key="pendidikanId">
                                  <span></span>
                                  <div>{{ pendidikan.institusi }}</div>
                                  <div>{{ pendidikan.jenjang }} - {{ pendidikan.nama_pendidikan }}</div>
                                  <div>{{ pendidikan.ipk }}</div>
                                  <div class="year">
                                    <span>{{ pendidikan.yearEnd }}</span>
                                    <span>{{ pendidikan.yearStart }}</span>
                                  </div>
                                </li> -->
                              </ul>
                            </div>  
                            <div style="margin-top:1em;background-color:#fff;">
                              <form-pendidikan ref="formPendidikan"></form-pendidikan>
                            </div>
                          </div>
                          <div class="baris-tambah">
                            <div class="baris-tambah-inner">
                              <span class="teks">Tambahkan Riwayat Pendidikan Baru</span>
                              <button class="tombol-tambah"  @click.prevent="tambahPendidikan">+ Tambah</button>
                            </div>
                          </div>
                        </div>
                    </li>   
                    <li style="margin:0;padding:0;">
                        <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#">Daftar Riwayat Jabatan</a>
                        <div class="uk-accordion-content ref-accordion-content">                                            
                          <div class="timeline">
                            <ul>
                              <li v-for="jabatan in riwayatJabatan" :key="jabatan.hr_jabatan_id">
                              <!-- <li v-for="(jabatan, index) in sortedTimelineDataJabatan" :key="index"> -->
                                <span></span>
                                <div>{{ jabatan.jabatan }}</div>
                                <div>{{ jabatan.unit }}</div>
                                <div>{{ jabatan.keterangan }}</div>
                                <div class="year">
                                  <span>{{ jabatan.tgl_mulai }}</span>
                                  <span>{{ jabatan.tgl_selesai }}</span>
                                </div>
                              </li>
                            </ul>
                            <div style="margin-top:1em;background-color:#fff;">
                              <form-jabatan ref="formJabatan"></form-jabatan>
                              <form-jabatan :riwayatJabatan="riwayatJabatan" @formJabatanClosed="handleFormJabatanClosed"></form-jabatan>
                            </div>
                          </div> 
                          <div class="baris-tambah">
                            <div class="baris-tambah-inner">
                              <span class="teks">Tambahkan Riwayat Jabatan Baru</span>
                              <button class="tombol-tambah"  @click.prevent="tambahJabatan">+ Tambah</button>
                            </div>
                          </div>
                        </div>
                    </li>  
                    <li style="margin:0;padding:0;">
                        <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#">Daftar Dokumen Pendukung</a>
                        <div class="uk-accordion-content ref-accordion-content">                                            
                          <!-- <div style="margin-top:1em;background-color:#fff;">
                              <form-dokumen ref="formDokumen"></form-dokumen>
                          </div> -->
                          <div class="baris-tambah">
                            <div class="baris-tambah-inner">
                              <span class="teks">Tambahkan Dokumen Pendukung Baru</span>
                              <button class="tombol-tambah"  @click.prevent="tambahDokumen">+ Tambah</button>
                            </div>
                          </div>
                        </div>
                    </li>
                </ul>
          </div>
      </div>
  </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
// import BaseTable from '@/Components/BaseTable';
import FormKeluarga from '@/Pages/HumanResource/Karyawan/DetailKaryawan/component/FormKeluarga.vue'
import FormPendidikan from '@/Pages/HumanResource/Karyawan/DetailKaryawan/component/FormPendidikan.vue'
// import FormJabatan from '@/Pages/HumanResource/Karyawan/DetailKaryawan/component/FormJabatan.vue'
// import FormDokumen from '@/Pages/HumanResource/Karyawan/DetailKaryawan/component/FormDokumen.vue'
// import SubReferensi from '@/Pages/MasterData/Referensi/components/SubReferensi.vue';

export default {
  props : {
      karyawanId : {type:String, required:true },
      keluargaId : {type:String, required:true },
      pendidikanId : {type:String, required:true },
      jabatanId : {type:String, required:true },
      dokumenId : {type:String, required:true },
  },

  components : {
      FormKeluarga,
      FormPendidikan,
      // FormJabatan,
      // FormDokumen,
  },
  
  data() {
      return {
          pageTitle: null,

          dataKeluarga: [
            { nama: 'Bian', 
              hubungan: 'Adik', 
              tanggal_lahir: '23 Mei 2009', 
              jns_kelamin: 'Laki-laki', 
              pendidikan_terakhir: 'SMP', 
              catatan: 'Adik kedua' 
            },
            { nama: 'Bian', 
              hubungan: 'Adik', 
              tanggal_lahir: '23 Mei 2009', 
              jns_kelamin: 'Laki-laki', 
              pendidikan_terakhir: 'SMP', 
              catatan: 'Adik kedua' 
            },
          ],

          timelineData: [
            {
              institusi: 'Institut Teknologi Bandung',
              jenjang: 'S1',
              nama_pendidikan: 'Jurusan Fisika',
              ipk: '',
              yearStart: '2018',
              yearEnd: '2023',
            },
            {
              institusi: 'Binar Academy',
              jenjang: '',
              nama_pendidikan: 'Fullstack Web Development',
              ipk: '4.60/5.00',
              yearStart: '2022',
              yearEnd: '2023',
            },
            {
              institusi: 'SMA Negeri 2 Cimahi',
              jenjang: '',
              nama_pendidikan: 'Jurusan IPA',
              ipk: '',
              yearStart: '2015',
              yearEnd: '2018',
            },
          ],

          timelineDataJabatan: [
            { 
              jabatan: 'Staff IT', 
              unit: 'Bank Darah', 
              keterangan: '-',
              yearStart: '2018', 
              yearEnd: '2019',
            },
            { jabatan: 'Staff Penunjang Medis', 
              unit: 'Bank Darah', 
              keterangan: '-',
              yearStart: '2019', 
              yearEnd: '2020', 
            },
            { jabatan: 'Staff Pelayanan', unit: 'Bank Darah', yearStart: '2015', yearEnd: '2018', keterangan: '-'},
          ],
          
          karyawan : {
              client_id : null,
              karyawan_id : null,
              nama : null,
              nip : null,
              jns_kelamin : null,
              tempat_lahir : null,
              tanggal_lahir : null,
              nik : null,
              no_telepon : null,
              email : null,
              tgl_masuk : null,
              status_perkawinan : null,
              jabatan_id : null,
              jabatan_nama : null,
              unit_id : null,
              unit_nama : null,
              url_foto : null,
              keahlian : null,
              status : null,
              rekening_gaji : null,
              is_aktif : true,
          },

          hrkeluarga : {
              client_id:null,
              hr_keluarga_id:null,
              karyawan_id:null,
              nama_keluarga : null,
              hubungan : null,
              tempat_lahir : null,
              tanggal_lahir : null,
              pendidikan : null,
              jns_kelamin : null,
              catatan : null,
              is_aktif : true,          
          },

          hrpendidikan : {
              client_id:null,
              hr_pendidikan_id:null,
              karyawan_id : null,
              jns_pendidikan : null,
              jenjang : null,
              nama_pendidikan : null,
              institusi : null,
              tahun_lulus : null,
              ipk : null,
              catatan : null,
              is_aktif : true,          
          },

          hrjabatan : {
              client_id : null,
              hr_jabatan_id : null,
              karyawan_id : null,
              jabatan_id : null,
              jabatan_nama : null,
              unit_id : null,
              unit_nama : null,
              tgl_mulai : null,
              tgl_selesai : null,
              is_selesai : null,
              is_aktif : true,          
          },

          hrdokumen : {
              client_id:null,
              hr_dokumen_id:null,
              jenis_dokumen : null,
              url_dokumen : null,
              catatan : null,
              is_aktif : true,          
          },

          // riwayatJabatan: [],
          // schedules: [],
          // isModalOpen: false,
      }
  },

  computed : {
      ...mapGetters(['errors']),
      ...mapGetters('auth',['karyawan_id']),
      ...mapGetters('auth',['hr_keluarga_id']),
      ...mapGetters('referensi',['pekerjaanRefs']),

      sortedTimelineData() {
        return this.timelineData.sort((a, b) => {
        const aStart = parseInt(a.yearStart);
        const bStart = parseInt(b.yearStart);
        const aEnd = parseInt(a.yearEnd);
        const bEnd = parseInt(b.yearEnd);
        return bStart - aStart || bEnd - aEnd;
        });
      },

      sortedTimelineDataJabatan() {
        return this.timelineData.sort((a, b) => {
        const aStart = parseInt(a.yearStart);
        const bStart = parseInt(b.yearStart);
        const aEnd = parseInt(a.yearEnd);
        const bEnd = parseInt(b.yearEnd);
        return bStart - aStart || bEnd - aEnd;
        });
      },
  },
  
  created() {
      this.initialize();
  },

  methods : {
      // ...mapActions('humanresource',['deleteKecamatan','dataKecamatan']),
      ...mapActions('karyawan',['createKaryawan','dataKaryawan','updateKaryawan','deleteKaryawan']),
      ...mapActions('hrdokumen',['uploadDokumen','createDokumen','updateDokumen','deleteDokumen']),
      ...mapActions('hrkeluarga',['createKeluarga','updateKeluarga','deleteKeluarga']),
      ...mapActions('hrjabatan',['createJabatan','updateJabatan','deleteJabatan']),
      ...mapActions('hrpendidikan',['createPendidikan','updatePendidikan','deletePendidikan']),
      ...mapActions('referensi',['listReferensi']),
      ...mapMutations(['CLR_ERRORS']),

      initialize() {
        this.CLR_ERRORS();
        this.pageTitle = `DATA KARYAWAN ${this.karyawanId}`;          
        this.listReferensi().then((response) => {
            if (response.success == true) { 
              this.checkData(); 
            }
            else { alert (response.message) }
        });
        this.retrieveDataKaryawan();
      },

      checkData(){
          this.$refs.refPekerjaan.refreshData(this.pekerjaanRefs);
      },

      retrieveDataKaryawan(){
        this.dataKaryawan(this.karyawanId).then((response) => {
            if (response.success == true) { 
              console.log(response.data);
              this.karyawan = response.data;
             }
            else { alert (response.message) }
        });
      },

      ubahDataKaryawan(){
        this.$router.push({ name: 'formKaryawan', params: { karyawanId: this.karyawanId } });
      },

      tambahKeluarga() {
        console.log('Tombol tambah diklik');
        this.$refs.formKeluarga.newKeluarga();
      },

      editKeluarga() {
        console.log('Tombol tambah diklik');
        this.$refs.formKeluarga.editKeluarga();
      },

      tambahPendidikan() {
        console.log('Tombol tambah diklik');
        this.$refs.formPendidikan.newPendidikan();
      },

      tambahJabatan() {
        console.log('Tombol tambah diklik');
        this.$refs.formJabatanKeluarga.newJabatan();
      },

      tambahDokumen() {
        console.log('Tombol tambah diklik');
        this.$refs.formDokumen.newDokumen();
      },

      // handleFormJabatanClosed(isClosed) {
      //   if (isClosed) {
      //     // Refresh data riwayat jabatan jika form ditutup
      //     this.refreshRiwayatJabatan();
      //   }
      // },
      // refreshRiwayatJabatan() {
      //   // Isi metode ini dengan logika untuk memperbarui data riwayat jabatan
      //   // Misalnya dengan mengambil data dari API atau memperbarui data secara lokal
      // },

      // openModal() {
      //   this.isModalOpen = true;
      // },
      // submitForm(newSchedule) {
      //   this.schedules.push(newSchedule);
      // },
      // formatDate(date) {
      //   const options = { year: 'numeric', month: 'long', day: 'numeric' };
      //   return new Date(date).toLocaleDateString('en-US', options);
      // },
      // checkTime(start, end) {
      //   const startTime = new Date(start).getTime();
      //   const endTime = new Date(end).getTime();
      //   const now = new Date().getTime();
      //   return now >= startTime && now <= endTime;
      // },
  }
}
</script>

<style>
.ref-accordion li {
    padding:0;
    margin:0;
    background-color:white;
}
li.uk {
    margin:0;
    padding:0;
}

.ref-accordion li a.ref-accordion-title {
    padding: 1em;
    color: #cc0202;
    font-weight:500;
    font-size:14px;
    margin:0;
}

.ref-accordion li.uk-open a.ref-accordion-title {
    color: #fff;
    background-color:#cc0202;
}

a.ref-accordion-title {        
    display: block;
    padding:0 0 0 0; 
    font-size:1em; 
    font-weight:500;
    text-transform: uppercase;
}

div.ref-accordion-content {
    margin:0;
    padding:0.5em;
    background-color: #fff;
}

dl.karyawan-description-list dt {
    margin:0;
    padding:0;
    font-size:12px;
    font-weight: bold;
    color:#333;
}

dl.karyawan-description-list dd {
    margin:0;
    padding:0;
    font-size:13px;
    font-weight: 400;
    color:#000;
}

.tabel-keluarga th {
  padding: 0.5em;
  font-size: 13px; 
  font-weight: bold;
  color: #333;
  text-align: center; 
}

.tabel-keluarga td {
  padding: 0.5em;
  font-size: 12px; 
  color: #000;
  text-align: center; 
}

.tabel-keluarga td:nth-child(3),
.tabel-keluarga td:nth-child(4),
.tabel-keluarga td:nth-child(5) {
  font-weight: normal; /* Menghapus pengaturan font-weight bold pada kolom Hubungan, Tanggal Lahir, dan Pendidikan Terakhir */
}

.tombol-hapus {
  border: none;
  background: none;
}

.timeline {
  padding: 5px 45px;
}

.timeline ul {
  position: relative;
}

.timeline li {
  position: relative;
  margin: 60px 35px;
  width: 100%;
  list-style: none;
  line-height: 25px;
}

.timeline li > span {
  position: absolute;
  left: -25px;
  height: 100%;
  border: 2px solid #d7cccc;
}

.timeline li > span::before,
.timeline li > span::after {
  content: '';
  position: absolute;
  width: 10px;
  height: 10px;
  border: 2px solid #d7cccc;
  left: -7px;
  background: #bb3131;
}

.timeline li > span::before {
  top: -15px;
}

.timeline li > span::after {
  top: 100%;
}

.timeline li div:nth-child(2) {
  font-size: 1em;
}

.timeline li div:nth-child(3),
.timeline li div:nth-child(4) {
  font-size: 0.8em;
  font-style: italic;
  color: rgba(26, 24, 24, 0.75);
}

.timeline li .year span {
  position: absolute;
  font-size: 0.8em;
  left: -75px;
  width: 20px;
  text-align: right;
}

.timeline li .year span:first-child {
  top: -20px;
}

.timeline li .year span:last-child {
  top: 100%;
}

.baris-tambah {
  background-color: #fff;
  border-radius: 4px;
  padding: 8px;
  margin: 0.5cm;
  transition: border-color 0.3s;
}

.baris-tambah:hover,
.baris-tambah:focus {
  border-left: 8px solid rgba(204, 2, 2, 0.3);
}

.baris-tambah-inner {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.baris-tambah .teks {
  font-size: 14px;
}

.baris-tambah .tombol-tambah {
  background-color: #cc0202;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.baris-tambah .tombol-tambah:hover {
  background-color: #cc0202;
}

.baris-tambah:hover,
.baris-tambah:focus .baris-tambah-inner {
  border-left: 8px solid rgb(204, 2, 2);
  box-shadow: 0 0 0 1px rgb(204, 2, 2);
}
</style>