<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;"  v-bind:class="rowData.is_aktif != true ? 'inaktif':'' ">   
        <td style="background-color:whitesmoke;border:none;width:20px;padding:0.5em;">
            <a v-if="isExpandedReg" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowRegExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowRegExpand"></a>
        </td>      
        <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 300;">{{rowData.tgl_periksa}}</p>
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 400;">{{rowData.trx_id}}</p>
        </td>
        <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.dokter_nama}}</p>
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 400;">{{rowData.unit_nama}}</p>
        </td>
        <td style="width:80px;padding:1em; font-size: 12px; text-align:center; color:black;">{{rowData.status}}</td>
    </tr>
    <tr>
        <Transition>
            <td colspan="7" v-if="isExpandedReg" style="background-color:#fff;border-top:0px solid #000;padding:0;margin:0;">
                <div style="padding:0.5em;background-color:white;">
                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                        <!-- <thead> -->
                            <!-- <th class="tb-header-reg-sub" style="text-align:left;color:black;">ID</th> -->
                            <!-- <th class="tb-header-reg-sub" style="font-size:12px;text-align:left;color:black;">Item Pemeriksaan</th>
                            <th class="tb-header-reg-sub" style="font-size:12px;text-align:left;color:black;">Hasil</th>
                            <th class="tb-header-reg-sub" style="font-size:12px;text-align:left;color:black;">Catatan</th>
                        </thead> -->
                        <tbody>
                            <template v-if="rowData.detail" v-for="dtl in rowData.detail">
                                <tr style="border-bottom:1px solid #ccc;" v-bind:class="dtl.is_aktif != true ? 'inaktif':'' ">   
                                    <td colspan="3" style="padding:1em; font-weight: 500; font-size: 11px; color:black;">
                                        <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{dtl.item_nama}}</p>
                                    </td>
                                </tr>
                                <tr v-if="dtl.hasil" style="border-bottom:1px solid #ccc;" v-bind:class="dtl.is_aktif != true ? 'inaktif':'' ">        
                                    <td style="padding:1em; font-size: 11px; color:black;">
                                        <p style="margin:0;padding:0;font-weight: 400;"><strong>uraian :</strong> {{dtl.hasil.uraian_hasil}}</p>
                                        <p style="margin:0;padding:0;font-weight: 400;"><strong>Kesan :</strong> {{dtl.hasil.kesan}}</p>
                                    </td>
                                    <td style="padding:1em; font-size: 11px; color:black;">
                                        <strong>catatan :</strong> {{ dtl.hasil.catatan }}
                                    </td>
                                    
                                </tr>
                            </template>

                            <!-- <tr v-if="rowData.detail" v-for="dtl in rowData.detail" style="border-bottom:1px solid #ccc;" v-bind:class="dtl.is_aktif != true ? 'inaktif':'' ">        
                                <td style="padding:1em; font-weight: 500; font-size: 11px; color:black;">
                                    <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 400;">{{dtl.item_nama}}</p>
                                </td>
                                <td style="padding:1em; font-size: 11px; color:black;">
                                    <template v-if="dtl.hasil" v-for="dt in dtl.hasil">
                                        <p style="margin:0;padding:0;font-weight: 400;"><strong>uraian :</strong> {{dt.uraian_hasil}}</p>
                                        <p style="margin:0;padding:0;font-weight: 400;"><strong>Kesan :</strong> {{dt.kesan}}</p>
                                    </template>
                                </td>
                                <td style="padding:1em; font-size: 11px; text-align:center; color:black;">
                                    {{ dtl.catatan }}
                                </td>
                            </tr> -->
                        </tbody>
                    </table>
                </div>
            </td>
        </Transition>
    </tr>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import RowFormResep from '@/Pages/RawatJalan/Pemeriksaan/SubFormResep/RowFormResep.vue';
export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        linkCallback : { type:Function, required:false, default:null }
    },
    components : {
        RowFormResep,
    },
    //emits : ['itemRequestSelected'],
    computed : {
        ...mapGetters('rawatJalan',['poliTransaction']),
    },
    name : 'row-sub-rad',
    data() {
        return {
            isExpandedReg : false,
            selectAll : true,
            buffer : [],
        }
    },
    methods : {        
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('resep',['deletePrescription']),
        ...mapActions('rawatJalan',['dataTransaksiPoli']),

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
            this.rowData.diskon = 0;
            this.rowData.subtotal = this.rowData.jumlah * this.rowData.nilai * 1;
            
            // this.rowData.komponen.forEach(komp => {
            //     komp.jumlah = this.rowData.jumlah;
            //     komp.subtotal = komp.jumlah * (komp.nilai - komp.diskon);
            //     this.rowData.subtotal = (this.rowData.subtotal*1 ) + komp.subtotal;
            //     this.rowData.diskon = (this.rowData.diskon*1 ) + komp.diskon;
            // });
            this.dataChange();
        },

        hapusResep(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus data resep. Lanjutkan ?`)){
                this.deletePrescription(data.trx_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.retrieveTransaksiPoli();
                    }
                    else { alert (response.message) }
                });
            };
        },
        retrieveTransaksiPoli(){
            let trx = JSON.parse(JSON.stringify(this.poliTransaction));
            console.log(trx.trx_id);
            this.dataTransaksiPoli(trx.trx_id);
        }
    }
}
</script>
<style>
th.tb-header-reg-sub {
    padding:0.5em; 
    background-color:#fff; 
    color:black;
    font-weight: 500;
    font-size:14px;
}
</style>