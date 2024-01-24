<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;">
        <td style="width:20px;padding:0.8em; font-weight: 500; font-size: 12px; color:black;">
            <a v-if="isExpandedReg" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowRegExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowRegExpand"></a>
        </td>
        <td style="padding:0.4em; font-weight: 400; font-size: 12px; color:black;">
            <search-select ref="tindakanPicker"
                :config = configSelect
                :searchUrl = urlTindakanPicker
                placeholder = "pilih tindakan"
                captionField = "tindakan_nama"
                valueField = "tindakan_nama"
                :itemListData = tindakanDesc
                :value = "rowData.tindakan_nama"
                v-on:item-select = "actSelected"
            ></search-select>
        </td>

        <td style="padding:0.4em; font-size: 12px; color:black;">
            <search-select  ref="searchDokter"
                :config = configSelect
                :searchUrl = urlDokterPicker
                placeholder = "pilih dokter"
                captionField = "dokter_nama"
                valueField = "dokter_nama"
                :itemListData = dokterDesc
                :value = "rowData.dokter_nama"
                v-on:item-select = "docSelected"
            ></search-select>
        </td>

        <td style="width:40px;padding:0.4em; font-size: 12px; text-align:center; color:black;">
            <input class="uk-input uk-form-small" type="number" v-model="rowData.jumlah" style="text-align: center;" @change="rowDataChange">
        </td>
        <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.nilai)}}</td>
        <td style="width:60px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.diskon)}}</td>
        <td style="width:80px;padding:1em; font-size: 12px; text-align:right; color:black;">{{formatCurrency(rowData.subtotal)}}</td>
        
        <td style="width: 50px; font-size: 12px; padding:1em; text-align:center; color:black;">
            <button type="button" @click.prevent="addFunction(rowData)">Tambah</button>
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
                                    <input class="uk-input uk-form-small" type="number" v-model="komp.diskon" style="text-align: right;" @change="rowDataChange">
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
import SearchSelect from '@/Components/SearchSelect.vue';

export default {
    props : {
        unitId : { type:String, required:false,default:null },
        rowData : { type:Object, required:false, default:null },
        addFunction : { type:Function, required : true },
        //actSelected : { type:Function , required:true },
        
        dataChange : { type:Function, required:false, default:null },
        activationChange : { type:Function, required:false, default:null },
        linkCallback : { type:Function, required:false, default:null },

        // urlTindakan : { type:String, required:true, default:''},
        // urlDokter : { type:String, required:true, default:''},
        // docSelected : { type:Function, required:true },
    },
    name : 'row-input-tindakan-inap',
    components : {
        SearchSelect,
    },
    data() {
        return {
            isExpandedReg : false,
            selectAll : true,
            buffer : [],
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m uk-text-uppercase",
                compStyle : "padding:0;margin:0;",
                retrieveAll : false,
            },

            tindakanDesc : [
                { colName : 'tindakan_nama', labelData : '', isTitle : true },
                { colName : 'tindakan_id', labelData : '', isTitle : false },
            ],

            dokterDesc : [
                { colName : 'dokter_nama', labelData : '', isTitle : true },
                { colName : 'dokter_id', labelData : '', isTitle : false },
            ],

            urlPicker : '',
            // urlTindakan : `/units/${this.poliTransaction.unit_id}/acts`,
            // urlDokterPicker : `/units/${this.poliTransaction.dokter_id}/doctors`,
            urlDokterPicker : '',
            urlTindakanPicker : '',
            //urlTindakan : '/acts'
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatJalan',['poliTransaction','poliActs','poliItemUsages']),
        ...mapGetters('pemeriksaan',['examinationItems','tindakanLists','bhpLists','examination']),
    },
    watch: {
        'unitId' : function(newVal, oldVal){
            if(newVal !== null || newVal !== ''){
                this.urlDokterPicker = `/units/${newVal}/doctors`;
                this.urlTindakanPicker = `/units/${newVal}/acts`;
            }
        }
    },
    methods : {

        clearInputData(){
            this.$refs.tindakanPicker.clearInputData();
        },

        actSelected(dtSelected) {
            if(dtSelected) {
                let data = JSON.parse(JSON.stringify(dtSelected));
                let trxData = this.poliTransaction;
                let hargaRes = data.harga.find(item => item.buku_harga_id == trxData.buku_harga_id && item.kelas_id == trxData.kelas_harga_id);
                                
                if(!hargaRes) {
                    alert('harga pemeriksaan tidak ditemukan. Item tidak dapat ditambahkan.'); 
                    this.clearInputData(); 
                    return false;
                }
                else {
                    if(!hargaRes.komponen) {
                        alert('komponen harga pemeriksaan tidak ditemukan. Item tidak dapat ditambahkan.'); 
                        //this.clearTindakan();
                        return false;
                    }
                }

                let exist = false;
                if(this.examinationItems) {
                    exist = this.examinationItems.find(item => item.item_id == data.tindakan_id);
                    if(exist) { 
                        this.clearInputData();
                        alert('data sudah ada dalam list');
                        return false; 
                    }
                    else {                    
                        let komp = [];
                        hargaRes.komponen.forEach(dt => {
                            komp.push({
                                client_id : null,
                                komp_detail_id : null,
                                detail_id : null,
                                reg_id : null,
                                trx_id : null,
                                sub_trx_id : null,
                                jumlah : 1,
                                harga_id : dt.harga_id,
                                komp_harga_id : dt.komp_harga_id,
                                komp_harga : dt.komp_harga,
                                nilai : dt.nilai,
                                diskon : 0,
                                nilai_diskon : 0,
                                subtotal : dt.nilai,
                                is_diskon : dt.is_diskon,
                                is_ubah_manual : dt.is_ubah_manual,
                                is_realisasi : false,
                                is_bayar : false,
                                is_aktif : dt.is_aktif,
                            });
                        });

                        this.rowData.detail_id = null;
                        this.rowData.reg_id = null;
                        this.rowData.trx_id = null;
                        this.rowData.sub_trx_id = null;
                        this.rowData.jenis = 'TINDAKAN';
                        this.rowData.item_id = data.tindakan_id;
                        this.rowData.item_nama = data.tindakan_nama;
                        this.rowData.jumlah = 1;
                        this.rowData.satuan = 'X';
                        this.rowData.kelas_harga_id = trxData.kelas_harga_id;
                        this.rowData.buku_harga_id = trxData.buku_harga_id;
                        this.rowData.nilai = hargaRes.nilai;
                        this.rowData.diskon = 0;
                        this.rowData.diskon_persen = 0;
                        this.rowData.harga_diskon = 0;
                        this.rowData.subtotal = hargaRes.nilai;
                        this.rowData.dokter_pengirim_id = trxData.dokter_pengirim_id;
                        this.rowData.is_aktif = true;
                        this.rowData.komponen = komp;
                        this.rowData.bhp = data.bhp;
                    }
                }
            }
        },

        docSelected(data) {
            if(data) {
                this.rowData.dokter_id = data.dokter_id;
                this.rowData.dokter_nama = data.dokter_nama;
            }
            else {
                this.rowData.dokter_id = null;
                this.rowData.dokter_nama = null;
            }
        },

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
            //this.dataChange();
        },

        updateValue(data) {
            this.rowData = data;
        }
    }
}
</script>