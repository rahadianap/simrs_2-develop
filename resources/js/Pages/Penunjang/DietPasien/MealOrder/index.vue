<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="MEAL ORDER" subTitle="Menu Transaksi Order Diet Pasien"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <base-table ref="tableMealOrder"
                :columns = "tbColumns" 
                :childColumns = "tbChildColumns"
                childNameIndex = "detail_diet"
                :config="configTable"
                :rowFunctions = rowFunctions
                :urlSearch="searchUrl">
            </base-table>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';

export default {
    components : {
        headerPage,
        BaseTable,
    },
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isUpdate : false,
            isExpanded : false,
            configTable : {                
                isExpandable : true,
                filterType : 'SIMPLE', 
            },
            rowFunctions : [
                { 'title':'Proses', 'icon':'file-edit','callback':this.orderedDiet },
            ],
            tbColumns : [
                { 
                    name : 'pasien_diet_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Proses', 'icon':'file-edit','callback':this.orderedDiet },
                    ],
                },          
                { name : 'trx_id', title : 'Transaksi ID', colType : 'text' },
                { name : 'nama_pasien', title : 'Nama Pasien', colType : 'text', },
                { name : 'status', title : 'Status', colType : 'text', },              
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', isSearchable : false, }  
                
            ],
            searchUrl : '/mealorder',
            tbChildColumns :[
                { name : 'nama_diet', title : 'Nama Diet', colType : 'text', textAlign : 'left', },
                { name : 'nama_makanan', title : 'Nama Makanan', colType : 'text', textAlign : 'left', },
                { name : 'schedule', title : 'Jadwal', colType : 'text', textAlign : 'left', },
                { name : 'qty', title : 'Qty', colType : 'text', textAlign : 'left', },  
            ],
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('dietPasien',['orderDiet']),
        ...mapMutations(['CLR_ERRORS']),

        formClosed(){
            this.$refs.tableMealOrder.refreshTable();
            UIkit.switcher('#switcherDiet').show(0);
        },

        orderedDiet(data) {
            this.CLR_ERRORS();
            if(confirm(`Order menu diet untuk transaksi ini?`)){
            console.log(data);
            this.orderDiet(data.pasien_diet_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tablePurchaseRequest.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>