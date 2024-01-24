<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formCreateLocation" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitLocation" style="padding:0;" >
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
                                <!-- <search-list
                                    :config = configSelect
                                    :dataLists = userRuangLists
                                    label = "Ruang / Kamar*"
                                    placeholder = "pilih ruang atau kamar"
                                    captionField = "ruang_nama"
                                    valueField = "ruang_nama"
                                    :detailItems = ruangDesc
                                    :value = "ruangData.ruang_nama"
                                    v-on:item-select = "ruangSelected">
                                </search-list> -->
                                <search-select
                                    :config = configSelect
                                    searchUrl = "/rooms"
                                    label = "Ruang / Kamar*"
                                    placeholder = "pilih ruang atau kamar"
                                    captionField = "ruang_nama"
                                    valueField = "ruang_nama"
                                    :itemListData = ruangDesc
                                    :value = "ruangData.ruang_nama"
                                    v-on:item-select = "ruangSelected"
                                ></search-select>
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
    name : 'form-create-location', 
    components : { SearchSelect,SearchList }, 
    data() {
        return {
            configSelect : {
                disabled : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            ruangDesc : [
                { colName : 'ruang_nama', labelData : '', isTitle : true },
                { colName : 'ruang_id', labelData : '', isTitle : false },
            ],
            isUpdate : false,
            ruangData : {
                ruang_id: null,
                ruang_nama: null,
            },
            urlSearch : '',
            userRuangLists : null,        
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
        // this.init();
    },
    
    methods : {
        ...mapActions('satusehatLokasi',['createLocation']),
        ...mapActions('ruang',['listRuang']),
        
        ...mapMutations(['CLR_ERRORS']),

        // init() {
        //     this.CLR_ERRORS();
        //     this.listRuang({'per_page':'ALL'}).then((response) => {
        //         if (response.success == true) {
        //             this.userRuangLists = response.data.data;
        //         }
        //     })
        // },

        clearData(){
            this.ruang_id = null;
            this.ruang_nama = null;
        },

        closeModal(){
            this.$emit('closed',true);
            this.clearData();
            UIkit.modal('#formCreateLocation').hide();
            return false;
        },

        ruangSelected(data){
            if(data) {
                this.ruangData.ruang_id = data.ruang_id;   
                this.ruangData.ruang_nama = data.ruang_nama;
            }
        },

        submitLocation(){
            this.CLR_ERRORS();
            this.createLocation(this.ruangData).then((response) => {
                if (response.success == true) {
                    alert(" data ruang berhasil dimapping.");
                    this.$emit('saveSucceed',true);
                    this.isUpdate = false;
                    UIkit.modal('#formCreateLocation').hide();
                }
            })        
        },

        newLocation(){
            this.configSelect.disabled = false;
            this.isUpdate = false;
            UIkit.modal('#formCreateLocation').show();
        }
    }
}
</script>
