<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="modalPemusnahan" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body" style="padding:0;">
            <form action="" @submit.prevent="submitPemusnahan">
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
                            <h5 style="color:indigo;padding:0;margin:0;">DATA PEMUSNAHAN</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                detail data pemusnahan persediaan darah.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                            <div class="uk-grid-small uk-width-3-4@l 1-1@m" uk-grid>                        
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Pemusnahan*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="date" v-model="pemusnahan.tgl_pemusnahan" style="color:black;" required>
                                    </div>
                                </div>
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Pemusnahan*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="time" v-model="pemusnahan.jam_pemusnahan" style="color:black;" required>
                                    </div>
                                </div>
                            
                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan*</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" required type="text" v-model="pemusnahan.catatan" style="color:black;"></textarea>
                                    </div>
                                </div>
                            </div>                            
                        </div>
                    </div>

                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-1@m" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">ITEM PEMUSNAHAN
                                <span style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    daftar item yang dimusnahkan
                                </span>
                            </h5>
                        </div>
                        <table-pemusnahan ref="tablePemusnahan" v-on:itemPemusnahanChange="updateItemList"></table-pemusnahan>
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
import TablePemusnahan from '@/Pages/Penunjang/BankDarah/Pemusnahan/components/TablePemusnahan.vue';

export default {
    name : 'modal-penerimaan',
    components : { SearchSelect,SearchList, TablePemusnahan },
    data() {
        return {
            formTitle : 'PENERIMAAN BARU',
            isUpdate : true,
            isLoading : true,

            // configSelect : {
            //     required : true,
            //     compClass : "uk-width-1-1@m",
            //     compStyle : "padding:0;margin:0;",
            // },

            // configItemSelect : {
            //     required : false,
            //     compClass : "uk-width-1-1@m",
            //     compStyle : "padding:0;margin:0;",
            // },

            // cssdDesc : [
            //     { colName : 'produk_nama', labelData : '', isTitle : true },
            //     { colName : 'produk_id', labelData : '', isTitle : false },
            // ],

            // unitDesc : [
            //     { colName : 'unit_nama', labelData : '', isTitle : true },
            //     { colName : 'unit_id', labelData : '', isTitle : false },
            // ],

            // satuanDesc : [
            //     { colName : 'value', labelData : '', isTitle : true },
            //     { colName : 'text', labelData : '', isTitle : false },
            // ],   
            
            // penerimaan : {
            //     darah_terima_id : null,
            //     asal_darah : null,
            //     nama_donor : null,
            //     tgl_terima : null,
            //     penerima : null,
            //     catatan : null,
            //     status : 'TERIMA',
            //     is_aktif : true,
            //     client_id : null,
            //     items : [],
            // },

            pemusnahan : {
                darah_musnah_id : null,
                tgl_pemusnahan : null,
                jam_pemusnahan : null,
                catatan : null,
                status : 'PEMUSNAHAN',
                is_aktif : true,
                client_id : null,
                items : [],
            },

            // darahItem : {
            //     darah_detail_id : null,
            //     darah_terima_id : null,
            //     tgl_terima : null,                
            //     darah_dist_id : null,
            //     tgl_distribusi : null,
            //     jam_distribusi : null,                
            //     no_labu : null,
            //     gol_darah : null,
            //     rhesus : 0,
            //     group_darah : null,
            //     volume : 0,
            //     satuan : 0,
            //     jumlah : 0,
            //     tgl_kadaluarsa : null,
            //     catatan : null,
            //     status : 'TERIMA',
            //     is_terpakai : false,                
            //     is_aktif : true,
            //     client_id : null,
            // },

            //itemUrl : '',
            //unitUrl : '',
        }
    },

    computed : {
        // ...mapGetters('refProduk',['isRefProdukExist','satuanProdukRefs']),
        // ...mapGetters('referensi',['isRefExist','golDarahRefs','rhesusRefs','groupDarahRefs','asalDarahRefs',]),
    },

    mounted() {     
    },

    methods : {
        ...mapActions('bankDarah',['getDarahLists','createPemusnahanDarah','dataPemusnahanDarah']),
        ...mapMutations(['CLR_ERRORS']),
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
            let data = JSON.parse(JSON.stringify(this.pemusnahan));
            this.clearPemusnahan();
            this.$emit('modalPemusnahanClosed',data);
            UIkit.modal('#modalPemusnahan').hide();            
        },

        /**modal call from other component for new data entry */
        newData(){
            this.CLR_ERRORS();
            this.clearPemusnahan();
            this.isUpdate = false;
            this.formTitle = "DATA BARU PEMUSNAHAN";
            this.$refs.tablePemusnahan.retrieveStockDarah();
            UIkit.modal('#modalPemusnahan').show();
        },

        clearPemusnahan() {
            this.pemusnahan.darah_musnah_id = null;
            this.pemusnahan.tgl_pemusnahan = this.getTodayDate();
            this.pemusnahan.jam_pemusnahan = null;
            this.pemusnahan.catatan = null;
            this.pemusnahan.is_aktif = true;
            this.pemusnahan.client_id = null;
            this.pemusnahan.items = [];
        },

        submitPemusnahan(){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus persediaan darah. Lanjutkan ?`)){
                this.isLoading = true;
                this.createPemusnahanDarah(this.pemusnahan).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false;
                        alert('Data berhasil disimpan.');
                        this.modalClosed();
                    }
                    else { alert(response.message); this.isLoading = false; }
                })
            };
            
        },

        updateItemList(data){
            this.pemusnahan.items = [];
            if(data){
                this.pemusnahan.items = data;
                console.log(this.pemusnahan);
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