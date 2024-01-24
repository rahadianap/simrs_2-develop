<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formKelompokTindakan" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitKelompokTindakan" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA KELOMPOK TINDAKAN</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModalKelompokTindakan" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
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
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kelompok Tindakan*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="kelompokTindakan.kelompok_tindakan" type="text" required>
                            </div>
                        </div>
                        <!-- <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis tindakan/pemeriksaan*</label>
                            <div class="uk-form-controls">
                                <select class="uk-select" v-model="kelompokTindakan.jenis_tindakan">
                                    <option value="TINDAKAN">TINDAKAN MEDIS</option>
                                    <option value="BEDAH">TINDAKAN OPERASI</option>
                                    <option value="RAD">RADIOLOGI</option>
                                    <option value="LAB">LABORATORIUM</option>
                                    <option value="PENUNJANG">PENUNJANG LAIN</option>
                                    <option value="LAIN">LAIN-LAIN</option>
                                </select>
                            </div>
                        </div> -->

                        <!-- <div class="uk-width-1-1@m">
                            <search-list
                                :config = configSelect
                                :dataLists = groupBillingLists.data
                                label = "Kelompok Tagihan"
                                placeholder = "Kelompok Tagihan"
                                captionField = "kelompok_tagihan"
                                valueField = "kelompok_tagihan_id"
                                :detailItems = billingDesc
                                :value = "kelompokTindakan.kelompok_tagihan_id"
                                v-on:item-select = "billingSelected"
                            ></search-list>
                        </div> -->
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea" v-model="kelompokTindakan.deskripsi" type="text" required>{{kelompokTindakan.deskripsi}}</textarea>
                            </div>
                        </div>   
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="kelompokTindakan.is_aktif" style="margin-left:5px;"> Data kelompok tindakan aktif</label>
                        </div>                     
                    </div>
                </form>                          
            </div>
        </div>
    </div> 
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'form-kelompok-tindakan', 
    components : { SearchList },
    data() {
        return {
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            billingDesc : [
                { colName : 'kelompok_tagihan', labelData : '', isTitle : true },
                { colName : 'kelompok_tagihan_id', labelData : '', isTitle : false },
            ],

            isUpdate : true,
            kelompokTindakan : {
                client_id:null,
                kelompok_tindakan_id:null,
                kelompok_tindakan : null,
                deskripsi:null,
                kelompok_tagihan_id:null,
                kelompok_tagihan:null,
                is_aktif : true,
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kelompokTagihan',['groupBillingLists']),
        
    },

    mounted() {
        this.init();
    },
    
    methods : {
        ...mapActions('kelompokTindakan',['createKelompokTindakan','dataKelompokTindakan','updateKelompokTindakan']),
        ...mapActions('kelompokTagihan',['listKelompokTagihan']),
        ...mapMutations(['CLR_ERRORS']),

        init() {
            this.listKelompokTagihan();
        },

        billingSelected(data){
            if(data) {
                this.kelompokTindakan.kelompok_tagihan = data.kelompok_tagihan;
                this.kelompokTindakan.kelompok_tagihan_id = data.kelompok_tagihan_id;                
            }
        },  
        closeModalKelompokTindakan(){
            this.$emit('closed',true);
            UIkit.modal('#formKelompokTindakan').hide();
        },

        editKelompokTindakan(id){
            this.clearKelompokTindakan();            
            this.dataKelompokTindakan(id).then((response)=>{
                if (response.success == true) {
                    this.fillKelompokTindakan(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formKelompokTindakan').show();
                } else {
                    alert(response.message);
                }
            })
        },

        submitKelompokTindakan(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createKelompokTindakan(this.kelompokTindakan).then((response) => {
                    if (response.success == true) {
                        alert("Kelompok tindakan baru berhasil dibuat.");
                        this.clearKelompokTindakan();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }   
            else {
                this.updateKelompokTindakan(this.kelompokTindakan).then((response) => {
                    if (response.success == true) {
                        alert("Kelompok tindakan berhasil diubah.");
                        this.clearKelompokTindakan();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModalKelompokTindakan();
                    }
                })
            }         
        },

        newKelompokTindakan(){
            this.clearKelompokTindakan();
            this.isUpdate = false;
            UIkit.modal('#formKelompokTindakan').show();
        },  

        clearKelompokTindakan(){
            this.kelompokTindakan.client_id = null;
            this.kelompokTindakan.kelompok_tindakan_id = null;
            this.kelompokTindakan.kelompok_tindakan = null;
            this.kelompokTindakan.kelompok_tagihan_id = null;
            this.kelompokTindakan.kelompok_tagihan = null;
            this.kelompokTindakan.deskripsi = null;
            this.kelompokTindakan.is_aktif = true;
        },

        fillKelompokTindakan(data){
            this.kelompokTindakan.client_id = null;
            this.kelompokTindakan.kelompok_tindakan_id = data.kelompok_tindakan_id;
            this.kelompokTindakan.kelompok_tindakan = data.kelompok_tindakan;
            this.kelompokTindakan.kelompok_tagihan_id = data.kelompok_tagihan_id;
            this.kelompokTindakan.kelompok_tagihan = data.kelompok_tagihan;
            this.kelompokTindakan.deskripsi = data.deskripsi;
            this.kelompokTindakan.is_aktif = data.is_aktif;
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