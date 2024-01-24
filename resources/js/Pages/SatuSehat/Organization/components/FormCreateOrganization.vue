<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formCreateOrganization" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitOrganization" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">MAPPING ORGANIZATION</h5>
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
                        <div class="uk-width-1-1@s uk-grid-small" uk-grid>              
                            <div class="uk-width-1-1@m">
                                <search-list
                                    :config = configSelect
                                    :dataLists = userUnitLists
                                    label = "Unit*"
                                    placeholder = "pilih unit pelayanan"
                                    captionField = "unit_nama"
                                    valueField = "unit_nama"
                                    :detailItems = unitDesc
                                    :value = "unitData.unit_nama"
                                    v-on:item-select = "unitSelected">
                                </search-list>
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
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'form-create-organization', 
    components : { SearchSelect,SearchList }, 
    data() {
        return {
            configSelect : {
                disabled : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            unitDesc : [
                { colName : 'unit_nama', labelData : '', isTitle : true },
                { colName : 'unit_id', labelData : '', isTitle : false },
            ],
            isUpdate : false,
            unitData : {
                unit_id: null,
                unit_nama: null,
            },
            urlSearch : '',
            userUnitLists : null,        
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
        this.init();
    },
    
    methods : {
        ...mapActions('satusehatOrganisasi',['createOrganization']),
        ...mapActions('users',['listUserUnit']),
        
        ...mapMutations(['CLR_ERRORS']),

        init() {
            this.CLR_ERRORS();
            this.listUserUnit({'per_page':'ALL'}).then((response) => {
                if (response.success == true) {
                    this.userUnitLists = response.data.data;
                }
            })
        },

        clearData(){
            this.unit_id = null;
            this.unit_nama = null;
        },

        closeModal(){
            this.$emit('closed',true);
            this.clearData();
            UIkit.modal('#formCreateOrganization').hide();
            return false;
        },

        unitSelected(data){
            if(data) {
                this.unitData.unit_id = data.unit_id;   
                this.unitData.unit_nama = data.unit_nama;
            }
        },

        submitOrganization(){
            this.CLR_ERRORS();
            this.createOrganization(this.unitData).then((response) => {
                if (response.success == true) {
                    alert(" data unit berhasil dimapping.");
                    this.$emit('saveSucceed',true);
                    this.isUpdate = false;
                    UIkit.modal('#formCreateOrganization').hide();
                }
            })        
        },

        newOrganization(){
            this.configSelect.disabled = false;
            this.isUpdate = false;
            UIkit.modal('#formCreateOrganization').show();
        }
    }
}
</script>
