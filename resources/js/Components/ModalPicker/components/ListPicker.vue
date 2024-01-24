<template>
    <div>
        <div style="padding:0 0.5em 0.5em 0.5em;">
            <label style="font-size:14px;color:black;">
                <input class="uk-checkbox" type="checkbox" value="true" v-model="isSelectAll" @change="selectAllChange"> Pilih semua item
            </label>
        </div>
        <div class="uk-grid-small" uk-grid>                
            <div class="uk-width-1-3@m" v-for="item in listItems" style="padding:0.2em;">
                <div class="uk-width-1-1@m uk-card uk-card-default uk-grid-small" uk-grid style="margin:0;padding:0.2em;border-top:1px solid #333;">
                    <div class="uk-form-controls uk-width-auto">
                        <label style="color:black; font-size:14px;">
                            <input class="uk-checkbox" type="checkbox" :value="item" v-model="datas" @change="dataChange">
                        </label>
                    </div>
                    <div class="uk-width-expand">
                        <p v-for="col in columns" style="padding:0;margin:0;">
                            <span  v-if="col.isCaption" style="color:black; font-weight:500;font-size:14px;padding:0;margin:0;" class="uk-text-uppercase">{{ getValue(item,col.name) }}</span>
                            <span  v-else style="font-size:11px; color:black;padding:0;margin:0;">{{ getValue(item,col.name) }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <picker-pagination ref="pagination" 
                :pagination = pagination 
                v-on:pageChange="pageChange" 
                v-on:numRowsChange="rowChange">
            </picker-pagination>
        </div>
    </div>
       
</template>
<script>    
import { mapGetters, mapMutations, mapActions } from 'vuex';
import httpReq from '@/Stores/httpReq';
import PickerPagination from '@/Components/ModalPicker/components/PickerPagination.vue';


export default {
    name : 'list-picker',
    components : { PickerPagination },
    props : {
        fieldDatas : { type:Object, required:true },
        listItems : { type:Object, required:false, default:[] },
        pagination : { type:Object, required:true },
    },
    data() {
        return {
            datas : [],
            isSelectAll : false,
            columns : [],
        }
    }, 
    computed : {
        ...mapGetters(['errors']),
    },
    watch : {
        listItems : function(newVal,oldVal){
            this.$refs.pagination.createPagination();
        },
        fieldDatas : function(newVal,oldVal){
            this.sortColumns();
            alert('field data change');
        },

    },
    mounted() {
        this.sortColumns();
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

        sortColumns(){
            this.columns = JSON.parse(JSON.stringify(this.fieldDatas));
            this.columns.sort(function(x, y) {
                // true values first
                return (x.isCaption === y.isCaption)? 0 : x? -1 : 1;
                // false values first
                // return (x === y)? 0 : x? 1 : -1;
            });
        },

        getValue(row,col) {
            let data = row;
            let cols = col.split('.');
            if(cols.length > 0) { cols.forEach(el => { data = data[el]; }); } 
            else { data = row.col; }
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

</style>