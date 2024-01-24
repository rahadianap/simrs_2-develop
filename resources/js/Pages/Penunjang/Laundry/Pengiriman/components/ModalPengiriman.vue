<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="modalDistLinen" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body" style="padding:0;">
            <form action="" @submit.prevent="submitPengiriman">
                <div class="uk-card-default uk-grid-small" uk-grid style="padding:0;margin:0; border-top:3px solid #cc0202; height: 70px;">
                    <div class="uk-width-expand" style="padding:1em;">
                        <h3 style="color:black; font-weight: 400;">{{formTitle}}</h3>
                    </div>
                    <div class="uk-width-auto" style="padding:0.8em;">
                        <button class="uk-button" @click.prevent="modalClosed" type="button" style="color:#333; font-weight: 500;">TUTUP</button>
                        <button class="uk-button" type="submit" style="color:#fff; font-weight: 500;background-color: #cc0202;margin-left:5px;">SIMPAN</button>
                    </div>
                </div>
                <div class="hims-form-container" style="padding:0 1.5em 2em 1.5em; margin:0;">                    
                    <div class="uk-grid-small hims-form-subpage" uk-grid  style="border:none;">
                        <div class="uk-width-1-4@m" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA PENGIRIMAN</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                detail data pengiriman linen ke unit-unit.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;">
                            <div class="uk-grid-small uk-width-3-4@l 1-1@m" uk-grid> 
                                <div class="uk-width-1-1">
                                    <h5 style="font-weight:500;">{{pengiriman.linen_dist_id}}</h5>
                                </div>
                                <div class="uk-width-1-4@m uk-hidden">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Status*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="text" v-model="pengiriman.status" disabled style="color:black;">
                                    </div>
                                </div>

                                
                       
                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Kirim*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="date" v-model="pengiriman.tgl_kirim" style="color:black;">
                                    </div>
                                </div>

                                <div class="uk-width-1-4@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Kirim*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" type="time" v-model="pengiriman.jam_kirim" style="color:black;">
                                    </div>
                                </div>

                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pengirim*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small uk-text-uppercase" type="text" v-model="pengiriman.pengirim" disabled style="color:black;">
                                    </div>
                                </div>  

                                <div class="uk-width-1-2@m">
                                    <search-select
                                        :config = configSelect
                                        :searchUrl = unitUrl
                                        label = "Unit Penerima*"
                                        placeholder = "pilih unit penerima"
                                        captionField = "unit_nama"
                                        valueField = "unit_nama"
                                        :itemListData = unitDesc
                                        :value = "pengiriman.unit_penerima"
                                        v-on:item-select = "unitSelected"
                                    ></search-select>
                                </div>

                                <div class="uk-width-1-2@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Penerima*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small uk-text-uppercase" type="text" v-model="pengiriman.penerima" style="color:black;">
                                    </div>
                                </div>                                
                                
                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" type="text" v-model="pengiriman.catatan" style="color:black;"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-1@m" style="padding:0 0.5em 0.5 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">ITEM PENGIRIMAN 
                                <span style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    daftar item linen yang dikirim
                                </span>
                            </h5>
                        </div>
                        <div class="uk-width-1-1@m uk-grid-small" uk-grid  style="padding:0 0.5em 0.2em 0.5em;"> 
                            <div class="uk-width-1-1@m" style="padding:0 0.5em 0.2em 0.5em;margin:0;">
                                <table class="uk-table hims-table">
                                    <thead class="uk-card uk-card-default uk-box-shadow-small" style="border-top:2px solid #cc0202;">
                                        <tr>
                                            <th style="padding:0.5em;width: 150px;">ID</th>
                                            <th style="padding:0.5em;">Produk</th>
                                            <th style="text-align:center;width:120px;padding:0.5em;">Satuan</th>
                                            <th style="text-align:center;width:70px;padding:0.5em;">Jumlah</th>
                                            <th style="text-align:center;width:50px;padding:0.5em;">...</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="2" style="padding:0.2em;margin:0;">    
                                                <search-select
                                                    :config = configItemSelect
                                                    :searchUrl = linenUrl
                                                    placeholder = "item linen"
                                                    captionField = "produk_nama"
                                                    valueField = "produk_nama"
                                                    :itemListData = linenDesc
                                                    :value = "linenItem.produk_nama"
                                                    v-on:item-select = "itemSelected"
                                                ></search-select>
                                            </td>                                            
                                            <td style="text-align:center;width:100px;padding:0.2em;margin:0;">
                                                <input class="uk-input uk-form-small" type="text" v-model="linenItem.satuan" style="text-align:center; color:black;" disabled>
                                            </td>
                                            <td style="text-align:center;width:100px;padding:0.2em;margin:0;">
                                                <input class="uk-input uk-form-small" type="number" v-model="linenItem.jml_keluar" min="0" style="text-align: center;">
                                            </td>
                                            <td style="padding:0.5em; font-size:12px;text-align:center;">
                                                <a href="#" uk-icon="icon:plus-circle;ratio:1" @click.prevent="addLinenItem"></a>
                                            </td>                               
                                        </tr>

                                        <tr  style="border-top:1px solid #ccc;" v-if="pengiriman.items.length > 0" v-for="dt in pengiriman.items">
                                            <td style="width:150px;font-weight: 500;">{{dt.produk_id}}</td>
                                            <td>{{dt.produk_nama}}</td>
                                            <td style="text-align:center;">{{dt.satuan}}</td>
                                            <td style="text-align:center;padding:0.5em 0.2em 0 0.2em;margin:0;">
                                                <input class="uk-input uk-form-small" type="number" v-model="dt.jml_keluar" min="0" style="text-align: center;">
                                            </td>
                                            <td style="padding:1em 0.5em 1em 0.5em; text-align:center;">
                                                <input class="uk-checkbox" type="checkbox" v-model="dt.is_aktif" style="text-align: center;" @change="changeActivationLinenItem">
                                            </td>
                                        </tr>
                                        <tr v-else>
                                            <td colspan="5">
                                                <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">belum ada data untuk ditampilkan</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>        
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'modal-pengiriman',
    components : { SearchSelect,SearchList },
    data() {
        return {
            formTitle : 'PENGIRIMAN LINEN',
            isUpdate : true,
            isLoading : true,
            configSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            configItemSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            linenDesc : [
                { colName : 'produk_nama', labelData : '', isTitle : true },
                { colName : 'produk_id', labelData : '', isTitle : false },
            ],
            unitDesc : [
                { colName : 'unit_nama', labelData : '', isTitle : true },
                { colName : 'unit_id', labelData : '', isTitle : false },
            ],
            satuanDesc : [
                { colName : 'value', labelData : '', isTitle : true },
                { colName : 'text', labelData : '', isTitle : false },
            ],   
            pengiriman : {
                linen_dist_id : null,
                unit_pengirim_id : null,
                unit_pengirim : null,
                unit_penerima_id : null,
                unit_penerima : null,
                pengirim : null,
                penerima : null,
                
                tgl_kirim : null,
                jam_kirim : null,
                tgl_terima : null,
                jam_terima : null,
                
                catatan : null,
                berat : 0,
                satuan : null,
                is_infeksius : false,
                kondisi : null,
                status : 'KIRIM',
                is_aktif : true,
                client_id : null,
                items : [],
            },

            linenItem : {
                linen_detail_id : null,
                trx_linen_id : null,
                produk_id : null,
                produk_nama : null,
                satuan : null,
                jml_terima : 0,
                jml_keluar : 0,
                jml_rusak : 0,
                jml_perawatan : 0,
                jml_awal : 0,
                jml_penyesuaian : 0,
                status : 'KIRIM',
                is_aktif : true,
                client_id : null,
            },

            linenLists : [],
            linenUrl : '',
            unitUrl : '',
        }
    },

    computed : {
        ...mapGetters('refProduk',['isRefProdukExist','satuanProdukRefs']),
    },

    mounted() {     
        this.initialize();
    },

    methods : {
        ...mapActions('linen',['getLinenLists','createPengirimanLinen','updatePengirimanLinen','deletePengirimanLinen','dataPengirimanLinen']),
        ...mapActions('refProduk',['listRefProduk']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            this.CLR_ERRORS();
            this.listRefProduk({per_page:'ALL'});
        },

        /**
         * get date today for default value input date 
         */
         getTodayDate(){
            let dt = new Date();
            let year = dt.getFullYear();
            let month = dt.getMonth() + 1;
            if(month < 10) {month = `0${month}`}
            let date = dt.getDate();
            if(date < 10) {date = `0${date}`}
            let str_dt = `${year}-${month}-${date}`
            return str_dt;
        },

        /** function called before modal closed */
        modalClosed(){
            let data = JSON.parse(JSON.stringify(this.pengiriman));
            this.clearLinenItem();
            this.clearPengiriman();
            this.$emit('modalDistLinenClosed',data);
            UIkit.modal('#modalDistLinen').hide();            
        },

        /**modal call from other component for new data entry */
        newData(){
            if(this.linenUrl == null || this.linenUrl == '') {
                this.linenUrl = '/linens/distributions/product';
                this.unitUrl = '/units';
            }
            
            this.CLR_ERRORS();
            this.clearPengiriman();
            this.clearLinenItem();
            this.isUpdate = false;
            this.formTitle = "DATA BARU - PENGIRIMAN LINEN";
            UIkit.modal('#modalDistLinen').show();
        },
        
        /**modal call from other component for update data entry */
        editData(refData) {
            if(this.linenUrl == null || this.linenUrl == '') {
                this.linenUrl = '/linens/distributions/product';
                this.unitUrl = '/units';
            }

            this.CLR_ERRORS();
            this.clearPengiriman();
            this.clearLinenItem();
            this.formTitle = "UBAH DATA - PENGIRIMAN LINEN";
            this.isUpdate = true;
            this.isLoading = true;

            if(refData) {
                this.dataPengirimanLinen(refData.linen_dist_id).then((response) => {
                    if (response.success == true) {
                        this.fillPengiriman(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        UIkit.modal('#modalDistLinen').show();
                    }
                    else { alert('data pengiriman linen tidak ditemukan'); }
                })
            }
        },

        clearLinenItem() {
            this.linenItem.linen_detail_id = null;
            this.linenItem.trx_linen_id = null;
            this.linenItem.produk_id = null;
            this.linenItem.produk_nama = null;
            this.linenItem.satuan = null;
            this.linenItem.jml_keluar = 0;
            this.linenItem.jml_terima = 0;
            this.linenItem.jml_rusak = 0;
            this.linenItem.jml_perawatan = 0;            
            this.linenItem.jml_awal = 0;
            this.linenItem.jml_penyesuaian = 0;
            this.linenItem.status = null;
            this.linenItem.kondisi = null;
            this.linenItem.is_aktif = true;
            this.linenItem.client_id = null;
        },

        clearPengiriman() {
            this.pengiriman.linen_dist_id = null;

            this.pengiriman.unit_pengirim_id = null;
            this.pengiriman.unit_pengirim = null;
            this.pengiriman.unit_penerima_id = null;
            this.pengiriman.unit_penerima = null;

            this.pengiriman.pengirim = null;
            this.pengiriman.penerima = null;

            this.pengiriman.tgl_terima = this.getTodayDate();
            this.pengiriman.jam_terima = null;
            this.pengiriman.tgl_kirim = this.getTodayDate();
            this.pengiriman.jam_kirim = null;

            this.pengiriman.tgl_selesai = null;
            this.pengiriman.berat = 0;
            this.pengiriman.satuan = null;
            this.pengiriman.catatan = null;            
            this.pengiriman.is_infeksius = false;
            this.pengiriman.status = 'KIRIM';
            this.pengiriman.is_aktif = true;
            this.pengiriman.client_id = null;
            this.pengiriman.items = [];        
        },

        fillPengiriman(data) {
            if(data) {
                this.pengiriman.linen_dist_id = data.linen_dist_id;

                this.pengiriman.unit_pengirim_id = data.unit_pengirim_id;
                this.pengiriman.unit_pengirim = data.unit_pengirim;
                this.pengiriman.unit_penerima_id = data.unit_penerima_id;
                this.pengiriman.unit_penerima = data.unit_penerima;

                this.pengiriman.pengirim = data.pengirim;
                this.pengiriman.penerima = data.penerima;
                
                this.pengiriman.tgl_terima = data.tgl_terima;
                this.pengiriman.jam_terima = data.jam_terima;
                this.pengiriman.tgl_kirim = data.tgl_kirim;
                this.pengiriman.jam_kirim = data.jam_kirim;

                this.pengiriman.tgl_selesai = data.tgl_selesai;
                
                this.pengiriman.berat = data.berat;
                this.pengiriman.satuan = data.satuan;
                this.pengiriman.is_infeksius = data.is_infeksius;
                this.pengiriman.catatan = data.catatan;
                this.pengiriman.status = data.status;
                this.pengiriman.is_aktif = data.is_aktif;
                this.pengiriman.client_id = data.client_id;
                this.pengiriman.items = data.items;        
            }
        },

        addLinenItem() {
            let data = JSON.parse(JSON.stringify(this.linenItem));
            let dt = this.pengiriman.items.find(item => item.produk_id === data.produk_id);
            if(!dt) {
                this.pengiriman.items.push(data);
                this.clearLinenItem();
            }
            else {
                alert('Item linen sudah ada di dalam daftar pengiriman. Data tidak dapat ditambahkan.');
                this.clearLinenItem();
            }
        },

        changeActivationLinenItem(){
            let filteredData = this.pengiriman.items.filter(
                item => { 
                    return item.is_aktif == true || (item.linen_detail_id !== null && item.linen_detail_id !== '') 
                }
            );
            this.pengiriman.items = JSON.parse(JSON.stringify(filteredData));
        },

        itemSelected(data) {
            this.clearLinenItem();
            if(data) {
                this.linenItem.produk_id = data.produk_id;
                this.linenItem.produk_nama = data.produk_nama;
                this.linenItem.satuan = data.satuan;
                this.linenItem.jml_keluar = 1;
            }
        },

        unitSelected(data) {
            if(data) {
                this.pengiriman.unit_penerima_id = data.unit_id;
                this.pengiriman.unit_penerima = data.unit_nama;                
            }
        },

        satuanSelected(data){
            this.pengiriman.satuan = data.value;
        },

        submitPengiriman(){
            this.CLR_ERRORS();
            this.isLoading = true;
            
            if(this.isUpdate) {
                //update data
                this.updatePengirimanLinen(this.pengiriman).then((response) => {
                    if (response.success == true) {
                        this.fillPengiriman(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        alert('Data berhasil disimpan.');
                        this.modalClosed();
                    }
                    else { alert(response.message); this.isLoading = false; }
                })
            }
            else {
                //data baru
                this.createPengirimanLinen(this.pengiriman).then((response) => {
                    if (response.success == true) {
                        this.fillPengiriman(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        alert('Data berhasil disimpan.');
                        this.modalClosed();
                    }
                    else { alert(response.message); this.isLoading = false; }
                })
            }
        }
    }
}

</script>
<style>
/* .uk-input, .uk-textarea, .uk-select, .uk-checkbox, .uk-radio {
    border:1px solid #333;
}

.hims-form-subpage {
    padding:1em;
    margin:0 0.5em 0.5em 0.5em;
    border-top:1px solid #cc0202;
} */
</style>