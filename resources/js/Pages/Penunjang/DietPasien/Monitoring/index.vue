<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <ul id="switcherDiet" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">List</a></li>
            <li><a href="#">Form</a></li>
        </ul>
        <header-page title="MONITORING GIZI & DIET PASIEN" subTitle="Menu Monitoring Gizi dan Diet Pasien"></header-page>
        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                <p>{{ errors.invalid }}</p>
                </div>
                <div style="margin-top:1em;">
                    <base-table ref="tableMealOrder"
                        :columns = "tbColumns" 
                        :config="configTable"
                        :childColumns = "tbChildColumns"
                        childNameIndex = "detail_monitoring"
                        :rowFunctions = rowFunctions
                        :urlSearch="searchUrl">
                    </base-table>
                </div>
            </li>
            <li style="padding:0;margin:0;">
                <form-monitoring-diet ref="formMonitoringDiet" v-on:closed="formClosed" v-on:succeed="formClosed"></form-monitoring-diet>
            </li>
        </ul>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormMonitoringDiet from '@/Pages/Penunjang/DietPasien/Monitoring/FormMonitoringDiet.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormMonitoringDiet
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
                { 'title':'Proses', 'icon':'file-edit','callback':this.orderDiet },
            ],
            tbColumns : [
                { 
                    name : 'pasien_diet_id', 
                    title : 'ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Proses', 'icon':'file-edit','callback':this.orderDiet },
                    ],
                },          
                { name : 'trx_id', title : 'Transaksi ID', colType : 'text' },
                { name : 'nama_pasien', title : 'Nama Pasien', colType : 'text', },
                { name : 'status', title : 'Status', colType : 'text', },              
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', isSearchable : false, }  
                
            ],
            searchUrl : '/patient/diet/monitoring',
            tbChildColumns :[
                { name : 'nama_diet', title : 'Nama Diet', colType : 'text', textAlign : 'left',},
                { name : 'start_date', title : 'Tanggal', colType : 'text', textAlign : 'left', },
                { name : 'meal_set', title : 'Set', colType : 'text', textAlign : 'left', },
                { name : 'schedule', title : 'Jadwal', colType : 'text', textAlign : 'left', },
                { name : 'nama_menu', title : 'Nama Menu', colType : 'text', textAlign : 'left', },
                { name : 'qty', title : 'Qty', colType : 'text', textAlign : 'left', },  
                { name : 'status', title : 'Status', colType : 'text', textAlign : 'left', },
            ],
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('dietPasien',['approveDiet']),
        ...mapMutations(['CLR_ERRORS']),

        formClosed(){
            this.$refs.tableMealOrder.refreshTable();
            UIkit.switcher('#switcherDiet').show(0);
        },

        orderDiet(data) {
            this.$refs.formMonitoringDiet.editPengguna(data);
            UIkit.switcher('#switcherDiet').show(1);
        }
    }
}
</script>
