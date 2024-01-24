<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <ul id="switcherDiet" class="uk-subnav uk-subnav-pill uk-hidden" uk-switcher style="padding:0;margin:0;">
            <li><a href="#">List</a></li>
            <li><a href="#">Form</a></li>
        </ul>
        <header-page title="DIET PASIEN" subTitle="Modul Transaksi Diet Pasien"></header-page>
        <ul class="uk-switcher" style="padding:0;margin:0;">
            <li style="padding:0;margin:0;">
                <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                <p>{{ errors.invalid }}</p>
                </div>
                <div style="margin-top:1em;">
                    <base-table ref="tableRawatInap"
                        :columns = "tbColumns" 
                        :childColumns = "tbChildColumns"
                        childNameIndex = "detail_diet"
                        :config="configTable"
                        :urlSearch="searchUrl">
                        <template v-slot:form-filter="slotProps" >
                            <filter-diet-pasien></filter-diet-pasien>
                        </template>
                    </base-table>
                </div>
            </li>
            <li style="padding:0;margin:0;">
                <form-diet-pasien ref="formDietPasien" v-on:closed="formClosed" v-on:succeed="formClosed"></form-diet-pasien>
            </li>
        </ul>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import FormDietPasien from '@/Pages/Penunjang/DietPasien/FormDietPasien.vue';
import FilterDietPasien from '@/Pages/Penunjang/DietPasien/FilterDietPasien.vue';

export default {
    components : {
        headerPage,
        BaseTable,
        FormDietPasien,
        FilterDietPasien
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
                filterType : 'ADVANCED', 
            },
            // rowFunction : [ { 'title':'Ubah Data Pengguna', 'icon':'ban','callback':this.editFunction },],
            tbColumns : [                
                // { name : 'trx_id', title : 'Transaksi ID', colType : 'link', linkCallback: this.addDiet },
                { 
                    name : 'trx_id', 
                    title : 'Transaksi ID', 
                    colType : 'linkmenus', 
                    functions: [
                        { 'title':'Tambah Diet', 'icon':'plus','callback':this.addDiet },
                        { 'title':'Ubah Diet', 'icon':'file-edit','callback':this.editDiet },
                    ],
                },   
                { name : 'no_rm', title : 'No. RM', colType : 'text', }, 
                { name : 'nama_pasien', title : 'Nama Pasien', colType : 'text', },              
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', isSearchable : false, }  
                
            ],
            tbChildColumns :[
                // { name : 'pasien_diet_id', title : 'Diet ID', colType : 'linkmenus', textAlign : 'left',
                // functions: [
                //     { 'title':'Approve', 'icon':'check','callback':this.approvalDiet },
                // ]
                // },
                { name : 'nama_diet', title : 'Nama Diet', colType : 'linkmenus', textAlign : 'left',
                    functions: [
                        { 'title':'Approve', 'icon':'check','callback':this.approvalDiet },
                    ]
                },
                { name : 'start_date', title : 'Tanggal', colType : 'text', textAlign : 'left', },
                { name : 'meal_set', title : 'Set', colType : 'text', textAlign : 'left', },
                { name : 'schedule', title : 'Jadwal', colType : 'text', textAlign : 'left', },
                { name : 'nama_menu', title : 'Nama Menu', colType : 'text', textAlign : 'left', },
                { name : 'qty', title : 'Qty', colType : 'text', textAlign : 'left', },  
                { name : 'status', title : 'Status', colType : 'text', textAlign : 'left', },
            ],
            searchUrl : '/patient/diet'
         }
    },
    mounted() {
    },

    methods : {
        ...mapActions('dietPasien',['approveDiet']),
        ...mapMutations(['CLR_ERRORS']),

        addDiet(data){
            this.$refs.formDietPasien.newDiet(data);
            UIkit.switcher('#switcherDiet').show(1);
        },

        editDiet(data){
            this.$refs.formDietPasien.editDiet(data);
            UIkit.switcher('#switcherDiet').show(1);
        },

        approvalDiet(data) {
            this.CLR_ERRORS();
            if(confirm(`Approve menu diet untuk transaksi ini?`)){
            this.approveDiet(data.pasien_diet_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tableRawatInap.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        },

        formClosed(){
            this.$refs.tableRawatInap.refreshTable();
            UIkit.switcher('#switcherDiet').show(0);
        },


    }
}
</script>