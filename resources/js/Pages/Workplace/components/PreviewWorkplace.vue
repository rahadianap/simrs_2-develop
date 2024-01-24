<template>    
<div class="uk-modal-container" uk-modal="bg-close:false;" id="previewws">
        <div class="uk-modal-dialog uk-modal-body">
    <div class="uk-container1 uk-container-small1">
        <div class="uk-grid-small uk-card uk-card-default" uk-grid style="padding:0;margin:0 0.5em 0 0.5em;position:sticky;top:0px;background-color:white;z-index:99;">
            <div class="uk-width-expand@m" style="padding:1em 0 0.1em 0.5em;margin:0;">
                <h5 class="uk-text-uppercase" style="font-weight:bold;text-align:left;width:100%;">INFORMASI TEMPAT KERJA</h5>
            </div> 
            <div class="uk-width-auto@m uk-grid-small" uk-grid style="text-align:center;">
                <div class="uk-width-expand" v-if="workplace.is_admin" style="text-align:center;padding:0.5em;">
                    <button type="button" @click.prevent="updateData" class="uk-button uk-button-default uk-button-medium " style="font-size:12px;">Ubah</button>                  
                </div>                              
                <div class="uk-width-expand" style="text-align:center;padding:0.5em;">
                    <button type="button" @click.prevent="closeModal" class="uk-button uk-button-primary uk-button-medium uk-box-shadow-large uk-modal-close" style="font-size:12px;">Tutup</button>                  
                </div>
            </div> 
        </div>
        <div>             
            <div class="uk-grid-small" uk-grid style="padding:1em;margin:0.5em;">
                <div class="uk-width-1-4@s uk-text-align-center" style="padding:0 0.5em 1em 0.5em;">
                    <div style="text-align:center;" >
                        <div v-if="workplace.url_logo == null || workplace.url_logo == ''" class="uk-width-1-1"  style="overflow:hidden;height:140px;width:140px;margin:0;padding:0;">
                            <img id="avatarWrapper" src="@/Assets/workplace.png" alt="" uk-img>
                        </div>
                        <div v-else class="uk-width-1-1"  style="overflow:hidden;height:140px;width:140px;margin:0 auto;padding:0;">
                            <img id="avatarWrapper" :data-src="workplace.url_logo" alt="" uk-img>
                        </div>                                
                    </div>
                    <div style="text-align:center;">
                        <h5 class="uk-text-uppercase" style="font-weight:bold;">{{workplace.client_nama}}</h5>
                        <p style="margin:0 0 1em 0; padding:0;font-size:12px;">
                            <span style="font-size:10px;" class="uk-label uk-label-default">{{workplace.client_tipe}}</span>
                        </p>
                        <p style="font-size:14px;font-style:italic;">{{workplace.deskripsi}}</p>
                    </div>
                </div>
                <div class="uk-width-3-4@s uk-grid-small" uk-grid style="border-left:1px solid #f0f0f0;"> 
                    <div>
                        <table>
                            <tr><td style="font-size:14px;font-weight: bold;color:black;">Perijinan :</td></tr>
                            <tr><td style="font-size:14px;color:#666;">{{workplace.no_ijin}} , tanggal terbit : {{workplace.tgl_terbit_ijin}}</td></tr>
                            <tr style="height:1em;"></tr>
                            <tr><td style="font-size:14px;font-weight: bold;color:black;">Alamat :</td></tr>
                            <tr><td style="font-size:14px;color:#666;">{{workplace.alamat}}</td></tr>
                            <tr><td style="font-size:14px;color:#666;">{{workplace.kota}}</td></tr>
                            <tr style="height:1em;"></tr>
                            <tr><td style="font-size:14px;font-weight: bold;color:black;">Kontak :</td></tr>
                            <tr>
                                <td style="font-size:14px;color:#666;">
                                    <span uk-icon="icon: receiver" style="color:#666;margin-right:5px;"></span>{{workplace.no_telepon}}
                                    <span uk-icon="icon: whatsapp" style="color:#666;margin-right:5px;margin-left:5px;"></span>{{workplace.no_whatsapp}}
                                </td>
                            </tr>
                            <tr><td style="font-size:14px;color:black;"></td></tr>
                            <tr><td style="font-size:14px;color:#666;"><span uk-icon="icon: mail" style="color:#666;margin-right:5px;"></span>{{workplace.email}}</td></tr>
                            <tr><td style="font-size:14px;color:#666;"><span uk-icon="icon: instagram" style="color:#666;margin-right:5px;"></span>{{workplace.link_instagram}}</td></tr>
                            <tr style="height:1em;"></tr>
                            <tr><td style="font-size:14px;font-weight: bold;color:black;">Lokasi :</td></tr>                             
                        </table>
                    </div>                            
                    <div class="uk-width-1-1@m" style="scrollbar-width: thin; overflow: auto; width:100%; height:300px;background-color: !!transparent;">
                        <iframe :src="workplace.loc_embed_src" width="100%" height="300px"></iframe>
                    </div> 
                </div>
            </div>
        </div>
    </div> 
    </div> 
    </div> 
</template>

<script>
import { mapMutations, mapActions } from 'vuex';

export default {
    name : 'preview-workplace',
    data() {
        return {
            workplace : {
                client_id:null,
                client_nama : null,
                client_tipe : null,
                deskripsi : '',
                kota : '',
                alamat : '',
                no_ijin : '',
                tgl_terbit_ijin:null,
                no_telepon : null,
                email: null,
                no_whatsapp:null,
                link_instagram : null,
                admin_id : null,
                admin_email : null,
                path_logo : null,
                url_logo : null,
                loc_embed_src : null,
                loc_embed_code : null,
                is_admin : false,
            },
        }
    },
    methods : {
        ...mapActions(['dataClient']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            UIkit.modal('#previewws').hide();
        },

        showWorkplace(clientId){
            this.clearWorkplace();      
            this.CLR_ERRORS();

            this.dataClient(clientId).then((response)=>{
                if (response.success == true) {
                    this.fillWorkplace(response.data);
                    UIkit.modal('#previewws').show();
                } else {
                    alert(response.message)
                }
            })
        },

        clearWorkplace(){
            this.workplace.client_id = null;
            this.workplace.client_nama = null;
            this.workplace.client_tipe = null;
            this.workplace.deskripsi = '';
            this.workplace.kota = '';
            this.workplace.alamat = '';
            this.workplace.no_ijin = '';
            this.workplace.tgl_terbit_ijin = null;
            this.workplace.no_telepon = null;
            this.workplace.email = null;
            this.workplace.no_whatsapp = null;
            this.workplace.link_instagram = null;
            this.workplace.admin_id = null;
            this.workplace.admin_email = null;
            this.workplace.path_logo = null;
            this.workplace.url_logo = null;
            this.workplace.loc_embed_src = null;
            this.workplace.loc_embed_code = null;
            this.workplace.is_admin = false;
        },

        fillWorkplace(data){
            this.workplace.client_id = data.client_id;
            this.workplace.client_nama = data.client_nama;
            this.workplace.client_tipe = data.client_tipe;
            this.workplace.deskripsi = data.deskripsi;
            this.workplace.kota = data.kota;
            this.workplace.alamat = data.alamat;
            this.workplace.no_ijin = data.no_ijin;
            this.workplace.tgl_terbit_ijin = data.tgl_terbit_ijin;
            this.workplace.no_telepon = data.no_telepon;
            this.workplace.email = data.email;
            this.workplace.no_whatsapp = data.no_whatsapp;
            this.workplace.link_instagram = data.link_instagram;
            this.workplace.admin_id = data.admin_id;
            this.workplace.admin_email = data.admin_email;
            this.workplace.path_logo = data.path_logo;
            this.workplace.url_logo = data.url_logo;
            this.workplace.loc_embed_src = data.loc_embed_src;
            this.workplace.loc_embed_code = data.loc_embed_code;
        }
    }
}
</script>

<style>
</style>