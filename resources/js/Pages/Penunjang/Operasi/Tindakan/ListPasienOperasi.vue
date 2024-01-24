<template>
    <div>
        <header-page title="TINDAKAN OPERASI" subTitle="transaksi dan hasil operasi"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;" class="uk-responsive">
            <store-table ref="tableOps"
                :columns = "tbColumns" 
                :buttons = "buttons"
                :config = "configTable"
                :storeData = "operasiLists"
                :searchCallback = "listOperasi"
                :paramFilter = "operasiFilterTable">
                <template v-slot:tbl-body>
                    <tbody style="min-height:50vh;">                        
                        <row-registrasi-ops  v-if="operasiLists"
                            v-for="dt in operasiLists.data" 
                            :rowData="dt"
                            :linkCallback = viewHasilOps>
                        </row-registrasi-ops>
                    </tbody>
                </template>
                <template v-slot:form-filter>                    
                    <filter-operasi></filter-operasi>
                </template>
            </store-table>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import StoreTable from '@/Components/StoreTable';

import RowRegistrasiOps from '@/Pages/Penunjang/Operasi/components/RowRegistrasiOps.vue';
import ViewTindakanOps from '@/Pages/Penunjang/Operasi/Tindakan/ViewTindakanOps.vue';
import ModalRealisasiOps from '@/Pages/Penunjang/Operasi/Tindakan/ModalRealisasiOps.vue';
import FormRealisasiOps from '@/Pages/Penunjang/Operasi/Tindakan/FormRealisasiOps.vue';
import FilterOperasi from '@/Pages/Penunjang/Operasi/Tindakan/FilterOperasi.vue';

export default {
    name : 'list-pasien-operasi',
    
    components : {
        headerPage,
        StoreTable,
        RowRegistrasiOps,
        ViewTindakanOps,
        ModalRealisasiOps,
        FormRealisasiOps,
        FilterOperasi,
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('operasi',['operasiLists','operasiFilterTable'])
    },

    data(){
        return {
            isExpanded : true,
            isUpdate : false,
            configTable : { isExpandable : true, filterType : 'ADVANCED', },            
            tbColumns : [
                { name : 'registrasi', title : 'REG', colType : 'text', },
                { name : 'pasien', title : 'Pasien', colType : 'text', },
                { name : 'dokter_nama', title : 'Dokter', colType : 'text', },
                { name : 'unit_nama', title : 'Unit', colType : 'text', },
                { name : 'no_antrian', title : 'No', colType : 'text',},
                { name : 'status', title : 'Status', colType : 'text', },
                { name : 'transaksi.total', title : 'Total', colType : 'text',textAlign:'right' },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', },
            ], 
            buttons : [
                // { title : 'Data Baru', icon:'plus-circle', 'callback' : this.addRegistrasi },
            ],
            //searchUrl : '/operation/registrations',
            dataLists : null,
        }
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('operasi',['listOperasi']),


        addRegistrasi(){
            this.CLR_ERRORS();
            this.isUpdate = false;
            // UIkit.switcher('#switcherHasilOps').show(2);
            // UIkit.switcher('#switcherFormRegOps').show(0);
            // this.$refs.listAsalPasien.showMasterPasien();
            // this.$refs.formRegistrasiOps.newData();
            this.$router.push({ name: 'realisasiOperasi', params: { trxId: data.reg_id } }); 
        },

        viewHasilOps(data) {
            if(data) {
                console.log(data);
                this.$router.push({ name: 'realisasiOperasi', params: { trxId: data.trx_id } }); 
            }
        }
    }
}

</script>