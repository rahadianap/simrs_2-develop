<template>
    <!--  default, kondisi isCheckboxAksi:true & isExpandable:true-->
    <tr v-if="isCheckboxAksi & isCheckboxColumns">
        <td :colspan="isExpandable ? 0 : 1" :width="isExpandable ? '10px' : '25px'">
            <!-- update checkbox agar per rowdata dan hilang otomatis saat mengganti tipe filterData-->
            <input class="uk-checkbox checkbox-asset" 
                type="checkbox" 
                aria-label="Checkbox" 
                v-model.trim="isChecked" 
                v-model="checkbox_selected_row" 
                @change="handleSelectionChange()" 
                @submit.prevent="submitFilter($event.target)">
        </td>
        <td v-if="isExpandable" style="background-color:#fafafa;border:none;" :width="isCheckboxAksi && isExpandable ? '50px' : '25px'">
            <a v-if="isExpanded" href="#" uk-icon="icon:chevron-up;ratio:1;" @click.prevent="changeRowExpand"></a>
            <!-- <a v-else="isExpanded2" href="#" uk-icon="icon:chevron-up;ratio:1;" @click.prevent="changeRowExpand2"></a> -->
            <a v-else href="#" uk-icon="icon:chevron-down;ratio:1;" @click.prevent="changeRowExpand"></a>
        <!-- <td v-else style="background-color:#fafafa;border:none;width:5px;padding:0;"></td> -->
        </td>

        <template v-for="col in columns" @selection-change="handleSelectionChange">
            <td v-if="col.colType == 'link'" class="row-link" @click.prevent="col.linkCallback(rowData)" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">{{ getValue(rowData,col.name) }}</td> 
            <td v-else-if="col.colType == 'label'" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">{{getValue(rowData,col.name)}}</td>
            <td v-else-if="col.colType == 'boolean'" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">
            <!-- <span v-if="rowData[col.name]" class="uk-label uk-icon"  style="padding:0px 6px 3px;border-radius:15px;width:14px" uk-icon="eye" uk-tooltip="Aktif"></span>
                 <span v-else class="uk-label-danger uk-icon" style="padding:5px 6px;border-radius:15px;width:14px" uk-icon="eye-slash" uk-tooltip="Nonaktif"></span> --> 
                 <span v-if="rowData[col.name]" class="uk-label"  style="padding:1px 6px 1px;border-radius:15px;font-size:12px;" uk-tooltip="Aktif">Aktif</span>
                 <span v-else class="uk-label-danger" style="padding:2px 6px;border-radius:15px;font-size:12px" uk-tooltip="Nonaktif">Nonaktif</span>
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

        <td class="uk-child-width-1-2@s uk-child-width-1-2@m uk-text-center" style="padding-right:0px;padding-left:0px;">
           <!--fungsi hapus di kolom Aksi, akan visible jika isAksiDelete=true -->
            <div v-if="isAksiDelete" class="boxedicon" uk-tooltip="Hapus Data" style="margin-right:25px;" @click="deleteFunction(rowData)">
                <img 
                    :src="iconTrash" 
                    alt="iconTrash" 
                    style="max-width:auto;width:16px;height:18px;" 
                    :show="showDialog"
                    :cancel="cancel"
                    :confirm="confirm"
                />
                <!-- <Dialog
                    :show="showDialog"
                    :cancel="cancel"
                    :confirm="confirm"
                /> -->
            </div>
            
            <div class="boxedicon" uk-tooltip="Ubah Data" @click="updateFunction(rowData)">
                <img 
                    :src="iconEdit" 
                    alt="iconEdit" 
                    style="max-width:auto;width:16px;height:18px;" 
                />
            </div>
        </td>
    </tr>

    <!-- isCheckboxColumns: false -->
    <tr v-if="!isCheckboxColumns">
        <td v-if="isExpandable" style="background-color:#fafafa;border:none;" :width="isCheckboxAksi && isExpandable ? '50px' : '25px'">
            <a v-if="isExpanded" href="#" uk-icon="icon:chevron-up;ratio:1;" @click.prevent="changeRowExpand"></a>
            <!-- <a v-else="isExpanded2" href="#" uk-icon="icon:chevron-up;ratio:1;" @click.prevent="changeRowExpand2"></a> -->
            <a v-else href="#" uk-icon="icon:chevron-down;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <template v-for="col in columns" @selection-change="handleSelectionChange">
            <td v-if="col.colType == 'link'" class="row-link" @click.prevent="col.linkCallback(rowData)" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">{{ getValue(rowData,col.name) }}</td> 
            <td v-else-if="col.colType == 'label'" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">{{getValue(rowData,col.name)}}</td>
            <td v-else-if="col.colType == 'boolean'" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">
            <!-- <span v-if="rowData[col.name]" class="uk-label uk-icon"  style="padding:0px 6px 3px;border-radius:15px;width:14px" uk-icon="eye" uk-tooltip="Aktif"></span>
                 <span v-else class="uk-label-danger uk-icon" style="padding:5px 6px;border-radius:15px;width:14px" uk-icon="eye-slash" uk-tooltip="Nonaktif"></span> --> 
                 <span v-if="rowData[col.name]" class="uk-label"  style="padding:1px 6px 1px;border-radius:15px;font-size:12px;" uk-tooltip="Aktif">Aktif</span>
                 <span v-else class="uk-label-danger" style="padding:2px 6px;border-radius:15px;font-size:12px" uk-tooltip="Nonaktif">Nonaktif</span>
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

        <td class="uk-child-width-1-2@s uk-child-width-1-2@m uk-text-center" style="padding-right:0px;padding-left:0px;">
           <!--fungsi hapus di kolom Aksi, akan visible jika isAksiDelete=true -->
            <div v-if="isAksiDelete" class="boxedicon" uk-tooltip="Hapus Data" style="margin-right:25px;" @click="deleteFunction(rowData)">
                <img 
                    :src="iconTrash" 
                    alt="iconTrash" 
                    style="max-width:auto;width:16px;height:18px;" 
                    :show="showDialog"
                    :cancel="cancel"
                    :confirm="confirm"
                />
            </div>
            
            <div class="boxedicon" uk-tooltip="Ubah Data" @click="updateFunction(rowData)">
                <img 
                    :src="iconEdit" 
                    alt="iconEdit" 
                    style="max-width:auto;width:16px;height:18px;" 
                />
            </div>
        </td>
    </tr>

    <!-- isCHeckboxAksi: false -->
    <tr v-if="!isCheckboxAksi">
 
        <td v-if="isExpandable" style="background-color:#fafafa;border:none;"  :width="isCheckboxAksi && isExpandable ? '20px' : '50px'">
            <a v-if="isExpanded" href="#" uk-icon="icon:chevron-up;ratio:1;" @click.prevent="changeRowExpand"></a>
            <!-- <a v-else="isExpanded2" href="#" uk-icon="icon:chevron-up;ratio:1;" @click.prevent="changeRowExpand2"></a> -->
            <a v-else href="#" uk-icon="icon:chevron-down;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <!-- fixing bug colom isexpandable=false saat isCheckboxAksi=true -->
        <td v-if="!isExpandable" style="background-color:#fafafa;border:none;width:20px;">
        </td> 

        <template v-for="col in columns" @selection-change="handleSelectionChange">
            <td v-if="col.colType == 'link'" class="row-link" @click.prevent="col.linkCallback(rowData)" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">{{ getValue(rowData,col.name) }}</td> 
            <td v-else-if="col.colType == 'label'" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">{{getValue(rowData,col.name)}}</td>
            <td v-else-if="col.colType == 'boolean'" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">
            <!-- <span v-if="rowData[col.name]" class="uk-label uk-icon"  style="padding:0px 6px 3px;border-radius:15px;width:14px" uk-icon="eye" uk-tooltip="Aktif"></span>
                 <span v-else class="uk-label-danger uk-icon" style="padding:5px 6px;border-radius:15px;width:14px" uk-icon="eye-slash" uk-tooltip="Nonaktif"></span> --> 
                 <span v-if="rowData[col.name]" class="uk-label"  style="padding:1px 6px 1px;border-radius:15px;font-size:12px;" uk-tooltip="Aktif">Aktif</span>
                 <span v-else class="uk-label-danger" style="padding:2px 6px;border-radius:15px;font-size:12px" uk-tooltip="Nonaktif">Nonaktif</span>
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


    <!-- isExpandable -->
    <tr v-if="isExpandable">
        <Transition>
            <td colspan="99" v-if="isExpanded" style="background-color:#fefefe;border-bottom:1px solid #eee;">
                <div style="padding:0;margin:0;">
                    <div style="padding: 0.2em 0.5em 0.5em 0.5em;margin:0;">
                        <div class="uk-grid-small uk-width-1-1@l 1-1@m" uk-grid style="padding:0;margin:0;"> 
                            <div v-for="child_column in childColumnChunk" class="uk-width-1-3@m">
                                <dl class="uk-description-list pasien-description-list">
                                    <template v-for="child_data in child_column">
                                        <dt>{{ child_data.title }}</dt>
                                        <dd>
                                            <template v-if="child_data.colType != 'boolean'">
                                                {{ rowData[child_data.name] }}
                                            </template>
                                            <template v-else>
                                                <span v-if="rowData[child_data.name]" class="uk-label"  style="padding:1px 6px 1px;border-radius:15px;font-size:12px;" uk-tooltip="Aktif">Aktif</span>
                                                <span v-else class="uk-label-danger" style="padding:2px 6px;border-radius:15px;font-size:12px" uk-tooltip="Nonaktif">Nonaktif</span>
                                            </template>

                                        </dd>
                                    </template>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </Transition>
    </tr>


</template>

<script>
// import Assets from '@/Assets/index.vue';
import iconTrash from '@/Assets/icon-trash.svg';
import iconEdit from '@/Assets/icon-notepencil.svg';
import Dialog from '@/Components/DialogBox/index.vue';

export default {
    name : 'base-row',
    components:{
        Dialog,
    },
    props : {
        childTitle : {type:String, required: false, default : null }, 
        childSubtitle : {type:String, required: false, default : null }, 
        rowData : {type:Object , required: false, default : []},
        isExpandable : { type:Boolean, require:false, default: false},
        isExpandable2 : { type:Boolean, require:false, default: false},
        columns : {type:Array , required: false, default : [] },
        childNameIndex : {type:String, required: false, default : '' }, 
        childColumns : { type:Array , required: false, default : [] },
        updateFunction: {type: Function, require: false},
        deleteFunction: {type: Function, required: false},
        isChecked: {
            type: Boolean,
            required: false,
            default: false
        },
        isCheckboxAksi:  { type:Boolean, require:true, default: true}, //menampilkan atau sembunyikan kedua kolom Checkbox dan kolom Aksi
        isCheckboxColumns:  { type:Boolean, require:true, default: true}, //hanya menampilkan atau sembunyikan kolom Checkbox saja
        isAksiColumns: { type:Boolean, require:true, default: true}, //hanya menampilkan atau sembunyikan kolom Aksi saja
        isAksiDelete:  {type:Boolean, default : []}, //hanya menampilkan atau sembunyikan row Delete pada kolom Aksi
    },
    data() {
        return {
            isExpanded : false,
            isExpanded2 : false,
            buffer : [],
            multipleSelection: [],
            iconTrash,
            iconEdit,
            showDialog: false,
            isChecked: [],
            checkedStatus: [],
            searchUrl: '/assets',
        }
    },
    methods : {
        submitFilter(target) {
            if (this.isLoading) {
                return false;
            }

            this.filterInfo = '';
            this.params = { 'page': 1 };
            let comps = target.elements;
            this.params.page = 1;

            // deklarasi checkbox yang dicentang
            this.checkedStatus = [];
            const checkedStatusBeforeFilter = this.checkedStatus;
            // end function

            for (let i = 0; i < comps.length; i++) {
                if (comps[i].type !== 'submit' && comps[i].type !== 'button') {
                if (
                    comps[i].value !== null &&
                    comps[i].value !== '' &&
                    comps[i].name !== null &&
                    comps[i].name !== ''
                )   {
                        this.filterInfo = `${this.filterInfo} <span class="filter-label"> ${comps[i].name} : ${comps[i].value} </span> `;
                        this.params[comps[i].name] = comps[i].value;
                    }
                }
            }

            if (this.filterInfo.length > 0) {
                this.filterInfo = `<strong>Pencarian : ${this.filterInfo}</strong>`;
            }

            this.retrieveData();

             // dapetin status checkbox after retrieveData()
            const checkedStatusAfterFilter = this.checkedStatus;

            // reset ceklis/centang pada checkbox yang sebelumnya diceklis
            for (const checkedItem of checkedStatusBeforeFilter) {
            if (!checkedStatusAfterFilter.some(item => item.dt_index === checkedItem.dt_index)) 
                {
                    checkedItem.checked = false;
                }
            }

            return false;
        },
        setIsChecked(check) {
            this.isChecked = check
            let resetCheckbox = 0;
            if (this.isMounted) {
                for (let checkStatus of this.checkedStatus) {
                if (checkStatus.isChecked) {
                    resetCheckbox = true;
                    }
                }
            }
            return resetCheckbox;
        },  
        rowCheckedChanged(index, isChecked) {
            let checkStatus = this.checkedStatus.find(c => c.index === index);
            if (!checkStatus) {
                this.checkedStatus.push({ index, isChecked });
            } else {
                checkStatus.isChecked = isChecked;
            }
        },
        updateCheckedStatus(rowData, isChecked) {
            if (!this.checkedStatus) {
                this.checkedStatus = [];
            }

            const checkedItem = this.checkedStatus.find(item => item.index === rowData.dt_index);
            if (isChecked) {
                if (!checkedItem) {
                    this.checkedStatus.push({ index: rowData.dt_index });
                }
            } else {
                if (checkedItem) {
                    const index = this.checkedStatus.indexOf(checkedItem);
                    this.checkedStatus.splice(index, 1);
                }
            }
        },
        // setIsChecked(check) {
        //     // CSS 'checked' berdasarkan status isChecked
        //     // const checkboxElement = document.getElementById('checkbox');
        //     //         if (checkboxElement) {
        //     //         if (check) {
        //     //             checkboxElement.style.backgroundImage = "url(@/Assets/checklist-white-14x9px.svg)";
        //     //         } else {
        //     //             checkboxElement.style.backgroundImage = "none";
        //     //         }
        //     // }
        //     if (this.rowData.is_aktif === check ) {
        //         this.isChecked = check;
        //         this.isDisabled = false;

        //     } else {
        //         this.isChecked = false;
        //         this.isDisabled = false;
        //     }
        // },

        handleSelectionChange(selection) {
            console.log("SelectionChange", selection);
            this.multipleSelection = selection;
        },
        handleClose(tag) {
            this.$refs.table.toggleRowSelection(tag, false);
        },

        changeRowExpand() {
            this.isExpanded = !this.isExpanded;
            return false;
        },
        changeRowExpand2() {
            this.isExpanded2 = !this.isExpanded2;
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
        },

        // dialog box
        cancel() {
            console.log('cancel');
            this.showDialog = false;
        },
        confirm() {
            console.log('confirm');
            this.showDialog = false;
        },
        // end dialogbox

    },
    mounted() {
        // saya comment soalnya bikin error
        // this.$on('checkbox-all', isChecked => {
        //     console.log(isChecked,'isChecked')
        //     this.isChecked = isChecked
        // })
    },
    watch: {
        isChecked(newVal) {
            this.$emit('checkedChanged', newVal)
            let resetCheckbox = 0;
            if (this.isMounted) {
                for (let checkStatus of this.checkedStatus) {
                if (checkStatus.isChecked) {
                    resetCheckbox = true;
                    }
                }
            }
            return resetCheckbox;
        }
    },
    computed: {
        childColumnChunk() {
            let chunk_data = []
            let row_count = Math.ceil(this.childColumns.length / 3)
            for (let i = 0; i < this.childColumns.length; i += row_count) {
                chunk_data.push(this.childColumns.slice(i, i+row_count))
            }
            return chunk_data
        },
        
        // reset checkbox per row, setelah submit filter is_aktif
        checkbox_selected_row() {
            let resetCheckbox = 0;
            if (this.isMounted) {
                for (let checkStatus of this.checkedStatus) {
                if (checkStatus.isChecked) {
                    resetCheckbox = true;
                    }
                }
            }
            return resetCheckbox;
        }
    }
}
</script>

<style>
table dt {
    font-weight: bold !important;
}
.uk-checkbox:checked, .uk-checkbox:indeterminate, .uk-radio:checked {
    background-color: #cc0202;
    border-color: white;
    border-radius: 3px;
}
.uk-checkbox:checked {
    background-image: url(@/Assets/checklist-white-14x9px.svg);
    background-size: 10px;
}
.uk-checkbox, .uk-radio {
    display: inline-block;
    height: 16px;
    width: 16px;
    overflow: hidden;
    margin-top: -4px;
    vertical-align: middle;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-color: transparent;
    background-repeat: no-repeat;
    background-position: 50% 50%;
    border: 1px solid #ccc;
    border-radius: 3px;
    transition: .2s ease-in-out;
    transition-property: background-color,border;
}
.boxedicon{
    height: 22px;
    width: 22px;
    position: relative;
    background-color: #cc0202;
    border-radius: 8px;
    justify-content: center;
    align-items: center;
    display: inline-grid;
}
</style>

