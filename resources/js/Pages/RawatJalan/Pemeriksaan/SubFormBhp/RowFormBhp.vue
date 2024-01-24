<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;"  v-bind:class="rowData.is_aktif != true ? 'inaktif':'' ">        
        <td style="padding:0.4em; font-weight: 400; font-size: 12px; color:black;">
            <!-- <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 400;">{{rowData.depo_nama}}</p> -->
            <select v-model="rowData.depo_id" class="uk-select uk-form-small">
                <option v-for="dt in depoLists" :value="dt.depo_id">{{ dt.depo_nama }}</option>
            </select>
        </td>
        <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.item_nama}}</p>
        </td>
        <td style="padding:1em; font-weight: 400; font-size: 12px; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 400;">{{rowData.tindakan_nama}}</p>
        </td>
        <td style="width:40px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
            <input class="uk-input uk-form-small" type="number" v-model="rowData.jumlah" style="text-align: center;" @change="rowDataChange">
        </td>
        <td style="padding:1em; font-size: 12px; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 400;">{{rowData.satuan}}</p>
        </td>
        <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.nilai)}}</td>
        <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.subtotal)}}</td>
        <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.bhp_tindakan" style="text-align: center;" @change="bhpTindakanChange">
        </td>
        <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" style="text-align: center;" @change="activationChange">
        </td>
    </tr>
    
</template>

<script>
export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        dataChange : { type:Function, required:false, default:null },
        activationChange : { type:Function, required:false, default:null },
        bhpTindakanChange : { type:Function, required:false, default:null },
        linkCallback : { type:Function, required:false, default:null },
        depoLists : { type:Object, required:false }
    },
    //emits : ['itemRequestSelected'],
    name : 'row-form-bhp',
    data() {
        return {
            selectAll : true,
            buffer : [],
        }
    },
    methods : {
        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
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
            if(!this.rowData.bhp_tindakan){ this.rowData.subtotal = this.rowData.jumlah * this.rowData.nilai * 1; }
            else { this.rowData.subtotal = 0; }
            this.dataChange();
        }
    }
}
</script>