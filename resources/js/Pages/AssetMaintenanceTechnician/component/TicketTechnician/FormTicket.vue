<template>
    <!-- Popup Modal tambah data ticket -->
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="formTicket" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body" style="border-radius:10px;">
            <div class="uk-container hims-form-container" style="min-height:50vh; background-color:#fff;">
                <form action="" @submit.prevent="submitTicket" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" style="border-radius:10px;justify-content:center;text-align:center;" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">TAMBAH DATA TIKET</h5>
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
                                
                                <!-- Id client -->
                                <!-- <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">ID Client*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.client_id" type="text" placeholder="masukkan ID client" required>
                                    </div>
                                </div> -->

                                <!-- Nama asset dari table assets -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <!-- <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">ID Asset*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.asset_id" type="text" placeholder="masukkan ID aset" required>
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
                                <!-- <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Mainenance*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.jenis_maintenance" type="text" placeholder="jenis maintenance" required>
                                    </div>
                                </div> -->

                                <!-- tanggal tiket -->
                                <!-- <div class="uk-width-1-3@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Tiket*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.tgl_tiket" type="date" placeholder="tanggal tiket" required>
                                    </div>
                                </div> -->

                                <!-- status ticket -->
                                <!-- <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Status Tiket*</label>
                                    <div class="uk-form-controls">
                                        // <input class="uk-input uk-form-small" v-model="mtickets.status" type="text" placeholder="status tiket" required> //
                                        <select class="uk-select uk-form-small" v-model="mtickets.status" required>
                                            <option value="Open">Open</option>
                                            <option value="On-going">On-going</option>
                                            <option value="Closed">Closed</option>
                                        </select>
                                    </div>
                                </div> -->

                                <!-- prioritas -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Prioritas*</label>
                                    <!-- <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.prioritas" type="text" placeholder="prioritas tiket" required>
                                    </div> -->
                                    <div class="uk-form-controls">
                                        <combobox 
                                            :color-options="colors" 
                                            empty-option="None" 
                                            input-id="'colorInput'" 
                                            v-model="selectedPriorityName"
                                            v-model.trim="mtickets.prioritas"
                                            :value = "mtickets.prioritas"                                        >
                                        </combobox>
                                    </div>
                                </div>

                                <!-- lokasi aset -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <!-- <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Lokasi Aset*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.lokasi_asset" type="text" placeholder="lokasi asset" required>
                                    </div> -->
                                    <search-select
                                    :config = configLokasiSelect
                                    searchUrl = "/assets/locations"
                                    label = "Lokasi*"
                                    placeholder = "pilih lokasi aset"
                                    captionField = "lokasi_nama"
                                    valueField = "lokasi_nama"
                                    :itemListData = lokasiDesc
                                    :value = "mtickets.lokasi_asset"
                                    v-on:item-select = "lokasiSelected"
                                    ></search-select>
                                </div>

                                <!-- nama teknisi -->
                                <div class="uk-width-1-3@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Teknisi</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.nama_teknisi" type="text" placeholder="nama teknisi">
                                    </div>
                                </div>

                                 <!-- tindakan -->
                                <div class="uk-width-1-3@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tindakan</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.tindakan" type="text" placeholder="tindakan">
                                    </div>
                                </div>

                                 <!-- catatan tindakan -->
                                 <div class="uk-width-1-3@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan Tindakan</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.catatan_tindakan" type="text" placeholder="catatan tindakan">
                                    </div>
                                </div>

                                 <!-- tanggal selesai -->
                                 <!-- <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Selesai</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mtickets.tgl_selesai" type="date" placeholder="tanggal selesai tiket">
                                    </div>
                                </div> -->

                                <!-- keterangan tiket -->
                                <div class="uk-width-1-1@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Keterangan*</label>
                                    <div class="uk-form-controls">
                                        <textarea input class="uk-input uk-textarea" v-model="mtickets.keterangan" type="text" placeholder="keterangan tiket" required></textarea>
                                    </div>
                                </div>
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
import Combobox from '@/Components/BaseTableAsset/Combobox.vue';


export default {
    name : 'form-ticket', 
    components : 
    {
        SearchList,
        SearchSelect,
        Combobox,
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

            // config prioritas
            selectedPriorityName: "", 
            colors: [
                {
                    label: "#66ff00",
                    priority: "Rendah"
                },
                {
                    label: "#FFFF00",
                    priority: "Sedang"
                },
                {
                    label: "#FF0000",
                    priority: "Tinggi"
                }
            ],

            // comboboxOptions: [
            //     { value: 'Rendah', label: 'Rendah' },
            //     { value: 'Sedang', label: 'Sedang' },
            //     { value: 'Tinggi', label: 'Tinggi' },
            // ],
            // SuccessClass: 'uk-label-success', // Nilai untuk opsi Rendah
            // WarningClass: 'uk-label-warning', // Nilai untuk opsi Sedang
            // DangerClass: 'uk-label-danger', // Nilai untuk opsi Tinggi


            isUpdate : true,
            mtickets : {
                maint_ticket_id:null,
                asset_id:null,
                jenis_maintenance:'Corrective',
                tgl_tiket : null,
                keterangan : null,
                prioritas:null,
                lokasi_asset : null,
                nama_teknisi:null,
                tindakan:null,
                catatan_tindakan:null,
                tgl_selesai : null,
                status : 'Open',
                client_id : null,
                created_by : null,
                updated_by:null,
                is_aktif : true,
            },
            unitData : {
                maint_ticket_id:null,
                asset_id:null,
                jenis_maintenance:'Corrective',
                tgl_tiket : null,
                keterangan : null,
                prioritas:null,
                lokasi_asset : null,
                nama_teknisi:null,
                tindakan:null,
                catatan_tindakan:null,
                tgl_selesai : null,
                status : 'Open',
                client_id : null,
                created_by : null,
                updated_by:null,
                is_aktif : true,

            },
            lokasi : {
                lokasi_aset_id : null,
            },
            asset : {
                asset_id : null,
            }

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

        prioritasSelected(data) {
            if (data && data.selectedPriorityName) {
                this.mtickets.prioritas = data.selectedPriorityName;
            }
        },

        setColor(color, colorPriority) {
          this.selectedColorLabel = color;
          this.selectedPriorityName = colorPriority;
          this.active = false;
          this.noSelection = false;
          this.$emit('input', this.selectedColorLabel);
        },

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formTicket').hide();
            return false;
        },

        // submitTicket(){
        //     this.CLR_ERRORS();
        //     if(!this.isUpdate) {
        //         this.createTicket(this.mtickets).then((response) => {
        //             if (response.success == true) {
        //                 alert("Data Ticket baru berhasil dibuat.");
        //                 // this.fillTicket(response.data);
        //                 this.clearTicket();
        //                 this.$emit('saveSucceed',true);
        //                 this.isUpdate = true;
        //                 UIkit.modal('#formTicket').hide();
        //             }
        //         })
        //     }
        //     else {
        //         this.updateTicket(this.mtickets).then((response) => {
        //             if (response.success == true) {
        //                 // this.fillTicket(response.data);
        //                 alert("Data Tiket berhasil diubah.");
        //                 // this.$emit('saveSucceed',true);
        //                 // this.isUpdate = true;
        //                 this.clearTicket();
        //                 this.$emit('saveSucceed',true);
        //                 this.isUpdate = false;
        //                 UIkit.modal('#editFormTicket').hide();
        //                 this.closeModal();
        //             }
        //         })
        //     }            
        // },

        submitTicket(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createTicket(this.mtickets)
                    .then((response) => {
                        if (response.success === true) {
                            alert("Data Tiket baru berhasil dibuat.");
                            this.clearTicket();
                            this.$emit('saveSucceed', true);
                            this.isUpdate = true;
                            UIkit.modal('#formTicket').hide();
                        }
                    })
                    .catch((error) => {
                        console.error("Terjadi kesalahan saat membuat tiket baru:", error);
                    });
            }
            else {
                this.updateTicket(this.mtickets)
                    .then((response) => {
                        if (response.success === true) {
                            alert("Data Tiket berhasil diubah.");
                            this.clearTicket();
                            this.$emit('saveSucceed', true);
                            this.isUpdate = false;
                            UIkit.modal('#editFormTicket').hide();
                            this.closeModal();
                        }
                    })
                    .catch((error) => {
                        console.error("Terjadi kesalahan saat mengubah tiket:", error);
                    }
                );
            }            
        },

        newTicket(){
            this.clearTicket();
            this.isUpdate = false;
            UIkit.modal('#formTicket').show();
        },

        editTicket(maint_ticket_id){
            console.log(444);
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
            this.mtickets.jenis_maintenance = 'Corrective';
            this.mtickets.tgl_tiket = null;
            this.mtickets.keterangan = null;
            this.mtickets.prioritas = null;
            this.mtickets.lokasi_asset = null;
            this.mtickets.nama_teknisi = null;
            this.mtickets.tindakan = null;
            this.mtickets.catatan_tindakan = null;
            this.mtickets.tgl_selesai = null;
            this.mtickets.status = null;
            this.mtickets.client_id = null;
            this.mtickets.created_by = null;
            this.mtickets.updated_by = null;
            this.mtickets.is_aktif = true;
        },

        fillTicket(data){
            this.mtickets.maint_ticket_id = data.maint_ticket_id;
            this.mtickets.asset_id = data.asset_id; 
            this.mtickets.jenis_maintenance = data.jenis_maintenance;
            this.mtickets.tgl_tiket = data.tgl_tiket;
            this.mtickets.keterangan = data.keterangan;
            this.mtickets.prioritas = data.prioritas;
            this.mtickets.lokasi_asset = data.lokasi_asset;
            this.mtickets.nama_teknisi = data.nama_teknisi;
            this.mtickets.tindakan = data.tindakan;
            this.mtickets.catatan_tindakan = data.catatan_tindakan;
            this.mtickets.tgl_selesai = data.tgl_selesai;
            this.mtickets.status = data.status;
            this.mtickets.is_aktif = data.is_aktif;
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

</style>