<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;">
        <td style="background-color:#fafafa;border:none;width:20px;">
            <a v-if="isExpanded" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowExpand"></a>
        </td> 
        <td style="width:140px;">
            <div style="margin:0;padding:0;">
                <a href="#" @click.prevent="linkCallback(rowData)" class="row-link-menu">
                    {{rowData.propinsi_id}}
                </a>
            </div>      
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.propinsi}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.kode_bpjs}}</p>
        </td>
        <td class="uk-text-uppercase">
            <p style="margin:0;padding:0;font-weight: 500;">{{rowData.nama_bpjs}}</p>
        </td>
        
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_prioritas" disabled style="margin-left:5px;border:1px solid black;">
        </td>
        <td style="width:50px;text-align: center;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" disabled style="margin-left:5px;border:1px solid black;">
        </td>
        <td style="width:50px;text-align: center;">
            <button>Ubah</button>
        </td>
    </tr>
    <!-- <tr>
        <Transition>
            <td colspan="9" v-if="isExpanded" style="background-color:#eee;border-bottom:1px solid #eee;">
                <div style="padding:0;margin:0;background-color: whitesmoke;">
                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                        <thead>
                            <th class="child-table-header" style="text-align:left;padding:1em;">ID</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">Group</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">Kode Internal</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">Nama Internal</th>                           
                            <th class="child-table-header" style="text-align:left;padding:1em;">Kode Bridging</th>
                            <th class="child-table-header" style="text-align:left;padding:1em;">Nama Bridging</th>                           
                        </thead>
                        <tbody>
                            <tr v-for="dt in rowData.bridging">
                                 <td style="width: 130px;padding:1em; text-align:left;">{{dt.item_id}}</td>
                                <td style="padding:1em; text-align:left;">{{dt.item_nama}}</td>
                                <td style="width: 50px;padding:1em; text-align:center;">{{dt.jumlah}}</td>
                                <td style="width: 50px;padding:1em; text-align:center;">{{dt.satuan}}</td>
                                <td style="width: 50px;padding:1em; text-align:center;">{{formatCurrency(dt.nilai)}}</td>
                                <td style="width: 50px;padding:1em; text-align:center;">{{formatCurrency(dt.subtotal)}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </Transition>
    </tr> -->
</template>
<script>
export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        selectAllCallback : { type:Function, required:false, default:null },
        rowFunctions : { type:Object, required:false, default:null },
        linkCallback : { type:Function, required:false, default:null }
    },
    emits : ['itemRequestSelected'],
    name : 'row-propinsi',
    data() {
        return {
            isExpanded : false,
            selectAll : true,
            buffer : [],
        }
    },
    methods : {
        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        changeRowExpand() {
            this.isExpanded = !this.isExpanded;
            return false;
        },

        dateFormatting(data) {
            let d = new Date(data);
            let val = d.toLocaleDateString();
            return val;
        },

        getTotal(data){
            let total = 0;
            data.forEach(function(dt){
                total = (total*1) + (dt.nilai*1);
            });
            return total;
        }
    }
}
</script>