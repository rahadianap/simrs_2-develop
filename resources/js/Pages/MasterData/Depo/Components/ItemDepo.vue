<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <!-- <header-page title="PEMETAAN ITEM DEPO" subTitle="pemetaan item persediaan depo"></header-page> -->
        <div class="uk-grid uk-grid-small" style="padding:0.5em 1em 0.5em 1em;">
            <div class="uk-width-auto" style="padding:0;">
                <a href="#" @click.prevent="closeModalItem" uk-icon="icon:arrow-left;ratio:1.5" style="display:block;width:30px;"></a>
            </div>
            <div class="uk-width-expand"  style="padding:0;margin:0;">
                <ul class="uk-breadcrumb" style="padding:0;margin:0;">
                    <li>
                        <a href="#" @click.prevent="closeModalItem" class="uk-text-uppercase">
                            <h4 style="font-weight:500;margin:0;padding:0;display: inline-block;" class="uk-text-uppercase">PEMETAAN ITEM DEPO</h4>
                        </a>                    
                    </li>
                    <li v-if="depoInfo.depo_nama !== null">
                        <span style="font-weight:400;color:#333;font-size:14px;"  class="uk-text-uppercase">{{depoInfo.depo_nama}}</span>
                    </li>           
                </ul>
            </div>
        </div>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div class="uk-card" style="margin-top:1em;padding:0;">
            <base-table ref="tableItemDepo"
                :columns = "tbColumns" 
                :config="configTable" 
                :buttons = "buttons"
                :urlSearch="searchUrl">
            </base-table>
        </div>
        <modal-picker ref="itemPicker" 
            title="Pilih Item Persediaan" 
            :fieldDatas = columns 
            :urlSearch="pickerSearchUrl" 
            viewType="table"
            :proceedFunction="saveData"
        ></modal-picker>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import HeaderPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import ModalPicker from '@/Components/ModalPicker';

export default {
    name : 'item-depo',
    components : {
        HeaderPage,
        BaseTable,
        ModalPicker
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('depo',['depoLists']),
    },

    data() {
        return {
            depoInfo : {
                client_id : null,
                depo_id :null,
                depo_nama : null,
                items : []
            },
            depo : {
                client_id : null,
                depo_id :null,
                depo_nama : null,
                items : []
            },
            isLoading : false,
            searchUrl : '',
            pickerSearchUrl : '',
            configTable : {                
                isExpandable : false,
                filterType : 'REGULAR', 
            },
            tbColumns : [                
                { 
                    name : 'produk_id', 
                    title : 'PRODUK ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Non-aktifkan', 'icon':'ban','callback':this.removeItemDepo },   
                    ],
                },
                { 
                    name : 'produk_nama', 
                    title : 'PRODUK', 
                    colType : 'text',
                    isSearchable  : true,
                },
                { 
                    name : 'jenis_produk', 
                    title : 'JENIS', 
                    colType : 'text',
                    isSearchable  : true,
                },
                { 
                    name : 'jml_total', 
                    title : 'Total', 
                    colType : 'number',
                    isSearchable  : true,
                },
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    options : [
                        { value:0, text:'Aktif' },
                        { value:1, text:'Nonaktif' },
                    ]
                },
                
            ], 
            buttons : [
                { title : 'Item Medis', icon:'plus-circle', 'callback' : this.addItemMedis },
                { title : 'Item Umum', icon:'plus-circle', 'callback' : this.addItemUmum },
                { title : 'Item Dapur', icon:'plus-circle', 'callback' : this.addItemDapur },
                
            ],
            columns : [ 
                { 
                    name : 'produk_id', 
                    title : 'ID', 
                    colType : 'text',
                    isCaption : false,
                },
                { 
                    name : 'produk_nama', 
                    title : 'Nama',
                    colType : 'text',
                    isCaption : false,
                },            
                { 
                    name : 'jenis_produk', 
                    title : 'Jenis', 
                    colType : 'text',
                    isCaption : true,
                },
            ],
        }
    },

    mounted() {
    },

    methods : {
        ...mapActions('depo',['listDepo','deleteDepo','dataDepo','addItemDepo','deleteItemDepo']),
        ...mapMutations(['CLR_ERRORS']),

        depoData(depoId) {
            this.dataDepo(depoId).then((response)=>{
                if (response.success == true) {
                    this.fillData(response.data);
                    this.isUpdate = true;
                } else {
                    alert(response.message);
                    this.$emit('close',true);
                }
                this.isLoading = false;
            })
        },

        closeModalItem(){
            this.CLR_ERRORS();
            this.clearData();
            this.$emit('close',true);
        },

        clearData(){
            this.depoInfo.depo_id = null;
            this.depoInfo.client_id = null;
            this.depoInfo.depo_nama = null;
        },

        fillData(data) {
            this.depoInfo.depo_id = data.depo_id;
            this.depoInfo.client_id = null;
            this.depoInfo.depo_nama = data.depo_nama;
            this.searchUrl = `/depos/${data.depo_id}/products`;
        },

        addItemMedis(){
            this.pickerSearchUrl = `/products/medical/items`;
            this.$refs.itemPicker.showModalPicker(this.pickerSearchUrl);
        },

        addItemUmum(){
            this.pickerSearchUrl = `/products/general/items`;
            this.$refs.itemPicker.showModalPicker(this.pickerSearchUrl);
        },

        addItemDapur(){
            this.pickerSearchUrl = `/products/kitchen/items`;
            this.$refs.itemPicker.showModalPicker(this.pickerSearchUrl);
        },
        
        saveData(data){
            this.CLR_ERRORS();
            if(data.length < 1) { return false; }
            let depoItems = [];
            data.forEach(dt => {
                depoItems.push({
                    produk_id : dt.produk_id,
                    produk_nama : dt.produk_nama,
                    jenis_produk : dt.jenis_produk,
                    jml_awal : 0,
                    jml_masuk : 0,
                    jml_keluar : 0,
                    jml_penyesuaian : 0,
                    jml_total : 0,
                });
            });
            this.depo.depo_id = this.depoInfo.depo_id;
            this.depo.items = depoItems;
            
            this.addItemDepo(this.depo).then((response) => {
                if (response.success == true) {
                    this.$refs.itemPicker.closeModalPicker();
                    alert("Item depo berhasil ditambahkan.");
                    this.$refs.tableItemDepo.retrieveData();
                }
            })           
        },
        removeItemDepo(data){
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menonaktifkan depo item ${data.produk_nama}. Lanjutkan ?`)){
                this.deleteItemDepo(data.depo_produk_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableItemDepo.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>