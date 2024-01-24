<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formDtd" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitDtd" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DTD DIAGNOSA</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
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
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No DTD*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="dtd.no_dtd" type="text" required :disabled="isUpdate" placeholder="no dtd">
                            </div>
                        </div>
                        <div class="uk-width-2-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama DTD</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="dtd.nama_dtd" type="text" placeholder="nama dtd">
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Label DTD</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="dtd.label_dtd" type="text" placeholder="label DTD"></textarea>
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
    name : 'form-dtd-diagnosa', 
    data() {
        return {
            isUpdate : true,
            dtd : {
                client_id:null,
                no_dtd:null,
                nama_dtd : null,
                label_dtd:null,
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
        ...mapActions('dtd',['createDtd','updateDtd','dataDtd']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formDtd').hide();
            return false;
        },

        submitDtd(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createDtd(this.dtd).then((response) => {
                    if (response.success == true) {
                        alert("DTD baru berhasil dibuat.");
                        this.clearDtd();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }
            else {
                this.updateDtd(this.dtd).then((response) => {
                    if (response.success == true) {
                        this.fillDtd(response.data);
                        alert("Dtd berhasil diubah.");
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                    }
                })
            }            
        },

        newDtd(){
            this.clearDtd();
            this.isUpdate = false;
            UIkit.modal('#formDtd').show();
        },

        editDtd(dtdId){
            this.clearDtd();            
            this.dataDtd(dtdId).then((response)=>{
                if (response.success == true) {
                    this.fillDtd(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formDtd').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        clearDtd(){
            this.dtd.client_id = null;
            this.dtd.no_dtd = null;
            this.dtd.nama_dtd = null;
            this.dtd.label_dtd = '';
            this.dtd.is_aktif = true;
        },

        fillDtd(data){
            this.dtd.client_id = null;
            this.dtd.no_dtd = data.no_dtd;
            this.dtd.nama_dtd = data.nama_dtd;
            this.dtd.label_dtd = data.label_dtd;
            this.dtd.is_aktif = data.is_aktif;
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