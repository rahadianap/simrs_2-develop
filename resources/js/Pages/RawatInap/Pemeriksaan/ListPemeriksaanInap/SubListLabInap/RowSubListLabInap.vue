<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;"  v-bind:class="rowData.is_aktif != true ? 'inaktif':'' ">   
        <td style="background-color:white;border:none;width:20px;padding:0.5em;">
            <a v-if="isExpandedReg" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowRegExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowRegExpand"></a>
        </td>      
        <td style="background-color:white;padding:1em; width:200px; font-weight: 500; font-size: 12px; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.trx_id}}</p>
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 400;">{{rowData.tgl_transaksi}}</p>
        </td>
        <td style="background-color:white;padding:1em; font-weight: 500; font-size: 12px; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.dokter_nama}}</p>
        </td>
        <td style="background-color:white;padding:1em; width:150px; font-weight: 500; font-size: 12px; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 400;">{{rowData.unit_nama}}</p>
        </td>
        <td style="background-color:white;width:80px;padding:1em; font-size: 12px; text-align:center; color:black;">{{rowData.status}}</td>
        <td v-if="rowData.status == 'DAFTAR'" style="width:150px;margin:0;padding:0.5em 0.2em 0.2em 0.2em;">
            <button style="margin-right:5px;" @click.prevent="fnEditCallback(rowData)"><span uk-icon="icon:pencil;ratio:0.7"></span>Ubah</button>
            <button @click.prevent="fnDeleteCallback(rowData)"><span uk-icon="icon:trash;ratio:0.7"></span>Hapus</button>
        </td>
        <td v-else></td>
    </tr>
    <tr>
        <Transition>
            <td colspan="6" v-if="isExpandedReg" style="background-color:#fff;border-top:0px solid #000;padding:0;margin:0;">
                <div style="padding:0.5em;background-color:white;">
                    <table class="uk-table uk-table-striped1 child-table1" style="padding:0;margin:0;">
                        <thead>
                            <th style="width:10px;"></th>
                            <th class="tb-header-reg-sub" style="font-size:12px;text-align:left;color:black;">Item Pemeriksaan</th>
                            <th class="tb-header-reg-sub" style="font-size:12px;text-align:center;color:black;">Nilai</th>
                            <th class="tb-header-reg-sub" style="font-size:12px;text-align:left;color:black;">Normal</th>
                        </thead>
                        <tbody>
                            <template v-if="rowData.detail" v-for="dtl in rowData.detail">
                                <tr style="border-bottom:1px solid #ccc;" v-bind:class="dtl.is_aktif != true ? 'inaktif':'' ">   
                                    <td style="width:10px;"></td>
                                    <td colspan="4" style="padding:1em; font-weight: 500; font-size: 11px; color:black;">
                                        <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{dtl.item_nama}}</p>
                                    </td>
                                </tr>
                                <tr v-if="dtl.hasil" v-for="res in dtl.hasil" style="border-bottom:1px solid #ccc;" v-bind:class="dtl.is_aktif != true ? 'inaktif':'' ">        
                                    <td style="width:10px;"></td>
                                    <td style="padding:1em; font-size: 11px; color:black;">
                                        <p style="margin:0;padding:0;font-weight: 400;"><strong>{{res.signa}}</strong> {{res.hasil_nama}}</p>
                                    </td>
                                    <td style="padding:1em; font-size: 11px; text-align:center; color:black;">
                                        {{ res.hasil_nilai }} {{ res.satuan }}
                                    </td>
                                    <td style="padding:1em; font-size: 11px; color:black;">
                                        <p style="margin:0;padding:0;font-weight: 400;font-style:italic;">
                                            <span style="font-weight: 500;">Lk.</span> {{res.hasil_normal_lk}}
                                            <span style="font-weight: 500;">Pr.</span> {{res.hasil_normal_pr}}
                                        </p>
                                    </td>
                                </tr>
                            </template>
                            
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
        linkCallback : { type:Function, required:false, default:null },
        fnEditCallback : { type:Function, required:true },
        fnDeleteCallback : { type:Function, required:true },
    },
    components : {
        //RowFormResep,
    },
    //emits : ['itemRequestSelected'],
    computed : {
        ...mapGetters('rawatJalan',['poliTransaction']),
    },
    name : 'row-sub-list-lab-inap',
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