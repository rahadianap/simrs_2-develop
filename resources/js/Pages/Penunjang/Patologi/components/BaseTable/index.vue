<template>    
    <div class="uk-card">        
        <slot name="tbl-button">
            <div class="uk-grid-small hims-table-filter-container" uk-grid style="margin:0;padding:0.5em;">            
                <div v-if="config.filterType == 'SIMPLE'" class="uk-width-auto" style="margin:0;padding:0 0.5em 0 0.5em;">
                    <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                        <form action="" @submit.prevent="submitFilter($event.target)" style="padding:0;margin:0;">
                            <input type="text" name="keyword" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:0;" placeholder="kata pencarian">
                            <button type="submit" class="hims-table-btn uk-width-auto"><span uk-icon="search"></span> Cari</button>
                        </form>
                    </div>                    
                </div>

                <div v-else-if="config.filterType == 'REGULAR'" class="uk-width-auto" style="margin:0;padding:0 0.5em 0 0.5em;">
                    <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                        <form action="" @submit.prevent="submitFilter($event.target)" style="padding:0;margin:0;">
                            <select name = "is_aktif" class="uk-select uk-form-small uk-width-auto select-status-aktif" style="border:none; border-right:1px solid #eee; height:33px;">
                                <option value="true">Data Aktif</option>
                                <option value="false">Data Non Aktif</option>
                                <option value="">Semua Data</option>
                            </select>
                            <input type="text" name="keyword" class="uk-input uk-form-small uk-width-auto text-keyword-regular" style="border:none;height:33px;" placeholder="kata pencarian">
                            <button type="submit" class="hims-table-btn uk-width-auto"><span uk-icon="search"></span> Cari</button>
                        </form>
                    </div>                    
                </div>

                <div class="uk-width-expand" style="padding:0;margin:0;">
                    <div class="uk-button-group uk-box-shadow-large" style="padding:0;margin:0;border-radius:5px;">
                        <button v-if="config.filterType == 'ADVANCED'" class="hims-table-btn" uk-toggle="target: .toggle-filter">
                            <span uk-icon="paint-bucket"></span>Filter
                        </button>
                        <button v-for="btn in buttons" class="hims-table-btn" @click.prevent="btn.callback">
                            <span :uk-icon="btn.icon"></span>{{btn.title}}
                        </button>
                    </div>
                </div>                
            </div>
        </slot>        
        <div v-if="config.filterType == 'ADVANCED'" ref="formFilterTable" class="toggle-filter form-filter-table" hidden>
            <form action="" @submit.prevent="submitFilter($event.target)" class="uk-grid-small uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l" uk-grid >
                <slot name="form-filter">
                </slot>
                <div class="uk-width-auto">
                    <button type="submit" class="uk-button-small uk-button">Cari</button>
                </div>
            </form>
        </div>
        <div v-if="config.filterType == 'ADVANCED'" class="toggle-filter uk-width-1-1 table-filter-info">
            <p style="padding:0;margin:0;" v-html="filterInfo"></p>
        </div>

        <div style="margin:0;padding:0;" class="uk-overflow-auto">
            <table class="uk-table hims-table uk-table-responsive1 uk-box-shadow-large uk-table-middle">
                <slot name="tbl-header">
                    <thead>
                        <tr>
                            <th></th>
                            <th v-for="col in columns" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">{{ col.title }}</th>
                        </tr>
                    </thead>
                </slot>
                
                <slot name="tbl-body">
                    <tbody v-if="datas.data && datas.data.length > 0">
                        <base-row :columns = "columns"  v-for="dt in datas.data" :rowData="dt" :childNameIndex="childNameIndex" :childColumns="childColumns" :isExpandable="config.isExpandable">                            
                        </base-row>
                    </tbody>
                    <tbody v-else>
                        <tr>
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
        }
    },

    computed : {
        ...mapGetters(['errors']),
    },

    watch : {
        urlSearch(newVal,oldVal){
            if(newVal !== '' && newVal !== null) {
                this.retrieveData();
            }
        },       
    },
    mounted(){
        this.retrieveData();
    },    
    methods :{
        ...mapMutations(['CLR_ERRORS']),        

        submitFilter(target) {
            this.filterInfo = '';
            let comps = target.elements;
            this.params.page = 1;
            for (let i = 0; i < comps.length; i++ ) {
                if(comps[i].type !== 'submit' && comps[i].type !== 'button') {
                    if(comps[i].value !== null && comps[i].value !== ''){
                        this.filterInfo = `${this.filterInfo} <span class="filter-label"> ${comps[i].name} : ${comps[i].value} </span> `;
                    }
                    this.params[comps[i].name] = comps[i].value; 
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
            this.isLoading = false;
            this.CLR_ERRORS();  
            if(this.urlSearch == '' || this.urlSearch == null) { return false; }
            httpReq.get(this.urlSearch, { params : this.params })
            .then((response) => {
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
                    this.$refs.pagination.createPagination();
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

        refreshTable(){
            this.$forceUpdate();
        },

        tableData(){
            return this.datas;
        }

    }
}
</script>

<style>
.hims-table {
    margin:0;
    padding:0;
}
.hims-table thead {
    border:none;
    border-top:2px solid #cc0202;
    position:sticky;
    top:0;
    z-index:100;
}

.hims-table th {
    /* position:sticky; 
    z-index:99; */
    padding:1em;
    /* top:0;  */
    width: auto; 
    text-align:left;
    white-space: pre-wrap;
    font-size: 14px;
    border:none; 
    color: white;
    font-weight: 400;
    /* background-color : #1E90FF;  */
    background-color : #cc0202; 
    /* background-color : #fefefe; */
}

.hims-table tbody tr {
    padding:0;
    margin:0;
    border-top:1px solid #f9f9f9;
}

.hims-table td {
    white-space: pre-wrap; 
    font-size: 12px; 
    border: none;
    color: black;
    padding: 1.2em 1em 1.2em 1em;
    background-color: #fefefe;
}

.hims-table tbody tr:hover td {
    background-color: #fafafa;
}

.hims-table .row-link {
    font-size: 12px;
    font-weight:500;
    text-decoration:none;
}

.hims-table .row-link:hover {
    font-weight:500;
    text-decoration:underline;
    cursor:pointer;
}
a.row-link-menu {
    font-weight:500;
    font-size: 12px; 
    text-decoration:none;
    color:black;
}
a.row-link-menu:hover {
    font-weight:500;
    text-decoration:underline;
    cursor:pointer;
}


.hims-table-btn {
    border:none;
    padding:0.5em 1em 0.5em 1em;
    min-width:20px;
    background-color: #fefefe;
}

.hims-table-btn span {
    margin-right:5px;
}

.hims-table-btn:hover {
    background-color: #cc0202;
    color:white;
}

.hims-table-btn:first-child {
    border-bottom-left-radius: 5px;
    border-top-left-radius: 5px;
}

.hims-table-btn:last-child {
    border-bottom-right-radius: 5px;
    border-top-right-radius: 5px;
}

.form-filter-table {
    background-color: white;
    margin-top: 1em;
    padding:0.5em 1em 1em 1em;
    border-radius:5px;
}

.form-filter-table label {
    font-size:12px;
}

.form-filter-table input, .form-filter-table select {
    font-size:12px;
    border:1px solid #cc0202;
}

.form-filter-table button {
    margin-top:1.6em;
    padding:0 0.5em 0 0.5em; 
    width:100px; 
    background-color: #fafafa;
    color:#333;
}

.table-filter-info {
    padding:0.5em;
    font-size:12px; 
    font-style:italic;
}

.table-filter-info .filter-label {
    margin:5px;
    background-color: #cc0202;
    color:white;
    font-size:12px;
    padding: 0.5em 1em 0.5em 1em;
    /* border-radius:50px; */
    font-size: 400;
    display: inline-block;
}

.text-keyword-simple {
    border:none;
    border-right:1px solid #f0f0f0;
    height:33px;
    color:black;
    border-top-left-radius:5px;
    border-bottom-left-radius:5px;
}

.text-keyword-regular {
    border:none;
    border-right:1px solid #f0f0f0;
    height:33px;
    color:black;
    margin-left:0;
}

.select-status-aktif {
    width:140px;
    border:none; 
    border-right:1px solid #ccc;
    height:33px;
    margin-left:0;
    border-top-left-radius:5px;
    border-bottom-left-radius:5px;
}


th.child-table-header {
    padding:0.5em;
    text-align: center;
    font-size:12px;
    background-color:#fff;
    font-weight: 500;
    color:#000;
}

.child-table tr td {
    padding:0.5em;
    background-color:whitesmoke;
}


</style>