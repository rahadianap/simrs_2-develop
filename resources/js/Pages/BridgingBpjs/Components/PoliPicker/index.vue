<template>
    <div class="uk-modal uk-modal-container" :id="idModal">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">{{title}}</h2>
            <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                <p>{{ errors.invalid }}</p>
            </div>
            <div class="uk-grid-small hims-table-filter-container" uk-grid style="margin:0;padding:0.5em;">            
                <div class="uk-width-auto" style="margin:0;padding:0 0.5em 0 0.5em;">
                    <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                        <!-- <form action="" @submit.prevent="retrieveData" style="padding:0;margin:0;">
                            <input type="text" v-model="params.keyword" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:0;" placeholder="kata pencarian">
                            <button type="submit" class="hims-table-btn uk-width-auto" style="background-color:#cc0202;color:#fff;"><span uk-icon="search"></span> Cari</button>
                        </form> -->
                    </div>                    
                </div>
                <!-- <div class="uk-width-expand" style="text-align:right;">
                    <div class="uk-button-group uk-box-shadow-large" style="padding:0;margin:0;border-radius:5px;">
                        <button class="hims-table-btn" @click.prevent="changeView(false)"><span uk-icon="thumbnails"></span></button>
                        <button class="hims-table-btn" @click.prevent="changeView(true)"><span uk-icon="table"></span></button>
                    </div>
                </div> -->
            </div>
            <div style="margin:1em 0 0 0;padding:0;" class="uk-overflow-auto">                
                <div>
                    <table class="uk-table uk-table-striped hims-picker-table uk-table-responsive">
                        <thead class="uk-card uk-card-default uk-box-shadow-medium" style="border-top:1px solid #cc0202;">
                            <tr>
                                <th v-for="col in fieldDatas">{{ col.title }}</th>
                                <th>...</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="listItems && listItems.length > 0" v-for="item in listItems">
                                <td v-for="col in fieldDatas">
                                    <p v-if = "col.isCaption" style="font-weight: 500;">{{ getValue(item,col.name) }}</p>
                                    <p v-else>{{ getValue(item,col.name) }}</p>
                                </td>
                                <td style="width:80px;"><button @click.prevent="proceedFunction(item)"><span uk-icon="check"></span> pilih</button></td>
                            </tr>
                            <tr v-else>
                                <td :colspan="fieldDatas.length + 1" style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">sedang mengambil data...</td>
                            </tr>
                        </tbody>
                        <!-- <tfoot>
                            <tr style="border-bottom:1px solid #cc0202;">
                                <td :colspan="fieldDatas.length + 1">
                                    <room-picker-pagination ref="pagination" 
                                        :pagination = pagination 
                                        v-on:pageChange="pageChange" 
                                        v-on:numRowsChange="rowChange">
                                    </room-picker-pagination>
                                </td>
                            </tr>
                        </tfoot> -->
                    </table>
                </div>
            </div>
            <!-- <div style="text-align:center;padding:1em 0 0 0;">
                <button @click.prevent="proceedFunction(selectedDatas)" class="uk-button uk-button-small">Proses Data</button>
            </div>  -->
        </div>
    </div>
</template>
<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import httpReq from '@/Stores/httpReq';
import TablePicker from '@/Components/ModalPicker/components/TablePicker.vue';
import ListPicker from '@/Components/ModalPicker/components/ListPicker.vue';
import RoomPickerPagination from '@/Pages/RawatInap/Components/ModalPickerRuang/RoomPickerPagination.vue';

export default {
    name : 'poli-picker',
    props : {
        //listItems : { type:Object, required: false, default:null },
        title : {type:String , required:false, default:'' },
        //urlSearch : { type:String, required:true, },
        //retrieveAll : { type:Boolean, required:false, default:false },
        fieldDatas : { type:Object, required:true },
        proceedFunction : { type:Function, required:true },
        isMultiSelect : { type:Boolean, required:false, default:true },
    },

    components : {
        TablePicker,
        ListPicker, 
        RoomPickerPagination
    },
    data() {
        return {
            params : {
                keyword : '',
                per_page: 10,
                page : 1,
            },
            listItems : [],
            datas : [],
            selectedDatas : null,
            idModal : 'poliPickerModal',
            isSelectAll : false,
            isTableView : true,
            pagination : { current_page :0, per_page:10, total_data:0, num_pages:0, },
            urlData : '',
        }
    },
    watch :{ 
        // 'urlSearch': function(newVal, oldVal) {
        //     if(newVal !== null && newVal !== '') { this.urlData = newVal; }
        //     else { this.urlData = ''; }
        // },
    },
    computed : {
        ...mapGetters(['errors']),
    },
    mounted() {
        this.initialize();
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            this.idModal = `modalpicker-${Date.now()}`;
        },

        changeView(data){
            this.isTableView = data;
        },
        rowNumberChange(data){
            this.params.per_page = data;
            //this.retrieveData();
        },
        pageChange(data) {
            this.params.page = data;
            //this.retrieveData();
        },

        selectedDataChange(data){
            this.selectedDatas = data;
        },

        dataChange(data) {
            this.selectedDatas = data;
        },

        showModalPicker(items) {
            this.listItems = items.sort(function(a, b){return a.kode - b.kode});
            // if(url) { this.urlData = url };
            // this.retrieveData();
            UIkit.modal(`#${this.idModal}`).show();
        },

        closeModalPicker() {
            UIkit.modal(`#${this.idModal}`).hide();
        },

        selectAllChange(){
            if(this.isSelectAll) {
                this.datas = this.listItems;
            }
            else {
                this.datas = [];
            }
        },

        /**ambil data berdasarkan url search yang diberikan**/
        retrieveData() {
            // this.CLR_ERRORS();  
            // this.listItems = [];
            // if(this.retrieveAll) { this.params.per_page = 'ALL'; }
            // if(this.urlData == '' || this.urlData == null) { return false; }
            
            // httpReq.get(this.urlData, { params : this.params })
            // .then((response) => {
            //     if (response.data.success == false) {
            //         this.$store.commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
            //     } else {
            //         this.listItems = response.data.data.data;
            //         this.pagination.current_page = response.data.data.current_page;
            //         this.pagination.last_page = response.data.data.last_page;
            //         this.pagination.per_page = response.data.data.per_page;
            //         this.pagination.total_data = response.data.data.total;
            //         this.pagination.num_pages = response.data.data.last_page;
            //     }
            // })            
            // .catch((error) => {
            //     this.$store.commit('SET_ERRORS', { invalid: error }, { root: true });
            // })
        },

        nextPage() {
            // this.params.page = this.params.page + 1;
            // this.retrieveData();
        },

        getValue(row,col) {
            try {
                let data = row;
                let cols = col.split('.');
                if(cols.length > 0) { cols.forEach(el => { data = data[el]; }); } 
                else { data = row.col; }
                return data;
            }
            catch(err){}
        },

        rowChange(data) {
            // this.datas = [];
            // this.$emit('dataChange',this.datas);
            // this.isSelectAll = false;
            // this.$emit('numRowsChange',data); 
        },

        dataSelected(data){
            if(data) {

            }
        }
    }

}
</script>
