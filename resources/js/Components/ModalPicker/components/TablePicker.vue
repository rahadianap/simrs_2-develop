<template>
    <div>
        <table class="uk-table uk-table-striped hims-picker-table uk-table-responsive">
            <thead class="uk-card uk-card-default uk-box-shadow-medium" style="border-top:1px solid #cc0202;">
                <tr>
                    <th>
                        <input v-if="isMultiSelect" class="uk-checkbox" type="checkbox" value="true" v-model="isSelectAll" @change="selectAllChange">
                    </th>
                    <th v-for="col in fieldDatas">{{ col.title }}</th>
                </tr>
            </thead>
            <tbody>                
                <tr v-if="listItems && listItems.length > 0" v-for="item in listItems">
                    <td>
                        <input class="uk-checkbox" type="checkbox" :value="item" v-model="datas" @change="dataChange">
                    </td>
                    <td v-for="col in fieldDatas">{{ getValue(item,col.name) }}</td>
                </tr>
                <tr v-else>
                    <td v-if="isLoading" :colspan="fieldDatas.length + 1">
                        <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">sedang mengambil data...</p>
                    </td>
                    <td v-else :colspan="fieldDatas.length + 1">
                        <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">Data tidak ditemukan</p>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr style="border-bottom:1px solid #cc0202;">
                    <td :colspan="fieldDatas.length + 1">
                        <picker-pagination ref="pagination" 
                            :pagination = pagination 
                            v-on:pageChange="pageChange" 
                            v-on:numRowsChange="rowChange">
                        </picker-pagination>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>
<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import httpReq from '@/Stores/httpReq';
import PickerPagination from '@/Components/ModalPicker/components/PickerPagination.vue';

export default {
    name : 'table-picker',
    components : { PickerPagination },
    props : {
        fieldDatas : { type:Object, required:true },
        listItems : { type:Object, required:false, default:[] },
        pagination : { type:Object, required:true },
        isMultiSelect : { type:Boolean, required:false, default:true },
    },
    data() {
        return {
            datas : [],
            isSelectAll : false,
            isLoading : false,
        }
    }, 
    computed : {
        ...mapGetters(['errors']),
    },
    watch : {
        listItems : function(newVal,oldVal){
            this.$refs.pagination.createPagination();
        }
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        
        selectAllChange(){
            if(this.isSelectAll) { 
                this.datas = this.listItems;
                this.$emit('dataChange',this.datas); 
            }
            else { 
                this.datas = []; 
                this.$emit('dataChange',this.datas); 
            }
        },

        setIsLoading(flag) {
            this.isLoading = flag;
        },

        getValue(row,col) {
            // let data = row;
            // let cols = col.split('.');
            // if(cols.length > 0) { cols.forEach(el => { data = data[el]; }); } 
            // else { data = row.col; }
            // return data;
            let data = row;
            let cols = col.split('.');
            if(cols.length > 0) { 
                cols.forEach(el => {
                    try { data = data[el]; } 
                    catch (error) { return ''; }
                }); 
            } 
            else {  data = row.col; }
            return data;
        }, 
        
        dataChange() {
            this.$emit('dataChange',this.datas); 
        },

        pageChange(data) {
            this.datas = [];
            this.$emit('dataChange',this.datas);
            this.isSelectAll = false;
            this.$emit('pageChange',data); 
        },

        rowChange(data) {
            this.datas = [];
            this.$emit('dataChange',this.datas);
            this.isSelectAll = false;
            this.$emit('numRowsChange',data); 
        }
    }

}
</script>
<style>
    
/* .hims-picker-table th {
    font-size:14px;
    font-weight:500; 
    color:black;
    padding:1em;
}

.hims-picker-table td {
    font-size:12px;
    font-weight:400; 
    color: black;
    padding:1em;
} */
</style>