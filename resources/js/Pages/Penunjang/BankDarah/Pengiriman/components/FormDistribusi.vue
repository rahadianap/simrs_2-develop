<!-- <template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="modalDarah" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body" style="padding:0;">
            <form action="" @submit.prevent="">
                <div class="uk-card-default uk-grid-small" uk-grid style="padding:0;margin:0; border-top:3px solid #cc0202; height: 70px;">
                    <div class="uk-width-expand" style="padding:1em;">
                        <h3 style="color:black; font-weight: 400;">{{formTitle}}</h3>
                    </div>
                    <div class="uk-width-auto" style="padding:0.8em;">
                        <button class="uk-button" @click.prevent="modalClosed" type="button" style="color:#333; font-weight: 500;">TUTUP</button>
                        <button class="uk-button" type="submit" style="color:#fff; font-weight: 500;background-color: #cc0202;margin-left:5px;">SIMPAN</button>
                    </div>
                </div>
                <div class="hims-form-container" style="padding:0 1.5em 2em 1.5em; margin:0;">                    
                    <div class="uk-grid-small hims-form-subpage" uk-grid  style="border:none;">
                        <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA PENERIMAAN</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                detail data penerimaan persediaan darah.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                            <div class="uk-grid-small uk-width-3-4@l 1-1@m" uk-grid> 
                                <div class="uk-width-1-1">
                                    <h5 style="font-weight:500;">{{penerimaan.darah_terima_id}}</h5>
                                </div>
                                <div class="uk-width-1-4@m uk-hidden">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Status*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="text" v-model="penerimaan.status" disabled style="color:black;">
                                    </div>
                                </div>
                       
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Terima*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="date" v-model="penerimaan.tgl_terima" style="color:black;">
                                    </div>
                                </div>

                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Asal Darah*</label>
                                    <div class="uk-form-controls">
                                        <select v-model="penerimaan.asal_darah" class="uk-select uk-form-small">
                                            <option v-if="isRefExist" v-for="dt in asalDarahRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Donor</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small uk-text-uppercase" type="text" v-model="penerimaan.nama_donor" style="color:black;">
                                    </div>
                                </div>        
                            
                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" type="text" v-model="penerimaan.catatan" style="color:black;"></textarea>
                                    </div>
                                </div>
                            </div>                            
                        </div>
                    </div>

                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-1@m" style="padding:0 0.5em 0.5 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">ITEM PENERIMAAN 
                                <span style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    daftar item yang diterima
                                </span>
                            </h5>
                        </div>
                        <div class="uk-width-1-1@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;"> 
                            <div class="uk-width-1-1@m" style="padding:0 0.5em 0.2em 0.5em;margin:0;">
                                <table class="uk-table hims-table">
                                    <thead class="uk-card uk-card-default uk-box-shadow-small" style="border-top:2px solid #cc0202;">
                                        <tr>
                                            <th style="padding:0.5em;">No Labu</th>
                                            <th style="width:120px;padding:0.5em;">Gol Darah</th>
                                            <th style="text-align:center;width:120px;padding:0.5em;">Rhesus</th>
                                            <th style="padding:0.5em;">Group Darah</th>
                                            <th style="text-align:center;width:70px;padding:0.5em;">Volume(CC)</th>
                                            <th style="text-align:center;width:70px;padding:0.5em;">Kadaluarsa</th>                                            
                                            <th style="text-align:center;width:50px;padding:0.5em;">...</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        <tr style="border-top:1px solid #ccc;" v-if="penerimaan.items.length > 0" v-for="dt in penerimaan.items">
                                            <td style="padding:0.2em;margin:0;">    
                                                <input class="uk-input uk-form-small uk-form-blank" type="text" v-model="dt.no_labu" style="color:black;border:none;border-bottom:1px solid #aaa;" :disabled="dt.darah_detail_id!==null">
                                            </td>
                                            <td style="text-align:center;padding:0.2em;margin:0;">
                                                <select class="uk-select uk-form-small uk-form-blank" v-model="dt.gol_darah" style="color:black;border:none;border-bottom:1px solid #aaa;">
                                                    <option v-for="dt in golDarahRefs.json_data" :value="dt">{{dt}}</option>
                                                    <option value=""></option>
                                                </select>
                                            </td>
                                            <td style="text-align:center;width:70px;padding:0.2em;margin:0;">
                                                <select class="uk-select uk-form-blank uk-form-small" v-model="dt.rhesus" style="color:black;border:none;border-bottom:1px solid #aaa;">
                                                    <option v-for="dt in rhesusRefs.json_data" :value="dt.value">{{dt.value}}</option>
                                                    <option value=""></option>
                                                </select>
                                            </td>
                                            <td style="text-align:center;padding:0.2em;margin:0;">
                                                <select class="uk-select uk-form-blank uk-form-small" v-model="dt.group_darah" style="color:black;border:none;border-bottom:1px solid #aaa;">
                                                    <option v-for="dt in groupDarahRefs.json_data" :value="dt.value">{{dt.value}}</option>
                                                    <option value=""></option>
                                                </select>
                                            </td>
                                            <td style="padding:0.2em; text-align:center;">
                                                <input class="uk-input uk-form-blank uk-form-small" type="number" v-model="dt.volume" style="text-align: center;border:none;border-bottom:1px solid #aaa;">
                                            </td>
                                            <td style="padding:0.2em; text-align:center;">
                                                <input class="uk-input uk-form-blank uk-form-small" type="date" v-model="dt.tgl_kadaluarsa" style="text-align: center;border:none;border-bottom:1px solid #aaa;">
                                            </td>
                                            <td style="padding:0.8em 0.5em 0 0.5em; text-align:center;">
                                                <input class="uk-checkbox" type="checkbox" v-model="dt.is_aktif" style="text-align: center;" @change="changeActivationDarahItem">
                                            </td>
                                        </tr>                                        
                                        <tr>
                                            <td style="padding:0.2em;margin:0;">    
                                                <input class="uk-input uk-form-small" type="text" v-model="darahItem.no_labu" style="color:black;">
                                            </td>
                                            <td style="text-align:center;width:70px;padding:0.2em;margin:0;">
                                                <select class="uk-select uk-form-small" v-model="darahItem.gol_darah" style="color:black;">
                                                    <option v-for="dt in golDarahRefs.json_data" :value="dt">{{dt}}</option>
                                                    <option value=""></option>
                                                </select>
                                            </td>
                                            <td style="text-align:center;width:70px;padding:0.2em;margin:0;">
                                                <select class="uk-select uk-form-small" v-model="darahItem.rhesus" style="color:black;">
                                                    <option v-for="dt in rhesusRefs.json_data" :value="dt.value">{{dt.value}}</option>
                                                    <option value=""></option>
                                                </select>
                                            </td>
                                            <td style="text-align:center;padding:0.2em;margin:0;">
                                                <select class="uk-select uk-form-small" v-model="darahItem.group_darah" style="color:black;">
                                                    <option v-for="dt in groupDarahRefs.json_data" :value="dt.value">{{dt.value}}</option>
                                                    <option value=""></option>
                                                </select>
                                            </td>
                                            <td style="padding:0.2em; text-align:center;">
                                                <input class="uk-input uk-form-small" type="number" v-model="darahItem.volume" style="text-align: center;">
                                            </td>
                                            <td style="padding:0.2em; text-align:center;">
                                                <input class="uk-input uk-form-small" type="date" v-model="darahItem.tgl_kadaluarsa" style="text-align: center;">
                                            </td>
                                            
                                            <td style="padding:0.5em; font-size:12px;text-align:center;">
                                                <a href="#" uk-icon="icon:plus-circle;ratio:1" @click.prevent="addDarahItem"></a>
                                            </td>                               
                                        </tr>
                                        <tr v-if="penerimaan.items.length <= 0">
                                            <td colspan="7">
                                                <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">belum ada data untuk ditampilkan</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>        
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'form-distribusi',
    components : { SearchSelect,SearchList,FormPermintaan },
    data() {
        return {
            formTitle : 'PENGIRIMAN LINEN',
            isUpdate : true,
            isLoading : true,
            configSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            configItemSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            cssdDesc : [
                { colName : 'produk_nama', labelData : '', isTitle : true },
                { colName : 'produk_id', labelData : '', isTitle : false },
            ],
            unitDesc : [
                { colName : 'unit_nama', labelData : '', isTitle : true },
                { colName : 'unit_id', labelData : '', isTitle : false },
            ],
            satuanDesc : [
                { colName : 'value', labelData : '', isTitle : true },
                { colName : 'text', labelData : '', isTitle : false },
            ],   
            pengiriman : {
                cssd_dist_id : null,
                unit_pengirim_id : null,
                unit_pengirim : null,
                unit_penerima_id : null,
                unit_penerima : null,
                pengirim : null,
                penerima : null,
                
                tgl_kirim : null,
                jam_kirim : null,
                tgl_terima : null,
                jam_terima : null,
                
                catatan : null,
                kondisi : null,
                status : 'KIRIM',
                is_aktif : true,
                client_id : null,
                items : [],
            },

            cssdItem: {
                cssd_detail_id : null,
                trx_cssd_id : null,
                produk_id : null,
                produk_nama : null,
                satuan : null,
                jml_terima : 0,
                jml_keluar : 0,
                jml_rusak : 0,
                jml_perawatan : 0,
                jml_awal : 0,
                jml_penyesuaian : 0,
                status : 'KIRIM',
                is_aktif : true,
                client_id : null,
            },


            cssdLists : [],
            itemUrl : '',
            unitUrl : '',

            permintaan : {
                darah_dist_id : null,
                reg_id : null,
                jns_registrasi : null,
                dokter_id : null,
                dokter_nama : null,
                pasien_id : null,
                pasien_nama : null,
                no_rm : null,
                unit_id : null,
                unit_nama : null,
                ruang_id : null,
                ruang_nama : null,
                bed_no : null,
                penjamin_id : null,
                penjamin_nama : null,
                is_bpjs : false,
                usia : null,
                tempat_lahir : null,
                tgl_lahir : null,
                jenis_kelamin : null,
                tgl_permintaan : null,
                jam_permintaan : null,
                peminta : null,
                tgl_dibutuhkan : null,
                jam_dibutuhkan : null,
                tgl_distribusi : null,
                jam_distribusi : null,
                pengirim : null,
                penerima : null,
                gol_darah : null,
                rhesus : null,
                haemoglobin : null,
                jml_permintaan : null,
                volume_per_labu : null,
                diagnosa : null,
                is_kirim : false,
                status : null,
                catatan : null,
                is_aktif : true,
                client_id : null,
                items : [],
            },

            permintaanItem: {
                darah_detail_id : null,
                no_labu : null,
                gol_darah : null,
                rhesus : null,
                group_darah : null,
                volume : null,
                jumlah : 0,
                status : 'PERMINTAAN',
                is_aktif : true,
                is_pakai : false,
                client_id : null,
            },
        }
    },

    computed : {
        ...mapGetters('refProduk',['isRefProdukExist','satuanProdukRefs']),
    },

    mounted() {     
        this.initialize();
    },

    methods : {
        ...mapActions('cssd',['getCssdLists','createPengirimanCssd','updatePengirimanCssd','deletePengirimanCssd','dataPengirimanCssd']),
        ...mapActions('refProduk',['listRefProduk']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            this.CLR_ERRORS();
            this.listRefProduk({per_page:'ALL'});
        },

        /**
         * get date today for default value input date 
         */
         getTodayDate(){
            let dt = new Date();
            let year = dt.getFullYear();
            let month = dt.getMonth() + 1;
            if(month < 10) {month = `0${month}`}
            let date = dt.getDate();
            if(date < 10) {date = `0${date}`}
            let str_dt = `${year}-${month}-${date}`
            return str_dt;
        },

        /** function called before modal closed */
        modalClosed(){
            // let data = JSON.parse(JSON.stringify(this.pengiriman));
            // this.clearCssdItem();
            // this.clearPengiriman();
            this.$emit('modalCreateDarahClosed',true);
            UIkit.modal('#modalCreateDarah').hide();            
        },

        /**modal call from other component for new data entry */
        newData(){
            if(this.itemUrl == null || this.itemUrl == '') {
                this.itemUrl = '/sterilizations/distributions/product';
                this.unitUrl = '/units';
            }
            
            this.CLR_ERRORS();
            this.clearPengiriman();
            this.clearCssdItem();
            this.isUpdate = false;
            this.formTitle = "DATA BARU PERMINTAAN DARAH";
            UIkit.modal('#modalCreateDarah').show();
        },
        
        /**modal call from other component for update data entry */
        editData(refData) {
            if(this.itemUrl == null || this.itemUrl == '') {
                this.itemUrl = '/sterilizations/distributions/product';
                this.unitUrl = '/units';
            }

            this.CLR_ERRORS();
            this.clearPengiriman();
            this.clearCssdItem();
            this.formTitle = "UBAH DATA PERMINTAAN DARAH";
            this.isUpdate = true;
            this.isLoading = true;

            if(refData) {
                this.dataPengirimanCssd(refData.darah_dist_id).then((response) => {
                    if (response.success == true) {
                        this.fillPengiriman(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        UIkit.modal('#modalCreateDarah').show();
                    }
                    else { alert('data pengiriman cssd tidak ditemukan'); }
                })
            }
        },

        clearCssdItem() {
            this.cssdItem.cssd_detail_id = null;
            this.cssdItem.trx_cssd_id = null;
            this.cssdItem.produk_id = null;
            this.cssdItem.produk_nama = null;
            this.cssdItem.satuan = null;
            this.cssdItem.jml_keluar = 0;
            this.cssdItem.jml_terima = 0;
            this.cssdItem.jml_rusak = 0;
            this.cssdItem.jml_perawatan = 0;            
            this.cssdItem.jml_awal = 0;
            this.cssdItem.jml_penyesuaian = 0;
            this.cssdItem.status = null;
            this.cssdItem.kondisi = null;
            this.cssdItem.is_aktif = true;
            this.cssdItem.client_id = null;
        },

        clearPengiriman() {
            this.pengiriman.cssd_dist_id = null;

            this.pengiriman.unit_pengirim_id = null;
            this.pengiriman.unit_pengirim = null;
            this.pengiriman.unit_penerima_id = null;
            this.pengiriman.unit_penerima = null;

            this.pengiriman.pengirim = null;
            this.pengiriman.penerima = null;

            this.pengiriman.tgl_terima = this.getTodayDate();
            this.pengiriman.jam_terima = null;
            this.pengiriman.tgl_kirim = this.getTodayDate();
            this.pengiriman.jam_kirim = null;

            this.pengiriman.tgl_selesai = null;
            this.pengiriman.catatan = null;         
            this.pengiriman.status = 'KIRIM';
            this.pengiriman.is_aktif = true;
            this.pengiriman.client_id = null;
            this.pengiriman.items = [];        
        },

        fillPengiriman(data) {
            if(data) {
                this.pengiriman.cssd_dist_id = data.cssd_dist_id;

                this.pengiriman.unit_pengirim_id = data.unit_pengirim_id;
                this.pengiriman.unit_pengirim = data.unit_pengirim;
                this.pengiriman.unit_penerima_id = data.unit_penerima_id;
                this.pengiriman.unit_penerima = data.unit_penerima;

                this.pengiriman.pengirim = data.pengirim;
                this.pengiriman.penerima = data.penerima;
                
                this.pengiriman.tgl_terima = data.tgl_terima;
                this.pengiriman.jam_terima = data.jam_terima;
                this.pengiriman.tgl_kirim = data.tgl_kirim;
                this.pengiriman.jam_kirim = data.jam_kirim;

                this.pengiriman.tgl_selesai = data.tgl_selesai;
                this.pengiriman.catatan = data.catatan;
                this.pengiriman.status = data.status;
                this.pengiriman.is_aktif = data.is_aktif;
                this.pengiriman.client_id = data.client_id;
                this.pengiriman.items = data.items;        
            }
        },

        addCssdItem() {
            let data = JSON.parse(JSON.stringify(this.cssdItem));
            let dt = this.pengiriman.items.find(item => item.produk_id === data.produk_id);
            if(!dt) {
                this.pengiriman.items.push(data);
                this.clearCssdItem();
            }
            else {
                alert('Item cssd sudah ada di dalam daftar pengiriman. Data tidak dapat ditambahkan.');
                this.clearCssdItem();
            }
        },

        changeActivationCssdItem(){
            let filteredData = this.pengiriman.items.filter(
                item => { 
                    return item.is_aktif == true || (item.cssd_detail_id !== null && item.cssd_detail_id !== '') 
                }
            );
            this.pengiriman.items = JSON.parse(JSON.stringify(filteredData));
        },

        itemSelected(data) {
            this.clearCssdItem();
            if(data) {
                this.cssdItem.produk_id = data.produk_id;
                this.cssdItem.produk_nama = data.produk_nama;
                this.cssdItem.satuan = data.satuan;
                this.cssdItem.jml_keluar = 1;
            }
        },

        unitSelected(data) {
            if(data) {
                this.pengiriman.unit_penerima_id = data.unit_id;
                this.pengiriman.unit_penerima = data.unit_nama;                
            }
        },

        satuanSelected(data){
            this.pengiriman.satuan = data.value;
        },

        submitPengiriman(){
            this.CLR_ERRORS();
            this.isLoading = true;
            
            if(this.isUpdate) {
                //update data
                this.updatePengirimanCssd(this.pengiriman).then((response) => {
                    if (response.success == true) {
                        this.fillPengiriman(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        alert('Data berhasil disimpan.');
                        this.modalClosed();
                    }
                    else { alert(response.message); this.isLoading = false; }
                })
            }
            else {
                //data baru
                this.createPengirimanCssd(this.pengiriman).then((response) => {
                    if (response.success == true) {
                        this.fillPengiriman(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        alert('Data berhasil disimpan.');
                        this.modalClosed();
                    }
                    else { alert(response.message); this.isLoading = false; }
                })
            }
        }
    }
}

</script>
<style>
.uk-input, .uk-textarea, .uk-select, .uk-checkbox, .uk-radio {
    border:1px solid #333;
} -->
