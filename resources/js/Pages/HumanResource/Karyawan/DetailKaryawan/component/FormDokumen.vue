<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formDokumen" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body uk-border-rounded">
            <div class="hims-form-container" style="min-height:50vh; background-color:#fff;">
                <form action="" @submit.prevent="submitDokumen" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA DOKUMEN KARYAWAN</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeDokumenModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
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
                            <div class="uk-width-1-1@s uk-grid-small" uk-grid> 
                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Dokumen*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="hrdokumen.jenis_dokumen" type="text" placeholder="jenis dokumen" required>
                                    </div>
                                </div>
                                
                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" v-model="hrdokumen.catatan" type="text" placeholder="catatan"></textarea>
                                    </div>
                                </div>

                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">URL Dokumen</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="hrdokumen.url_dokumen" type="text" placeholder="url dokumen">
                                    </div>
                                </div>

                                <div class="uk-width-1-1@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;font-weight: 400;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="hrdokumen.is_aktif" >
                                        </label>
                                        <span style="font-size:12px; color:black; padding:0.2em 0.2em 0.2em 0.4em; margin:0;">Data dokumen aktif.</span>                                        
                                    </div>
                                </div>
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

export default {
    name : 'form-dokumen', 
            
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
            hrdokumen : {
                client_id:null,
                hr_dokumen_id:null,
                jenis_dokumen : null,
                url_dokumen : null,
                catatan : null,
                is_aktif : true,          
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('hrdokumen',['createDokumen','updateDokumen','deleteDokumen']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            if(!this.isRefExist) { this.listReferensi(); };
        },

        closeDokumenModal(){
            this.$emit('formDokumenClosed',true);
            UIkit.modal('#formDokumen').hide();
            return false;
        },

        submitDokumen(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createDokumen(this.hrdokumen).then((response) => {
                    if (response.success == true) {
                        alert("Dokumen pendukung baru berhasil dibuat.");
                        this.fillDokumen(response.data);
                        this.isUpdate = true;
                        this.closeDokumenModal();
                    }
                })
            }
            else {
                this.updateDokumen(this.hrdokumen).then((response) => {
                    if (response.success == true) {
                        this.fillDokumen(response.data);
                        alert("Dokumen pendukung berhasil diubah.");
                        this.isUpdate = true;
                        this.closeDokumenModal();
                    }
                })
            }            
        },

        newDokumen(){
            this.clearDokumen();
            this.isUpdate = false;
            UIkit.modal('#formDokumen').show();
        },

        editDokumen(hr_dokumen_id){
            this.clearDokumen();            
            this.dataDokumen(hr_dokumen_id).then((response)=>{
                if (response.success == true) {
                    this.fillDokumen(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formDokumen').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        clearDokumen(){
            this.hrdokumen.client_id = null;
            this.hrdokumen.hr_dokumen_id = null;
            this.hrdokumen.karyawan_id = null;
            this.hrdokumen.nama_dokumen = null;
            this.hrdokumen.hubungan = null;
            this.hrdokumen.tempat_lahir = null;
            this.hrdokumen.tanggal_lahir = null;
            this.hrdokumen.pendidikan = null;
            this.hrdokumen.jns_kelamin = null;
            this.hrdokumen.catatan = null;
            this.hrdokumen.is_aktif = null;
        },

        fillDokumen(data){
            this.hrdokumen.client_id = null,
            this.hrdokumen.hr_dokumen_id = data.hr_dokumen_id;
            this.hrdokumen.karyawan_id = data.karyawan_id;
            this.hrdokumen.nama_dokumen = data.nama_dokumen;
            this.hrdokumen.hubungan = data.hubungan;
            this.hrdokumen.tempat_lahir = data.tempat_lahir;
            this.hrdokumen.tanggal_lahir = data.tanggal_lahir;
            this.hrdokumen.pendidikan = data.pendidikan;
            this.hrdokumen.jns_kelamin = data.jns_kelamin;
            this.hrdokumen.catatan = data.catatan;
            this.hrdokumen.is_aktif = data.is_aktif;
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

