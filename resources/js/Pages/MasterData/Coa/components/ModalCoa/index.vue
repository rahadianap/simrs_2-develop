<template>
    <div class="uk-modal uk-modal-container" :id="idModal">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Code Of Account</h2>
            <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                <p>{{ errors.invalid }}</p>
            </div>
            <div>
                <base-table ref="tableModalCoa"
                    :columns = "tbColumns" 
                    :config="configTable" 
                    :buttons = "buttons"
                    :urlSearch="searchUrl"  
                    v-on:updateListDataTable="updateListData">
                    <template v-slot:tbl-body>
                        <tbody v-if="dataLists" style="min-weight:50vh;">
                            <row-modal-coa
                                v-for="dt in dataLists" 
                                :rowData="dt"
                                :linkCallback = fnCallback>
                            </row-modal-coa>
                        </tbody>
                    </template>
                </base-table>
            </div>
        </div>
    </div>
</template>

<script>
import BaseTable from '@/Components/BaseTable';
export default {
    props : { 
        idModal : { type:String, required:true, },
    },
    name : 'modal-coa',
    components : {
        BaseTable,
    },
    methods : {
        updateListData(data) {

        },

        fnCallback(data){

        }
    }
}
</script>