<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formKeluarga" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body uk-border-rounded">
            <div class="hims-form-container" style="min-height:50vh; background-color:#fff;">
                <form action="" @submit.prevent="submitKeluarga" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA KELUARGA KARYAWAN</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeKeluargaModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
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
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Anggota Keluarga*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="hrkeluarga.nama_keluarga" type="text" placeholder="nama anggota keluarga" required>
                                    </div>
                                </div>
                                
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Hubungan</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="hrkeluarga.hubungan" type="text" placeholder="hubungan keluarga">
                                    </div>
                                </div>

                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="hrkeluarga.tempat_lahir" type="text" placeholder="tempat lahir">
                                    </div>
                                </div>

                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Lahir</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="hrkeluarga.tanggal_lahir" type="date" placeholder="tanggal lahir">
                                    </div>
                                </div>

                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin</label>
                                    <div class="uk-form-controls">
                                        <select v-model="hrkeluarga.jns_kelamin" class="uk-select uk-form-small" required>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Pendidikan Terakhir</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="hrkeluarga.pendidikan" type="text" placeholder="pendidikan terakhir">
                                    </div>
                                </div>

                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" v-model="hrkeluarga.catatan" type="text" placeholder="catatan"></textarea>
                                    </div>
                                </div>

                                <div class="uk-width-1-1@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;font-weight: 400;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="hrkeluarga.is_aktif" >
                                        </label>
                                        <span style="font-size:12px; color:black; padding:0.2em 0.2em 0.2em 0.4em; margin:0;">Data keluarga aktif.</span>                                        
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
    name : 'form-keluarga', 
            
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
            hrkeluarga : {
                client_id:null,
                hr_keluarga_id:null,
                karyawan_id:null,
                nama_keluarga : null,
                hubungan : null,
                tempat_lahir : null,
                tanggal_lahir : null,
                pendidikan : null,
                jns_kelamin : null,
                catatan : null,
                is_aktif : true,          
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
        // ...mapGetters('referensi',['pendidikanRefs','isRefExist']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('hrkeluarga',['createKeluarga','updateKeluarga','deleteKeluarga']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            if(!this.isRefExist) { this.listReferensi(); };
        },

        closeKeluargaModal(){
            this.$emit('formKeluargaClosed',true);
            UIkit.modal('#formKeluarga').hide();
            return false;
        },

        submitKeluarga(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.hrkeluarga.karyawan_id = this.karyawan.karyawan_id;
                this.createKeluarga(this.hrkeluarga).then((response) => {
                    if (response.success == true) {
                        alert("Anggota keluarga baru berhasil dibuat.");
                        this.fillKeluarga(response.data);
                        this.isUpdate = true;
                        this.closeKeluargaModal();
                    }
                })
            }
            else {
                this.updateKeluarga(this.hrkeluarga).then((response) => {
                    if (response.success == true) {
                        this.fillKeluarga(response.data);
                        alert("Anggota keluarga berhasil diubah.");
                        this.isUpdate = true;
                        this.closeKeluargaModal();
                    }
                })
            }
            console.log(this.hrkeluarga); 
            // this.$emit('dataKeluargaSaved', this.hrkeluarga);           
        },

        newKeluarga(){
            this.clearKeluarga();
            this.isUpdate = false;
            UIkit.modal('#formKeluarga').show();
        },

        editKeluarga(keluargaId){
            this.clearKeluarga();            
            this.dataKeluarga(keluargaId).then((response)=>{
                if (response.success == true) {
                    this.fillKeluarga(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formKeluarga').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        clearKeluarga(){
            this.hrkeluarga.client_id = null;
            this.hrkeluarga.hr_keluarga_id = null;
            this.hrkeluarga.karyawan_id = null;
            this.hrkeluarga.nama_keluarga = null;
            this.hrkeluarga.hubungan = null;
            this.hrkeluarga.tempat_lahir = null;
            this.hrkeluarga.tanggal_lahir = null;
            this.hrkeluarga.pendidikan = null;
            this.hrkeluarga.jns_kelamin = null;
            this.hrkeluarga.catatan = null;
            this.hrkeluarga.is_aktif = null;
        },

        fillKeluarga(data){
            this.hrkeluarga.client_id = null,
            this.hrkeluarga.hr_keluarga_id = data.hr_keluarga_id;
            this.hrkeluarga.karyawan_id = data.karyawan_id;
            this.hrkeluarga.nama_keluarga = data.nama_keluarga;
            this.hrkeluarga.hubungan = data.hubungan;
            this.hrkeluarga.tempat_lahir = data.tempat_lahir;
            this.hrkeluarga.tanggal_lahir = data.tanggal_lahir;
            this.hrkeluarga.pendidikan = data.pendidikan;
            this.hrkeluarga.jns_kelamin = data.jns_kelamin;
            this.hrkeluarga.catatan = data.catatan;
            this.hrkeluarga.is_aktif = data.is_aktif;
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