<template>
    <div style="padding:0;margin:0;">
        <table class="uk-table uk-table-striped1 child-table1" style="padding:0;margin:0;">
            <thead>
                <th class="tb-header-transaksi" colspan="4">PEMERIKSAAN</th>
            </thead>
            <tbody>
                <row-sub-pemeriksaan
                    v-if="data && data.pemeriksaan" 
                    v-for="dt in data.pemeriksaan"
                    :rowData = "dt">
                </row-sub-pemeriksaan>
            </tbody>
        </table>
    </div>
</template>

<script>
import RowSubPemeriksaan from '@/Pages/MedicalRecord/MedicalRecordList/SubPemeriksaan/RowSubPemeriksaan.vue';

export default {
    name : 'list-pemeriksaan',
    props : {
        data : { type:Object, required:false }
    },
    components : {
        RowSubPemeriksaan,
    }
}
</script>