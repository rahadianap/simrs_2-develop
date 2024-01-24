<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formPendidikan" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body uk-border-rounded">
            <div class="hims-form-container" style="min-height:50vh; background-color:#fff;">
                <form action="" @submit.prevent="submitPendidikan" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA PENDIDIKAN KARYAWAN</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closePendidikanModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
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
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Pendidikan*</label>
                                    <div class="uk-form-controls">
                                        <select v-model="hrpendidikan.jns_pendidikan" class="uk-select uk-form-small" required>
                                            <option value="F">Formal</option>
                                            <option value="NF">Non Formal</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenjang*</label>
                                    <div class="uk-form-controls">
                                        <select v-model="hrpendidikan.jenjang" class="uk-select uk-form-small">
                                            <option v-if="isRefExist" v-for="dt in pendidikanRefs.json_data" :value="dt.value">{{dt.text}}</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pendidikan*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="hrpendidikan.nama_pendidikan" type="text" placeholder="nama pendidikan" required>
                                    </div>
                                </div>

                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Institusi*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="hrpendidikan.institusi" type="text" placeholder="institusi" required>
                                    </div>
                                </div>

                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Lulus*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="hrpendidikan.tgl_lulus" type="date" placeholder="tanggal lulus" required>
                                    </div>
                                </div>

                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">IPK/Nilai*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="hrpendidikan.ipk" type="text" placeholder="indeks prestasi kumulatif" required>
                                    </div>
                                </div>

                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" v-model="hrpendidikan.catatan" type="text" placeholder="catatan"></textarea>
                                    </div>
                                </div>

                                <div class="uk-width-1-1@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                                    <div class="uk-form-controls">
                                        <label style="color:black; font-size:14px;font-weight: 400;">
                                            <input class="uk-checkbox" type="checkbox" value="1" v-model="hrpendidikan.is_aktif" >
                                        </label>
                                        <span style="font-size:12px; color:black; padding:0.2em 0.2em 0.2em 0.4em; margin:0;">Data pendidikan aktif.</span>                                        
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
    name : 'form-pendidikan', 
            
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
            hrpendidikan : {
                client_id:null,
                hr_pendidikan_id:null,
                karyawan_id : null,
                jns_pendidikan : null,
                jenjang : null,
                nama_pendidikan : null,
                institusi : null,
                tahun_lulus : null,
                ipk : null,
                catatan : null,
                is_aktif : true,          
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('referensi',['pendidikanRefs','isRefExist']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('hrpendidikan',['createPendidikan','updatePendidikan','deletePendidikan']),
        ...mapActions('referensi',['listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            if(!this.isRefExist) { this.listReferensi(); };
        },

        closePendidikanModal(){
            this.$emit('formPendidikanClosed',true);
            UIkit.modal('#formPendidikan').hide();
            return false;
        },

        submitPendidikan(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createPendidikan(this.hrpendidikan).then((response) => {
                    if (response.success == true) {
                        alert("Pendidikan baru berhasil dibuat.");
                        this.fillPendidikan(response.data);
                        this.isUpdate = true;
                        this.closePendidikanModal();
                    }
                })
            }
            else {
                this.updatePendidikan(this.hrpendidikan).then((response) => {
                    if (response.success == true) {
                        this.fillPendidikan(response.data);
                        alert("Pendidikan berhasil diubah.");
                        this.isUpdate = true;
                        this.closePendidikanModal();
                    }
                })
            }            
        },

        newPendidikan(){
            this.clearPendidikan();
            this.isUpdate = false;
            UIkit.modal('#formPendidikan').show();
        },

        editPendidikan(hr_pendidikan_id){
            this.clearPendidikan();            
            this.dataPendidikan(hr_pendidikan_id).then((response)=>{
                if (response.success == true) {
                    this.fillPendidikan(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formPendidikan').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        clearPendidikan(){
            this.hrpendidikan.client_id = null;
            this.hrpendidikan.hr_pendidikan_id = null;
            this.hrpendidikan.karyawan_id = null;
            this.hrpendidikan.jns_pendidikan = null;
            this.hrpendidikan.jenjang = null;
            this.hrpendidikan.nama_pendidikan = null;
            this.hrpendidikan.institusi = null;
            this.hrpendidikan.tahun_lulus = null;
            this.hrpendidikan.ipk = null;
            this.hrpendidikan.catatan = null;
            this.hrpendidikan.is_aktif = true; 
        },

        fillPendidikan(data){
            this.hrpendidikan.client_id = null;
            this.hrpendidikan.hr_pendidikan_id = data.hr_pendidikan_id;
            this.hrpendidikan.karyawan_id = data.karyawan_id;
            this.hrpendidikan.jns_pendidikan = data.jns_pendidikan;
            this.hrpendidikan.jenjang = data.jenjang;
            this.hrpendidikan.nama_pendidikan = data.nama_pendidikan;
            this.hrpendidikan.institusi = data.institusi;
            this.hrpendidikan.tahun_lulus = data.tahun_lulus;
            this.hrpendidikan.ipk = data.ipk;
            this.hrpendidikan.catatan = data.catatan;
            this.hrpendidikan.is_aktif = data.is_aktif;
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