<template>
    <template v-if="rowData.item_tipe == 'ITEM_RACIKAN' ">
        <tr style="border-bottom:1px solid #ccc;"  v-bind:class="rowData.is_aktif != true ? 'inaktif':'' ">
            <td style="width:20px; padding:1em; font-weight: 500; font-size: 12px; color:black;">
                <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.item_tipe}}</p> 
            </td>
            <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.group_racikan}}</p>
            </td>
            <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.item_nama}}</p>
            </td>
            <td style="width:40px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
                <input class="uk-input uk-form-small" type="number" v-model="rowData.jumlah" style="text-align: center;" @change="rowDataChange">
            </td>
            <td style="padding:1em; font-size: 12px; color:black; text-align:center;">
                <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 400;">{{rowData.satuan}}</p>
            </td>
            <td style="padding:1em; font-size: 12px; color:black;">
                <p style="margin:0;padding:0;font-weight: 400;">
                    <strong>{{ rowData.signa }}</strong> {{ rowData.signa_deskripsi }}
                    <!-- <span style="margin-left:5px;font-style:italic;"><a href="#" @click.prevent="signaCallback(rowData)">ubah</a></span> -->
                </p>
            </td>        
            <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.nilai)}}</td>
            <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.subtotal)}}</td>
            
            <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
                <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" style="text-align: center;" @change="activationChange">
            </td>                    
        </tr> 
    </template>
    <template v-else-if="rowData.item_tipe == 'HEADER_RACIKAN' ">
        <tr style="border-bottom:1px solid #ccc;"  v-bind:class="rowData.is_aktif != true ? 'inaktif':'' ">
            <td style="width:20px; padding:1em; font-weight: 500; font-size: 12px; color:black;">
                <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.item_tipe}}</p> 
            </td>
            <td colspan="2" style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.group_racikan}}</p>
            </td>
            <td style="width:40px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
                <input class="uk-input uk-form-small" type="number" v-model="rowData.jumlah" style="text-align: center;">
            </td>
            <td style="padding:1em; font-size: 12px; color:black; text-align:center;">
                <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 400;">{{rowData.satuan}}</p>
            </td>
            <td style="padding:1em; font-size: 12px; color:black;">
                <p style="margin:0;padding:0;font-weight: 400;">
                    <strong>{{ rowData.signa }}</strong> {{ rowData.signa_deskripsi }}
                    <span style="margin-left:5px;font-style:italic;"><a href="#" @click.prevent="signaCallback(rowData)">ubah</a></span>
                </p>
            </td>        
            <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.nilai)}}</td>
            <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.subtotal)}}</td>
            
            <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
                <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" style="text-align: center;" @change="activationChange">
            </td>        
        </tr>
    </template>
    <template v-else>
        <tr style="border-bottom:1px solid #ccc;"  v-bind:class="rowData.is_aktif != true ? 'inaktif':'' ">
            <td style="width:20px; padding:1em; font-weight: 500; font-size: 12px; color:black;">
                <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.item_tipe}}</p> 
            </td>
            <td colspan="2" style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
                <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.item_nama}}</p>
            </td>
            <td style="width:40px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
                <input class="uk-input uk-form-small" type="number" v-model="rowData.jumlah" style="text-align: center;" @change="rowDataChange">
            </td>
            <td style="padding:1em; font-size: 12px; color:black; text-align:center;">
                <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 400;">{{rowData.satuan}}</p>
            </td>
            <td style="padding:1em; font-size: 12px; color:black;">
                <p style="margin:0;padding:0;font-weight: 400;">
                    <strong>{{ rowData.signa }}</strong> {{ rowData.signa_deskripsi }}
                    <span style="margin-left:5px;font-style:italic;"><a href="#" @click.prevent="signaCallback(rowData)">ubah</a></span>
                </p>
            </td>        
            <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.nilai)}}</td>
            <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.subtotal)}}</td>
            
            <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
                <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" style="text-align: center;" @change="activationChange">
            </td>        
        </tr>
    </template>
</template>

<script>
export default {
props : {
    rowData : { type:Object, required:false, default:null },
    dataChange : { type:Function, required:false, default:null },
    activationChange : { type:Function, required:false, default:null },
    linkCallback : { type:Function, required:false, default:null },
    signaCallback : {type:Function, required:false },
},
name : 'row-form-apotek',
data() {
    return {
        isExpandedReg : false,
        selectAll : true,
        buffer : [],
    }
},
methods : {
    formatCurrency(value) {
        let val = (value/1).toFixed(2).replace('.', ',')
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
    },

    changeRowRegExpand() {
        this.isExpandedReg = !this.isExpandedReg;
        return false;
    },

    dateFormatting(data) {
        let d = new Date(data);
        let val = d.toLocaleDateString();
        return val;
    },

    getTotal(data){
        let total = 0;
        data.forEach(function(dt){ total = (total*1) + (dt.nilai*1); });
        return total;
    },

    rowDataChange(){
        this.rowData.diskon = 0;
        this.rowData.subtotal = this.rowData.jumlah * this.rowData.nilai * 1;
        this.dataChange();
    },

    ubahSigna(data) {
        this.signaCallback();
    }
}
}
</script>