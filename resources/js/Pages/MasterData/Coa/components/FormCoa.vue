<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formCoa" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitCoa" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">COA - Code Of Accounts</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>  
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-expand@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Level Akun</label>
                            <div class="uk-form-controls">
                                <select v-model="coa.level_coa" class="uk-select uk-form-small" @change="getHeaderAkun" :disabled="isUpdate">
                                    <option v-for="option in levelcoa" v-bind:value="option.id" v-bind:key="option.id">{{option.text}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-2-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Akun Header</label>
                            <div class="uk-form-controls">
                                <select v-model="coa.coa_header" @change="coaHeaderSelected" class="uk-select uk-form-small" :required="!isCoaGroup" :disabled="isCoaGroup || isUpdate">
                                    <option v-for="option in listHeader" :selected="option.kode_coa == coa.coa_header" v-bind:value="option.kode_coa" v-bind:key="option.coa_id">{{option.kode_coa}} - {{option.nama_coa}}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kode COA*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="coa.kode_coa" type="text" placeholder="kode coa" required :disabled="isUpdate">
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Akun</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="coa.nama_coa" type="text" placeholder="nama akun coa">{{coa.nama_coa}}</textarea>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nilai Normal</label>
                            <div class="uk-form-controls">
                                <select v-model="coa.nilai_normal" class="uk-select uk-form-small" required>
                                    <option value="D">DEBET</option>
                                    <option value="K">KREDIT</option>
                                </select>
                            </div>
                        </div> 
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;font-weight: 500;"><input class="uk-checkbox" type="checkbox" v-model="coa.is_valid_coa" style="margin-left:5px;"> Akun COA Valid.</label>
                            <p style="font-size:11px; color:black; padding:0.2em 0.2em 0.2em 2em; margin:0; font-style: italic;">Akun COA valid untuk pemetaan transaksi.</p>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;font-weight: 500;"><input class="uk-checkbox" type="checkbox" v-model="coa.is_aktif" style="margin-left:5px;"> Akun COA Aktif.</label>
                            <p style="font-size:11px; color:black; padding:0.2em 0.2em 0.2em 2em; margin:0; font-style: italic;">Akun COA aktif dalam sistem.</p>
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
    name : 'form-coa', 
    data() {
        return {
            isUpdate : true,
            isCoaGroup : true,
            listHeader :[],
            coa : {
                client_id :null,
                coa_id :null,
                kode_coa :null,
                nama_coa : null,
                coa_header : null,
                coa_header_id : null,
                level_coa : null,
                level_nama : null,
                nilai_normal : null,
                is_valid_coa : true,
                is_aktif : true,
            },

            levelcoa : [
                { id:'0', text:'Level 00' },
                { id:'1', text:'Level 01' },
                { id:'2', text:'Level 02' },
                { id:'3', text:'Level 03' },
                { id:'4', text:'Level 04' },
                { id:'5', text:'Level 05' },
            ]
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('coa',['coaHeaderLists']),
    },

    mounted() {
    },
    
    methods : {
        ...mapActions('coa',['createCoa','updateCoa','dataCoa','listCoa','listCoaHeader']),
        ...mapMutations(['CLR_ERRORS']),

        getHeaderAkun(){
            if(this.coa.level_coa == 0) {
                this.isCoaGroup = true;
                this.listHeader = [];
            } 
            else {
                let headerIndex = (this.coa.level_coa * 1) - 1;
                this.isCoaGroup = false;
                this.retrieveListCoaHeader(headerIndex);
            }
        },

        retrieveListCoaHeader(level){
            this.CLR_ERRORS();
            this.listHeader = [];
            this.listCoaHeader(level).then((response) => {
                if (response.success == true) {
                    this.listHeader = response.data;
                }
            })
        },

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formCoa').hide();
        },

        submitCoa(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createCoa(this.coa).then((response) => {
                    if (response.success == true) {
                        alert("COA baru berhasil dibuat.");
                        this.clearCoaNext();
                        this.isUpdate = false;
                        this.closeModal();
                    }
                })
            }
            else {
                this.updateCoa(this.coa).then((response) => {
                    if (response.success == true) {
                        this.fillCoa(response.data);
                        alert("COA berhasil diubah.");
                        this.isUpdate = true;
                        this.closeModal();
                    }
                })
            }            
        },

        newCoa(data){
            this.clearCoa();
            this.coa.level_coa = (data.level_coa * 1) + 1;
            this.getHeaderAkun();
            this.coa.coa_header_id = data.coa_id;
            this.coa.coa_header = data.kode_coa;
            let level = this.levelcoa.filter(item => item.id == this.coa.level_coa);
            this.coa.level_nama = level[0].text;
            this.isUpdate = false;
            this.isCoaGroup = false;
            UIkit.modal('#formCoa').show();
        },

        newGroupCoa(){
            this.clearCoa();
            this.coa.level_coa = 0;
            this.coa.level_nama = this.levelcoa[0].text;
            this.isUpdate = false;
            this.isCoaGroup = true;
            UIkit.modal('#formCoa').show();
        },

        editCoa(id){
            this.clearCoa();            
            this.dataCoa(id).then((response)=>{
                if (response.success == true) {
                    this.fillCoa(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formCoa').show();
                } else {
                    alert(response.message);
                }
            })
        }, 

        clearCoa(){
            this.coa.coa_id = null;
            this.coa.client_id = null;
            this.coa.kode_coa = null;
            this.coa.nama_coa = null;
            this.coa.coa_header = null;
            this.coa.coa_header_id = null;
            this.coa.level_coa = null;
            this.coa.is_valid_coa = true;
            this.coa.level_nama = null;
            this.coa.nilai_normal = null;
            this.coa.is_aktif = true;
        },

        clearCoaNext(){
            this.coa.coa_id = null;
            this.coa.kode_coa = null;
            this.coa.nama_coa = null;
            this.coa.is_valid_coa = true;
            this.coa.nilai_normal = null;
            this.coa.is_aktif = true;
        },

        fillCoa(data){
            this.isCoaGroup = false;
            this.coa.client_id = null;
            this.coa.coa_id = data.coa_id;
            this.coa.kode_coa = data.kode_coa;
            this.coa.nama_coa = data.nama_coa;
            this.coa.coa_header = data.coa_header;
            this.coa.coa_header_id = data.coa_header_id;
            this.coa.level_coa = data.level_coa;
            this.coa.is_valid_coa = data.is_valid_coa;
            this.coa.level_nama = data.level_nama;
            this.coa.nilai_normal = data.nilai_normal;
            this.coa.is_aktif = data.is_aktif;
            if(data.level_coa == 0) {
                this.isCoaGroup = true;
            }
            let level = this.levelcoa.filter(item => item.id == this.coa.level_coa);
            this.coa.level_nama = level[0].text;            
            this.getHeaderAkun();
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