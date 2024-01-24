<template>    
    <div class="uk-container">        
        <div class="uk-container" style="padding:0;">
            <div class="uk-grid-small hims-table-filter-container" uk-grid style="margin:0;padding:0.5em;"> 
                <div class="uk-grid uk-grid-small uk-width-1-1" style="padding:0.5em 1em 1em 1em;">
                    <div class="uk-width-auto" style="padding:0;">
                        <a href="#" @click.prevent="closeFormOpname" uk-icon="icon:arrow-left;ratio:1.5" style="display:block;width:30px;"></a>
                    </div>
                    <div class="uk-width-expand" style="padding:0;margin:0;">
                        <ul class="uk-breadcrumb" style="padding:0;margin:0;">
                            <li>
                                <a href="#" @click.prevent="closeFormOpname" class="uk-text-uppercase">
                                    <h4 style="font-weight:500;margin:0;padding:0;display: inline-block;" class="uk-text-uppercase">DATA OPNAME
                                        <span class="uk-label" style="padding:0.2em 0.5em 0.2em 0.5em;margin:0;font-size:11px;font-style:italic;">{{opname.status}}</span>
                                    </h4>
                                </a>
                            </li>
                            <li v-if="opname.depo_nama !== null">
                                <span style="font-weight:400;color:#333;font-size:14px;"  class="uk-text-uppercase">{{opname.depo_nama}}</span>
                            </li>
                        </ul>
                    </div>
                </div>    

                <div class="uk-width-1-1" style="margin:0;padding:1em 0 0 0.5em;">
                    <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                        <button type="button" class="hims-table-btn uk-width-auto" @click.prevent="closeFormOpname"><span uk-icon="arrow-left"></span> Kembali</button>
                        <button type="button" class="hims-table-btn uk-width-auto"><span uk-icon="search"></span> Refresh</button>
                        <button type="button" class="hims-table-btn uk-width-auto" @click="saveData"><span uk-icon="file-text"></span> Simpan</button>
                        <span class="uk-width-expand@m" style="background-color:#fff;"></span>
                        <button type="button" class="hims-table-btn uk-width-auto" @click="approveData" :disabled="!isUpdate"><span uk-icon="lock"></span> Selesai</button>
                    </div>                    
                </div>
            </div>
            <div style="margin:0.2em 0 0 0;padding:0;">
                <table class="uk-table hims-table uk-table-responsive uk-box-shadow-large">
                    <thead>
                        <tr>
                            <th style="padding:1em;margin:0;text-align:center;">ID</th>
                            <th style="padding:1em;margin:0;">Nama Item</th>
                            <th style="padding:1em;margin:0;text-align:center;">Jenis</th>
                            <th style="padding:1em;margin:0;text-align:center;">Jml Sistem</th>
                            <th style="padding:1em;margin:0;text-align:center;">Jml Riil</th>
                            <th style="padding:1em;margin:0;text-align:center;">Selisih</th>
                        </tr>
                    </thead>
                    <tbody v-if="datas.data && datas.data.length > 0">
                        <tr v-for = "dt in datas.data">
                            <td style="padding:1em;margin:0;font-weight:500;text-align:center;width:150px;">{{dt.produk_id}}</td>
                            <td style="padding:1em;margin:0;">{{dt.produk_nama}}</td>
                            <td style="padding:1em;margin:0;text-align:center;">{{dt.jenis_produk}}</td>
                            <td style="padding:1em;margin:0;text-align:center;width:80px;">{{dt.jml_total}}</td>
                            <td style="padding:0.5em 0 0 0;margin:0;text-align:center;width:80px;">
                                <input type="number" class="uk-input uk-form-small" v-model="dt.total_so" style="text-align:center;" @change="calculateSelisih(dt)">
                            </td>
                            <td style="padding:1em;margin:0;text-align:center;width:80px;">{{dt.selisih_so}}</td>
                        </tr>                    
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="6">
                                <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">tidak ada data untuk ditampilkan</p>
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
    name : 'form-opname',
    components : {
        BaseRow,
        FullPagination,
    },
    props : {
        data : {type:Object , required: false, default : null },
        config : {type:Object , required: false, default : null },
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

            isUpdate : false,
            opname : {
                so_id : null,                
                depo_id : null,
                depo_nama : null,
                tgl_rencana : null,
                tgl_so : null,
                status : null,
                catatan : null,
                produk : [],
            },            
            urlSearch : '',
            searchOpnameUrl : '',
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
        ...mapActions('stockOpname',['dataOpname','updateOpname','approveOpname']),    

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
            if(this.urlSearch == '' || this.urlSearch == null) { return false; this.$emit('ready',true); }
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
                    this.$emit('ready',true);
                }
            })            
            .catch((error) => {
                this.$store.commit('SET_ERRORS', { invalid: error }, { root: true });
            })
        },    

        pageChange(index) {            
            this.saveData();
            this.params.page = index;
            this.retrieveData();
        },

        calculateSelisih(data) {
            data.selisih_so = (data.total_so - data.jml_total);
        },

        saveData() {
            this.opname.produk = JSON.parse(JSON.stringify(this.datas.data));
            this.updateOpname(this.opname).then((response)=>{
                if (response.success == true) {
                    alert('data berhasil diperbaharui.');
                    this.isUpdate = true;
                } else {
                    alert(response.message)
                }
            })
        },

        approveData() {
            if(confirm(`Proses ini akan mengakhiri proses Stock Opname dan membuka kunci depo. Status tidak dapat diubah kembali. Lanjutkan ?`)){
                this.approveOpname(this.opname.so_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.closeFormOpname();
                    }
                    else { alert (response.message) }
                });
            };
        },

        closeFormOpname() {
            this.clearOpname();  
            this.$emit('closed',true);
        },

        editDataOpname(id){
            this.clearOpname();
            this.dataOpname(id).then((response)=>{
                if (response.success == true) {
                    this.fillOpname(response.data);
                } else {
                    alert(response.message)
                }
            })
        },

        clearOpname(){
            this.opname.so_id = null;
            this.opname.depo_id = null;
            this.opname.depo_nama = null;
            this.opname.tgl_rencana = null;
            this.opname.tgl_so = null;
            this.opname.status = null;
            this.opname.catatan = null;
            this.isUpdate = false;
        },
        
        fillOpname(data){
            this.opname.so_id = data.so_id;
            this.opname.depo_id = data.depo_id;
            this.opname.depo_nama = data.depo_nama;
            this.opname.tgl_rencana = data.tgl_rencana;
            this.opname.tgl_so = data.tgl_so;
            this.opname.status = data.status;
            this.opname.catatan = data.catatan;
            this.urlSearch = `/stock/opname/${this.opname.depo_id}/detail`;
        },

    }
}
</script>
