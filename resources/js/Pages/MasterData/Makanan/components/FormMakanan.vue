<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formMakanan" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitMakanan" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA MAKANAN</h5>
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
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Makanan*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="makanan.nama_makanan" type="text" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <search-list
                                :config = configSelect
                                :dataLists = groupMealsLists.data
                                label = "Kelompok Makanan"
                                placeholder = "Kelompok Makanan"
                                captionField = "kelompok"
                                valueField = "kelompok"
                                :detailItems = mealsDesc
                                :value = "makanan.kelompok"
                                v-on:item-select = "mealsSelected"
                            ></search-list>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Makanan*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="makanan.jns_makanan" type="text" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea" v-model="makanan.catatan" type="text" required>{{makanan.catatan}}</textarea>
                            </div>
                        </div>   
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="makanan.is_aktif" style="margin-left:5px;"> Data kelompok tindakan aktif</label>
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
    name : 'form-makanan', 
    components : { SearchList },
    data() {
        return {
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            mealsDesc : [
                { colName : 'kelompok', labelData : '', isTitle : true },
                { colName : 'kelompok_makanan_id', labelData : '', isTitle : false },
            ],

            isUpdate : true,
            makanan : {
                client_id:null,
                makanan_id:null,
                nama_makanan : null,
                catatan:null,
                kelompok_makanan_id:null,
                kelompok:null,
                jns_makanan:null,
                is_aktif : true,
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kelompokMakanan',['groupMealsLists']),
        
    },

    mounted() {
        this.init();
    },
    
    methods : {
        ...mapActions('makanan',['createMakanan','dataMakanan','updateMakanan']),
        ...mapActions('kelompokMakanan',['listKelompokMakanan']),
        ...mapMutations(['CLR_ERRORS']),

        init() {
            this.listKelompokMakanan();
        },

        mealsSelected(data){
            if(data) {
                this.makanan.kelompok = data.kelompok;
                this.makanan.kelompok_makanan_id = data.kelompok_makanan_id;                
            }
        },  
        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formMakanan').hide();
            return false;
        },

        editMakanan(id){
            this.clearMakanan();            
            this.dataMakanan(id).then((response)=>{
                if (response.success == true) {
                    this.fillMakanan(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formMakanan').show();
                } else {
                    alert(response.message);
                }
            })
        },

        submitMakanan(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createMakanan(this.makanan).then((response) => {
                    if (response.success == true) {
                        alert(" data makanan baru berhasil dibuat.");
                        this.clearMakanan();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }   
            else {
                this.updateMakanan(this.makanan).then((response) => {
                    if (response.success == true) {
                        alert(" data makanan berhasil diubah.");
                        this.clearMakanan();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModal();
                    }
                })
            }         
        },

        newMakanan(){
            this.clearMakanan();
            this.isUpdate = false;
            UIkit.modal('#formMakanan').show();
        },  

        clearMakanan(){
            this.makanan.client_id = null;
            this.makanan.makanan_id = null;
            this.makanan.makanan = null;
            this.makanan.kelompok_makanan_id = null;
            this.makanan.kelompok = null;
            this.makanan.jns_makanan = null;
            this.makanan.catatan = null;
            this.makanan.is_aktif = true;
        },

        fillMakanan(data){
            this.makanan.client_id = null;
            this.makanan.makanan_id = data.makanan_id;
            this.makanan.nama_makanan = data.nama_makanan;
            this.makanan.kelompok = data.kelompok;
            this.makanan.jns_makanan = data.jns_makanan;
            this.makanan.catatan = data.catatan;
            this.makanan.is_aktif = data.is_aktif;
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
    border-radius:50px;
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

.hims-accordion-title {
    border-bottom:1px solid #cc0202; 
    font-size:14px; 
    font-weight:500; 
    background-color:#cc0202; 
    color:white; 
    padding:0.5em 1em 0.5em 1em;
    border-radius:5px;
}

.hims-accordion-title:hover {
    color:white; 
}
.hims-accordion-title::before {
    color:white;
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