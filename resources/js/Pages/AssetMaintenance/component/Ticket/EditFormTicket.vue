<template>
    <!-- Popup Modal EDIT data asset -->
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="editFormTicket" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body" style="border-radius:10px;">
            <div class="uk-container hims-form-container" style="min-height:50vh; background-color:#fff;">
                <form action="" @submit.prevent="submitTicket" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" style="border-radius:10px;justify-content:center;text-align:center;" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">EDIT DATA TICKET</h5>
                        </div>                                
                        <!-- <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div> -->
                    </div>
                    <div style="margin:0 10px;border-top:2px solid #cc0202;">                  
                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                            <p>{{ errors.invalid }}</p>
                        </div>              
                        <div class="uk-grid-small hims-form-subpage-asset" style="margin:30px 0" uk-grid >
                            <div class="uk-width-1-1@s uk-grid-small" style="margin-left:1px;padding-left:1px;" uk-grid>
                                <!-- id asset dari tabel assets -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <!-- <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">ID Asset</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.asset_id" type="text" placeholder="masukkan ID aset" required >
                                    </div> -->
                                    <search-select
                                    :config = configAssetSelect
                                    searchUrl = "/assets"
                                    label = "Nama Aset*"
                                    placeholder = "pilih nama aset"
                                    captionField = "nama_asset"
                                    valueField = "asset_id"
                                    :itemListData = assetDesc
                                    :value = "mtickets.asset_id"
                                    v-on:item-select = "assetSelected"
                                    ></search-select>
                                </div>

                                <!-- jenis maintenance -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Maintenance*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.jenis_maintenance" type="text" placeholder="masukkan jenis maintenance" required>
                                    </div>
                                </div>

                                 <!-- prioritas -->
                                 <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Prioritas*</label>
                                    <!-- <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.prioritas" type="text" placeholder="masukkan prioritas tiket" required>
                                    </div> -->
                                    <div class="uk-form-controls">
                                        <select v-model="mtickets.prioritas" class="uk-select uk-form-small" required>
                                            <option value="Rendah">Rendah</option>
                                            <option value="Sedang">Sedang</option>
                                            <option value="Tinggi">Tinggi</option>
                                        </select>
                                    </div>
                                </div>

                                 <!-- lokasi aset -->
                                 <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <!-- <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Lokasi Asset*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.lokasi_asset" type="text" placeholder="lokasi asset" required>
                                    </div> -->
                                    <search-select
                                    :config = configLokasiSelect
                                    searchUrl = "/assets/locations"
                                    label = "Lokasi*"
                                    placeholder = "ganti lokasi aset"
                                    captionField = "lokasi_nama"
                                    valueField = "lokasi_nama"
                                    :itemListData = lokasiDesc
                                    :value = "mtickets.lokasi_asset"
                                    v-on:item-select = "lokasiSelected"
                                    ></search-select>
                                </div>

                                  <!-- nama teknisi -->
                                  <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Teknisi*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.nama_teknisi" type="text" placeholder="nama teknisi" required>
                                    </div>
                                </div>

                                <!-- tindakan -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tindakan*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.tindakan" type="text" placeholder="tindakan" required>
                                    </div>
                                </div>

                                <!-- catatan tindakan -->
                                 <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan Tindakan</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.catatan_tindakan" type="text" placeholder="catatan tindakan">
                                    </div>
                                </div>

                                <!-- tanggal selesai maintenance -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Selesai*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.tgl_selesai" type="date" placeholder="tanggal selesai maintenance" required>
                                    </div>
                                </div>

                                <!-- status -->
                                <!-- <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Status*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.status" type="text" placeholder="status maintenance" required>
                                    </div>
                                </div>     -->

                                <!-- tgl tiket -->
                                <!-- <div class="uk-width-1-3@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Tiket Dibuat*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.tgl_tiket" type="date" placeholder="mtickets.tgl_tiket">
                                    </div>
                                </div> -->

                                <!-- keterangan ticket -->
                                <div class="uk-width-1-1@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Keterangan</label>
                                    <div class="uk-form-controls">
                                        <textarea input class="uk-input uk-form-small" v-model="mtickets.keterangan" type="text" placeholder="keterangan" required></textarea>
                                    </div>
                                </div>

                                <!-- is_aktif -->
                                <!-- <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                                    <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="mtickets.is_aktif" style="margin-left:5px;"> Data Tiket aktif</label>
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-collapse uk-card uk-card-default hims-form-footer uk-column-1-2@m" style="border-radius: 10px;">
                        <div class="uk-width-auto uk-column-1-1@s" style="padding:1em 1em 0.2em 0.8em;margin:0;">
                            <!-- <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="mtickets.is_aktif" style="margin-left:5px;"> Data Tiket aktif</label> -->
                        </div>
                        <div class="uk-grid-match uk-align-center uk-align-right@m uk-column-1-2@s" style="margin-bottom:6px;margin-top:1px">
                            <div class="uk-width-auto" style="padding:0.5em;">
                                <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;border-radius:10px;padding:0 85px;">Tutup</button>                  
                            </div>
                            <div class="uk-width-auto" style="padding:0.5em;">
                                <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;border-radius:10px;padding:0 85px;">Simpan</button>                  
                            </div>
                        </div>
                    </div>
                </form>                          
            </div>
        </div>
    </div> 
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchList from '@/Components/SearchList.vue';
import SearchSelect from '@/Components/SearchSelect.vue';

export default {
    name : 'edit-form-ticket', 
    components : {
        SearchList,
        SearchSelect,
    },
    // mixins: [Vue2Filters.mixin],

    data() {
        return {
            // config select id aset
            configAssetSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            assetDesc : [
                { colName : 'nama_asset', labelData : '', isTitle : true },
                { colName : 'deskripsi', labelData : '', isTitle : false },
                { colName : 'asset_id', labelData : '', isTitle : false },
            ],

            // config select lokasi aset
            configLokasiSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            lokasiDesc : [
                { colName : 'lokasi_nama', labelData : '', isTitle : true },
                { colName : 'deskripsi', labelData : '', isTitle : false },
                { colName : 'lokasi_aset_id', labelData : '', isTitle : false },
            ],

            isUpdate : true,
            mtickets : {
                maint_ticket_id:null,
                asset_id:null,
                jenis_maintenance:null,
                tgl_tiket : null,
                keterangan : null,
                prioritas:null,
                lokasi_asset : null,
                nama_teknisi:null,
                tindakan:null,
                catatan_tindakan:null,
                tgl_selesai : null,
                // tgl_perolehan : null,
                status : null,
                client_id : null,
                is_aktif : true,
                updated_by : null,
            },
            unitData : {
                maint_ticket_id:null,
                asset_id:null,
                jenis_maintenance:null,
                tgl_tiket : null,
                keterangan : null,
                prioritas:null,
                lokasi_asset : null,
                nama_teknisi:null,
                tindakan:null,
                catatan_tindakan:null,
                tgl_selesai : null,
                // tgl_perolehan : null,
                status : null,
                client_id : null,
                is_aktif : true,
                updated_by : null,
            },
            lokasi : {
                lokasi_aset_id : null,
            },
            asset : {
                asset_id : null,
            },

            // status_asset : [
            //     { id:'Terverifikasi', text:'Terverifikasi' },
            //     { id:'Tidak Terverifikasi', text:'Tidak Terverifikasi' },
            //     { id:'Tidak Tahu', text:'Tidak Tahu' },
            // ]
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
        // this.createTicket();
        // this['assets/form-asset']();
        // try {
    },
    

    methods : {
        ...mapActions('assetMaintenance',['createTicket','updateTicket','dataTicket']),
        ...mapActions('lokasi'['dataLokasi']),
        ...mapActions('asset'['dataAsset']),
        ...mapMutations(['CLR_ERRORS']),

        lokasiSelected(data){ 
            if(data) {
                this.mtickets.lokasi_asset = data.lokasi_nama;
                this.lokasi.lokasi_aset_id = data.lokasi_aset_id;
            }
        },

        assetSelected(data){ 
            if(data) {
                this.mtickets.asset_id = data.asset_id;
                this.asset.asset_id = data.asset_id;
            }
        },

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#editFormTicket').hide();
            return false;
        },

        submitTicket(){
            this.CLR_ERRORS();
            console.log(this.isUpdate)
            if(!this.isUpdate) {
                this.createTicket(this.mtickets).then((response) => {
                    if (response.success == true) {
                        alert("Data Ticket baru berhasil dibuat.");
                        // this.fillTicket(response.data);
                        this.clearTicket();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        // UIkit.modal('#editFormTicket').hide();
                    }
                })
            } 
            
            else {
                console.log("Masuk ke update");
                this.updateTicket(this.mtickets).then((response) => {
                    if (response.success == true) {
                        // this.fillTicket(response.data);
                        alert("Data Ticket berhasil diubah.");
                        // this.$emit('saveSucceed',true);
                        // this.isUpdate = true;
                        this.clearTicket();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModal();
                    }
                })
            }            
        },

        newTicket(){
            this.clearTicket();
            this.isUpdate = false;
            UIkit.modal('#editFormTicket').show();
        },

        editTicket(maint_ticket_id){
                this.clearTicket();            
                this.dataTicket(maint_ticket_id).then((response)=>{
                    if (response.success == true) {
                        this.fillTicket(response.data);
                        this.isUpdate = true;
                        UIkit.modal('#editFormTicket').show();
                    } else {
                        alert(response.message)
                    }
                })
            
        },   

        clearTicket(){
            this.mtickets.maint_ticket_id = null;
            this.mtickets.asset_id = null;
            this.mtickets.jenis_maintenance = null;
            this.mtickets.tgl_tiket = null;
            this.mtickets.keterangan = null;
            this.mtickets.prioritas = null;
            this.mtickets.lokasi_asset = null;
            this.mtickets.nama_teknisi = null;
            this.mtickets.tindakan = null;
            this.mtickets.catatan_tindakan = null;
            this.mtickets.tgl_selesai = null;
            // this.mtickets.tgl_perolehan = null;
            this.mtickets.status = null;
            this.mtickets.client_id = null;
            this.mtickets.created_by = true;
            this.mtickets.updated_by = null;
        },

        fillTicket(data){
            this.mtickets.maint_ticket_id = data.maint_ticket_id;
            this.mtickets.asset_id = data.asset_id;
            this.mtickets.jenis_maintenance = data.jenis_maintenance;
            this.mtickets.tgl_tiket = data?.tgl_tiket.slice(0,10);
            this.mtickets.keterangan = data.keterangan;
            this.mtickets.prioritas = data.prioritas;
            this.mtickets.lokasi_asset = data.lokasi_asset;
            this.mtickets.nama_teknisi = data.nama_teknisi;
            this.mtickets.tindakan = data.tindakan;
            this.mtickets.catatan_tindakan = data.catatan_tindakan;
            this.mtickets.is_aktif = data.is_aktif;
            this.mtickets.tgl_selesai = data?.tgl_selesai.slice(0,10);
            this.mtickets.status = data.tgl_pemakaian;
            this.mtickets.client_id = data.client_id;
            this.mtickets.created_by = data.created_by;
            this.mtickets.updated_by = data.updated_by;
        },
    }
}
</script>


<style>
/* .uk-input, .uk-textarea, .uk-checkbox {
    border: 1px solid #999;
}

.hims-form-container label {
    color:#333;
    font-size:12x;
}

.hims-button-primary {
    background-color: #cc0202;
    color: #fafafa;
    border:2px solid #cc0202;
}

.uk-button {
    border:2px solid #aaa;
    font-weight:400;
}

.hims-button-primary {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}

.hims-button-primary:hover {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}

.hims-accordion-title {
    border-bottom:1px solid #cc0202; 
    font-size:14px; 
    font-weight:500; 
    background-color:#cc0202; 
    color:white; 
    padding:0.5em 1em 0.5em 1em;
    border-radius:5px;
}

.hims-accordion-title:hover {
    color:white; 
}
.hims-accordion-title::before {
    color:white;
}

.hims-form-header {
    padding:0.5em 0 0 0; 
    margin:1em 0 0 0;
    position:sticky;
    top:0;
    background-color:white;
    z-index:99;
    color:#cc0202;
}

.hims-form-header h5 {
    color:#333;
    font-weight:500;
    font-size:18px;
}


.hims-form-subpage {
    padding:1em;
    margin:0 0.5em 0.5em 0.5em;
    border-top:1px solid #cc0202;
} */
.hims-form-footer {
    padding:0.5em 0 0 0; 
    margin:1em 0 0 0;
    position:sticky;
    bottom:0;
    background-color:white;
    z-index:99;
    color:#cc0202;
}

.hims-form-footer h5 {
    color:#333;
    font-weight:500;
    font-size:18px;
}
</style>