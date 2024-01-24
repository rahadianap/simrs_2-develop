<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="modalDarah" style="min-height:50vh;">
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
                                            <option v-for="dt in asalDarahLists" :value="dt.asal_darah">{{dt.asal_darah}}</option>
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
                                                    <option v-for="dt in groupDarahLists" :value="dt.jenis_produk_darah">{{dt.jenis_produk_darah}} ({{dt.inisial}})</option>
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
                                                    <option v-for="dt in groupDarahLists" :value="dt.jenis_produk_darah">{{dt.jenis_produk_darah}} ({{dt.inisial}})</option>
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
                darah_terima_id : null,
                asal_darah : null,
                nama_donor : null,
                tgl_terima : null,
                penerima : null,
                catatan : null,
                status : 'TERIMA',
                is_aktif : true,
                client_id : null,
                items : [],
            },

            darahItem : {
                darah_detail_id : null,
                darah_terima_id : null,
                tgl_terima : null,                
                darah_dist_id : null,
                tgl_distribusi : null,
                jam_distribusi : null,                
                no_labu : null,
                gol_darah : null,
                rhesus : 0,
                group_darah : null,
                volume : 0,
                satuan : 0,
                jumlah : 0,
                tgl_kadaluarsa : null,
                catatan : null,
                status : 'TERIMA',
                is_terpakai : false,                
                is_aktif : true,
                client_id : null,
            },

            itemUrl : '',
            unitUrl : '',
            groupDarahLists : null,
            asalDarahLists : null,
        }
    },

    computed : {
        ...mapGetters('refProduk',['isRefProdukExist','satuanProdukRefs']),
        ...mapGetters('referensi',['isRefExist','golDarahRefs','rhesusRefs','groupDarahRefs','asalDarahRefs',]),
    },

    mounted() {     
        this.initialize();
    },

    methods : {
        ...mapActions('bankDarah',['getDarahLists','createPenerimaanDarah','updatePenerimaanDarah','dataPenerimaanDarah']),
        ...mapActions('produkDarah',['listProdukDarah']),
        ...mapActions('asalDarah',['listAsalDarah']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            this.CLR_ERRORS();
            this.listReferensi({per_page:'ALL'});

            this.listProdukDarah({per_page:'ALL'}).then((response) => {
                if (response.success == true) {
                    this.groupDarahLists = response.data.data;
                }
            });

            this.listAsalDarah({per_page:'ALL'}).then((response) => {
                if (response.success == true) {
                    this.asalDarahLists = response.data.data;
                }
            });
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
            this.clearDarahItem();
            this.clearPenerimaan();
            this.$emit('modalDarahClosed',data);
            UIkit.modal('#modalDarah').hide();            
        },

        /**modal call from other component for new data entry */
        newData(){
            this.CLR_ERRORS();
            this.clearPenerimaan();
            this.clearDarahItem();
            this.isUpdate = false;
            this.formTitle = "DATA BARU PENERIMAAN";
            UIkit.modal('#modalDarah').show();
        },
        
        /**modal call from other component for update data entry */
        editData(refData) {
            this.CLR_ERRORS();
            this.clearPenerimaan();
            this.clearDarahItem();
            this.formTitle = "UBAH DATA PENERIMAAN";
            this.isUpdate = true;
            this.isLoading = true;

            if(refData) {
                this.dataPenerimaanDarah(refData.darah_terima_id).then((response) => {
                    if (response.success == true) {
                        this.fillPenerimaan(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        UIkit.modal('#modalDarah').show();
                    }
                    else { alert('data penerimaan tidak ditemukan'); }
                })
            }
        },

        clearDarahItem() {
            this.darahItem.darah_detail_id = null;
            this.darahItem.darah_terima_id = null;
            this.darahItem.tgl_terima = null;                
            this.darahItem.darah_dist_id = null;
            this.darahItem.tgl_distribusi = null;
            this.darahItem.jam_distribusi = null;                
            this.darahItem.no_labu = null;
            this.darahItem.gol_darah = null;
            this.darahItem.rhesus = 0;
            this.darahItem.group_darah = null;
            this.darahItem.volume = 0;
            this.darahItem.satuan = 0;
            this.darahItem.jumlah = 0;
            this.darahItem.tgl_kadaluarsa = null;
            this.darahItem.catatan = null;
            this.darahItem.status = 'TERIMA';
            this.darahItem.is_terpakai = false;                
            this.darahItem.is_aktif = true;
            this.darahItem.client_id = null;
        },

        clearPenerimaan() {
            this.penerimaan.darah_terima_id = null;
            this.penerimaan.asal_darah = null;
            this.penerimaan.nama_donor = null;
            this.penerimaan.tgl_terima = this.getTodayDate();
            this.penerimaan.penerima = null;
            this.penerimaan.catatan = null;
            this.penerimaan.status = 'TERIMA';
            this.penerimaan.is_aktif = true;
            this.penerimaan.client_id = null;
            this.penerimaan.items = [];
        },

        fillPenerimaan(data) {
            if(data) {
                this.penerimaan.darah_terima_id = data.darah_terima_id;
                this.penerimaan.asal_darah = data.asal_darah;
                this.penerimaan.nama_donor = data.nama_donor;
                this.penerimaan.tgl_terima = data.tgl_terima;
                this.penerimaan.penerima = data.penerima;
                this.penerimaan.catatan = data.catatan;
                this.penerimaan.status = data.status;
                this.penerimaan.is_aktif = data.is_aktif;
                this.penerimaan.client_id = data.client_id;
                this.penerimaan.items = data.items;
            }
        },

        addDarahItem() {
            let data = JSON.parse(JSON.stringify(this.darahItem));
            let dt = this.penerimaan.items.find(item => item.no_labu === data.no_labu);

            if(!dt) { this.penerimaan.items.push(data); this.clearDarahItem(); }
            else { alert('No labu sudah ada di dalam daftar penerimaan. Data tidak dapat ditambahkan.'); this.clearDarahItem(); }
        },

        changeActivationDarahItem(){
            let filteredData = this.penerimaan.items.filter(
                item => { return item.is_aktif == true || (item.darah_detail_id !== null && item.darah_detail_id !== '') }
            );
            this.penerimaan.items = JSON.parse(JSON.stringify(filteredData));
        },

        submitPenerimaan(){
            this.CLR_ERRORS();
            this.isLoading = true;
            
            if(this.isUpdate) {
                //update data
                this.updatePenerimaanDarah(this.penerimaan).then((response) => {
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
                this.createPenerimaanDarah(this.penerimaan).then((response) => {
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