<template>
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="formHarga" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body" >
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitHarga" style="padding:0;margin:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid style="padding:0;margin:0;">
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">HARGA PELAYANAN</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="newHarga(harga)" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Baru</button>
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModalHarga" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid style="border:none;">
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA PELAYANAN</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                data playanan dan kelas harga.
                            </p>
                        </div>
                        <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">ID Layanan*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small uk-text-uppercase" v-model="harga.tindakan_id" type="text" required disabled style="color:black;">
                                </div>
                            </div>

                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Layanan*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small uk-text-uppercase" v-model="harga.tindakan_nama" type="text" required disabled style="color:black;">
                                </div>
                            </div>

                            <div class="uk-width-1-4@m">
                                <!-- <search-list
                                    :config = configSelect
                                    :dataLists = kelasLists
                                    label = "Kelas harga*"
                                    placeholder = "pilih kelas layanan"
                                    captionField = "kelas_nama"
                                    valueField = "kelas_nama"
                                    :detailItems = kelasDesc
                                    :value = "harga.kelas_nama"
                                    v-on:item-select = "kelasSelected"
                                ></search-list> -->
                                <search-select
                                    :config = configSelect
                                    searchUrl = "/services/classes"
                                    :itemListData = kelasDesc
                                    label = "Kelas harga*"
                                    placeholder = "pilih kelas layanan"
                                    captionField = "kelas_nama"
                                    valueField = "kelas_nama"
                                    :value = "harga.kelas_nama"
                                    v-on:item-select = "kelasSelected"
                                ></search-select>
                            </div>
                            <div class="uk-width-1-1@m" style="margin:0;padding:1em 0.2em 0.2em 1.2em;">
                                <div class="uk-form-controls">
                                    <label style="color:black; font-size:14px;font-weight: 400;">
                                        <input class="uk-checkbox" type="checkbox" value="1" v-model="harga.is_aktif" disabled> Data Harga Aktif
                                    </label>
                                    <span style="font-size:12px; color:black; padding:0.2em 0.2em 0.2em 1.2em; margin:0; font-style: italic;">kelompok harga aktif.</span>                                        
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid>
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">KOMPONEN HARGA</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                nilai (harga) pelayanan merupakan penjumlahan dari komponen.
                            </p>
                        </div>
                        <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                            <div class="uk-width-1-1">
                                <table class="uk-table hims-table uk-table-responsive uk-box-shadow-small">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>KOMPONEN</th>
                                            <!-- <th style="width:60px;text-align:center;">HARGA LAMA</th> -->
                                            <th style="width:60px;text-align:center;">HARGA BARU</th>
                                            <th style="width:60px;text-align:center;">DISKON</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="isUpdate">
                                            <td colspan="2" style="padding:0.5em;margin:0;color:black;font-size:12px;">
                                                <select v-model="komponen.komp_harga_id" class="uk-select uk-form-small" @change="getKomponenHarga">
                                                    <option v-for="option in komponenHargaLists" v-bind:value="option.komp_harga_id" v-bind:key="option.komp_harga_id">{{option.komp_harga_id}} - {{option.komp_harga}}</option>
                                                </select>
                                            </td>
                                            <td colspan="2" style="padding:0.5em;margin:0;color:black;font-size:12px;">
                                                <input class="uk-input uk-form-small" type="number" v-model="komponen.nilai_rencana" placeholder="masukkan nilai...">
                                            </td>    
                                            <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;">
                                                <a href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="addData" style="padding:0;margin:0;color:blue;display:inline-block;"></a>
                                            </td>
                                        </tr>
                                        <tr v-for="itm in harga.detail" style="border-top:1px solid #eee;" :class=" !itm.is_aktif ? 'data-inactive' : ''">
                                            <td style="max-width:140px;font-weight:500;">
                                                {{itm.komp_harga_id}}
                                            </td>
                                            <td style="font-weight:400;">
                                                {{itm.komp_harga}}
                                            </td>
                                            <!-- <td style="width:100px;text-align:center;padding:0.5em 0.2em 0.5em 0.2em;">
                                                <input class="uk-input uk-form-small" v-model="itm.nilai" type="number" style="text-align:right;" disabled>
                                            </td> -->
                                            <td style="width:100px;text-align:center;padding:0.5em 0.2em 0.5em 0.2em;">
                                                <input class="uk-input uk-form-small" v-model="itm.nilai_rencana" type="number" style="text-align:right;" @change="calculateTotal">
                                            </td>
                                            <td style="width:25px;text-align:center;padding:1.1em 0.2em 0.5em 0.2em;">
                                                <input class="uk-checkbox" v-model="itm.is_diskon" type="checkbox">
                                            </td> 
                                        </tr>
                                        <tr style="padding:0;margin:0;border-top:1px solid #aaa;">
                                            <td colspan="2" style="text-align:right;font-size: 14px; font-weight: 500;">Harga Pelayanan</td>
                                            <td style="width:100px;text-align:right; font-weight: 500; font-size:14px;">
                                                <p style>{{formatCurrency(harga.nilai)}}</p>
                                            </td>
                                            <td style="width:100px;text-align:right; font-weight: 500; font-size:14px;">
                                                <p style>{{formatCurrency(harga.nilai_rencana)}}</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div> 
                    </div> 
                </form>                          
            </div>
        </div>
    </div> 
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchList from '@/Components/SearchList';
import SearchSelect from '@/Components/SearchSelect.vue';

export default {
    name : 'modal-harga', 
    components : {
        SearchList,
        SearchSelect    
    },
    data() {
        return {
            isUpdate : true,
            configSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
                retrieveAll : true,
            },            
            kelasDesc : [
                { colName : 'kelas_nama', labelData : '', isTitle : true },
                { colName : 'kelas_id', labelData : '', isTitle : false },
            ],
            tindakan : {
                tindakan_id : null,
                tindakan_nama : null,
            },             
            harga : {
                harga_id : null,
                buku_harga_id : null,
                tindakan_id : null,
                tindakan_nama : null,

                kelas_id : null,
                kelas_nama : null,
                nilai : 0,
                detail : [],
                is_aktif : true,
            },
            komponen : {
                'harga_id' : null,
                'komp_harga' : null,
                'komp_harga_id' : null,
                'nilai' : 0,
                'nilai_rencana' : 0,
                'is_diskon' : false,
                'is_ubah_manual': false,
                'is_aktif' : true,
            },
            kelasLists : null,
            komponenHargaLists : [],
            
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('harga',['createHarga','dataHarga','updateHarga','deleteHarga']),
        ...mapActions('kelas',['listKelas']),
        ...mapActions('komponenHarga',['listKomponenHarga']),
        
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            this.CLR_ERRORS();
            this.listKelas({'per_page':'ALL'}).then((response)=>{
                if (response.success == true) {
                    this.kelasLists = response.data.data;
                }
            });

            this.listKomponenHarga({'per_page':'ALL'}).then((response)=>{
                if (response.success == true) {
                    this.komponenHargaLists = response.data.data;
                }
            });
        },

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },
        
        closeModalHarga(){
            this.$emit('modalHargaClosed',true);
            UIkit.modal('#formHarga').hide();
        },

        submitHarga(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createHarga(this.harga).then((response) => {
                    if (response.success == true) {
                        alert("harga baru berhasil ditambahkan.");
                        this.newHarga(this.harga);
                        this.isUpdate = false;
                    }
                })
            }    
            else {
                this.updateHarga(this.harga).then((response) => {
                    if (response.success == true) {
                        alert("harga berhasil diubah.");
                        this.clearHarga();
                        this.isUpdate = false;
                        this.closeModalHarga();
                    }
                })
            }         
        },
        
        newHarga(data){
            let val = JSON.parse(JSON.stringify(data));
            this.clearHarga();
            this.harga.buku_harga_id = val.buku_harga_id;
            this.harga.buku_nama = val.buku_nama;
            this.harga.tindakan_id = val.tindakan_id;
            this.harga.tindakan_nama = val.tindakan_nama;
            this.isUpdate = false;
            this.configSelect.disabled = false;
            UIkit.modal('#formHarga').show();
        },  

        clearHarga(){
            this.harga.harga_id = null;
            this.harga.buku_harga_id = null;
            this.harga.tindakan_id = null;
            this.harga.tindakan_nama = null;
            this.harga.kelas_id = null;
            this.harga.kelas_nama = null;
            this.harga.nilai = 0;
            this.harga.nilai_rencana = 0;
            this.harga.detail = [];
            let me = this;

            this.komponenHargaLists.forEach(function(dt){
                me.harga.detail.push({
                    'harga_id' : null,
                    'komp_harga' : dt.komp_harga,
                    'komp_harga_id' : dt.komp_harga_id,
                    'nilai' : 0,
                    'nilai_rencana' : 0,
                    'is_diskon' : false,
                    'is_ubah_manual': false,
                    'is_aktif' : true,
                });
            });
        },

        editHarga(id){
            this.clearHarga();            
            this.dataHarga(id).then((response)=>{
                if (response.success == true) {
                    this.fillHarga(response.data);
                    this.isUpdate = true;
                    this.configSelect.disabled = true;
                    UIkit.modal('#formHarga').show();
                } else {
                    alert(response.message);
                }
            })
        },   

        fillHarga(data){
            this.harga.harga_id = data.harga_id;
            this.harga.buku_harga_id = data.buku_harga_id;
            this.harga.buku_nama = data.buku_nama;
            this.harga.tindakan_id =data.tindakan_id;
            this.harga.tindakan_nama = data.tindakan_nama;
            this.harga.kelas_id = data.kelas_id;
            this.harga.kelas_nama = data.kelas_nama;
            this.harga.nilai = data.nilai;
            this.harga.nilai_rencana = data.nilai_rencana;
            this.harga.detail = data.detail;
            
        },

        kelasSelected(data){
            if(data) {
                this.harga.kelas_id = data.kelas_id;
                this.harga.kelas_nama = data.kelas_nama;
            }
        },

        calculateTotal(){
            this.harga.nilai_rencana = 0;
            let me = this;
            this.harga.detail.forEach(function(dt){
                me.harga.nilai_rencana = (me.harga.nilai_rencana * 1) + (dt.nilai_rencana * 1);
            })
        },

        addData(){
            let data = JSON.parse(JSON.stringify(this.komponen));
            let isExist = this.harga.detail.find( item => item.komp_harga_id == data.komp_harga_id );
            if(isExist) { alert('data komponen sudah ada.'); return false; }
            else {
                this.harga.detail.push({
                    'harga_id' : null,
                    'komp_harga' : data.komp_harga,
                    'komp_harga_id' : data.komp_harga_id,
                    'nilai' : 0,
                    'nilai_rencana' : data.nilai_rencana,
                    'is_diskon' : data.is_diskon,
                    'is_ubah_manual': false,
                    'is_aktif' : true,
                });
                this.calculateTotal();
                this.clearKomponen();
            }
        },

        clearKomponen(){
            this.komponen.harga_id = null;
            this.komponen.komp_harga = null;
            this.komponen.komp_harga_id = null;
            this.komponen.nilai = 0;
            this.komponen.nilai_rencana = 0;
            this.komponen.is_diskon = false;
            this.komponen.is_ubah_manual = false;
            this.komponen.is_aktif = true;
        },

        getKomponenHarga(){
            let val = this.komponenHargaLists.find( item => item.komp_harga_id == this.komponen.komp_harga_id );
            if(val) {
                this.komponen.komp_harga = val['komp_harga'];
            }
        }
    }
}
</script>
