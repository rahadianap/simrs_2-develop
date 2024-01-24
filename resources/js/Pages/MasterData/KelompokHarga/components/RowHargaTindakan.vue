<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;">
        <td style="background-color:#fafafa;border:none;width:20px;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td>
            <div class="uk-inline" style="margin:0;padding:0;">
                <a href="#" class="row-link-menu" @click.prevent = "rowFunctions(rowData)">
                    {{rowData.tindakan_id}}
                </a>
            </div>    
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.tindakan_nama}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-size:12px;">{{rowData.deskripsi}}</p>
        </td>
        
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" disabled style="margin-left:5px;border:1px solid black;">
        </td>
    </tr>
    <tr>
        <Transition>
            <td colspan="8" v-if="isExpanded" style="background-color:#fefefe;border-bottom:1px solid #eee;">
                <div style="padding:0;margin:0;">
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-expand"><h5 style="font-weight:500;padding:0;margin:0;">DAFTAR HARGA</h5></div>
                        <div class="uk-width-auto"><button @click.prevent="rowFunctions(rowData)">Harga Baru</button></div>
                    </div>
                    
                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:1em 0 0 0;">
                        <thead>
                            <th class="child-table-header" style="width:150px;text-align:left;padding:1em;">Buku Harga</th>
                            <th class="child-table-header" style="width:150px;text-align:left;padding:1em;">Kelas ID</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">Kelas</th>
                            <th class="child-table-header" style="width:120px; text-align:right;padding:1em;">Harga</th>
                            <th class="child-table-header" style="width:120px; text-align:right;padding:1em;">Harga Rencana</th>
                            <th class="child-table-header" style="width:60px; text-align:center;padding:1em;">DISETUJUI</th>
                            <th class="child-table-header" style="width:80px; text-align:center;padding:1em;">...</th>
                        </thead>
                        <tbody>
                            <tr v-if="rowData.detail.length > 0" v-for="child in rowData.detail">
                                <td style="width: 150px;padding:1em;font-weight: 500;">
                                    <a href="#" class="row-link-menu">{{child.buku_nama}}</a>
                                    <div uk-dropdown="mode: click">
                                        <ul class="uk-list" style="margin:0;padding:0;">
                                            <li>
                                                <a href="#" @click.prevent="childApproveFunctions(child)" style="color:black;font-size:12px;display:block;line-height:30px;font-weight:400;">
                                                    <span uk-icon="check" style="font-size:10px;margin-right:5px;"></span><span>Setujui</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" @click.prevent="childEditFunctions(child)" style="color:black;font-size:12px;display:block;line-height:30px;font-weight:400;">
                                                    <span uk-icon="file-edit" style="font-size:10px;margin-right:5px;"></span><span>Ubah Data</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" @click.prevent="childDeleteFunctions(child)" style="color:black;font-size:12px;display:block;line-height:30px;font-weight:400;">
                                                    <span uk-icon="trash" style="font-size:10px;margin-right:5px;"></span><span>Hapus Data</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td style="width: 150px;padding:1em;font-weight: 500;">
                                    <a href="#" class="row-link-menu">{{child.kelas_id}}</a>
                                    <div uk-dropdown="mode: click">
                                        <ul class="uk-list" style="margin:0;padding:0;">
                                            <li>
                                                <a href="#" @click.prevent="childApproveFunctions(child)" style="color:black;font-size:12px;display:block;line-height:30px;font-weight:400;">
                                                    <span uk-icon="check" style="font-size:10px;margin-right:5px;"></span><span>Setujui</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" @click.prevent="childEditFunctions(child)" style="color:black;font-size:12px;display:block;line-height:30px;font-weight:400;">
                                                    <span uk-icon="file-edit" style="font-size:10px;margin-right:5px;"></span><span>Ubah Data</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" @click.prevent="childDeleteFunctions(child)" style="color:black;font-size:12px;display:block;line-height:30px;font-weight:400;">
                                                    <span uk-icon="trash" style="font-size:10px;margin-right:5px;"></span><span>Hapus Data</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td style="width: auto;padding:1em;">{{child.kelas_nama}}</td>
                                <td style="width: 80px;padding:1em; text-align:right;">{{ formatCurrency(child.nilai) }}</td>
                                <td style="width: 80px;padding:1em; text-align:right;">{{ formatCurrency(child.nilai_rencana)  }}</td>
                                <td style="width:60px;text-align: center;">
                                    <input class="uk-checkbox" type="checkbox" v-model="child.is_approve" disabled style="margin-left:5px;border:1px solid black;">
                                </td>
                                <td style="width:80px;text-align: center;">
                                    <a href="#" title="approval" class="uk-icon-link" uk-icon="check" @click.prevent="childApproveFunctions(child)" style="color:black; margin-right: 5px;"></a>
                                    <a href="#" title="ubah data" class="uk-icon-link" uk-icon="file-edit" @click.prevent="childEditFunctions(child)" style="color:black; margin-right: 5px;"></a>
                                    <a href="#" title="hapus" class="uk-icon-link" uk-icon="trash" @click.prevent="childDeleteFunctions(child)" style="color:black;"></a>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="7" style="text-align:center; text-style:italic; ">( belum ada detail harga )</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </Transition>
    </tr>
</template>
<script>
export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        rowFunctions : { type:Function, required:false, default:null },
        childEditFunctions : { type:Function, required:false, default:null },
        childApproveFunctions : { type:Function, required:false, default:null },
        childDeleteFunctions : { type:Function, required:false, default:null },
    },
    name : 'row-harga-tindakan',
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
        },
        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

    }
}
</script>