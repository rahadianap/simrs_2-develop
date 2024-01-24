<template>
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="formJabatan" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container hims-form-container" style="min-height:50vh; background-color:#fff;">
                <form action="" @submit.prevent="submitJabatan" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA JABATAN</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeJabatanModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <!-- <div>                   -->
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>              
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA UTAMA</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                informasi utama terkait data jabatan.
                            </p>
                        </div>
                        <div class="uk-width-3-4@s uk-grid-small" uk-grid> 
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Jabatan*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="jabatan.jabatan_nama" type="text" placeholder="nama jabatan" required>
                                </div>
                            </div>
                            
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="jabatan.deskripsi" type="text" placeholder="deskripsi" required>
                                </div>
                            </div>
                            
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggung Jawab*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="jabatan.tanggung_jawab" type="text" placeholder="tanggung jawab jabatan" required>
                                </div>
                            </div>

                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Berlaku*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="jabatan.tgl_berlaku" type="date" placeholder="tanggal berlaku" required>
                                </div>
                            </div>

                            <div class="uk-width-1-1@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                                <div class="uk-form-controls">
                                    <label style="color:black; font-size:14px;font-weight: 400;">
                                        <input class="uk-checkbox" type="checkbox" value="1" v-model="jabatan.is_aktif" >
                                    </label>
                                    <span style="font-size:12px; color:black; padding:0.2em 0.2em 0.2em 0.4em; margin:0;">Data jabatan aktif.</span>                                        
                                </div>
                            </div>
                        </div>
                    </div>  
                    <!-- </div> -->
                </form>                          
            </div>
        </div>
    </div> 
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';

export default {
    name : 'form-jabatan', 
    data() {
        return {
            isLoading : true,
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
                retrieveAll : true,
            },

            isUpdate : true,
            jabatan : {
                client_id:null,
                jabatan_id:null,
                jabatan_nama : null,
                deskripsi : null,
                tanggung_jawab : null,
                tgl_berlaku : null,
                // status : null,
                is_aktif : true,          
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
        // ...mapGetters('referensi',['','isRefExist']),
    },

    mounted() {
        // this.initialize();
    },
    
    methods : {
        ...mapActions('jabatan',['createJabatan','updateJabatan','dataJabatan']),
        // ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        // initialize(){
        //     if(!this.isRefExist) { this.listReferensi(); };
        // },

        closeJabatanModal(){
            this.$emit('formJabatanClosed',true);
            UIkit.modal('#formJabatan').hide();
            return false;
        },

        submitJabatan(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createJabatan(this.jabatan).then((response) => {
                    if (response.success == true) {
                        alert("Jabatan baru berhasil dibuat.");
                        // this.clearJabatan();
                        this.fillJabatan(response.data);
                        this.$emit('saveSucceed',true);
                        // this.isUpdate = false;
                        this.isUpdate = true;
                        this.closeJabatanModal();
                    }
                })
            }
            else {
                this.updateJabatan(this.jabatan).then((response) => {
                    if (response.success == true) {
                        this.fillJabatan(response.data);
                        alert("Jabatan berhasil diubah.");
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        // this.closeJabatanModal();
                    }
                })
            }            
        },

        newJabatan(){
            this.clearJabatan();
            this.isUpdate = false;
            UIkit.modal('#formJabatan').show();
        },

        editJabatan(jabatanId){
            // this.clearJabatan();            
            this.isLoading = true;
            this.dataJabatan(jabatanId).then((response)=>{
                if (response.success == true) {
                    this.fillJabatan(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formJabatan').show();
                } else {
                    alert(response.message)
                }
                this.isLoading = false;
            })
        },   

        clearJabatan(){
            this.jabatan.client_id = null;
            this.jabatan.jabatan_id = null;
            this.jabatan.jabatan_nama = null;
            this.jabatan.deskripsi = null;
            this.jabatan.tanggung_jawab = null;
            this.jabatan.tgl_berlaku = null;
            this.jabatan.status = null;
            this.jabatan.is_aktif = null;
        },

        fillJabatan(data){
            this.jabatan.client_id = null,
            this.jabatan.jabatan_id = data.jabatan_id;
            this.jabatan.jabatan_nama = data.jabatan_nama;
            this.jabatan.deskripsi = data.deskripsi;
            this.jabatan.tanggung_jawab = data.tanggung_jawab;
            this.jabatan.tgl_berlaku = data.tgl_berlaku;
            this.jabatan.status = data.status;
            this.jabatan.is_aktif = data.is_aktif;
        },
    }
}
</script>

<style>
/* .uk-input, .uk-textarea, .uk-checkbox, .uk-select {
    border: 1px solid #999;
    color: black;
}

.hims-form-container label {
    color:#000;
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