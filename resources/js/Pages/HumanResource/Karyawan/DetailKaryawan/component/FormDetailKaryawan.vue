<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formDetailKaryawan" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body uk-border-rounded">
            <div class="hims-form-container" style="min-height:50vh; background-color:#fff;">
                <form action="" @submit.prevent="submitJabatan" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA JABATAN KARYAWAN</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeJabatanModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
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
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jabatan*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="hrjabatan.jabatan_nama" type="text" placeholder="jabatan" required>
                                    </div>
                                </div>
                                
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Unit*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="hrjabatan.unit_nama" type="text" placeholder="unit" required>
                                    </div>
                                </div>
                                
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Mulai*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="hrjabatan.tgl_mulai" type="date" placeholder="tanggal mulai" required>
                                    </div>
                                </div>

                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Selesai*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="hrjabatan.tgl_selesai" type="date" placeholder="tanggal selesai" required>
                                    </div>
                                </div>

                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Keterangan*</label>
                                    <div class="uk-form-controls">
                                        <input class="textarea uk-form-small" v-model="hrjabatan.keterangan" type="text" placeholder="keterangan" required>
                                    </div>
                                </div>

                                <div class="uk-width-1-1@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;font-weight: 400;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="jabatan.is_aktif" > Data Aktif
                                        </label>
                                        <span style="font-size:12px; color:black; padding:0.2em 0.2em 0.2em 0.4em; margin:0; font-style: italic;">data jabatan aktif.</span>                                        
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
    name : 'form-detail-karyawan', 
            
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
            hrjabatan : {
                client_id : null,
                hr_jabatan_id : null,
                karyawan_id : null,
                jabatan_id : null,
                jabatan_nama : null,
                unit_id : null,
                unit_nama : null,
                tgl_mulai : null,
                tgl_selesai : null,
                is_selesai : null,
                is_aktif : true,          
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('referensi',['','isRefExist']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('hrjabatan',['createJabatan','updateJabatan','dataJabatan']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            if(!this.isRefExist) { this.listReferensi(); };
        },

        closeJabatanModal(){
            this.$emit('formJabatanClosed',true);
            UIkit.modal('#formJabatan').hide();
            return false;
        },

        submitJabatan(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createJabatan(this.hrjabatan).then((response) => {
                    if (response.success == true) {
                        alert("Jabatan baru berhasil dibuat.");
                        this.fillJabatan(response.data);
                        this.isUpdate = true;
                        this.closeJabatanModal();
                    }
                })
            }
            else {
                this.updateJabatan(this.hrjabatan).then((response) => {
                    if (response.success == true) {
                        this.fillJabatan(response.data);
                        alert("Jabatan berhasil diubah.");
                        this.isUpdate = true;
                        this.closeJabatanModal();
                    }
                })
            }            
        },

        newJabatan(){
            this.clearJabatan();
            this.isUpdate = false;
            UIkit.modal('#formJabatan').show();
        },

        editJabatan(hr_jabatan_id){
            this.clearJabatan();            
            this.dataJabatan(hr_jabatan_id).then((response)=>{
                if (response.success == true) {
                    this.fillJabatan(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formJabatan').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        clearJabatan(){
            this.hrjabatan.client_id = null;
            this.hrjabatan.hr_jabatan_id = null;
            this.hrjabatan.karyawan_id = null;
            this.hrjabatan.jabatan_id = null;
            this.hrjabatan.jabatan_nama = null;
            this.hrjabatan.unit_id = null;
            this.hrjabatan.unit_nama = null;
            this.hrjabatan.tgl_mulai = null;
            this.hrjabatan.tgl_selesai = null;
            this.hrjabatan.is_selesai = null;
            this.hrjabatan.is_aktif = null;
        },

        fillJabatan(data){
            this.hrjabatan.client_id = null,
            this.hrjabatan.hr_jabatan_id = data.hr_jabatan_id;
            this.hrjabatan.karyawan_id = data.karyawan_id;
            this.hrjabatan.jabatan_id = data.jabatan_id;
            this.hrjabatan.jabatan_nama = data.jabatan_nama;
            this.hrjabatan.unit_id = data.unit_id;
            this.hrjabatan.unit_nama = data.unit_nama;
            this.hrjabatan.tgl_mulai = data.tgl_mulai;
            this.hrjabatan.tgl_selesai = data.tgl_selesai;
            this.hrjabatan.is_selesai = data.is_selesai;
            this.hrjabatan.is_aktif = data.is_aktif;
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