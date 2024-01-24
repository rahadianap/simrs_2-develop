<template>
    <!-- <div style="padding:0;margin:0;">         -->
        <div class="uk-grid-small" uk-grid style="padding:0.5em 1em 0.5em 0;margin:0;">            
            <div class="uk-width-auto@m">
                <button @click.prevent="fnAddCallback">Tambah Pemeriksaan</button>
            </div>                                        
        </div>
        <div style="padding:0 0.5em 0.2em 0.5em;">
            <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                <thead>
                    <th class="tb-header-reg" style="width:20px;text-align:center;color:white;"></th>
                    <th class="tb-header-reg" style="width:200px;text-align:left;color:white;">ID</th>
                    <th class="tb-header-reg" style="width:120px;text-align:left;color:white;">Tanggal</th>
                    <th class="tb-header-reg" style="text-align:left;color:white;">Dokter</th>
                    <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Total</th>                                                
                    <th class="tb-header-reg" style="width:40px;text-align:center;color:white;">Status</th>
                    <th class="tb-header-reg" style="width:120px;text-align:center;color:white;">...</th>
                </thead>
                <tbody>
                    <row-list-pemeriksaan 
                        v-if="poliExamination" 
                        v-for="dt in poliExamination"
                        :rowData = "dt"
                        :linkCallback = "fnEditCallback">
                    </row-list-pemeriksaan>
                    <tr v-else>
                        <td colspan="7" style="font-style:italic;font-size:12px;color:black;">data tidak ditemukan</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
    <!-- </div> -->
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import RowListPemeriksaan from '@/Pages/RawatJalan/Pemeriksaan/ListPemeriksaan/RowListPemeriksaan.vue';

export default {
    name : 'sub-list-pemeriksaan',
    props : {
        fnAddCallback : { type:Function, required:true },
        fnEditCallback : { type:Function, required:true },
    },
    components : { RowListPemeriksaan },
    computed : {
        ...mapGetters('rawatJalan',['poliTransaction','poliExamination']),
    },
}
</script>