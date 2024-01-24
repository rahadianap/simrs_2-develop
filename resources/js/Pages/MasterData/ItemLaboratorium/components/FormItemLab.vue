<template>
    <div class="uk-container">
        <div class="uk-grid uk-grid-small" style="padding:0.5em 1em 1em 1em;">
            <div class="uk-width-auto" style="padding:0;">
                <a href="#" @click.prevent="closeModalItemLab" uk-icon="icon:arrow-left;ratio:1.5" style="display:block;width:30px;"></a>
            </div>
            <div class="uk-width-expand" style="padding:0;margin:0;">
                <ul class="uk-breadcrumb" style="padding:0;margin:0;">
                    <li>
                        <a href="#" @click.prevent="closeModalItemLab" class="uk-text-uppercase">
                            <h4 style="font-weight:500;margin:0;padding:0;display: inline-block;" class="uk-text-uppercase">MASTER ITEM LABORATORIUM DISINI</h4>
                        </a>
                    </li>
                    <li v-if="itemLab.hasil_nama !== null">
                        <span style="font-weight:400;color:#333;font-size:14px;">{{itemLab.hasil_nama}}</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="uk-container hims-form-container" style="min-height:70vh; background-color:#fff;padding:0 0.5em 0.5em 0.5em;">
            <form action="" @submit.prevent="submitItemLab" style="padding:0;" >
                <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                    <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                        <h5 class="uk-text-uppercase">ITEM HASIL LABORATORIUM</h5>
                    </div>                                
                    <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                        <button type="button" @click.prevent="closeModalItemLab" class="uk-button-close uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                    </div>
                    <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                        <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                    </div>
                </div>
                <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                    <p>{{ errors.invalid }}</p>
                </div>
                <div class="uk-grid-small hims-form-subpage" uk-grid >
                    <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">DATA UTAMA</h5>
                        <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                            informasi utama terkait pemeriksaan.
                        </p>
                    </div>
                    <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                        <div class="uk-width-3-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" :disabled="isUpdate" v-model="itemLab.hasil_nama" type="text" placeholder="nama item pemeriksaan" required style="color:black;">
                            </div>
                        </div>
                        <div class="uk-width-1-4@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No Urut</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="itemLab.no_urut" type="text" placeholder="no urut" style="color:black;">
                            </div>
                        </div>
                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Klasifikasi*</label>
                            <div class="uk-form-controls">
                                <select required v-model="itemLab.klasifikasi" class="uk-form-small uk-select" @change="klasifikasiSelected">
                                    <option v-for="dt in klasifikasiItemLabRefs.json_data" :value="dt.value">{{dt.value}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Sub Klasifikasi</label>
                            <div class="uk-form-controls">
                                <select v-model="itemLab.sub_klasifikasi" class="uk-form-small uk-select">
                                    <option v-for="dt in subKlasifikasi"  :value="dt">{{dt}}</option>
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m" style="margin:0;padding:0.5em 0.2em 0 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:12px; font-weight:400;">
                                    <input class="uk-checkbox" type="checkbox" v-model="itemLab.is_aktif"> 
                                    Item Aktif
                                    <span style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 0.2em;margin:0;">
                                    data item hasil pemeriksaan lab aktif
                                    </span> 
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-grid-small hims-form-subpage" uk-grid >
                    <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                        <h5 style="color:indigo;padding:0;margin:0;">ACUAN NILAI NORMAL                            
                        </h5>
                        <span style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                acuan nilai normal pemeriksaan lab
                            </span>
                    </div>
                    <div class="uk-width-3-4@s"  style="padding:0 0.5em 1em 0.5em;">
                        <div style="margin:0;padding:0;" class="uk-overflow-auto">
                            <table class="uk-table hims-table uk-table-responsive">
                                <thead class="uk-card uk-card-default uk-box-shadow-small" style="border-top:2px solid #cc0202;">
                                    <tr>
                                        <th>GROUP</th>
                                        <th>JENIS HASIL</th>
                                        <th>LAKI-LAKI</th>
                                        <th>PEREMPUAN</th>
                                        <th style="text-align:center;">AKTIF</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="itemLab.normal.length > 0" v-for="dt in itemLab.normal" :class=" !dt.is_aktif ? 'data-inactive' : ''">
                                        <td style="font-weight:500;">{{dt.normal_group}}</td>
                                        <td style="width:120px;">{{dt.jenis_hasil}}</td>
                                        <td>
                                            {{dt.lk_normal}}
                                        </td>
                                        <td>{{dt.pr_normal}}</td>
                                        <td style="padding:1em 0.5em 1em 0.5em; font-size:12px;text-align:center;">
                                            <input class="uk-checkbox" type="checkbox" @change="aktifChange" v-model="dt.is_aktif">
                                        </td>
                                    </tr>
                                    <tr v-else>
                                        <td colspan="6">
                                            <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">belum ada data untuk ditampilkan</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <form class="uk-width-1-1" action="" @submit.prevent="addDataNormal" style="padding:0;margin:0.5em 0 1em 0;">
                            <div class="uk-width-1-1 uk-box-shadow-large" uk-grid style="border-radius:5px;padding:0 0 0 0 ;margin:0;">         
                                <div class="uk-grid-small uk-width-full" uk-grid style="margin:0;padding:1em;">                                    
                                    <div class="uk-width-1-1@m uk-grid-small" uk-grid  style="padding:0;margin:0;">
                                        <div class="uk-width-1-2@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Group Nilai*</label>
                                            <div class="uk-form-controls">
                                                <select v-model="addedNormal.normal_group" class="uk-form-small uk-select" style="width:100%;" required>
                                                    <option v-for="dt in groupLabNormalRefs.json_data"  :value="dt.value">{{dt.value}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Hasil*</label>
                                            <div class="uk-form-controls">
                                                <select v-model="addedNormal.jenis_hasil" class="uk-form-small uk-select" style="width:100%;" required>
                                                    <option value="NILAI">Rentang Nilai</option>
                                                    <option value="PILIHAN">Pilihan</option>
                                                    <option value="TEXT">Text Bebas</option>
                                                    <option value="">Tidak memiliki hasil</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="uk-width-1-4@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Satuan</label>
                                            <div class="uk-form-controls">
                                                <select v-model="addedNormal.satuan" class="uk-form-small uk-select" style="width:100%;">
                                                    <option value=""></option>
                                                    <option v-for="dt in satuanLabNormalRefs.json_data"  :value="dt.value">{{dt.value}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-2@m uk-grid-small" uk-grid style="padding:0.5em 0 0 0;margin:0.5em 0 0 0;">
                                        <div v-if="addedNormal.jenis_hasil" class="uk-width-1-1@" style="background-color:#ccc;margin:0 0 0 1em;padding:0.5em;">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;font-weight:500;">LAKI-LAKI</label>
                                        </div>
                                        <div v-if="addedNormal.jenis_hasil == 'NILAI'" class="uk-width-1-3@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nilai Bawah</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small uk-text-uppercase" v-model="addedNormal.lk_nilai_min" type="number" step=".01" required>
                                            </div>
                                        </div>
                                        <div v-if="addedNormal.jenis_hasil == 'NILAI'" class="uk-width-1-3@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Operator</label>
                                            <div class="uk-form-controls">
                                                <select v-model="addedNormal.lk_operator" class="uk-form-small uk-select" style="width:100%;text-align:center;border:1px solid #cc0202;">
                                                    <option value="=">=</option>
                                                    <option value=">="><span>&#8805;</span></option>
                                                    <option value="<="><span>&#8804;</span></option>
                                                    <option value=">"><span>&#62;</span></option>
                                                    <option value="<"><span>&#60;</span></option>
                                                    <option value="-">~</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div v-if="addedNormal.jenis_hasil == 'NILAI'" class="uk-width-1-3@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nilai Atas</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" v-model="addedNormal.lk_nilai_maks" type="number" step=".01" required>
                                            </div>
                                        </div>
                                        <div v-if="addedNormal.jenis_hasil == 'PILIHAN'" class="uk-width-1-1@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Pilihan</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" v-model="addedNormal.lk_nilai_pilihan" type="text" required>
                                            </div>
                                        </div>
                                        <div v-if="addedNormal.jenis_hasil == 'PILIHAN' || addedNormal.jenis_hasil == 'TEXT'" class="uk-width-1-1@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nilai Normal</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" v-model="addedNormal.lk_normal" type="text">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="uk-width-1-2@m uk-grid-small" uk-grid style="padding:0.5em 0 0 0;margin:0.5em 0 0 0;">
                                        <div v-if="addedNormal.jenis_hasil" class="uk-width-1-1@" style="background-color:#ccc;margin:0 0 0 1em;padding:0.5em;">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;font-weight:500;">PEREMPUAN</label>
                                        </div>
                                        <div v-if="addedNormal.jenis_hasil == 'NILAI'" class="uk-width-1-3@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nilai Bawah </label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small uk-text-uppercase" v-model="addedNormal.pr_nilai_min" type="number" step=".01" required>
                                            </div>
                                        </div>
                                        <div v-if="addedNormal.jenis_hasil == 'NILAI'" class="uk-width-1-3@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Operator</label>
                                            <div class="uk-form-controls">
                                                <select v-model="addedNormal.pr_operator" class="uk-form-small uk-select" style="width:100%;text-align:center;border:1px solid #cc0202;">
                                                    <option value="=">=</option>
                                                    <option value=">="><span>&#8805;</span></option>
                                                    <option value="<="><span>&#8804;</span></option>
                                                    <option value=">"><span>&#62;</span></option>
                                                    <option value="<"><span>&#60;</span></option>
                                                    <option value="-">~</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div v-if="addedNormal.jenis_hasil == 'NILAI'" class="uk-width-1-3@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nilai Atas</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" v-model="addedNormal.pr_nilai_maks" type="number" step=".01" required>
                                            </div>
                                        </div>
                                        <div v-if="addedNormal.jenis_hasil == 'PILIHAN'" class="uk-width-1-1@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Pilihan</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" v-model="addedNormal.pr_nilai_pilihan" type="text" required>
                                            </div>
                                        </div>
                                        <div v-if="addedNormal.jenis_hasil == 'PILIHAN' || addedNormal.jenis_hasil == 'TEXT'" class="uk-width-1-1@m">
                                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nilai Normal</label>
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" v-model="addedNormal.pr_normal" type="text">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-1@m" style="padding:1em 0 0 0;margin:0.5em 0 0 0;text-align:right;">
                                        <button type="submit" class="uk-button-small" style="line-height: 20px;">SIMPAN</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </form>                          
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'form-item-lab', 
    components : { 
        SearchList,
    },
    data() {
        return {
            isUpdate : true,
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            KlasifikasiDesc : [
                { colName : 'value', labelData : '', isTitle : true },
            ],

            itemLab : {
                client_id:null,
                lab_hasil_id:null,
                hasil_nama : null,
                klasifikasi : null,   
                sub_klasifikasi : null,    
                no_urut : null,         
                rl_kode : null,
                is_aktif : true,
                normal : [],
            },

            addedNormal : {
                lab_normal_id : null,
                normal_group : null,
                jenis_hasil : '',
                is_semua_gender : true,
                //laki-laki
                lk_operator : null,
                lk_nilai_min : null,
                lk_nilai_maks : null,
                lk_nilai_pilihan : null,
                lk_normal : null,
                //perempuan
                pr_operator : null,
                pr_nilai_min : null,
                pr_nilai_maks : null,
                pr_nilai_pilihan : '',
                pr_normal : '',
                satuan : null,
                is_aktif : null,
            },

            subKlasifikasi : null,
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('refPenunjang',['groupLabNormalRefs','satuanLabNormalRefs','klasifikasiItemLabRefs']),
    },

    mounted() {
        //this.initialize();
    },
    
    methods : {
        ...mapActions('itemLab',['createItemLab','updateItemLab','dataItemLab']),
        ...mapActions('refPenunjang',['listRefPenunjang']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            let params = { is_aktif:true, per_page:'ALL' };
            this.listRefPenunjang(params);
        },

        closeModalItemLab(){    
            this.clearItemLab();        
            this.$emit('closed',true);
        },

        klasifikasiSelected(){
            let sub = this.klasifikasiItemLabRefs.json_data.find(item => item['value'] == this.itemLab.klasifikasi);
            if(sub) {
                this.subKlasifikasi = sub.sub;
            }
        },

        submitItemLab(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createItemLab(this.itemLab).then((response) => {
                    if (response.success == true) {
                        alert("item hasil pemeriksaan lab baru berhasil dibuat.");
                        this.fillItemLab(response.data);
                        this.$emit('succeed',true);
                        this.isUpdate = true;
                    }
                })
            }
            else {
                this.updateItemLab(this.itemLab).then((response) => {
                    if (response.success == true) {
                        this.fillItemLab(response.data);
                        alert("item hasil pemeriksaan lab berhasil diubah.");
                        this.$emit('succeed',true);
                        this.isUpdate = true;
                    }
                })
            }            
        },

        newItemLab(){
            this.clearItemLab();
            this.isUpdate = false;
        },

        editItemLab(id){
            this.clearItemLab();
            this.isUpdate = true;
            this.retrieveData(id);
        },

        retrieveData(id){
            this.dataItemLab(id).then((response)=>{
                if (response.success == true) {
                    this.fillItemLab(response.data);
                } else {
                    alert(response.message)
                }
            })
        },

        clearItemLab(){
            this.itemLab.client_id = null;
            this.itemLab.lab_hasil_id = null;
            this.itemLab.hasil_nama = null;
            this.itemLab.klasifikasi = null;
            this.itemLab.sub_klasifikasi = null;
            this.itemLab.no_urut = null;
            this.itemLab.rl_kode = null;
            this.itemLab.is_aktif = true;
            this.itemLab.normal = [];
            this.clearAddedNormal();
        },

        fillItemLab(data){
            this.itemLab.client_id = null;
            this.itemLab.lab_hasil_id = data.lab_hasil_id;
            this.itemLab.hasil_nama = data.hasil_nama;
            this.itemLab.klasifikasi = data.klasifikasi;            
            this.itemLab.sub_klasifikasi = data.sub_klasifikasi;
            this.itemLab.no_urut = data.no_urut;
            this.itemLab.rl_kode = data.rl_kode;
            this.itemLab.is_aktif = data.is_aktif;
            this.itemLab.normal = data.normal;
            this.clearAddedNormal();
            this.klasifikasiSelected();
        },

        refreshData(data){
            this.retrieveData(data.lab_hasil_id);
        },

        addDataNormal(){
            
            if(this.addedNormal.normal_group == '' || this.addedNormal.normal_group == null ){
                alert('Group nilai normal belum dipilih');
                return false;
            }

            if(this.addedNormal.jenis_hasil == '' || this.addedNormal.jenis_hasil == null ){
                alert('Jenis hasil belum dipilih');
                return false;
            }
            
            let exist = this.itemLab.normal.find(item => item['normal_group'] == this.addedNormal.normal_group);
            if(exist) {
                alert("Normal group sudah ada dalam list nilai normal.");
                this.clearAddedNormal();
                return false;
            }
            else {
                if(this.addedNormal.jenis_hasil == 'NILAI') {
                    this.addedNormal.lk_normal = `${this.addedNormal.lk_nilai_min} ${this.addedNormal.lk_operator} ${this.addedNormal.lk_nilai_maks} ${this.addedNormal.satuan}`;
                    this.addedNormal.pr_normal = `${this.addedNormal.pr_nilai_min} ${this.addedNormal.pr_operator} ${this.addedNormal.pr_nilai_maks} ${this.addedNormal.satuan}`;
                }
                let dt = JSON.parse(JSON.stringify(this.addedNormal));
                this.itemLab.normal.push(dt);
                this.clearAddedNormal();
            }
        },

        clearAddedNormal(){
            this.addedNormal.is_aktif = true;
            this.addedNormal.lab_normal_id = null;
            this.addedNormal.normal_group = null;
            this.addedNormal.jenis_hasil = null;
            this.addedNormal.satuan = null;
            
            this.addedNormal.lk_nilai_maks = null;
            this.addedNormal.lk_nilai_min = null;
            this.addedNormal.lk_operator = null;
            this.addedNormal.lk_normal = '';
            this.addedNormal.lk_nilai_pilihan = '';
            
            this.addedNormal.pr_nilai_maks = null;
            this.addedNormal.pr_nilai_min = null;
            this.addedNormal.pr_operator = null;
            this.addedNormal.pr_normal = '';
            this.addedNormal.pr_nilai_pilihan = '';
            
        },

        editNormal(data) {
            if(data) {
                this.addedNormal.lab_normal_id = data.lab_normal_id;
                this.addedNormal.normal_group = data.normal_group;
                this.addedNormal.jenis_hasil = data.jenis_hasil;
                this.addedNormal.is_semua_gender = true;
                //laki-laki
                this.addedNormal.lk_operator = data.lk_operator;
                this.addedNormal.lk_nilai_min = data.lk_nilai_min;
                this.addedNormal.lk_nilai_maks = data.lk_nilai_maks;
                this.addedNormal.lk_nilai_pilihan = data.lk_nilai_pilihan;
                this.addedNormal.lk_normal = data.lk_normal;
                //perempuan
                this.addedNormal.pr_operator = data.pr_operator;
                this.addedNormal.pr_nilai_min = data.pr_nilai_min;
                this.addedNormal.pr_nilai_maks = data.pr_nilai_maks;
                this.addedNormal.pr_nilai_pilihan = data.pr_nilai_pilihan;
                this.addedNormal.pr_normal = data.pr_normal;

                this.addedNormal.satuan = data.satuan;
                this.addedNormal.is_aktif = data.is_aktif;
            }
        },

        aktifChange() {
            this.itemLab.normal = this.itemLab.normal.filter( item => { return item.is_aktif == 1 || (item.lab_normal_id !== null && item.lab_normal_id !== ''); })
        }
    }
}
</script>

<style>
/* .uk-input, .uk-textarea, .uk-checkbox {
    border: 1px solid #999;
}

.hims-form-container label {
    color:#333;
    font-size:12x;
}

.hims-button-primary {
    background-color: #cc0202;
    color: #fafafa;
    border:2px solid #cc0202;
}

.uk-button {
    border:2px solid #aaa;
    font-weight:400;
}

.hims-button-primary {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}

.hims-button-primary:hover {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}
.hims-form-header {
    padding:0.5em 0 0 0; 
    margin:1em 0 0 0;
    position:sticky;
    top:0;
    background-color:white;
    z-index:99;
    color:#cc0202;
}

.hims-form-header h5 {
    color:#333;
    font-weight:500;
    font-size:18px;
}


.hims-form-subpage {
    padding:1em;
    margin:0 0.5em 0.5em 0.5em;
    border-top:1px solid #cc0202;
}

tr.data-inactive td {
    text-decoration: line-through;
    font-style: italic;
} */

</style>