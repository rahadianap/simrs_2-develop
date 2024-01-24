<template>
    <div class="uk-grid-small" uk-grid style="padding:0.5em 1em 0.5em 0;margin:0;">
        <div class="uk-width-auto@m">
            <button @click.prevent="fnAddCallback">Tambah Resep</button>
        </div>                                        
    </div>
    <div style="padding:0 0.5em 0.2em 0.5em;">
        <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
            <thead>
                <th class="tb-header-reg" style="width:20px;text-align:center;color:white;"></th>
                <th class="tb-header-reg" style="width:200px;text-align:left;color:white;">Item</th>
                <th class="tb-header-reg" style="width:120px;text-align:left;color:white;">Tanggal</th>
                <th class="tb-header-reg" style="text-align:left;color:white;">Dokter</th>
                <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Total</th>                                                
                <th class="tb-header-reg" style="width:40px;text-align:center;color:white;">Status</th>
                <th class="tb-header-reg" style="width:120px;text-align:center;color:white;">...</th>
            </thead>
            <tbody>
                <row-list-resep 
                    v-if="poliResepLists" 
                    v-for="dt in poliResepLists"
                    :rowData = "dt"
                    :linkCallback = "fnEditCallback">
                </row-list-resep>
            </tbody>
        </table>
    </div>
    
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import RowFormResep from '@/Pages/RawatJalan/Pemeriksaan/SubFormResep/RowFormResep.vue';
import RowListResep from '@/Pages/RawatJalan/Pemeriksaan/SubFormResep/RowListResep.vue';
import ModalResep from '@/Pages/RawatJalan/components/ModalResep';
import ModalSigna from '@/Pages/RawatJalan/components/ModalSigna';

export default {
    name : 'sub-list-resep',
    props : {
        fnAddCallback : { type:Function, required:true },
        fnEditCallback : { type:Function, required:true },
    },
    components : {
        RowFormResep,
        RowListResep,
        ModalResep,
        ModalSigna,
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatJalan',['poliTransaction','poliItemPrescripts','poliResepLists']),
        ...mapGetters('resep',['prescriptionItems','prescription']),
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('rawatJalan',['CLR_POLI_TRANSACTION','SET_POLI_TRANSACTION','SET_ITEM_PRESCRIPTS']),
        ...mapMutations('resep',['CLR_PRESCRIPTION','NEW_PRESCRIPTION','SET_PRESCRIPTION','SET_PRESCRIPTION_ITEMS']),
        ...mapActions('resep',['createPrescription','updatePrescription','dataPrescription']),
        ...mapActions('rawatJalan',['dataTransaksiPoli']),

        addResepBaru(){
            alert('add resep baru');
        },

        editResep(){
            alert('edit resep');
        }
    }
}
</script>