<template>
    <div class="uk-modal uk-modal-container" :id="idModal">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Pencarian Template Inform Consent</h2>
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
                            <tr v-if="!isLoading" v-for="item in informedLists.data">
                                <td><p style="font-weight: 500;">{{ item.template_id }}</p></td>
                                <td><p style="font-weight: 400;">{{ item.template_nama }}</p></td>
                                <td><p style="font-weight: 500;">{{ item.deskripsi }}</p></td>
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
    name : 'inform-picker',
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
            isLoading : false,
            keyword : '',
            listItems : [],
            bufferItems : [],
            datas : [],
            selectedDatas : null,
            idModal : 'informPickerModal',
            isSelectAll : false,
            isTableView : true,
            //pagination : { current_page :1, per_page:10, total_data:0, num_pages:0,keyword:null },
            urlData : '/informed',
            dataTipe : '',
        }
    },
    watch :{ 
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('informedConsent',['informedLists']),
    },
    mounted() {
        this.initialize();
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('bpjsReferensi',['refBpjsDokter']),
        ...mapActions('jknAntrian',['jknRefPoli']),

        ...mapActions('informedConsent',['listInformed','deleteInformed']),

        initialize() {
            this.idModal = `modalpicker-${Date.now()}`;
        },

        showModalPicker() {            
            this.retrieveData();
            UIkit.modal(`#${this.idModal}`).show();
        },

        closeModalPicker() {
            UIkit.modal(`#${this.idModal}`).hide();
        },

        /**ambil data berdasarkan url search yang diberikan**/
        retrieveData() {
            this.CLR_ERRORS();
            this.listItems = null;
            this.listInformed({'keyword':this.keyword}).then((response) => {
                    if (response.success == true) {
                        // alert (response.message);
                        // console.log(this.informedLists);
                    }
                    else { alert (response.message) }
                });;
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
