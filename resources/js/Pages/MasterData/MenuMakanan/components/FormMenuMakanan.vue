<template>
    <div class="uk-container hims-form-container" id="formMenuMakanan" style="min-height:50vh;">
        <div class="uk-container" style="background-color:#fff;padding:1em;margin-top:1em;">
            <form action="" @submit.prevent="submitMenuMakanan">
                <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid>
                    <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                        <h5 class="uk-text-uppercase">FORM MENU MAKANAN</h5>
                    </div>                                
                    <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                        <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;border:none;">Tutup</button>                  
                    </div>
                    <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                        <button type="submit" class="uk-button hims-button-primary1 uk-button-default uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                    </div>
                </div>
                <div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA DIET</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                informasi diet menuMakanan.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="menuMakanan.deskripsi" type="text" style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Menu</label>
                                <div class="uk-form-controls">
                                    <search-select
                                        :config = configSelect
                                        :searchUrl = urlSearchMenu
                                        placeholder = "Menu Diet Pagi...."
                                        captionField = "nama_menu"
                                        valueField = "menu_id"
                                        :itemListData = menuDesc
                                        :value = menuMakanan.menu_id
                                        v-on:item-select = "menuSelected"
                                    ></search-select>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Sequence</label>
                                <div class="uk-form-controls">
                                    <select v-model="menuMakanan.sequence" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                        <option v-if="isRefPenunjangExist" v-for="dt in sequenceRefs.json_data" :value="dt">{{dt}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kelas</label>
                                <div class="uk-form-controls">
                                    <search-select
                                        :config = configSelect
                                        :searchUrl = urlSearchKelas
                                        placeholder = "VIP, VVIP, Kelas I, ..."
                                        captionField = "kelas_nama"
                                        valueField = "kelas_id"
                                        :itemListData = kelasDesc
                                        :value = menuMakanan.kelas_id
                                        v-on:item-select = "kelasSelected"
                                    ></search-select>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Meal Set</label>
                                <div class="uk-form-controls">
                                    <select v-model="menuMakanan.meal_set" class="uk-select uk-form-small">
                                        <option value="Pagi">Pagi</option>
                                        <option value="Siang">Siang</option>
                                        <option value="Sore">Sore</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                <div class="uk-form-controls">
                                    <textarea class="uk-textarea uk-form-small" v-model="menuMakanan.catatan" type="text"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA DETAIL MENU DIET</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                informasi menu diet menuMakanan.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Makanan</label>
                                <div class="uk-form-controls">
                                    <search-select
                                        :config = configSelect
                                        :searchUrl = makananUrl
                                        label = ""
                                        placeholder = "Apel, Jeruk, Nasi Putih, ..."
                                        captionField = "nama_makanan"
                                        valueField = "makanan_id"
                                        :itemListData = makananDesc
                                        :value = "addedMakanan.nama_makanan"
                                        v-on:item-select = "makananSelected"
                                    ></search-select>
                                </div>
                            </div>
                            <div class="uk-width-1-6@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Menu Standar</label>
                                <div class="uk-form-controls">
                                    <label style="color:black; font-size:14px;">
                                        <input class="uk-checkbox" type="checkbox" value="1" v-model="addedMakanan.is_standard">
                                    </label>
                                </div>
                            </div>
                            <div class="uk-width-1-6@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Menu Opsional</label>
                                <div class="uk-form-controls">
                                    <label style="color:black; font-size:14px;">
                                        <input class="uk-checkbox" type="checkbox" value="1" v-model="addedMakanan.is_optional">
                                    </label>
                                </div>
                            </div>
                            <div class="uk-width-1-1@m">
                                <div class="uk-form-controls">
                                    <button type="button" @click.prevent="addMakanan" class="uk-button-small uk-button-primary uk-box-shadow-small" style="border-radius: 5px; padding:0.2em 0.5em 0.2em 0.5em;">Insert</button>
                                </div>
                            </div>
                        </div>
                        <table class="uk-table hims-table1 uk-table-responsive1 uk-box-shadow-small">
                            <thead>
                                <tr style="border-bottom:1px solid #cc0202;color:white;padding:0.5em;font-weight:500;background-color:#cc0202;">
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Makanan ID</th>
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Nama Makanan</th>
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Kelompok</th>
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Tipe Makanan</th>
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Standard</th>
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Opsional</th>
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">...</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="itm in menuMakanan.foods" style="border-top:1px solid #eee;">
                                    <td style="padding:1em;font-weight:500;font-size:11px;color:black;">{{itm.makanan_id}}</td>
                                    <td style="padding:1em;font-size:11px;color:black;">{{itm.nama_makanan}}</td>
                                    <td style="padding:1em;font-size:11px;color:black;">{{itm.kelompok}}</td>
                                    <td style="padding:1em;font-size:11px;color:black;">{{itm.jns_makanan}}</td>
                                    <td style="padding:1em;font-size:11px;color:black;">{{itm.is_standard}}</td>
                                    <td style="padding:1em;font-size:11px;color:black;">{{itm.is_optional}}</td>
                                    <td style="padding:0.4em;margin:0;color:black;font-size:12px;text-align:center;">
                                        <button type="button" style="border:none;background-color: white; color:red;"><span uk-icon="minus-circle"></span></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
    name : 'form-menu-makanan', 
    components : {
        SearchSelect, SearchList
    },
    data() {
        return {
            tags: [],
            isLoading : true,
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            makananDesc : [
                { colName : 'nama_makanan', labelData : '', isTitle : true },
                { colName : 'makanan_id', labelData : '', isTitle : false },
            ],

            menuDesc : [
                { colName : 'nama_menu', labelData : '', isTitle : true },
                { colName : 'menu_id', labelData : '', isTitle : false },
            ],

            kelasDesc : [
                { colName : 'kelas_nama', labelData : '', isTitle : true },
                { colName : 'kelas_id', labelData : '', isTitle : false },
            ],

            isUpdate : true,
            makananUrl : '/meals',
            urlSearchMenu : '/menu',
            urlSearchKelas : '/services/classes',

            menuMakanan : {
                deskripsi : null,
                menu_id : null,
                nama_menu : null,
                sequence : null,
                kelas_id : null,
                nama_kelas : null,
                meal_set : null,
                catatan : null,  
                foods : [],           
            },

            addedMakanan : {
                makanan_id : null,
                nama_makanan : null,
                kelompok : null,
                jns_makanan : null,
                is_standard : false,
                is_optional : false
            }
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('refPenunjang',['isRefPenunjangExist', 'sequenceRefs']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('rawatJalan',['dataTransaksiPoli']),
        ...mapActions('menuMakanan',['createMenuMakanan']),
        ...mapActions('refPenunjang',['listRefPenunjang']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            let params = { is_aktif:true, per_page:'ALL' };
            this.listRefPenunjang(params);
        },

        newMenuMakanan() {
            // this.clearMenu();
            this.isUpdate = false;
            UIkit.modal('#formMenuMakanan').show();
        },

        closeModal(){
            this.clearPengguna();
            this.$emit('closed',true);
        },

        submitMenuMakanan(){            
            this.CLR_ERRORS();
            this.createMenuMakanan(this.menuMakanan).then((response) => {
                if (response.success == true) {
                    alert("Data menu makanan baru berhasil dibuat.");
                    // this.fillDiet(response.data);
                    this.$emit('saveSucceed',true);
                    this.isUpdate = true;
                }
            })         
        },

        newPengguna(){
            this.clearPengguna();
            this.isUpdate = false;
            this.isLoading = false;
        },

        editPengguna(data){
            this.clearPengguna();
            this.isLoading = true;
            this.dataTransaksiPoli(data.trx_id).then((response)=>{
                if (response.success == true) {
                    // this.fillDiet(response.data);
                    this.menuMakanan.foods = [];
                    this.isUpdate = true;
                } 
                else { alert(response.message); }
                this.isLoading = false;
            })
        },   

        clearPengguna(){
            this.menuMakanan.trx_id = null;
            this.menuMakanan.pasien_id = null;
            this.menuMakanan.nama_pasien = null;
            this.menuMakanan.tempat_lahir = null;
            this.menuMakanan.tgl_lahir = null;
            this.menuMakanan.usia = null;
            this.menuMakanan.unit_id = false;
            this.menuMakanan.unit_nama = null;
            this.menuMakanan.kelas_harga_id = null;
            this.menuMakanan.kelas_harga = null;
            this.menuMakanan.dokter_id = null;
            this.menuMakanan.dokter_nama = null;
            this.menuMakanan.is_aktif = null;    
            this.menuMakanan.foods = [];
        },

        fillDiet(data){
            this.menuMakanan.trx_id = data.trx_id;
            this.menuMakanan.pasien_id = data.pasien_id;
            this.menuMakanan.nama_pasien = data.nama_pasien;
            this.menuMakanan.tempat_lahir = data.tempat_lahir;
            this.menuMakanan.tgl_lahir = data.tgl_lahir;
            this.menuMakanan.usia = data.usia;
            this.menuMakanan.unit_id = data.unit_id;
            this.menuMakanan.unit_nama = data.unit_nama;
            this.menuMakanan.kelas_harga_id = data.kelas_harga_id;
            this.menuMakanan.kelas_harga = data.kelas_harga;
            this.menuMakanan.dokter_id = data.dokter_id;
            this.menuMakanan.dokter_nama = data.dokter_nama;
            this.menuMakanan.foods = data.foods;
        },
        
        makananSelected(data) {
            this.addedMakanan.makanan_id = null;
            this.addedMakanan.nama_makanan = null;
            if(data) {
                this.addedMakanan.makanan_id = data.makanan_id;
                this.addedMakanan.nama_makanan = data.nama_makanan; 
                this.addedMakanan.kelompok = data.kelompok; 
                this.addedMakanan.jns_makanan = data.jns_makanan;  
            }
        },

        menuSelected(data) {
            if(data){
                this.menuMakanan.menu_id = data.menu_id;
                this.menuMakanan.nama_menu = data.nama_menu;
            }
        },

        kelasSelected(data) {
            if(data){
                this.menuMakanan.kelas_id = data.kelas_id;
                this.menuMakanan.nama_kelas = data.kelas_nama;
            }
        },

        addMakanan() {
            if(this.addedMakanan.nama_makanan == "" || this.addedMakanan.nama_makanan == null) {
                alert("Data tidak dapat ditambahkan. ada data yang masih kurang.");
                return false;
            }

            const findItem = this.menuMakanan.foods.find(item => item.nama_makanan == this.addedMakanan.nama_makanan)
            if(findItem) {
                alert("Data sudah ada dalam list.");
                return false;
            }
            let addedVal = JSON.parse(JSON.stringify(this.addedMakanan));
            this.menuMakanan.foods.push(addedVal);
            this.clearAddedDiet();
        },

        clearAddedDiet() {
            this.addedMakanan.makanan_id = null;
            this.addedMakanan.nama_makanan = null;
            this.addedMakanan.is_standard = false;
            this.addedMakanan.is_optional = false;
        },
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400&display=swap');
  
  .container{
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    width:100%;
    height:100vh;
    background: linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(102,126,234,1) 50%, rgba(69,252,250,1) 100%);
  }
  
  a {
  position: absolute;
  right: 15px;
  bottom: 15px;
  font-weight: bold;
  text-decoration: none;
  color: #00003a;
  font-size: 20px;
}
  
  
/*tag input style*/
  
  .tag-input {
    width: 50%;
    border: 1px solid #D9DFE7;
    background: #fff;
    border-radius: 4px;
    font-size: 0.9em;
    min-height: 45px;
    box-sizing: border-box;
    padding: 0 10px;
    font-family: "Roboto";
    margin-bottom: 10px;
  }

  .tag-input__tag {
    height: 24px;
    color: white;
    float: left;
    font-size: 14px;
    margin-right: 10px;
    background-color: #667EEA;
    border-radius: 15px;
    margin-top: 10px;
    line-height: 24px;
    padding: 0 8px;
    font-family: "Roboto";
  }

  .tag-input__tag > span {
    cursor: pointer;
    opacity: 0.75;
    display: inline-block;
    margin-left: 8px;
  }

  .tag-input__text {
    border: none;
    outline: none;
    font-size: 1em;
  line-height: 40px;
  background: none;
  }

</style>