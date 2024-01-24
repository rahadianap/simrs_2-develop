<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;"  v-bind:class="rowData.is_aktif != true ? 'inaktif':'' ">
        <td style="width:20px;padding:0.8em; font-weight: 500; font-size: 12px; color:black;">
            <a v-if="isExpandedReg" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowRegExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowRegExpand"></a>
        </td>
        <td style="padding:0.4em; font-weight: 500; font-size: 12px; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.item_nama}}</p>
            <p style="margin:0;padding:0;font-weight:350;">{{rowData.item_id}}</p>
        </td>
        <td style="padding:0.4em; font-size: 12px; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.dokter_nama}}</p>
            <p style="margin:0;padding:0;font-weight:350;">{{rowData.dokter_id}}</p>
        </td>
        <td style="width:40px;padding:1em; font-size: 12px; text-align:center; color:black;">
            {{formatCurrency(rowData.jumlah)}}
            <!-- <input class="uk-input uk-form-small" type="number" v-model="rowData.jumlah" style="text-align: center;" @change="rowDataChange" :disabled="examination.is_realisasi"> -->
        </td>
        <!-- <td style="width:60px;padding:1em; font-size: 12px; text-align:center; color:black;">{{rowData.satuan}}</td> -->
        <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.nilai)}}</td>
        <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.diskon)}}</td>
        <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.subtotal)}}</td>
        
        <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
            <input class="uk-checkbox" type="checkbox" v-model="rowData.is_aktif" style="text-align: center;" @change="activationChange(rowData)">
        </td>
    </tr>
    <tr>
        <Transition>
            <td colspan="8" v-if="isExpandedReg" style="background-color:#fefefe;border-bottom:1px solid #eee;margin:0;padding:0;">
                <div style="padding:0;margin:0;">
                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                        <thead>
                            <th style="width:20px;padding:1em;"></th>
                            <th style="width:140px;text-align:left;color:black;font-weight:500;font-size:11px;">ID</th>
                            <th style="text-align:left;color:black;font-weight:500;font-size:11px;">Komponen</th>
                            <th style="width:40px;text-align:center;color:black;font-weight:500;font-size:11px;">Jumlah</th>
                            <th style="width:40px;text-align:right;color:black;font-weight:500;font-size:11px;">Nilai</th>
                            <th style="width:60px;text-align:center;color:black;font-weight:500;font-size:11px;">Disc(rp)</th>
                            <th style="width:80px;text-align:right;color:black;font-weight:500;font-size:11px;">Subtotal</th>
                            <th style="width:50px;"></th>
                        </thead>
                        <tbody>
                            <tr v-if="rowData.komponen" v-for="komp in rowData.komponen" style="background-color:#fff;">   
                                <td style="width:20px;padding:1em;background-color:#fff;"></td>                             
                                <td style="width:140px; padding:1em; font-size: 12px; text-align:left; color:black; font-weight:500;background-color:#fff;">{{komp.komp_harga_id}}</td>
                                <td style="padding:1em; font-size: 12px; text-align:left; color:black;background-color:#fff;">{{komp.komp_harga}}</td>
                                <td style="padding:1em; font-size: 12px; text-align:center; color:black;background-color:#fff;">{{komp.jumlah}}</td>
                                <td style="width:100px; padding:1em; font-size: 12px; text-align:right; color:black;background-color:#fff;">{{formatCurrency(komp.nilai)}}</td>
                                <td style="width:100px; padding: 0.4em; font-size: 12px; text-align:center; color:black;background-color:#fff;">
                                    <input :disabled="examination.is_realisasi" class="uk-input uk-form-small" type="number" v-model="komp.diskon" style="text-align: right;" @change="rowDataChange">
                                </td>
                                <td style="width:100px; padding:1em; font-size: 12px; text-align:right; color:black;background-color:#fff;">{{formatCurrency(komp.subtotal)}}</td>
                                <td style="width: 50px;background-color:#fff;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </Transition>
    </tr>
</template>

<script>

import { mapGetters, mapActions, mapMutations } from 'vuex';
export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        dataChange : { type:Function, required:false, default:null },
        activationChange : { type:Function, required:false, default:null },
        linkCallback : { type:Function, required:false, default:null }
    },
    computed : {
        ...mapGetters('pemeriksaan',['examination']),
    },
    //emits : ['itemRequestSelected'],
    name : 'row-form-tindakan',
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
            data.forEach(function(dt){
                total = (total*1) + (dt.nilai*1);
            });
            return total;
        },

        // kompChange(){
        //     let subtotal = 0;
        //     let diskon = 0;
        //     this.rowData.komponen.forEach(el => {
        //         diskon = (diskon + el.diskon) * 1;
        //         el.subtotal = el.nilai - el.diskon;
        //         subtotal = subtotal + el.subtotal;
        //     });
        //     this.rowData.diskon = diskon;
        //     this.rowData.subtotal = subtotal;
        //     this.dataChange();
        // },

        rowDataChange(){
            let total = 0;
            let diskon = 0;
            this.rowData.subtotal = 0;
            this.rowData.diskon = 0;
            
            this.rowData.komponen.forEach(komp => {
                komp.jumlah = this.rowData.jumlah;
                komp.subtotal = komp.jumlah * (komp.nilai - komp.diskon);
                this.rowData.subtotal = (this.rowData.subtotal*1 ) + komp.subtotal;
                this.rowData.diskon = (this.rowData.diskon*1 ) + komp.diskon;
            });

            this.dataChange(this.rowData);
        }
    }
}
</script>