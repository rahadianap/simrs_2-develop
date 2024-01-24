<template>    
    <div class="uk-card">        
        <slot name="tbl-button">
            <div class="uk-grid-small hims-table-filter-container" uk-grid style="margin:0;padding:0.5em;">
                <template v-if="config.filterType == 'ADVANCED'">
                    <div class="uk-width-auto" style="margin:0;padding:0 0.5em 0 0.5em;">
                        <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                            <form action="" @submit.prevent="filterData" style="padding:0;margin:0;">
                                <select v-model="is_aktif" name = "is_aktif" class="uk-select uk-form-small uk-width-auto select-status-aktif" style="border:none; border-right:1px solid #eee; height:33px;">
                                    <option value="true">Data Aktif</option>
                                    <option value="false">Data Non Aktif</option>
                                    <option value=" ">Semua Data</option>
                                </select>
                                <input type="text" name="keyword" v-model="keyword" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:0;" placeholder="kata pencarian">
                                <button type="submit" class="hims-table-btn uk-width-auto"><span uk-icon="search"></span> Cari</button>
                                <button type="button" class="hims-table-btn" @click.prevent="toggleFilterSection">
                                    <span uk-icon="paint-bucket"></span>Filter
                                </button>
                            </form>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="uk-width-auto" style="margin:0;padding:0 0.5em 0 0.5em;">
                        <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                            <form action="" @submit.prevent="submitFilter($event.target)" style="padding:0;margin:0;">
                                <select v-if="config.filterType == 'REGULAR'" v-model="is_aktif" name = "is_aktif" class="uk-select uk-form-small uk-width-auto select-status-aktif" style="border:none; border-right:1px solid #eee; height:33px;">
                                    <option value="true">Data Aktif</option>
                                    <option value="false">Data Non Aktif</option>
                                    <option value=" ">Semua Data</option>
                                </select>
                                <input type="text" name="keyword" v-model="keyword" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:0;" placeholder="kata pencarian">
                                <button type="submit" class="hims-table-btn uk-width-auto"><span uk-icon="search"></span> Cari</button>
                            </form>
                        </div>
                    </div>
                </template>

                <div class="uk-width-expand" style="padding:0;margin:0;">
                    <div class="uk-button-group uk-box-shadow-large" style="padding:0;margin:0;border-radius:5px;">
                        <button v-for="btn in buttons" class="hims-table-btn" @click.prevent="btn.callback">
                            <span :uk-icon="btn.icon"></span>{{btn.title}}
                        </button>
                    </div>
                </div>                
            </div>
        </slot>
        <div v-if="config.filterType == 'ADVANCED'" class="toggle-filter uk-width-1-1 table-filter-info">
            <p style="padding:0;margin:0;" v-html="filterInfo"></p>
        </div>
        <div class="uk-grid-small  uk-box-shadow-large" uk-grid style="margin:0;padding:0;">
            <div v-if="config.filterType == 'ADVANCED'" id="offcanvasFilterTable" style="margin:0;padding:1em;" class="uk-width-1-2@s uk-width-1-3@m uk-width-1-4@l tabel-filter-div">
                <h3>Filter Pencarian</h3>
                <form action="" @submit.prevent="submitFilter($event.target)">
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-1-1">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Data</label>
                            <select name = "is_aktif" class="uk-select uk-form-small" v-model="is_aktif">
                                <option value="true">Data Aktif</option>
                                <option value="false">Data Non Aktif</option>
                                <option value="">Semua Data</option>
                            </select>
                        </div>
                        <div class="uk-width-1-1">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kata Pencarian</label>
                            <div class="uk-form-controls">
                                <input type="text" name="keyword" v-model="keyword" class="uk-input uk-form-small" style="color:black;">
                            </div>
                        </div>
                        <slot name="form-filter"></slot>
                        <div class="uk-width-1-2@m">
                            <button class="button-filter-advanced btn-filter-primary uk-width-1-1" type="submit">Filter</button>
                        </div>
                        <div class="uk-width-1-2@m">
                            <button class="button-filter-advanced uk-width-1-1" type="button" @click.prevent="toggleFilterSection">Tutup</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="uk-width-expand" style="margin:0;padding:0;overflow:auto;">
                <div style="margin:0;padding:0;">
                    <table class="uk-table hims-table uk-table-responsive1">
                        <slot name="tbl-header">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th v-for="col in columns" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">{{ col.title }}</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center; background-color: #fff;padding:1em;" :colspan="columns.length + 1" v-if="isLoading">
                                        <p style="font-size:12px; font-weight: 500; font-style:italic;padding:0.5em; color:black;">
                                            <span uk-spinner></span>
                                            sedang mengambil data
                                        </p>
                                    </th>
                                </tr>
                            </thead>
                        </slot>                
                        <slot name="tbl-body">
                            <tbody v-if="datas.data && datas.data.length > 0">
                                <base-row :columns = "columns"  v-for="dt in datas.data" :rowData="dt" :childNameIndex="childNameIndex" :childColumns="childColumns" :isExpandable="config.isExpandable">                            
                                </base-row>
                            </tbody>
                            <tbody v-else>
                                <tr v-if="!isLoading">
                                    <td :colspan="columns.length + 1">
                                        <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">tidak ada data untuk ditampilkan</p>
                                    </td>
                                </tr>
                                
                            </tbody>
                        </slot>
                    </table>
                </div>
                <div style="margin-top:1em; background-color: transparent;padding:0;">
                    <full-pagination ref="pagination" 
                        :pagination = "pagination" 
                        v-on:numRowsChange = "updateNumRowChange" 
                        v-on:pageChange="pageChange">
                    </full-pagination>
                </div>
            </div>
        </div>
    </div>    
</template> 

<script>
import { mapGetters,mapState,mapMutations,mapActions } from 'vuex';
import httpReq from '@/Stores/httpReq';

import FullPagination from '@/Components/BaseTable/FullPagination.vue';
import BaseRow from '@/Components/BaseTable/BaseRow.vue';
import { nextTick } from 'process';

export default {
    name : 'base-table',
    components : {
        BaseRow,
        FullPagination,
    },
    props : {
        config : {type:Object , required: false, default : null },
        /**column table 
         * dengan properties :
         * name -> id dari data yang diambil (string)
         * title -> judul kolom (string)
         * colType ->jenis kolom (text, boolean, number, date, currency)
        */
        columns : { type:Array , required: false, default : [] },
        childNameIndex : {type:String, required: false, default : '' }, 
        childColumns : { type:Array , required: false, default : [] },
        /**
         * base button function,  ada fungsi utama dari table, dengan properties :
         * icon -> tombol icon
         * title -> judul di tombol
         * callback -> fungsi yang dijalankan saat
        */
        buttons : {type:Object, required : false, default : [] }, 
        urlSearch : {type:String, required : false, default:null },
        filter : {type:Object, required : false, default : { 'is_aktif':true, 'keyword': null } },
    },

    data() {
        return {
            params : { 
                page : 1,
            },
            pagination : { current_page :0,per_page:10,total_data:0,num_pages:0, },
            searchFields : [],
            datas : [],
            filterInfo : '',
            isLoading : false,
            keyword : null,
            is_aktif : true,
        }
    },

    computed : {
        ...mapGetters(['errors']),
    },

    watch : {
        urlSearch(newVal,oldVal){
            if(newVal !== '' && newVal !== null) {
                this.init();
            }
        },       
    },
    mounted(){
        this.toggleFilterSection();
        this.init();
    },    
    methods :{
        ...mapMutations(['CLR_ERRORS']),

        init(){
            if(this.config.filterType == 'ADVANCED') { this.filterData(); }
            else { this.retrieveData(); }
        },

        filterData(){
            console.log('filter data');
            let elms = document.getElementById('offcanvasFilterTable').getElementsByTagName("form");
            this.submitFilter(elms[0]);   
        },

        toggleFilterSection() {
            if(this.config.filterType == 'ADVANCED'){
                var comp = document.getElementById('offcanvasFilterTable');
                if (comp.style.display == 'none') { comp.style.display = 'block'; } 
                else { comp.style.display = 'none'; }
                return false; 
            }
        },

        submitFilter(target) {            
            if(this.isLoading) { return false; }
            this.filterInfo = '';
            this.params = { 'page' : 1 };
            let comps = target.elements;
            this.params.page = 1;
            for (let i = 0; i < comps.length; i++ ) {
                if(comps[i].type !== 'submit' && comps[i].type !== 'button') {
                    if(comps[i].value !== null && comps[i].value !== '' && comps[i].name !== null && comps[i].name !== ''){
                        this.filterInfo = `${this.filterInfo} <span class="filter-label"> ${comps[i].name} : ${comps[i].value} </span> `;
                        this.params[comps[i].name] = comps[i].value; 
                    }
                }
            }
            if(this.filterInfo.length > 0) {
                this.filterInfo = `<strong>Pencarian : ${this.filterInfo}</strong>`;
            }
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
            this.isLoading = true;
            this.CLR_ERRORS();  
            if(this.urlSearch == '' || this.urlSearch == null) { return false; }
            httpReq.get(this.urlSearch, { params : this.params })
            .then((response) => {
                this.isLoading = false;
                    
                if (response.data.success == false) {
                    this.$store.commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    this.$emit('on-ready',true);
                } else {
                    this.datas = response.data.data;
                    this.pagination.current_page = response.data.data.current_page;
                    this.pagination.last_page = response.data.data.last_page;
                    this.pagination.per_page = response.data.data.per_page;
                    this.pagination.total_data = response.data.data.total;
                    this.pagination.num_pages = response.data.data.last_page;
                    if(this.$refs.pagination) {
                        this.$refs.pagination.createPagination();
                    }
                    this.$emit('on-ready',true);
                    this.$emit('updateListDataTable',this.datas);
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

        refreshTable(){ this.$forceUpdate(); },

        tableData(){ return this.datas; }
    }
}
</script>
<style>
div.tabel-filter-div {
    background-color: #fefefe;
    color:black;
}
button.button-filter-advanced {
    padding:1em;
    border:none;
    background-color: whitesmoke;
    color:#000;
}
button.btn-filter-primary {
    padding:1em;
    border:none;
    background-color: #cc0202;
    color:#fff;
}

button.button-filter-advanced:hover {
    cursor: pointer;
    font-weight: 500;
} 
</style>
