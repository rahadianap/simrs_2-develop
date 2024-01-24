<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;" v-bind:class="rowData.is_aktif != true ? 'inaktif':'' ">
        <td style="width:150px; font-size:12px; color:black; padding:0.5em; margin:0;">
            <p style="margin:0; padding:0; font-weight: 500;">{{rowData.dokter_nama}}</p>
            <p style="font-size:11px; margin:0; padding:0; font-weight:300; font-style:italic;">{{rowData.dokter_id}}</p>
            <!-- {{rowData.dokter_id}} -->
        </td>
        <!-- <td style="font-size:12px;color:black; padding:0.5em;margin:0;">{{rowData.dokter_nama}}</td> -->
        <td style="width:200px;font-size:12px;color:black;padding:0.5em;margin:0;">
            <select v-model="rowData.posisi" class="uk-select uk-form-small" style="border:1px solid #cc0202;color:black;" @change="posisiChange(rowData)"> 
                <option v-for="pos in timOperasiRefs.json_data" v-bind:value="pos.value" v-bind:key="pos.value">{{pos.text}}</option>
                <option value=""></option>
            </select>
        </td>
        <td style="width:50px;text-align: center;padding:0.3em 0.2em 0.3em 0.2em;margin:0;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_operator" disabled style="border:1px solid black;">
        </td>
        <td style="font-size:12px;color:black; padding:0.5em;margin:0;">
            <search-list
                :config = configItemSelect
                :dataLists = subTindakanLists
                placeholder = "pilih sub tindakan paket..."
                captionField = "sub_tindakan_nama"
                valueField = "sub_tindakan_nama"
                :detailItems = actDesc
                :value = "rowData.tindakan_nama"
                v-on:item-select = "rowActSelected"
            ></search-list>    
        </td>
        <td style="width:50px;text-align: center;padding:0.3em 0.2em 0.3em 0.2em;margin:0;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" style="border:1px solid black;" @change="activeChange(rowData)">
        </td>
    </tr>
</template>

<script>

import { mapGetters,mapState,mapMutations,mapActions } from 'vuex';
import SearchList from '@/Components/SearchList.vue';

export default {
    props : {
        rowData : { type: Object, required:false },
        subTindakanLists : { type: Object, required: false },
        activeChange : { type:Function, required: false }
    },

    name : 'sub-row-tim',
    components : { 
        SearchList 
    },

    data() {
        return {
            configItemSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            actDesc : [
                { colName : 'sub_tindakan_nama', labelData : '', isTitle : true },
                { colName : 'sub_tindakan_id', labelData : '', isTitle : false },
            ],
        }
    },
    
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('referensi',['isRefExist','jenisOperasiRefs','skalaOperasiRefs','timOperasiRefs',]),   
    }, 
    
    methods : {
        rowActSelected(data){
            if(data) {
                this.rowData.tindakan_id = data.sub_tindakan_id;
                this.rowData.tindakan_nama = data.sub_tindakan_nama;                   
            }
        },
    }
}
</script>