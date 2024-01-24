<template>
    <div style="padding:0;margin:0;">        
        <div class="uk-grid-small" uk-grid style="padding:0.5em 1em 0.5em 0;margin:0;">            
            <div class="uk-width-auto@m">
                <button type="button" @click.prevent="TambahAsesmenInap">Tambah SOAP</button>
            </div>
        </div>
        <div style="padding:0 0.5em 0.2em 0.5em;">
            <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                <thead>
                    <th class="tb-header-reg" style="width:20px;text-align:center;color:white;background-color: #cc0202;"></th>
                    <th class="tb-header-reg" style="width:200px;text-align:left;color:white;background-color: #cc0202;">ID</th>
                    <th class="tb-header-reg" style="width:120px;text-align:left;color:white;background-color: #cc0202;">Tanggal</th>
                    <th class="tb-header-reg" style="text-align:left;color:white;background-color: #cc0202;">Dokter</th>
                    <th class="tb-header-reg" style="width:40px;text-align:center;color:white;background-color: #cc0202;">Status</th>
                    <th class="tb-header-reg" style="width:120px;text-align:center;color:white;background-color: #cc0202;">...</th>
                </thead>
                <tbody>
                    <row-list-soap-inap
                        v-if="inpatientAssesments" 
                        v-for="dt in inpatientAssesments"
                        :rowData = "dt"
                        :linkCallback = "EditAsesmenInap">
                    </row-list-soap-inap>
                    <tr v-else>
                        <td colspan="7" style="font-style:italic;font-size:12px;color:black;">data tidak ditemukan</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <modal-soap-inap ref="modalAsesmenInap" modalId="modalAsesmenInap"></modal-soap-inap>    
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import RowListSoapInap from '@/Pages/RawatInap/Pemeriksaan/ListPemeriksaanInap/SubListSoapInap/RowListSoapInap.vue';
import ModalSoapInap from '@/Pages/RawatInap/Pemeriksaan/ListPemeriksaanInap/SubListSoapInap/ModalSoapInap.vue';

export default {
    name : 'sub-list-soap-inap',
    props : {
        fnRefresh  : { type:Function, required:false },
    },
    components : { 
        RowListSoapInap, 
        ModalSoapInap 
    },
    computed : {
        ...mapGetters('rawatInap',['inpatientTransaction','inpatientAssesments']),
    },

    methods : {
        EditAsesmenInap(data){
            if(data){ 
                this.$refs.modalAsesmenInap.editAsesmen(data); 
            }
        },
        
        TambahAsesmenInap(){
            this.$refs.modalAsesmenInap.addAsesmen(this.inpatientTransaction);
        },
    }
}
</script>