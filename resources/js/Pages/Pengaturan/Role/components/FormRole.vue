<template>
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="formRole" style="min-height:50vh;padding:0;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container hims-form-container" style="min-height:50vh; background-color:#fff;">
                <form action="" @submit.prevent="submitRole" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA ROLE PENGGUNA</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div>                  
                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                            <p>{{ errors.invalid }}</p>
                        </div>              
                        <div class="uk-grid-small hims-form-subpage" uk-grid >
                            <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">ROLE PENGGUNA</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    informasi utama terkait role pengguna.
                                </p>
                            </div>
                            <div class="uk-width-3-4@s uk-grid-small" uk-grid> 
                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="role.role_nama" type="text" placeholder="nama role" required>
                                    </div>
                                </div>
                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" v-model="role.deskripsi" type="text" required placeholder="deskripsi role pengguna"></textarea>
                                    </div>
                                </div>                                       
                                <div class="uk-width-1-1" style="padding:0.2em 1em 0.2em 1em;margin:0;">
                                    <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="role.is_ubah_tanggal" style="margin-left:5px;"> Ijinkan mengubah tanggal</label>
                                </div>
                                <div class="uk-width-1-1" style="padding:0.2em 1em 0.2em 1em;margin:0;">
                                    <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="role.is_multi_dokter" style="margin-left:5px;"> Ijinkan menggunakan nama dokter </label>
                                </div>
                                <div class="uk-width-1-1" style="padding:0.2em 1em 0.2em 1em;margin:0;">
                                    <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="role.is_aktif" style="margin-left:5px;"> Role pengguna aktif</label>
                                </div>
                            </div>
                        </div>

                        <div class="uk-grid-small hims-form-subpage" uk-grid>
                            <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">ROLE MENU</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">daftar menu yang diberikan pada role pengguna </p>
                            </div>
                            <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                                <ul uk-accordion="multiple:true" style="width:100%;">
                                    <li v-for="(menu,mindex) in allMenus" :key="mindex" style="padding:0.2em 0 0.2em 0;margin:0;" class="uk-open">
                                        <a class="uk-accordion-title hims-accordion-title uk-box-shadow-large" href="#">{{ menu.menu_title }}</a>
                                        <div class="uk-accordion-content" style="margin:0;padding:0.5em;">                                            
                                            <div class="uk-grid-small" uk-grid style="padding:0 1em 0 1em;">
                                                <div v-for="(item,itemindex) in menu.items" :key="itemindex" class="uk-width-1-2@s uk-width-1-3@l" style="padding:0.1em;margin:0;">
                                                    <label style="color:black;font-size:12px;">
                                                        <input class="uk-checkbox" type="checkbox" :value="item" v-model="role.menus"> {{ item.menu_title }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
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

export default {
    name : 'form-role', 
    data() {
        return {
            isUpdate : true,
            role : {
                client_id:null,
                role_id:null,
                role_nama : null,
                deskripsi : null,
                is_multi_dokter : false,
                is_ubah_tanggal : false,
                is_aktif : true,
                menus :[],
            },
            bufferRoleMenus :[],
            allMenus : [],
        }
    },
    computed : {
        ...mapGetters(['errors','allClientMenus']),
    },

    mounted() {
        this.retrieveClientMenus();
    },
    
    methods : {
        ...mapActions('role',['createRole','updateRole','dataRole']),
        ...mapActions(['getAllClientMenus']),
        ...mapMutations(['CLR_ERRORS']),

        retrieveClientMenus() {
            this.getAllClientMenus().then((response) => {
                if (response.success == true) {
                    this.allMenus = response.data;
                }
            })
        },

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formRole').hide();
            return false;
        },

        submitRole(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createRole(this.role).then((response) => {
                    if (response.success == true) {
                        alert("Role pengguna baru berhasil dibuat.");
                        this.fillRole(response.data);
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                    }
                })
            }
            else {
                this.updateRole(this.role).then((response) => {
                    if (response.success == true) {
                        this.fillRole(response.data);
                        alert("Role pengguna berhasil diubah.");
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                    }
                })
            }            
        },

        newRole(){
            this.clearRole();
            this.isUpdate = false;
            UIkit.modal('#formRole').show();
        },

        editRole(roleId){
            this.clearRole();            
            this.dataRole(roleId).then((response)=>{
                if (response.success == true) {
                    this.fillRole(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formRole').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        clearRole(){
            this.role.client_id = null;
             this.role.role_id = null;
            this.role.role_nama = null;
            this.role.deskripsi = '';
            this.role.is_multi_dokter = false;
            this.role.is_ubah_tanggal = false;
            this.role.is_aktif = true;
            this.role.menus = [];
        },

        fillRole(data){
            this.role.client_id = null;
             this.role.role_id = data.role_id;
            this.role.role_nama = data.role_nama;
            this.role.deskripsi = data.deskripsi;
            this.role.is_multi_dokter = data.is_multi_dokter;
            this.role.is_ubah_tanggal = data.is_ubah_tanggal;
            this.role.is_aktif = data.is_aktif;
            this.role.menus = data.menus;
        },
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

.hims-accordion-title {
    border-bottom:1px solid #cc0202; 
    font-size:14px; 
    font-weight:500; 
    background-color:#cc0202; 
    color:white; 
    padding:0.5em 1em 0.5em 1em;
    border-radius:5px;
}

.hims-accordion-title:hover {
    color:white; 
}
.hims-accordion-title::before {
    color:white;
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
} */

</style>