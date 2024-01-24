<template>
    <tr>
        <td v-if="isExpandable" style="background-color:#fafafa;border:none;width:20px;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td v-else style="background-color:#fafafa;border:none;width:5px;padding:0;"></td>

        <template v-for="col in columns">
            <td v-if="col.colType == 'link'" class="row-link" @click.prevent="col.linkCallback(rowData)" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">{{ getValue(rowData,col.name) }}</td> 
            <td v-else-if="col.colType == 'label'" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">{{getValue(rowData,col.name)}}</td>
            <td v-else-if="col.colType == 'boolean'" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">
                <input class="uk-checkbox" type="checkbox" v-model="rowData[col.name]" disabled style="margin-left:5px;border:1px solid black;">
            </td>            
            <td v-else-if="col.colType == 'linkmenus'" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">
                <a href="#" class="row-link-menu">{{ getValue(rowData,col.name) }}</a>
                <div uk-dropdown="mode: click;">
                    <ul class="uk-list" style="margin:0;padding:0;">
                        <li v-if="col.functions" v-for="rf in col.functions">
                            <a href="#" @click.prevent="rf.callback(rowData)" style="color:black;font-size:12px;display:block;line-height:30px;"><span :uk-icon="rf.icon" style="font-size:10px;margin-right:5px;"></span><span>{{rf.title}}</span></a>
                        </li>
                    </ul>
                </div>
            </td>
            <td v-else-if="col.colType == 'rowfunction'" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">                
                <button type="button" class="uk-button uk-button-link"><span uk-icon="icon:menu;ratio:0.8"></span></button>
                <div uk-dropdown="mode: click;">
                    <ul class="uk-list" style="margin:0;padding:0;">
                        <li v-if="col.rowFunctions" v-for="rf in col.rowFunctions">
                            <a href="#" @click.prevent="rf.callback(rowData)" style="color:black;font-size:12px;display:block;line-height:30px;"><span :uk-icon="rf.icon" style="font-size:10px;margin-right:5px;"></span><span>{{rf.title}}</span></a>
                        </li>
                    </ul>
                </div>
            </td>
            <td v-else-if="col.colType == 'json'" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">{{ getJsonValue(rowData,col.name) }}</td>
            <td v-else v-bind:style="{ width: col.width, textAlign:col.textAlign,}">{{ getValue(rowData,col.name) }}</td>
        </template>
    </tr>
    <tr v-if="isExpandable">
        <Transition>
            <td :colspan="columns.length + 1" v-if="isExpanded" style="background-color:#f0f0f0;border-top:1px solid #efefef;">
                <slot name="child-row">
                    <div style="padding:0;margin:0;">
                        <h5 style="font-weight:500;padding:0;margin:0;">{{childTitle}}</h5>
                        <p style="font-weight:400;padding:0;margin:0;">{{childSubtitle}}</p>
                        <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                            <thead>
                                <th class="child-table-header" v-for="col in childColumns" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">{{ col.title }}</th>
                            </thead>
                            <tbody>
                                <tr v-for="child in rowData[childNameIndex]">
                                    <template v-for="colChild in childColumns">                                                
                                        <td v-if="colChild.colType == 'link'" class="row-link" @click.prevent="col.linkCallback"  v-bind:style="{ width: colChild.width, textAlign:colChild.textAlign }">{{ getValue(child,colChild.name) }}</td> 
                                        <td v-else-if="colChild.colType == 'label'"  v-bind:style="{ width: colChild.width, textAlign:colChild.textAlign,}">{{getValue(child,colChild.name)}}</td>
                                        <td v-else-if="colChild.colType == 'boolean'" v-bind:style="{ width: colChild.width, textAlign:colChild.textAlign,}">
                                            <input class="uk-checkbox" type="checkbox" v-model="child[colChild.name]" disabled style="margin-left:5px;border:1px solid black;">
                                        </td> 
                                        <td v-else-if="colChild.colType == 'linkmenus'" v-bind:style="{ width: colChild.width, textAlign:colChild.textAlign,}">
                                            <div class="uk-inline">
                                                <a href="#" class="row-link-menu">{{ getValue(child,colChild.name) }}</a>
                                                <div uk-dropdown="mode: click">
                                                    <ul class="uk-list" style="margin:0;padding:0;">
                                                        <li v-if="colChild.functions" v-for="rf in colChild.functions">
                                                            <a href="#" @click.prevent="rf.callback(child)" style="color:black;font-size:12px;display:block;line-height:30px;"><span :uk-icon="rf.icon" style="font-size:10px;margin-right:5px;"></span><span>{{rf.title}}</span></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>           
                                        <td v-else-if="colChild.colType == 'rowfunction'" v-bind:style="{ width: colChild.width, textAlign:colChild.textAlign,}">
                                            <div class="uk-inline">
                                                <button type="button" class="uk-button uk-button-link"><span uk-icon="icon:menu;ratio:0.8"></span></button>
                                                <div uk-dropdown="mode: click">
                                                    <ul class="uk-list" style="margin:0;padding:0;">
                                                        <li v-if="colChild.rowFunctions" v-for="rf in colChild.rowFunctions">
                                                            <a href="#" @click.prevent="rf.callback(child)" style="color:black;font-size:12px;display:block;line-height:30px;"><span :uk-icon="rf.icon" style="font-size:10px;margin-right:5px;"></span><span>{{rf.title}}</span></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                        <td v-else v-bind:style="{ width: colChild.width, textAlign:colChild.textAlign,}">{{ getValue(child,colChild.name) }}</td>
                                    </template>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </slot>                
            </td>
        </Transition>
    </tr>
</template>
<script>
export default {
    name : 'base-row',
    props : {
        childTitle : {type:String, required: false, default : null }, 
        childSubtitle : {type:String, required: false, default : null }, 
        rowData : {type:Object , required: false, default : []},
        isExpandable : { type:Boolean, require:false, default: false},
        columns : {type:Array , required: false, default : [] },
        childNameIndex : {type:String, required: false, default : '' }, 
        childColumns : { type:Array , required: false, default : [] },
    },
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

        getValue(row,col) {
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

        getJsonValue(row,col) { 
            let data = '';
            let cols = col.split('.');
            if(cols.length > 0) { 
                if(row[cols[0]]){
                    row[cols[0]].forEach(el => {
                        try { data = `${data}${el[cols[1]]}; `; } 
                        catch (error) { return ''; }
                    });
                }
                 
            } 
            else {  data = row.col; }
            return data;
        }
    }
}
</script>
