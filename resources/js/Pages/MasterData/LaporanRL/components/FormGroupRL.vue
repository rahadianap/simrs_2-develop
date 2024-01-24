<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formGroupRL" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitGroupRL" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">KELOMPOK RL</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModalGroupRL" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>                
                        
                    <div class="uk-width-1-1@s uk-grid-small" uk-grid style="padding:0.5em;">      
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kode RL*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="groupRL.kode_rl" type="text" required>
                            </div>
                        </div>

                        <div class="uk-width-2-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="groupRL.nama_laporan" type="text" required>
                            </div>
                        </div>

                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No Urut*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="groupRL.no_urut" type="text" required>
                            </div>
                        </div>

                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="groupRL.is_aktif" style="margin-left:5px;"> Kode RL aktif</label>
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
    name : 'form-group-rl', 
    data() {
        return {
            isUpdate : true,
            groupRL : {
                client_id:null,
                rl_id:null,
                rl_kode : null,
                nama_laporan:null,
                no_urut : null,
                is_aktif : true,
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
    },
    
    methods : {
        ...mapActions('kodeRL',['createGroupRL','dataGroupRL','updateGroupRL']),
        ...mapMutations(['CLR_ERRORS']),

        closeModalGroupRL(){
            this.$emit('closed',true);
            UIkit.modal('#formGroupRL').hide();
        },

        submitGroupRL(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createGroupRL(this.groupRL).then((response) => {
                    if (response.success == true) {
                        alert("Kode RL baru berhasil dibuat.");
                        this.clearGroupRL();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }    
            else {
                this.updateGroupRL(this.groupRL).then((response) => {
                    if (response.success == true) {
                        alert("Kode RL berhasil diubah.");
                        this.clearGroupRL();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModalGroupRL();
                    }
                })
            }         
        },

        newGroupRL(){
            this.clearGroupRL();
            this.isUpdate = false;
            UIkit.modal('#formGroupRL').show();
        },  

        editGroupRL(id){
            this.clearGroupRL();            
            this.dataGroupRL(id).then((response)=>{
                if (response.success == true) {
                    this.fillGroupRL(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formGroupRL').show();
                } else {
                    alert(response.message);
                }
            })
        },   

        clearGroupRL(){
            this.groupRL.client_id = null;
            this.groupRL.rl_id = null;
            this.groupRL.kode_rl = null;
            this.groupRL.nama_laporan = null;
            this.groupRL.is_aktif = true; 
            this.groupRL.no_urut = null;
        },

        fillGroupRL(data){
            this.groupRL.client_id = null;
            this.groupRL.rl_id = data.rl_id;
            this.groupRL.kode_rl = data.kode_rl;
            this.groupRL.nama_laporan = data.nama_laporan;
            this.groupRL.no_urut = data.no_urut;
            this.groupRL.is_aktif = data.is_aktif;
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