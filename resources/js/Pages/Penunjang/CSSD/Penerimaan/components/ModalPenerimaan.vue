<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="modalCssd" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body" style="padding:0;">
            <form action="" @submit.prevent="submitPenerimaan">
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
                                detail data penerimaan item dari unit-unit.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                            <div class="uk-grid-small uk-width-3-4@l 1-1@m" uk-grid> 
                                <div class="uk-width-1-1">
                                    <h5 style="font-weight:500;">{{penerimaan.cssd_terima_id}}</h5>
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
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Terima*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="time" v-model="penerimaan.jam_terima" style="color:black;">
                                    </div>
                                </div>
                                <div class="uk-width-1-2@m" style="padding:0;margin:0;">

                                </div>
                                <div class="uk-width-1-2@m">
                                    <search-select
                                        :config = configSelect
                                        :searchUrl = unitUrl
                                        label = "Unit Pengirim*"
                                        placeholder = "pilih unit pengirim"
                                        captionField = "unit_nama"
                                        valueField = "unit_nama"
                                        :itemListData = unitDesc
                                        :value = "penerimaan.unit_pengirim"
                                        v-on:item-select = "unitSelected"
                                    ></search-select>
                                </div>                            

                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pengirim*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small uk-text-uppercase" type="text" v-model="penerimaan.pengirim" style="color:black;">
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
                                            <th style="padding:0.5em;">Produk</th>
                                            <th style="text-align:center;width:120px;padding:0.5em;">Kondisi</th>
                                            <th style="text-align:center;width:120px;padding:0.5em;">Satuan</th>
                                            <th style="text-align:center;width:70px;padding:0.5em;">Jumlah</th>
                                            <th style="text-align:center;width:50px;padding:0.5em;">...</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="padding:0.2em;margin:0;">    
                                                <search-select
                                                    :config = configItemSelect
                                                    :searchUrl = itemUrl
                                                    captionField = "produk_nama"
                                                    valueField = "produk_nama"
                                                    :itemListData = cssdDesc
                                                    :value = "cssdItem.produk_nama"
                                                    v-on:item-select = "itemSelected"
                                                ></search-select>
                                            </td>
                                            <td style="text-align:center;width:70px;padding:0.2em;margin:0;">
                                                <select class="uk-select uk-form-small" v-model="cssdItem.kondisi" style="color:black;">
                                                    <option value="NON_STERIL">NON STERIL</option>
                                                    <option value="STERIL">STERIL</option>
                                                    <option value=""></option>
                                                </select>
                                            </td>
                                            <td style="text-align:center;width:70px;padding:0.2em;margin:0;">
                                                <input class="uk-input uk-form-small" type="text" v-model="cssdItem.satuan" style="text-align:center; color:black;" disabled>
                                            </td>
                                            <td style="text-align:center;width:70px;padding:0.2em;margin:0;">
                                                <input class="uk-input uk-form-small" type="number" v-model="cssdItem.jml_terima" min="0" style="text-align: center;">
                                            </td>
                                            <td style="padding:0.5em; font-size:12px;text-align:center;">
                                                <a href="#" uk-icon="icon:plus-circle;ratio:1" @click.prevent="addCssdItem"></a>
                                            </td>                               
                                        </tr>
                                        <tr  style="border-top:1px solid #ccc;" v-if="penerimaan.items.length > 0" v-for="dt in penerimaan.items">
                                            <td style="padding:0.2em;margin:0;">
                                                <p style="font-weight:500;padding:0;margin:0;">{{dt.produk_nama}}</p>
                                                <p style="font-weight:300;padding:0;margin:0;font-size: 10px; font-style: italic;">{{dt.produk_id}}</p>
                                            </td>
                                            <td style="text-align:center;padding:0.5em 0.2em 0 0.2em;margin:0;">
                                                <select class="uk-select uk-form-small" v-model="dt.kondisi" style="color:black;">
                                                    <option value="NON_STERIL">NON STERIL</option>
                                                    <option value="STERIL">STERIL</option>
                                                    <option value=""></option>
                                                </select>
                                            </td>
                                            <td style="text-align:center;">{{dt.satuan}}</td>
                                            <td style="text-align:center;padding:0.5em 0.2em 0 0.2em;margin:0;">
                                                <input class="uk-input uk-form-small" type="number" v-model="dt.jml_terima" min="0" style="text-align: center;">
                                            </td>
                                            <td style="padding:1em 0.5em 1em 0.5em; text-align:center;">
                                                <input class="uk-checkbox" type="checkbox" v-model="dt.is_aktif" style="text-align: center;" @change="changeActivationCssdItem">
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
            </form>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'modal-penerimaan',
    components : { SearchSelect,SearchList },
    data() {
        return {
            formTitle : 'PENERIMAAN BARU',
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
            penerimaan : {
                cssd_terima_id : null,
                unit_pengirim_id : null,
                unit_pengirim : null,
                unit_penerima_id : null,
                unit_penerima : null,
                pengirim : null,
                penerima : null,
                tgl_terima : null,
                jam_terima : null,
                tgl_selesai : null,
                catatan : null,
                berat : 0,
                satuan : null,
                is_infeksius : false,
                kondisi : null,
                status : 'TERIMA',
                is_aktif : true,
                client_id : null,
                items : [],
            },

            cssdItem : {
                cssd_detail_id : null,
                trx_cssd_id : null,
                produk_id : null,
                produk_nama : null,
                jml_terima : 0,
                satuan : null,
                jml_rusak : 0,
                jml_perawatan : 0,
                jml_selesai : 0,
                status : 'TERIMA',
                is_aktif : true,
                client_id : null,
            },

            itemUrl : '',
            unitUrl : '',
        }
    },

    computed : {
        ...mapGetters('refProduk',['isRefProdukExist','satuanProdukRefs']),
    },

    mounted() {     
        this.initialize();
    },

    methods : {
        ...mapActions('cssd',['getCssdLists','createPenerimaanCssd','updatePenerimaanCssd','deletePenerimaanCssd','dataPenerimaanCssd']),
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
            let data = JSON.parse(JSON.stringify(this.penerimaan));
            this.clearCssdItem();
            this.clearPenerimaan();
            this.$emit('modalCssdClosed',data);
            UIkit.modal('#modalCssd').hide();            
        },

        /**modal call from other component for new data entry */
        newData(){
            if(this.itemUrl == null || this.itemUrl == '') {
                this.itemUrl = '/sterilizations/product';
                this.unitUrl = '/units';
            }

            this.CLR_ERRORS();
            this.clearPenerimaan();
            this.clearCssdItem();
            this.isUpdate = false;
            this.formTitle = "DATA BARU PENERIMAAN";
            UIkit.modal('#modalCssd').show();
        },
        
        /**modal call from other component for update data entry */
        editData(refData) {
            if(this.itemUrl == null || this.itemUrl == '') {
                this.itemUrl = '/sterilizations/product';
                this.unitUrl = '/units';
            }

            this.CLR_ERRORS();
            this.clearPenerimaan();
            this.clearCssdItem();
            this.formTitle = "UBAH DATA PENERIMAAN";
            this.isUpdate = true;
            this.isLoading = true;

            if(refData) {
                this.dataPenerimaanCssd(refData.cssd_terima_id).then((response) => {
                    if (response.success == true) {
                        this.fillPenerimaan(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        UIkit.modal('#modalCssd').show();
                    }
                    else { alert('data penerimaan tidak ditemukan'); }
                })
            }
        },

        clearCssdItem() {
            this.cssdItem.cssd_detail_id = null;
            this.cssdItem.trx_cssd_id = null;
            this.cssdItem.produk_id = null;
            this.cssdItem.produk_nama = null;
            this.cssdItem.jml_terima = 0;
            this.cssdItem.satuan = null;
            this.cssdItem.jml_rusak = 0;
            this.cssdItem.jml_perawatan = 0;
            this.cssdItem.jml_selesai = 0;
            this.cssdItem.status = null;
            this.cssdItem.kondisi = null;
            this.cssdItem.is_aktif = true;
            this.cssdItem.client_id = null;
        },

        clearPenerimaan() {
            this.penerimaan.cssd_terima_id = null;
            this.penerimaan.unit_pengirim_id = null;
            this.penerimaan.unit_pengirim = null;
            this.penerimaan.unit_penerima_id = null;
            this.penerimaan.unit_penerima = null;
            this.penerimaan.pengirim = null;
            this.penerimaan.penerima = null;
            this.penerimaan.tgl_terima = this.getTodayDate();
            this.penerimaan.jam_terima = null;
            this.penerimaan.tgl_selesai = null;
            this.penerimaan.catatan = null;           
            this.penerimaan.status = 'TERIMA';
            this.penerimaan.is_aktif = true;
            this.penerimaan.client_id = null;
            this.penerimaan.items = [];        
        },

        fillPenerimaan(data) {
            if(data) {
                this.penerimaan.cssd_terima_id = data.cssd_terima_id;
                this.penerimaan.unit_pengirim_id = data.unit_pengirim_id;
                this.penerimaan.unit_pengirim = data.unit_pengirim;
                this.penerimaan.unit_penerima_id = data.unit_penerima_id;
                this.penerimaan.unit_penerima = data.unit_penerima;
                this.penerimaan.pengirim = data.pengirim;
                this.penerimaan.penerima = data.penerima;
                this.penerimaan.tgl_terima = data.tgl_terima;
                this.penerimaan.jam_terima = data.jam_terima;
                this.penerimaan.tgl_selesai = data.tgl_selesai;
                this.penerimaan.catatan = data.catatan;
                this.penerimaan.status = data.status;
                this.penerimaan.is_aktif = data.is_aktif;
                this.penerimaan.client_id = data.client_id;
                this.penerimaan.items = data.items;        
            }
        },

        addCssdItem() {
            let data = JSON.parse(JSON.stringify(this.cssdItem));
            let dt = this.penerimaan.items.find(item => item.produk_id === data.produk_id);
            if(!dt) {
                this.penerimaan.items.push(data);
                this.clearCssdItem();
            }
            else {
                alert('Item sudah ada di dalam daftar penerimaan. Data tidak dapat ditambahkan.');
                this.clearCssdItem();
            }
        },

        changeActivationCssdItem(){
            let filteredData = this.penerimaan.items.filter(
                item => { 
                    return item.is_aktif == true || (item.cssd_detail_id !== null && item.cssd_detail_id !== '') 
                }
            );
            this.penerimaan.items = JSON.parse(JSON.stringify(filteredData));
        },

        itemSelected(data) {
            this.clearCssdItem();
            if(data) {
                this.cssdItem.produk_id = data.produk_id;
                this.cssdItem.produk_nama = data.produk_nama;
                this.cssdItem.satuan = data.satuan;
                this.cssdItem.jml_terima = 1;
            }
        },

        unitSelected(data) {
            if(data) {
                this.penerimaan.unit_pengirim_id = data.unit_id;
                this.penerimaan.unit_pengirim = data.unit_nama;                
            }
        },

        satuanSelected(data){
            console.log(data);
            this.penerimaan.satuan = data.value;
        },

        submitPenerimaan(){
            this.CLR_ERRORS();
            this.isLoading = true;
            
            if(this.isUpdate) {
                //update data
                this.updatePenerimaanCssd(this.penerimaan).then((response) => {
                    if (response.success == true) {
                        this.fillPenerimaan(response.data);
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
                this.createPenerimaanCssd(this.penerimaan).then((response) => {
                    if (response.success == true) {
                        this.fillPenerimaan(response.data);
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
/* .uk-input, .uk-textarea, .uk-select, .uk-checkbox, .uk-radio {
    border:1px solid #333;
}

.hims-form-subpage {
    padding:1em;
    margin:0 0.5em 0.5em 0.5em;
    border-top:1px solid #cc0202;
} */
</style>