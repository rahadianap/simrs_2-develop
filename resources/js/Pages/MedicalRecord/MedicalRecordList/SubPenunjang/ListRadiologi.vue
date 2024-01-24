<template>
    <div style="padding:0;margin:0;">
        <table class="uk-table uk-table-striped child-table1" style="padding:0;margin:0;">
            <thead>
                <th  class="tb-header-transaksi" colspan="5">PEMERIKSAAN RADIOLOGI</th>
            </thead>
            <!-- <thead>
                <th class="tb-header-transaksi" style="width:20px;text-align:center;color:white;"></th>
                <th class="tb-header-transaksi" style="width:200px;text-align:left;color:white;">ID</th>
                <th class="tb-header-transaksi" style="width:40px;text-align:center;color:white;">Status</th>
            </thead> -->
            <tbody>
                <row-sub-rad
                    v-if="data && data.rad" 
                    v-for="dt in data.rad"
                    :rowData = "dt">
                </row-sub-rad>
            </tbody>
        </table>
    </div>
</template>

<script>
import RowSubRad from '@/Pages/MedicalRecord/MedicalRecordList/SubPenunjang/RowSubRad.vue';

export default {
    name : 'list-radiologi',
    props : {
        data : { type:Object, required:false }
    },
    components : {
        RowSubRad,
    }
}
</script>