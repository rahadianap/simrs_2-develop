<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formKodeRL" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitKodeRL" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">KODE LAPORAN RL</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModalKodeRL" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>                
                        
                    <div class="uk-width-1-1@s uk-grid-small" uk-grid style="padding:0.5em;">                                 
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Group RL*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="kode_header" type="text" disabled>
                            </div>
                        </div>

                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kode*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="kodeRL.kode_rl" type="text" required>
                            </div>
                        </div>

                        <div class="uk-width-3-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="kodeRL.nama_laporan" type="text" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No Urut*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="kodeRL.no_urut" type="text" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="kodeRL.is_valid_rl" style="margin-left:5px;"> Kode RL valid (bisa dipilih).</label>
                        </div>

                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="kodeRL.is_aktif" style="margin-left:5px;"> Kode RL aktif</label>
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
    name : 'form-kode-rl', 
    data() {
        return {
            groupData : null,
            kode_header : '',
            isUpdate : true,
            kodeRL : {
                client_id:null,
                rl_id:null,
                rl_kode : null,
                nama_laporan:null,
                header_rl : null,
                is_valid_rl : true,
                no_urut : null,
                is_group : false,
                rl_level : null,
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
        ...mapActions('kodeRL',['createKodeRL','dataKodeRL','updateKodeRL']),
        ...mapMutations(['CLR_ERRORS']),

        closeModalKodeRL(){
            this.$emit('closed',true);
            UIkit.modal('#formKodeRL').hide();
        },

        submitKodeRL(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createKodeRL(this.kodeRL).then((response) => {
                    if (response.success == true) {
                        alert("Kode RL baru berhasil dibuat.");
                        this.clearKodeRL();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }    
            else {
                this.updateKodeRL(this.kodeRL).then((response) => {
                    if (response.success == true) {
                        alert("Kode RL berhasil diubah.");
                        this.clearKodeRL();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModalKodeRL();
                    }
                })
            }         
        },

        newKodeRL(data){
            this.groupData = data;
            this.clearKodeRL();
            this.isUpdate = false;
            this.fillHeaderRL(this.groupData);
            UIkit.modal('#formKodeRL').show();
        },

        editKodeRL(dataGroup,id){
            this.groupData = dataGroup;
            this.clearKodeRL();            
            this.dataKodeRL(id).then((response)=>{
                if (response.success == true) {
                    this.fillKodeRL(response.data);
                    this.kode_header = `${dataGroup.kode_rl} - ${dataGroup.nama_laporan}`;
                    this.isUpdate = true;
                    UIkit.modal('#formKodeRL').show();
                } else {
                    alert(response.message);
                }
            })
        },   
        
        fillHeaderRL(data) {
            this.kodeRL.is_group = false;
            this.kodeRL.header_rl = data.kode_rl;
            this.kodeRL.header_id = data.rl_id;
            this.kodeRL.group_rl = data.group_rl;
            this.kodeRL.group_id = data.group_id;
            this.kodeRL.rl_level = (data.rl_level * 1) + 1;
            this.kode_header = `${data.kode_rl} - ${data.nama_laporan}`;
        },

        clearKodeRL(){
            this.kodeRL.client_id = null;
            this.kodeRL.rl_id = null;
            this.kodeRL.kode_rl = null;
            this.kodeRL.nama_laporan = null;
            this.kodeRL.no_urut = null;
            this.kodeRL.is_valid_rl = true;
            this.kodeRL.is_aktif = true;              
        },

        fillKodeRL(data){
            this.kodeRL.client_id = null;
            this.kodeRL.rl_id = data.rl_id;
            this.kodeRL.kode_rl = data.kode_rl;
            this.kodeRL.nama_laporan = data.nama_laporan;
            this.kodeRL.header_rl = data.header_rl;
            this.kodeRL.header_id = data.header_id;
            this.kodeRL.group_rl = data.group_rl;
            this.kodeRL.group_id = data.group_id;
            this.kodeRL.is_group = data.is_group;
            this.kodeRL.rl_level = data.rl_level;
            this.kodeRL.is_valid_rl = data.is_valid_rl;
            this.kodeRL.no_urut = data.no_urut;
            this.kodeRL.is_aktif = data.is_aktif;
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