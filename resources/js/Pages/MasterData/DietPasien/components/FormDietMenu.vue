<template>
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="formDietMenu" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitPR" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">MAPPING DIET MENU</h5>
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
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-1@s" style="padding:0 0.5em 0 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DETAIL MAKANAN
                                <span style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    data detail menu
                                </span>
                            </h5>
                        </div>
                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diet ID</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="menu.diet_id" type="text" style="color:black;" readonly>
                            </div>
                        </div>
                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Diet</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="menu.nama_diet" type="text" style="color:black;" readonly>
                            </div>
                        </div>
                        <div class="uk-width-1-1@s" style="padding:0 0.5em 0 0.5em;">
                            <table class="uk-table hims-table uk-table-striped">
                                <thead>
                                    <th>Nama Menu</th>
                                    <th></th>
                                    <th style="text-align: center;width:30px;">...</th>
                                </thead>
                                <tbody>    
                                    <tr>
                                        <td colspan="2" style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:200px;">
                                            <div class="uk-form-controls">
                                                <search-select
                                                    :config = configSelect
                                                    :searchUrl = urlSearch
                                                    placeholder = "Beras, Gula, Garam, etc..."
                                                    captionField = "nama_menu"
                                                    valueField = "menu_id"
                                                    :itemListData = menuDesc
                                                    :value = menu.menu_id
                                                    v-on:item-select = "produkSelected"
                                                ></search-select>
                                            </div>                        
                                        </td>
                                        <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;">
                                            <a href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="addDetailDiet" style="padding:0;margin:0;color:blue;display:inline-block;" :disabled="isUpdate"></a>
                                        </td>
                                    </tr>
                                    <tr v-for="dt in menu.detail_menu" v-bind:class="dt.is_aktif != true ? 'inaktif':'' ">
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;width:80px;font-weight:500;">{{dt.nama_menu}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>                          
            </div>
        </div>
    </div> 
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'form-mapping-menu-menu', 
    components : { SearchSelect,SearchList }, 
    data() {
        return {
            configSelect : {
                disabled : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            menuDesc : [
                { colName : 'nama_menu', labelData : '', isTitle : true },
                { colName : 'menu_id', labelData : '', isTitle : false },
            ],
            isUpdate : false,
            menu : {
                diet_menu_id: '',
                diet_id: '',
                nama_diet: '',
                menu_id : null,
                nama_menu : null,
                detail_menu: []
            },
            urlSearch : '/menu',
            arrItemDel_data: [],         
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },
    
    methods : {
        ...mapActions('dietMenu',['createDietMenu']),
        ...mapActions('diet',['dataDiet']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            this.$emit('closed',true);
            this.clearDetailItem();
            UIkit.modal('#formDietMenu').hide();
            return false;
        },

        produkSelected(data){
            if(data) {
                this.menu.menu_id = data.menu_id;
                this.menu.nama_menu = data.nama_menu;
                this.menu.is_aktif = true;  
            }
        },

        submitPR(){
            this.CLR_ERRORS();
            this.createDietMenu(this.menu).then((response) => {
                if (response.success == true) {
                    alert(" data menu diet berhasil dibuat.");
                    this.$emit('saveSucceed',true);
                    this.isUpdate = false;
                    UIkit.modal('#formDietMenu').hide();
                }
            });    
        },

        newDietMenu(id){
            this.clearDetailItem();            
            this.dataDiet(id).then((response)=>{
                if (response.success == true) {
                    this.fillDiet(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formDietMenu').show();
                } else {
                    alert(response.message);
                }
            })
        },

        fillDiet(data){
            this.menu.diet_id = data.diet_id;
            this.menu.nama_diet = data.nama_diet;
        },

        addDetailDiet(){
            let exist = this.menu.detail_menu.find(item => item.menu_id === this.menu.menu_id);
            if(exist) { 
                alert('Data sudah ada dalam daftar.'); 
                return false; 
            }
            else {
                this.menu.detail_menu.push(JSON.parse(JSON.stringify(this.menu)));
                this.clearDetailItem();
            }
        },

        clearDetailItem(){
            this.menu.menu_id = null;
            this.menu.nama_menu = null;
            // this.menu.detail_menu = [];
        },
    }
}
</script>