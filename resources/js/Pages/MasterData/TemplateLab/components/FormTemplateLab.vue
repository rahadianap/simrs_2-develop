<template>    
    <div class="uk-container-large">
        <div class="uk-grid uk-grid-small" style="padding:0.5em 1em 1em 1em;">
            <div class="uk-width-auto" style="padding:0;">
                <a href="#" @click.prevent="closeModalTemplate" uk-icon="icon:arrow-left;ratio:1.5" style="display:block;width:30px;"></a>
            </div>
            <div class="uk-width-expand" style="padding:0;margin:0;">
                <ul class="uk-breadcrumb" style="padding:0;margin:0;">
                    <li>
                        <a href="#" @click.prevent="closeModalTemplate" class="uk-text-uppercase">
                            <h4 style="font-weight:500;margin:0;padding:0;display: inline-block;" class="uk-text-uppercase">TEMPLATE HASIL LABORATORIUM</h4>
                        </a>
                    </li>
                    <li v-if="tindakan.tindakan_nama !== null">
                        <span style="font-weight:400;color:#333;font-size:14px;"  class="uk-text-uppercase">{{tindakan.tindakan_nama}}</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="uk-container hims-form-container" style="min-height:70vh; background-color:#fff;padding:0 0.5em 0.5em 0.5em;">
            <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                <p>{{ errors.invalid }}</p>
            </div>
            <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                <div uk-spinner="ratio : 2"></div>
                <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil data...</span>
            </div>
            <div class="uk-grid-small hims-form-subpage" uk-grid style="border:none;">
                <div class="uk-width-1-1@s" style="padding:0 0.5em 1em 0.5em;">
                    <h5 style="color:indigo;padding:0;margin:0;">DATA UTAMA</h5>
                    <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                        informasi utama terkait tindakan / pemeriksaan.
                    </p>
                </div>
                <div class="uk-width-1-1@s uk-grid-small" uk-grid>
                    <div class="uk-width-1-3@m">
                        <dl class="uk-description-list hims-description-list">
                            <dt>Pemeriksaan</dt>
                            <dd>{{tindakan.tindakan_nama}} ({{tindakan.klasifikasi}})</dd>
                            <dt>ID </dt>
                            <dd>{{tindakan.tindakan_id}}</dd>
                        </dl>
                    </div>
                    <div class="uk-width-1-3@m">
                        <dl class="uk-description-list hims-description-list">
                            <dt>Informasi</dt>
                            <dd>{{tindakan.deskripsi}}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="uk-grid-small hims-form-subpage" uk-grid >
                
                <div class="uk-width-1-1@s uk-grid-small" uk-grid>
                    <div class="uk-width-1-1" style="padding:0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">ITEM HASIL LABORATORIUM</h5>
                        <p style="color:#333; font-size:12px; padding:0;margin:0; font-style: italic; ">*item hasil pemeriksaan laboratorium akan langsung disimpan.</p>
                    </div>
                    <form class="uk-width-1-1" action="" @submit.prevent="saveData" style="padding:0;margin:0 0 1em 0;">
                        <div class="uk-width-1-1 uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;margin:0;">         
                            <div class="uk-grid-small uk-width-full" uk-grid style="margin:0;padding:0;">
                                <search-select
                                    :config = configSelect
                                    :searchUrl = urlPicker
                                    placeholder = "pilih produk"
                                    captionField = "hasil_nama"
                                    valueField = "lab_hasil_id"
                                    :itemListData = labDesc
                                    :value = "addedItem.lab_hasil_id"
                                    v-on:item-select = "itemSelected"
                                ></search-select>
                                <input class="uk-input uk-form-small uk-width-auto" type="text" placeholder="no urut" v-model="addedItem.no_urut" style="border:none;border-left:1px solid #ccc;margin:0;padding:0.2em 0 0 0;text-align:center;">
                                <button type="submit" class="uk-width-auto hims-table-btn-submit" style="border:none;">
                                    <span uk-icon="plus-circle"></span> Tambah Item
                                </button>
                                <button type="button" @click.prevent="refreshData" class="uk-width-auto hims-table-btn-submit" style="border:none;border-top-right-radius: 5px;border-bottom-right-radius: 5px;">
                                    <span uk-icon="refresh"></span> Refresh
                                </button>                        
                            </div>
                        </div>
                    </form>
                    <div class="uk-width-1-1 uk-overflow-auto" style="margin:0;padding:0;">
                        <table class="uk-table hims-table uk-table-responsive" style="">
                            <thead class="uk-card uk-card-default uk-box-shadow-small" style="border-top:2px solid #cc0202;">
                                <tr>
                                    <th style="width:150px;">ID</th>
                                    <th>KLASIFIKASI</th>
                                    <th>SUB KLASIFIKASI</th>
                                    <th>NAMA</th>
                                    <th>NO URUT</th>
                                    <th style="text-align:center;">...</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="tindakan.labItems" v-for="dt in tindakan.labItems">
                                    <td style="font-weight:500;width:150px;">{{dt.lab_hasil_id}}</td>
                                    <td style="width:120px;" class="uk-text-uppercase">{{dt.klasifikasi}}</td>
                                    <td style="width:120px;" class="uk-text-uppercase">{{dt.sub_klasifikasi}}</td>
                                    <td>{{dt.hasil_nama}}</td>
                                    <td style="width:80px;padding:0.5em;margin:0;">
                                        <input class="uk-input uk-form-small" v-model="dt.no_urut" type="text" style="text-align:center;">
                                    </td>
                                    <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;text-align:center;width:50px;">
                                        <a href="#" uk-icon="icon:trash;ratio:0.8" @click.prevent="deleteData(dt)"></a>
                                        <a href="#" uk-icon="icon:file-text;ratio:0.8" @click.prevent="updateData(dt)"></a>
                                    </td>
                                </tr>
                                <tr v-else>
                                    <td colspan="5">
                                        <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">belum ada data untuk ditampilkan</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template> 

<script>
import { mapGetters,mapState,mapMutations,mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';

export default {
    name : 'form-template-lab',
    components : {
        SearchSelect,
    },
    data() {
        return {
            isLoading : false,
            tindakan : {
                client_id:null,
                tindakan_id:null,
                tindakan_nama : null,
                deskripsi : null,                
                klasifikasi : 'LAB',
                meta_data : null,
                satuan : null,
                is_paket : false,
                is_diskon : true,
                is_cito : true,
                is_hitung_admin : true,
                is_tampilkan_dokter : false,
                is_lab_hasil : true,
                is_kamar : false,
                rl_kode : null,
                kelompok_tagihan_id : null,
                kelompok_tindakan_id : null,
                kelompok_tindakan : null,
                kelompok_vklaim_id : null,
                is_aktif : true,
                unitTindakan : [],
                bhpTindakan : [],
                labItems :[]
            },
            
            addedItem : {
                lab_hasil_id : null,
                hasil_nama : null,
                klasifikasi : null,
                sub_klasifikasi : null,
                no_urut : null,
            },
            labDesc : [
                { colName : 'hasil_nama', labelData : '', isTitle : true },
                { colName : 'klasifikasi', labelData : '', isTitle : false },
                { colName : 'lab_hasil_id', labelData : '', isTitle : false },
            ],
            configSelect : {
                required : true,
                compClass : "uk-width-expand",
                compStyle : "padding:0 0 0 0.2em;margin:0;border:none;",
                retrieveAll : false,
                qtyPerPage : 20,
            },
            urlPicker : '/labs/items/results'
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },  
    mounted() {
        this.initialize();
    },
    methods :{
        ...mapMutations(['CLR_ERRORS']), 
        ...mapActions('tindakan',['addLabTemplate','deleteLabTemplate','updateLabTemplate']),
        ...mapActions('tindakan',['listTindakan','deleteTindakan','dataTindakan']),
        
        initialize() {
            this.$emit('ready',true);
        },

        updateData(data) {
            this.CLR_ERRORS();  
            this.updateLabTemplate(data).then((response) => {
                if (response.success == true) {
                    this.addedItem.lab_hasil_id = null;
                    this.addedItem.hasil_nama = null;                    
                    this.addedItem.klasifikasi = null;
                    this.addedItem.sub_klasifikasi = null;
                    this.addedItem.no_urut = null;                    
                    alert("Item hasil berhasil diubah.");
                    this.refreshData();
                }
            }) 
        },

        saveData(data){
            this.CLR_ERRORS();            
            let dt = { 
                tindakan_id : this.tindakan.tindakan_id,
                tindakan_nama : this.tindakan.tindakan_nama,  
                lab_hasil_id : this.addedItem.lab_hasil_id,
                hasil_nama : this.addedItem.hasil_nama,
                klasifikasi : this.addedItem.klasifikasi,
                sub_klasifikasi : this.addedItem.sub_klasifikasi,
                no_urut : this.addedItem.no_urut,                
            }
            this.addLabTemplate(dt).then((response) => {
                if (response.success == true) {
                    this.addedItem.lab_hasil_id = null;
                    this.addedItem.hasil_nama = null;                    
                    this.addedItem.klasifikasi = null;
                    this.addedItem.sub_klasifikasi = null;
                    this.addedItem.no_urut = null;                    
                    alert("Item hasil berhasil dipetakan.");
                    this.refreshData();
                }
            }) 
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus item hasil ${data.hasil_nama} dari mappping tindakan ${this.tindakan.tindakan_nama}. Lanjutkan ?`)){
                this.deleteLabTemplate(data.lab_template_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.refreshData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        itemSelected(data) {
            this.addedItem.lab_hasil_id = null;
            this.addedItem.hasil_nama = null;
            this.addedItem.klasifikasi = null;
            this.addedItem.sub_klasifikasi = null;
            this.addedItem.no_urut = null;
            if(data) {
                this.addedItem.lab_hasil_id = data.lab_hasil_id;
                this.addedItem.hasil_nama = data.hasil_nama;
                this.addedItem.klasifikasi = data.klasifikasi;
                this.addedItem.sub_klasifikasi = data.sub_klasifikasi;
                this.addedItem.no_urut = data.no_urut;
            }
        },

        refreshData(){
            this.CLR_ERRORS();
            this.isLoading = true;
            this.dataTindakan(this.tindakan.tindakan_id).then((response)=>{
                if (response.success == true) {
                    this.isLoading = false;
                    this.fillTindakanLab(response.data);
                } else {
                    this.isLoading = false;
                    alert(response.message)
                }
            })
        },

        editTemplateLab(data){
            this.CLR_ERRORS();
            this.isLoading = true;
            this.clearTindakanLab();
            this.dataTindakan(data.tindakan_id).then((response)=>{
                if (response.success == true) {
                    this.isLoading = false;
                    this.fillTindakanLab(response.data);
                } else {
                    this.isLoading = false;
                    alert(response.message)
                }
            })
        },

        clearTindakanLab(){
            this.tindakan.client_id = null;
            this.tindakan.tindakan_id = null;
            this.tindakan.tindakan_nama = null;
            this.tindakan.klasifikasi = 'LAB';
            this.tindakan.deskripsi = null;
            this.tindakan.kelompok_tagihan_id = null;
            this.tindakan.kelompok_vklaim_id = null;
            this.tindakan.kelompok_tindakan_id = null;
            this.tindakan.is_diskon = true;
            this.tindakan.is_hitung_admin = true;
            this.tindakan.is_cito = false;
            this.tindakan.is_lab_hasil = false;
            this.tindakan.is_kamar = false;
            this.tindakan.is_paket = false;
            this.tindakan.is_tampilkan_dokter = false;
            this.tindakan.rl_kode = null;
            this.tindakan.meta_data = null;
            this.tindakan.satuan = null;
            this.tindakan.is_aktif = true;
            this.tindakan.unitTindakan = [];
            this.tindakan.bhpTindakan = [];
            this.tindakan.labItems = [];
        },

        fillTindakanLab(data){
            this.tindakan.client_id = null;
            this.tindakan.tindakan_id = data.tindakan_id;
            this.tindakan.tindakan_nama = data.tindakan_nama;
            this.tindakan.klasifikasi = data.klasifikasi;
            this.tindakan.deskripsi = data.deskripsi;
            this.tindakan.kelompok_tagihan_id = data.kelompok_tagihan_id;
            this.tindakan.kelompok_vklaim_id = data.kelompok_vklaim_id;
            this.tindakan.kelompok_tindakan_id = data.kelompok_tindakan_id;
            this.tindakan.is_diskon = data.is_diskon;
            this.tindakan.is_hitung_admin = data.is_hitung_admin;
            this.tindakan.is_cito = data.is_cito;
            this.tindakan.is_paket = data.is_paket;
            this.tindakan.is_tampilkan_dokter = data.is_tampilkan_dokter;
            this.tindakan.is_lab_hasil = data.is_lab_hasil;
            this.tindakan.is_kamar = data.is_kamar;
            this.tindakan.rl_kode = data.rl_kode;
            this.tindakan.meta_data = data.meta_data;
            this.tindakan.satuan = data.satuan;
            this.tindakan.is_aktif = data.is_aktif;
            this.tindakan.unitTindakan = data.unitTindakan;
            this.tindakan.bhpTindakan = data.bhpTindakan;
            this.tindakan.labItems = data.labItems;

            if(this.$refs.subFormUnit) {
                this.$refs.subFormUnit.refreshData(this.tindakan);
            }
            if(this.$refs.subFormBhp) {
                this.$refs.subFormBhp.refreshData(this.tindakan);
            }
            if(this.$refs.subFormItemLab) {
                if(data.klasifikasi == 'LAB') { this.$refs.subFormItemLab.refreshData(this.tindakan);}
            }
        },

        refreshDataTindakan(){
            this.retrieveTindakan(this.tindakan.tindakan_id);
        },

        closeModalTemplate(){
            this.clearTindakanLab();
            this.$emit('formLabClosed',true);
        }
    }
}
</script>
