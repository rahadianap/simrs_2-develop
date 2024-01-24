<template>
    <div style="padding:0;margin:0;">
        <table class="uk-table uk-table-striped1 child-table1" style="padding:0;margin:0;">
            <thead>
                <th class="tb-header-transaksi" colspan="4">PEMERIKSAAN LABORATORIUM</th>
            </thead>
            <tbody>
                <row-sub-lab
                    v-if="data && data.lab" 
                    v-for="dt in data.lab"
                    :rowData = "dt">
                </row-sub-lab>
            </tbody>
        </table>
    </div>
</template>

<script>
import RowSubLab from '@/Pages/MedicalRecord/MedicalRecordList/SubPenunjang/RowSubLab.vue';

export default {
    name : 'list-laboratorium',
    props : {
        data : { type:Object, required:false }
    },
    components : {
        RowSubLab,
    }
}
</script>