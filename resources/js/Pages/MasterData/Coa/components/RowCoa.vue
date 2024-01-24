
<template>
    <tr v-if="rowData" style="border-bottom:1px solid #eee;">        
        <td style="background-color:#fafafa;border:none;width: 170px;">
            <div style="margin:0;padding:0;">
                <a href="#" class="row-link-menu">
                    {{rowData.coa_id}}
                </a>
                <div uk-dropdown="mode: click">
                    <ul class="uk-list" style="margin:0;padding:0;">
                        <li v-if="rowFunctions" v-for="rf in rowFunctions">
                            <a href="#" @click.prevent="rf.callback(rowData)" style="color:black;font-size:12px;display:block;line-height:30px;font-weight:400;">
                                <span :uk-icon="rf.icon" style="font-size:10px;margin-right:5px;"></span><span>{{rf.title}}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>    
        </td>
        <td style="width:90px;">
            <p v-if="rowData.level_coa < 2" v-bind:style="{ margin:0,padding:0,fontWeight:600, }" class="uk-text-uppercase">{{rowData.kode_coa}}</p>
            <p v-else v-bind:style="{ margin:0,padding:0, }">{{rowData.kode_coa}}</p>
        </td> 
        <td>
            <p v-if="rowData.level_coa < 2" v-bind:style="{ margin:0,padding:0,fontWeight:600, }" class="uk-text-uppercase">{{rowData.nama_coa}}</p>
            <p v-else v-bind:style="{ margin:0,padding:0, }">{{rowData.nama_coa}}</p>
        </td> 
        <td style="max-width:40px;">
            <p v-if="rowData.nilai_normal == 'D'" class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 400;text-align:center;">DEBIT</p>
            <p v-else-if="rowData.nilai_normal == 'K'" class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 400;text-align:center;">KREDIT</p>
            <p v-else class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 400;text-align:center;">-</p>
        </td>
        <td>
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 400;text-align:center;">{{rowData.level_coa}}</p>
        </td>
        <td style="max-width: 220px;">
            <p style="margin:0;padding:0;font-size:12px;">{{rowData.coa_header}}</p>
            <!-- <p style="font-size:9px; margin:0;padding:0;font-weight: 400;">{{rowData.coa_header_id}}</p>      -->
        </td> 
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" disabled style="margin-left:5px;border:1px solid black;">
        </td>
    </tr>
    
</template>
<script>
export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        rowFunctions : { type:Object, required:false, default:null }
    },
    name : 'row-coa',
    data() {
        return {
            isExpanded : false,
            buffer : [],
        }
    },
    methods : {
        changeRowExpand() {
            this.isExpanded = !this.isExpanded;
            return false;
        },

        dateFormatting(data) {
            let d = new Date(data);
            let val = d.toLocaleDateString();
            return val;
        }

    }
}
</script>

<!-- <template>
    <tr>
        <td v-if="isExpandable">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1.5" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1.5" @click.prevent="changeRowExpand"></a>
        </td> 
        <td v-else style="background-color:#fafafa;border:none;width:5px;padding:0;"></td>

        <template v-for="col in columns">
            <td v-if="col.colType == 'link'" class="row-link" @click.prevent="col.linkCallback" :style="getStyleCoa(rowData,col.name)">{{ getValue(rowData,col.name) }}</td> 
            <td v-else-if="col.colType == 'label'" :style="getStyleCoa(rowData,col.name)">{{getValue(rowData,col.name)}}</td>
            <td v-else-if="col.colType == 'boolean'" :style="getStyleCoa(rowData,col.name)">
                <input class="uk-checkbox" type="checkbox" v-model="rowData[col.name]" disabled style="margin-left:5px;border:1px solid black;">
            </td>            
            <td v-else-if="col.colType == 'linkmenus'" :style="getStyleCoa(rowData,col.name)">
                <div class="uk-inline">
                    <a href="#" class="row-link-menu">{{ getValue(rowData,col.name) }}</a>
                    <div uk-dropdown="mode: click">
                        <ul class="uk-list" style="margin:0;padding:0;">
                            <li v-if="col.functions.length > 0" v-for="rf in col.functions">
                                <a href="#" @click.prevent="rf.callback(rowData)" style="color:black;font-size:12px;display:block;line-height:30px;"><span :uk-icon="rf.icon" style="font-size:10px;margin-right:5px;"></span><span>{{rf.title}}</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </td>
            <td v-else-if="col.colType == 'rowfunction'" :style="getStyleCoa(rowData,col.name)">
                <div class="uk-inline">
                    <button type="button" class="uk-button uk-button-link"><span uk-icon="icon:menu;ratio:0.8"></span></button>
                    <div uk-dropdown="mode: click">
                        <ul class="uk-list" style="margin:0;padding:0;">
                            <li v-if="col.rowFunctions.length > 0" v-for="rf in col.rowFunctions">
                                <a href="#" @click.prevent="rf.callback(rowData)" style="color:black;font-size:12px;display:block;line-height:30px;"><span :uk-icon="rf.icon" style="font-size:10px;margin-right:5px;"></span><span>{{rf.title}}</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </td>
            <td v-else :style="getStyleCoa(rowData,col.name)">
                {{ getValue(rowData,col.name) }}
            </td>
        </template>
                
    </tr>
    <tr v-if="isExpandable">
        <Transition>
            <td :colspan="columns.length + 1" v-if="isExpanded" style="background-color:#fafafa;">
                <slot name="child-row">
                    <div>
                        expandable area
                    </div>
                </slot>                
            </td>
        </Transition>
    </tr>
</template>
<script>
export default {
    name : 'row-coa',
    props : {
        rowData : {type:Object , required: false, default : []},
        isExpandable : { type:Boolean, require:false, default: false},
        columns : {type:Array , required: false, default : [] },
    },
    data() {
        return {
            isExpanded : false,
            buffer : [],
        }
    },
    methods : {
        getStyleCoa(data,colName) {
            let style = `font-weight:400; font-size:12px; padding-left:0.2em;color:#666;`;
            if(data.level_coa == 0 ){
                style = "font-weight:600; font-size:12px; padding-left:0.2em;color:#000;";
            }
            else if(data.level_coa == 1 ){
                style = "font-weight:500; font-size:12px; padding-left:0.2em;color:#000;";
            }
            if(colName == 'nama_coa') {
                style = `${style} padding-left:${((data.level_coa*1)-1)}em;`;
            }
            return style;
        },

        changeRowExpand() {
            this.isExpanded = !this.isExpanded;
            return false;
        },

        getValue(row,col) {
            let data = row;
            let cols = col.split('.');
            if(cols.length > 0) { cols.forEach(el => { data = data[el]; }); } 
            else { data = row.col; }
            return data;
        },  
    }
}
</script> -->
