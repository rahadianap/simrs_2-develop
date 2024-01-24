<template>
    <!-- Popup Modal tambah data kategori -->
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="formKategori" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container hims-form-container" style="min-height:50vh; background-color:#fff;">
                <form action="" @submit.prevent="submitKategori" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">TAMBAH DATA KATEGORI</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div>                  
                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                            <p>{{ errors.invalid }}</p>
                        </div>              
                        <div class="uk-grid-small hims-form-subpage" uk-grid >
                            <!-- <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">DATA UTAMA</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    informasi detail data asset.
                                </p>
                            </div> -->
                            <div class="uk-width-1-1@s uk-grid-small" uk-grid> 
                                <!-- nama Kategori -->
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Kategori*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="refDataGroup.value" type="text" placeholder="masukkan nama kategori" required>
                                    </div>
                                </div>

                                <!-- keterangan Kategori -->
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Keterangan Kategori*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="refDataGroup.text" type="text" placeholder="masukkan keterangan kategori" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="uk-grid-small hims-form-subpage" uk-grid>
                            <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">INFO TAMBAHAN</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">informasi tambahan terkait Asset</p>
                            </div>
                            <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                                
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Narahubung*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="assets.narahubung" type="text" placeholder="contact person">
                                    </div>
                                </div>
                                
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No. Telepon*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="assets.telepon" type="text" placeholder="nomor telepon">
                                    </div>
                                </div>
                                
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Email*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="assets.email" type="email" placeholder="alamat email">
                                    </div>
                                </div>                                

                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Alamat</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" rows="2" v-model="assets
                                        .alamat" type="text" placeholder="alamat pemasok atau pabrikan"></textarea>
                                    </div>
                                </div> 
                            </div>
                        </div> -->
                    </div>
                </form>                          
            </div>
        </div>
    </div> 
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';

export default {
    name : 'form-kategori', 
    // mixins: [Vue2Filters.mixin],

    data() {
        return {
            isUpdate : true,
            // listRefStatus : {
            //     ref_id : null,
            //     ref_nama : null,
            //     ref_data : []
            // },
            listRefGroup : {
                ref_id : null,
                ref_nama : null,
                ref_data : []
            },

            refDataGroup : {
                value : null,
                text : null,
            },

            // refDataStatus : {
            //     value : null,
            //     text : null,
            // }

            // status_asset : [
            //     { id:'Terverifikasi', text:'Terverifikasi' },
            //     { id:'Tidak Terverifikasi', text:'Tidak Terverifikasi' },
            //     { id:'Tidak Tahu', text:'Tidak Tahu' },
            // ]
        }
    },
    computed : {
        ...mapGetters('refAset',['refAsetStatus','refAsetGroup']),
        ...mapGetters(['errors']),
    },

    mounted() {
        // this.createKategori();
        // this['assets/form-asset']();
    },
    

    methods : {
        ...mapActions('refAset',['updateRefAset','listRefAsetGroup','listRefAsetStatus']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formKategori').hide();
            return false;
        },

        submitKategori(data){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.updateRefAset(data).then((response) => {
                    if (response.success == true) {
                        alert("Data Kategori baru berhasil dibuat.");
                        // this.fillKategori(response.data);
                        // this.clearKategori();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        UIkit.modal('#formKategori').hide();
                    }
                })
            }
            else {
                this.updateRefAset(data).then((response) => {
                    if (response.success == true) {
                        // this.fillKategori(response.data);
                        alert("Data Asset berhasil diubah.");
                        // this.$emit('saveSucceed',true);
                        // this.isUpdate = true;
                        // this.clearKategori();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        UIkit.modal('#editFormKategori').hide();
                        this.closeModal();
                    }
                })
            }            
        },

        newKategori(){
            this.clearKategori();
            this.isUpdate = false;
            UIkit.modal('#formKategori').show();
        },

        editKategori(data){
            console.log(444);
            this.clearKategori();            
            this.updateRefAset(data).then((response)=>{
                if (response.success == true) {
                    // this.fillKategori(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#editFormKategori').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        clearKategori(){
            // this.assets.asset_id = null;
            // this.assets.nomor_asset = null;
            // this.assets.nomor_seri = null;
            // this.assets.group_asset = null;
            // this.assets.nama_asset = null;
            // this.assets.deskripsi = null;
            // this.assets.brand = null;
            // this.assets.lokasi = null;
            // this.assets.nilai_asset = null;
            // this.assets.masa_pakai = null;
            // this.assets.is_aktif = null;
            // this.assets.tgl_perolehan = null;
            // this.assets.tgl_pemakaian = null;
            // this.assets.client_id = null;
            // this.assets.created_by = true;
            // this.assets.updated_by = null;
            // this.assets.updated_at = true;
            // this.assets.created_at = null;
            // this.assets.status = null;
        },

        fillKategori(data){
            // this.assets.asset_id = data.asset_id;
            // this.assets.nomor_asset = data.nomor_asset;
            // this.assets.nomor_seri = data.nomor_seri;
            // this.assets.group_asset = data.group_asset;
            // this.assets.nama_asset = data.nama_asset;
            // this.assets.deskripsi = data.deskripsi;
            // this.assets.brand = data.brand;
            // this.assets.lokasi = data.lokasi;
            // this.assets.nilai_asset = data.nilai_asset;
            // this.assets.masa_pakai = data.masa_pakai;
            // this.assets.is_aktif = data.is_aktif;
            // this.assets.tgl_perolehan = data.tgl_perolehan;
            // this.assets.tgl_pemakaian = data.tgl_pemakaian;
            // this.assets.client_id = data.client_id;
            // // this.assets.created_by = data.created_by;
            // // this.assets.updated_by = data.updated_by;
            // this.assets.status = data.status;
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