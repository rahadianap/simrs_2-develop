<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formMenu" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitMenu" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA MENU</h5>
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
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Menu*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="menu.nama_menu" type="text" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <search-list
                                :config = configSelect
                                :dataLists = groupMenuLists.data
                                label = "Kelompok Menu"
                                placeholder = "Kelompok Menu"
                                captionField = "kelompok"
                                valueField = "kelompok"
                                :detailItems = menuDesc
                                :value = "menu.kelompok"
                                v-on:item-select = "menuSelected"
                            ></search-list>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea" v-model="menu.catatan" type="text" required>{{menu.catatan}}</textarea>
                            </div>
                        </div>   
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="menu.is_aktif" style="margin-left:5px;"> Data menu aktif</label>
                        </div> 
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="menu.is_menu_khusus" style="margin-left:5px;"> Data menu khusus</label>
                        </div> 
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="menu.is_jual" style="margin-left:5px;"> Data menu untuk dijual</label>
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
    name : 'form-menu', 
    components : { SearchList },
    data() {
        return {
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            menuDesc : [
                { colName : 'kelompok', labelData : '', isTitle : true },
                { colName : 'kelompok_menu_id', labelData : '', isTitle : false },
            ],

            isUpdate : true,
            menu : {
                client_id:null,
                menu_id:null,
                nama_menu : null,
                kelompok_menu_id:null,
                kelompok: null,
                catatan:null,
                is_menu_khusus:null,
                is_jual:null,
                is_aktif : true,
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('kelompokMenu',['groupMenuLists']),
        
    },

    mounted() {
        this.init();
    },
    
    methods : {
        ...mapActions('menu',['createMenu','dataMenu','updateMenu']),
        ...mapActions('kelompokMenu',['listKelompokMenu']),
        ...mapMutations(['CLR_ERRORS']),

        init() {
            this.listKelompokMenu();
        },

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formMenu').hide();
            return false;
        },

        menuSelected(data){
            // console.log(data);
            if(data) {
                this.menu.kelompok = data.kelompok;
                this.menu.kelompok_menu_id = data.kelompok_menu_id;                
            }
        },

        editMenu(id){
            this.clearMenu();            
            this.dataMenu(id).then((response)=>{
                if (response.success == true) {
                    this.fillMenu(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formMenu').show();
                } else {
                    alert(response.message);
                }
            })
        },

        submitMenu(){
            this.CLR_ERRORS();
            // console.log(this.menu);
            if(!this.isUpdate) {
                this.createMenu(this.menu).then((response) => {
                    if (response.success == true) {
                        alert(" Data menu berhasil dibuat.");
                        this.clearMenu();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }   
            else {
                this.updateMenu(this.menu).then((response) => {
                    if (response.success == true) {
                        alert(" Data menu berhasil diubah.");
                        this.clearMenu();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModal();
                    }
                })
            }         
        },

        newMenu(){
            this.clearMenu();
            this.isUpdate = false;
            UIkit.modal('#formMenu').show();
        },  

        clearMenu(){
            this.menu.client_id = null;
            this.menu.menu_id = null;
            this.menu.nama_menu = null;
            this.menu.kelompok_menu_id = null;
            this.menu.kelompok = null;
            this.menu.catatan = null;
            this.menu.is_aktif = true;
        },

        fillMenu(data){
            this.menu.client_id = null;
            this.menu.menu_id = data.menu_id;
            this.menu.nama_menu = data.nama_menu;
            this.menu.kelompok = data.kelompok;
            this.menu.catatan = data.catatan;
            this.menu.is_aktif = data.is_aktif;
            this.menu.is_menu_khusus = data.is_menu_khusus;
            this.menu.is_jual = data.is_jual;
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