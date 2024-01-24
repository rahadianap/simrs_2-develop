<template>
    <div class="uk-container hims-form-container" style="min-height:50vh;">
        <div class="uk-container" style="background-color:#fff;padding:0;margin:0;">
            <form action="" @submit.prevent="submitPengguna">
                <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid>
                    <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                        <h5 class="uk-text-uppercase">{{pengguna.nama_lengkap}}</h5>
                    </div>                                
                    <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                        <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;border:none;">Kembali</button>                  
                    </div>
                    <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                        <button type="submit" class="uk-button hims-button-primary uk-button-default uk-button-medium uk-box-shadow-large" style="font-size:12px;border:none;background-color: #cc0202;">Simpan</button>                  
                    </div>
                </div>
                <div> 
                    <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                        <div uk-spinner="ratio : 2"></div>
                        <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil data...</span>
                    </div>                         
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>              
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA PENGGUNA</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                informasi utama pengguna.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Username</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pengguna.username" type="text" disabled style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Email</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pengguna.email" type="text" disabled style="color:black;">
                                </div>
                            </div>
                            <!-- <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Lengkap</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pengguna.nama_lengkap" type="text" disabled style="color:black;">
                                </div>
                            </div> -->
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jenis Kelamin</label>
                                <div class="uk-form-controls">
                                    <select class="uk-select uk-form-small" v-model="pengguna.jenis_kelamin" disabled style="color:black;">
                                        <option value=""></option>
                                        <option value="L">LAKI-LAKI</option>
                                        <option value="P">PEREMPUAN</option>                                            
                                    </select>
                                </div>
                            </div>     
                            
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pengguna.tempat_lahir" type="text" disabled style="color:black;">
                                </div>
                            </div>
                            
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Lahir*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pengguna.tanggal_lahir" type="text" disabled style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No.Telepon</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pengguna.no_telepon" type="text" disabled style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NIK</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pengguna.nik" type="text" disabled style="color:black;">
                                </div>
                            </div>             
                            
                            <div class="uk-width-1-2@m">
                                <search-select
                                    :config = configSelect
                                    :searchUrl = roleUrl
                                    label = "Role pengguna"
                                    placeholder = "role pengguna"
                                    captionField = "role_nama"
                                    valueField = "role_nama"
                                    :itemListData = roleDesc
                                    :value = "pengguna.role_nama"
                                    v-on:item-select = "roleSelected"
                                ></search-select>
                            </div>

                            <div class="uk-width-1-2@m" style="margin:0;padding:2.5em 0.2em 0.2em 1em;">
                                <div class="uk-form-controls">
                                    <label style="color:black; font-size:14px;">
                                        <input class="uk-checkbox" type="checkbox" value="1" v-model="pengguna.is_aktif"> Pengguna aktif
                                    </label>
                                    <span style="font-size:11px; color:black; padding:0.2em;margin:0;">pengguna aktif dan boleh mengakses aplikasi.</span>
                                </div>
                            </div>

                            <div class="uk-width-1-1@m" style="margin:0;padding:1em 0.2em 0.2em 1em;">
                                <div class="uk-form-controls">
                                    <label style="color:black; font-size:14px;">
                                        <input class="uk-checkbox" type="checkbox" value="1" v-model="pengguna.is_teknisi"> Teknisi
                                    </label>
                                </div>
                                <p style="font-size:11px; color:black; padding:0.2em 0.2em 0.2em 2em;margin:0;">pengguna bekerja sebagai teknisi.</p>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">UNIT PENGGUNA</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                unit yang dapat diakses pengguna.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid>
                            <div class="uk-width-1-1@m uk-grid-small" uk-grid style="margin-top:0.2em;padding-left:2em;">   
                                <p style="padding:0;margin:0;font-size:11px;color:black;">
                                    Unit pelayanan yang dapat diakses oleh pengguna
                                </p>  
                                <table class="uk-table hims-table1 uk-table-responsive uk-box-shadow-small">
                                    <thead>
                                        <tr style="border-bottom:1px solid #cc0202;color:white;padding:0.5em;font-weight:500;background-color:#cc0202;">
                                            <th style="color:white;padding:0.5em;font-weight:500;background-color:#cc0202;">ID</th>
                                            <th style="color:white;padding:0.5em;font-weight:500;background-color:#cc0202;">UNIT</th>
                                            <th style="color:white;padding:0.5em;font-weight:500;width:50px;text-align:center;background-color:#cc0202;">...</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="itm in pengguna.units" style="border-top:1px solid #eee;" :class=" !itm.is_aktif ? 'data-inactive' : ''">
                                            <td style="padding:1em;font-weight:500;font-size:12px;color:black;">{{itm.unit_id}}</td>
                                            <td style="padding:1em;font-size:12px;color:black;">{{itm.unit_nama}}</td>
                                            <td style="font-size:12px;width:25px;text-align:center;padding:1.2em 0.2em 0.5em 0.2em;color:black;"><input class="uk-checkbox" v-model="itm.is_aktif" type="checkbox" @change="addedUnitAktifasi(itm)"></td>
                                        </tr>
                                        <tr style="padding:0;margin:0;">
                                            <td colspan="2" style="padding:0.5em 0.2em 0.5em 0.2em;">
                                                <search-select
                                                    :config = configSelect
                                                    :searchUrl = unitUrl
                                                    label = ""
                                                    placeholder = "unit pelayanan"
                                                    captionField = "unit_nama"
                                                    valueField = "unit_nama"
                                                    :itemListData = unitDesc
                                                    :value = "addedUnit.unit_nama"
                                                    v-on:item-select = "unitSelected"
                                                ></search-select>
                                            </td>
                                            <td style="width:25px;text-align:center;padding:0.3em 0 0 0;">
                                                <button type="button" @click.prevent="addUserUnit" class="uk-button-small" style="background-color:#eee; border:none; border-radius:5px; padding:0.2em 0.5em 0.2em 0.5em;"><span uk-icon="plus-circle"></span></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>                        
                        </div>
                    </div>

                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">UNIT PENGGUNA</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                unit yang dapat diakses pengguna.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid>
                            <div class="uk-width-1-1@m uk-grid-small" uk-grid style="margin-top:0.2em;padding-left:2em;">  
                                <p style="padding:0;margin:0;font-size:11px;color:black;">
                                    Unit pelayanan yang dapat diakses oleh pengguna
                                </p> 
                                <table class="uk-table hims-table1 uk-table-responsive uk-box-shadow-small">
                                    <thead>
                                        <tr style="border-bottom:1px solid #cc0202;color:white;padding:0.5em;font-weight:500;background-color:#cc0202;">
                                            <th style="color:white;padding:0.5em;font-weight:500;background-color:#cc0202;">ID</th>
                                            <th style="color:white;padding:0.5em;font-weight:500;background-color:#cc0202;">DEPO</th>
                                            <th style="color:white;padding:0.5em;font-weight:500;width:50px;text-align:center;background-color:#cc0202;">...</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="itm in pengguna.depos" style="border-top:1px solid #eee;" :class=" !itm.is_aktif ? 'data-inactive' : ''">
                                            <td style="font-size:12px;padding:1em;font-weight: 500;color:black;">{{itm.depo_id}}</td>
                                            <td style="font-size:12px;padding:1em;color:black;">{{itm.depo_nama}}</td>
                                            <td style="font-size:12px;width:25px;text-align:center;padding:1.2em 0.2em 0.5em 0.2em;">
                                                <input class="uk-checkbox" v-model="itm.is_aktif" type="checkbox" @change="addedDepoAktifasi(itm)">
                                            </td>
                                        </tr>
                                        <tr style="padding:0;margin:0;">
                                            <td colspan="2" style="padding:0.5em 0.2em 0.5em 0.2em;">
                                                <search-select
                                                    :config = configSelect
                                                    :searchUrl = depoUrl
                                                    label = ""
                                                    placeholder = "depo persediaan"
                                                    captionField = "depo_nama"
                                                    valueField = "depo_nama"
                                                    :itemListData = depoDesc
                                                    :value = "addedDepo.depo_nama"
                                                    v-on:item-select = "depoSelected"
                                                ></search-select>
                                            </td>
                                            <td style="width:25px;text-align:center;padding:0.3em 0 0 0;">
                                                <button type="button" @click.prevent="addUserDepo" class="uk-button-small" style="background-color:#eee; border:none; border-radius:5px; padding:0.2em 0.5em 0.2em 0.5em;"><span uk-icon="plus-circle"></span></button>
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
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'user-form', 
    components : {
        SearchSelect, SearchList
    },
    data() {
        return {
            isLoading : true,
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            unitDesc : [
                { colName : 'unit_nama', labelData : '', isTitle : true },
                { colName : 'unit_id', labelData : '', isTitle : false },
            ],

            depoDesc : [
                { colName : 'depo_nama', labelData : '', isTitle : true },
                { colName : 'depo_id', labelData : '', isTitle : false },
            ],

            roleDesc : [
                { colName : 'role_nama', labelData : '', isTitle : true },
                { colName : 'role_id', labelData : '', isTitle : false },
            ],

            isUpdate : true,
            depoUrl : '/depos',
            unitUrl : '/units',
            roleUrl : '/roles',

            addedUnit : {
                user_unit_id : null,
                unit_id : null,
                unit_nama : null,
                is_aktif : true,
            },
            addedDepo : {
                user_depo_id : null,
                depo_id : null,
                depo_nama : null,
                is_aktif : true,
            },

            pengguna : {
                username : null,
                email : null,
                user_id : null,
                nama_lengkap : null,
                role_id : null,
                role_nama : null,
                is_aktif : false,
                jenis_kelamin : null,
                tempat_lahir : null,
                tanggal_lahir : null,
                no_telepon : null,
                instagram : null,
                nik : null,
                avatar_path : null,
                avatar_url : null,
                bio_singkat : null,
                is_aktif : null,
                is_teknisi : null,    
                units : [],
                depos : [],            
            }
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('users',['createUser','updateUser','dataUser','addUserUnits','addUserDepos']),
        ...mapActions('refProduk',['listRefProduk']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
        },

        closeModal(){
            this.clearPengguna();
            this.$emit('closed',true);
        },

        submitPengguna(){            
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createUser(this.pengguna).then((response) => {
                    if (response.success == true) {
                        alert("Data pengguna baru berhasil dibuat.");
                        this.fillPengguna(response.data);
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                    }
                })
            }
            else {
                this.updateUser(this.pengguna).then((response) => {
                    if (response.success == true) {
                        this.fillPengguna(response.data);
                        alert("Data pengguna berhasil diubah.");
                        this.$emit('saveSucceed',true);
                        // this.closeModal();
                        this.isUpdate = true;
                    }
                })
            }            
        },

        newPengguna(){
            this.clearPengguna();
            this.isUpdate = false;
            this.isLoading = false;
        },

        editPengguna(data){
            this.clearPengguna();
            this.isLoading = true;
            this.dataUser(data.email).then((response)=>{
                if (response.success == true) {
                    this.fillPengguna(response.data);
                    this.isUpdate = true;
                } 
                else { alert(response.message); }
                this.isLoading = false;
            })
        },   

        clearPengguna(){
            this.pengguna.username = null;
            this.pengguna.email = null;
            this.pengguna.user_id = null;
            this.pengguna.nama_lengkap = null;
            this.pengguna.role_id = null;
            this.pengguna.role_nama = null;
            this.pengguna.is_aktif = true;
            this.pengguna.jenis_kelamin = null;
            this.pengguna.tempat_lahir = null;
            this.pengguna.tanggal_lahir = null;
            this.pengguna.no_telepon = null;
            this.pengguna.instagram = null;
            this.pengguna.nik = null;
            this.pengguna.avatar_path = null;
            this.pengguna.avatar_url = null;
            this.pengguna.bio_singkat = null;
            this.pengguna.is_teknisi = null;
            this.pengguna.units = [];
            this.pengguna.depos = [];
        },

        fillPengguna(data){
            this.pengguna.username = data.username;
            this.pengguna.email = data.email;
            this.pengguna.user_id = data.user_id;
            this.pengguna.nama_lengkap = data.nama_lengkap;
            this.pengguna.role_id = data.role_id;
            this.pengguna.role_nama = data.role_nama;
            this.pengguna.is_aktif = data.is_aktif;
            this.pengguna.jenis_kelamin = data.jenis_kelamin;
            this.pengguna.tempat_lahir = data.tempat_lahir;
            this.pengguna.tanggal_lahir = data.tanggal_lahir;
            this.pengguna.no_telepon = data.no_telepon;
            this.pengguna.instagram = data.instagram;
            this.pengguna.nik = data.nik;
            this.pengguna.avatar_path = data.avatar_path;
            this.pengguna.avatar_url = data.avatar_url;
            this.pengguna.bio_singkat = data.bio_singkat;
            this.pengguna.is_teknisi = data.is_teknisi;
            this.pengguna.units = data.units;
            this.pengguna.depos = data.depos;
        },

        addedUnitAktifasi(data){
            this.pengguna.units = this.pengguna.units.filter(item => { return item['is_aktif'] == true || item['user_unit_id'] !== null });
        },

        addedDepoAktifasi(data){
            this.pengguna.depos = this.pengguna.depos.filter(item => { return item['is_aktif'] == true || item['user_depo_id'] !== null });
        },

        roleSelected(data) {
            this.pengguna.role_id = null;
            this.pengguna.role_nama = null;
            if(data) {
                this.pengguna.role_id = data.role_id;
                this.pengguna.role_nama = data.role_nama;
            }
        },
        unitSelected(data) {
            this.addedUnit.unit_id = null;
            this.addedUnit.unit_nama = null;
            if(data) {
                this.addedUnit.unit_id = data.unit_id;
                this.addedUnit.unit_nama = data.unit_nama;
            }
        },
        depoSelected(data) {
            this.addedDepo.depo_id = null;
            this.addedDepo.depo_nama = null;
            if(data) {
                this.addedDepo.depo_id = data.depo_id;
                this.addedDepo.depo_nama = data.depo_nama;
            }
        },
        addUserDepo() {
            if(this.addedDepo.depo_nama == "" || this.addedDepo.depo_nama == null) {
                alert("Data tidak dapat ditambahkan. ada data yang masih kurang.");
                return false;
            }
            const findItem = this.pengguna.depos.find(item => item['depo_id'] == this.addedDepo.depo_id)
            if(findItem) {
                alert("Data sudah ada dalam list depo.");
                this.clearAddedDepo();                
                return false;
            }
            let addedVal = JSON.parse(JSON.stringify(this.addedDepo));
            this.pengguna.depos.push(addedVal);
            this.clearAddedDepo();
        },

        clearAddedDepo() {
            this.addedDepo.depo_id = null;
            this.addedDepo.depo_nama = null;
            this.addedDepo.user_depo_id = null;
            this.addedDepo.is_aktif = true;
        },

        addUserUnit() {
            if(this.addedUnit.unit_nama == "" || this.addedUnit.unit_nama == null) {
                alert("Data tidak dapat ditambahkan. ada data yang masih kurang.");
                return false;
            }
            const findItem = this.pengguna.units.find(item => item['unit_id'] == this.addedUnit.unit_id)
            if(findItem) {
                alert("Data sudah ada dalam list unit.");
                this.clearAddedUnit();
                return false;
            }
            let addedVal = JSON.parse(JSON.stringify(this.addedUnit));
            this.pengguna.units.push(addedVal);
            this.clearAddedUnit();
        },

        clearAddedUnit() {
            this.addedUnit.unit_id = null;
            this.addedUnit.unit_nama = null;
            this.addedUnit.user_unit_id = null;
            this.addedUnit.is_aktif = true;
        },
    }
}
</script>

<style>

</style>