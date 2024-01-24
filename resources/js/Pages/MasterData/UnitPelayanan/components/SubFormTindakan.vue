<template>    
    <div class="hims-form-subpage">
        <div class="uk-card">             
            <div class="uk-grid-small hims-table-filter-container" uk-grid style="margin:0;padding:0.5em;">            
                <div class="uk-width-auto" style="margin:0;padding:0 0.5em 0 0.5em;">
                    <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                        <form action="" @submit.prevent="submitFilter($event.target)" style="padding:0;margin:0;">
                            <input type="text" v-model="params.keyword" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:0;" placeholder="kata pencarian">
                            <button type="submit" class="uk-width-auto hims-table-btn-submit">
                                <span uk-icon="search" style="margin-right:5px;"></span> Cari
                            </button>
                        </form>
                    </div>                    
                </div>                
                <div class="uk-width-expand" style="padding:0;margin:0;">
                    <div class="uk-button-group uk-box-shadow-large" style="padding:0;margin:0;border-radius:5px;">
                        <button type="button" class="hims-table-btn" @click="showPicker">
                            <span uk-icon="plus-circle"></span> Tambah Tindakan
                        </button>
                    </div>
                </div>                
            </div>
            <div class="uk-width-1-1" style="padding:0.5em;">
                <p style="color:#333; font-size:12px; padding:0;margin:0; font-style: italic; ">*Penambahan dan pengurangan data tindakan akan langsung disimpan.</p>
            </div>
            <div style="margin:0;padding:0;" class="uk-overflow-auto">
                <table class="uk-table hims-table uk-table-responsive">
                    <thead class="uk-card uk-card-default uk-box-shadow-small" style="border-top:2px solid #cc0202;">
                        <tr>
                            <th style="width:150px;">ID</th>
                            <th>Tindakan</th>
                            <th style="text-align:center;">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="datas.length > 0" v-for="dt in datas"  v-bind:class="dt.is_aktif != true ? 'inaktif':'' ">
                            <td style="font-weight:500;width:150px;">{{dt.tindakan_id}}</td>
                            <td class="uk-text-uppercase">{{dt.tindakan_nama}}</td>
                            <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;text-align:center;">
                                <a href="#" uk-icon="icon:trash;ratio:0.8" @click.prevent="deleteData(dt)"></a>
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="3">
                                <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">belum ada data untuk ditampilkan</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="margin-top:1em; background-color: transparent;padding:0;">
                <full-pagination ref="pagination" 
                    :pagination = "pagination" 
                    v-on:numRowsChange = "updateNumRowChange" 
                    v-on:pageChange="pageChange">
                </full-pagination>
            </div>

            <modal-picker ref="actModalPicker" 
                title="Pilih Tindakan dan Pemeriksaan" 
                :fieldDatas = columns 
                :urlSearch = "urlPicker" 
                viewType="table"
                :proceedFunction="saveData"
            ></modal-picker>
        </div>
    </div>
</template> 

<script>
import { mapGetters,mapState,mapMutations,mapActions } from 'vuex';
import httpReq from '@/Stores/httpReq';
import FullPagination from '@/Components/BaseTable/FullPagination.vue';
import ModalPicker from '@/Components/ModalPicker';

export default {
    name : 'table-unit-tindakan',
    components : {
        FullPagination,ModalPicker
    },

    data() {
        return {
            params : { 
                page : 1, 
                keyword : '',
            },
            pagination : { current_page :0,per_page:10,total_data:0,num_pages:0, },
            searchFields : [],
            datas : [],
            filterInfo : '',
            columns : [ 
                { 
                    name : 'tindakan_id', 
                    title : 'ID', 
                    colType : 'text',
                    isCaption : false,
                },
                { 
                    name : 'klasifikasi', 
                    title : 'Jenis',
                    colType : 'text',
                    isCaption : false,
                },            
                { 
                    name : 'tindakan_nama', 
                    title : 'Tindakan', 
                    colType : 'text',
                    isCaption : true,
                },
            ],
            urlSearch : '',
            unit : null,
            tindakan : {
                unit_id : null,
                unit_nama : null,
                tindakanUnit : [],
            },
            urlPicker : ''
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },  
    methods :{
        ...mapMutations(['CLR_ERRORS']), 
        ...mapActions('unitPelayanan',['addUnitTindakan','deleteUnitTindakan']),    
        
        saveData(data){
            this.CLR_ERRORS();
            if(data.length < 1) { return false; }
            this.tindakan.tindakanUnit = data;
            this.addUnitTindakan(this.tindakan).then((response) => {
                if (response.success == true) {
                    this.$refs.actModalPicker.closeModalPicker();
                    alert("Tindakan unit berhasil ditambahkan.");
                    this.$emit('updated',true);
                }
            })           
        },

        submitFilter(target) {
            this.filterInfo = '';
            let comps = target.elements;
            this.params.page = 1;
            this.retrieveData();
            return false;
        },

        updateNumRowChange(num){
            this.pagination.current_page = 1;
            this.pagination.num_pages = 0;
            this.pagination.total_data = 0;
            this.params.page = 1;
            this.params.per_page = num;
            this.retrieveData();
        },
        /**ambil data berdasarkan url search yang diberikan**/
        retrieveData() {
            this.CLR_ERRORS();  
            if(this.urlSearch == '' || this.urlSearch == null) { return false; }
            httpReq.get(this.urlSearch, { params : this.params })
            .then((response) => {
                if (response.data.success == false) {
                    this.$store.commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                } else {
                    this.datas = response.data.data.data;
                    this.pagination.current_page = response.data.data.current_page;
                    this.pagination.last_page = response.data.data.last_page;
                    this.pagination.per_page = response.data.data.per_page;
                    this.pagination.total_data = response.data.data.total;
                    this.pagination.num_pages = response.data.data.last_page;

                    this.$refs.pagination.createPagination();
                }
            })            
            .catch((error) => {
                this.$store.commit('SET_ERRORS', { invalid: error }, { root: true });
            })
        },    

        pageChange(index) {
            this.params.page = index;
            this.retrieveData();
        },

        showPicker() {
            this.urlPicker = `/acts`;
            this.$refs.actModalPicker.showModalPicker(this.urlPicker);
        },

        refreshData(data){
            this.datas = [];
            this.tindakan.unit_id = data.unit_id;
            this.tindakan.unit_nama = data.unit_nama;
            if(this.tindakan.unit_id !== null && this.tindakan.unit_id !== '' ) {
                this.urlSearch = `/units/${this.tindakan.unit_id}/acts`;
                this.retrieveData();
            }
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus tindakan ${data.tindakan_nama} dari unit ${this.tindakan.unit_nama}. Lanjutkan ?`)){
                this.deleteUnitTindakan(data.unit_tindakan_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$emit('updated',true);
                    }
                    else { alert (response.message) }
                });
            };
        },
    }
}
</script>