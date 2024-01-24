<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formKelas" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitKelas" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">KELAS PELAYANAN</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModalKelas" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>                
                        
                    <div class="uk-width-1-1@s uk-grid-small" uk-grid style="padding:0.5em;">                                 
                        <div class="uk-width-3-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Kelas*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="kelas.kelas_nama" type="text" required>
                            </div>
                        </div>
                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Inisial</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="kelas.inisial" type="text">
                            </div>
                        </div>                        
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kode RL</label>
                            <div class="uk-form-controls">
                                <select v-model="kelas.rl_kode" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                    <option v-for="option in kelasRL" v-bind:value="option.value" v-bind:key="option.value">{{option.text}}</option>
                                </select>                                
                            </div>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="kelas.is_kelas_harga" style="margin-left:5px;"> Kelas harga</label>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="kelas.is_kelas_kamar" style="margin-left:5px;"> Kelas kamar rawat inap</label>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="kelas.is_aktif" style="margin-left:5px;"> Kelas pelayanan aktif</label>
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
    name : 'form-kelas', 
    data() {
        return {
            isUpdate : true,
            kelas : {
                client_id : null,
                kelas_id : null,
                kelas_nama : null,
                rl_kode : null,
                is_kelas_kamar : true,
                is_kelas_harga : true,
                inisial : null,
                is_aktif : true,
            },
            kelasRL : [
                { value : 'VVIP', text : 'Kelas VVIP' },
                { value : 'VIP', text : 'Kelas VIP' },
                { value : 'I', text : 'Kelas I' },
                { value : 'II', text : 'Kelas II' },
                { value : 'III', text : 'Kelas III' },
                { value : 'KHUSUS', text : 'Kelas Khusus' },
                { value : 'NON KELAS', text : '' },
                
            ]
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kodeRL',['rlGroupLists','rlCodeLists']),
    },

    mounted() {
    },
    
    methods : {
        ...mapActions('kelas',['createKelas','dataKelas','updateKelas']),
        ...mapActions('kodeRL',['listGroupRL','listKodeRL']),
        ...mapMutations(['CLR_ERRORS']),
        
        closeModalKelas(){
            this.$emit('closed',true);
            UIkit.modal('#formKelas').hide();
        },

        submitKelas(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createKelas(this.kelas).then((response) => {
                    if (response.success == true) {
                        alert("Kelas pelayanan baru berhasil dibuat.");
                        this.clearKelas();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }    
            else {
                this.updateKelas(this.kelas).then((response) => {
                    if (response.success == true) {
                        alert("Kelas pelayanan berhasil diubah.");
                        this.clearKelas();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModalKelas();
                    }
                })
            }         
        },  

        newKelas(){
            this.clearKelas();
            this.isUpdate = false;
            UIkit.modal('#formKelas').show();
        },  

        editKelas(id){
            this.clearKelas();            
            this.dataKelas(id).then((response)=>{
                if (response.success == true) {
                    this.fillKelas(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formKelas').show();
                } else {
                    alert(response.message);
                }
            })
        },   

        clearKelas(){
            this.kelas.client_id = null;
            this.kelas.kelas_id = null;
            this.kelas.kelas_nama = null;
            this.kelas.rl_kode = null;
            this.kelas.inisial = null;
            this.kelas.is_kelas_kamar = true;
            this.kelas.is_kelas_harga = true;
            this.kelas.is_aktif = true;
        },

        fillKelas(data){
            this.kelas.client_id = null;
            this.kelas.kelas_id = data.kelas_id;
            this.kelas.kelas_nama = data.kelas_nama;
            this.kelas.rl_kode = data.rl_kode;
            this.kelas.inisial = data.inisial;
            this.kelas.is_kelas_kamar = data.is_kelas_kamar;
            this.kelas.is_kelas_harga = data.is_kelas_harga;
            this.kelas.is_aktif = data.is_aktif;
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