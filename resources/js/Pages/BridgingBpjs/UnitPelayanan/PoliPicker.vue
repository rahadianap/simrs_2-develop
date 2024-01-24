<template>
    <div class="uk-modal uk-modal-container" :id="idModal">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Pencarian Kode Bridging</h2>
            <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                <p>{{ errors.invalid }}</p>
            </div>
            <div class="uk-grid-small hims-table-filter-container" uk-grid style="margin:0;padding:0.5em;">            
                <div class="uk-width-auto" style="margin:0;padding:0 0.5em 0 0.5em;">
                    <div class="uk-grid-small uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0;">
                        <form action="" @submit.prevent="retrieveData" style="padding:0;margin:0;">
                            <input type="text" v-model="keyword" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:0;" placeholder="kata pencarian">
                            <button type="submit" class="hims-table-btn uk-width-auto" style="background-color:#cc0202;color:#fff;"><span uk-icon="search"></span> Cari</button>
                        </form>
                    </div>                    
                </div>
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
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import TablePicker from '@/Components/ModalPicker/components/TablePicker.vue';
import ListPicker from '@/Components/ModalPicker/components/ListPicker.vue';
//import RoomPickerPagination from '@/Pages/RawatInap/Components/ModalPickerRuang/RoomPickerPagination.vue';

export default {
    name : 'poli-picker',
    props : {
        title : {type:String , required:false, default:'' },
        fieldDatas : { type:Object, required:true },
        proceedFunction : { type:Function, required:true },
        isMultiSelect : { type:Boolean, required:false, default:true },
    },

    components : {
        TablePicker,
        ListPicker, 
        //RoomPickerPagination
    },
    data() {
        return {
            keyword : 'ANA',
            listItems : [],
            datas : [],
            selectedDatas : null,
            idModal : 'poliPickerModal',
            isSelectAll : false,
            isTableView : true,
            pagination : { current_page :0, per_page:10, total_data:0, num_pages:0, },
            urlData : '',
            dataTipe : '',
        }
    },
    watch :{ 
    },
    computed : {
        ...mapGetters(['errors']),
    },
    mounted() {
        this.initialize();
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('bpjsReferensi',['refBpjsPoli']),
        ...mapActions('jknAntrian',['jknRefPoli']),

        initialize() {
            this.idModal = `modalpicker-${Date.now()}`;
        },

        showModalPicker(tipeData) {
            this.dataTipe = tipeData;
            this.retrieveData(this.dataTipe);
            UIkit.modal(`#${this.idModal}`).show();
        },

        closeModalPicker() {
            UIkit.modal(`#${this.idModal}`).hide();
        },

        /**ambil data berdasarkan url search yang diberikan**/
        retrieveData(tipeData) {
            if(this.keyword == null || this.keyword == '') { this.keyword = 'NON'; } 
            this.CLR_ERRORS();
            this.listItems = null;
            if(tipeData == 'BPJS') {
                this.refBpjsPoli(this.keyword).then((response) => {
                    if (response.success == true) {
                        let items = response.data;
                        this.listItems = items.sort(function(a, b){return a.kode - b.kode});
                    }
                });
            }
            else if(tipeData == 'JKN') {
                this.jknRefPoli().then((response) => {
                    if (response.success == true) {
                        let items = response.data;
                        this.listItems = items.sort(function(a, b){return a.nmpoli - b.nmpoli});
                    }
                });
            }
            
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
    }

}
</script>
