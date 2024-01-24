<template>
    <div>
        <div class="uk-grid-small" uk-grid style="padding:1em;">
            <div class="uk-width-expand@m" style="padding:0;margin:0;">
                <h4 class="dash-title" style="margin:0;padding:0;font-size:18px;font-weight:500;color:#333;">Tempat Kerja Anda</h4>
                <p style="margin:0;padding:0;font-size:12px;">masuk salah satu tempat kerja untuk beraktivitas</p>
            </div>
            <div class="uk-width-auto@m" style="padding:0;margin:0;">
                <button type="button" @click.prevent="addWorkplace" class="uk-button uk-box-shadow-small wps-button">Buat Tempat kerja</button>
            </div>
            <!-- <div uk-alert class="uk-alert-primary uk-width-1-1" v-if="errors.invalid"> 
                {{ errors.invalid }}
            </div> -->
        </div>
        <div v-if="clients.length > 0" class="uk-flex uk-flex-wrap" uk-height-match>
            <div v-for="client in clients" :key="client.client_id" class="uk-width-1-3@l uk-width-1-2@s" style="padding:0.5em 1em 0.5em 1em;">
                <div class="uk-card uk-card-default uk-card-hover uk-grid-small" uk-grid style="padding:1em 0.5em 1em 0.5em;border-radius:10px;min-height:120px;">
                    <div v-if="client.detail.url_logo == null || client.detail.url_logo == '' || client.detail.url_logo == 'noimage' " class="uk-width-auto@m" style="padding:0.5em;width:100px;">
                        <img class="wps-card-logo" src="@/Assets/noimage.png" alt="" style="">
                    </div>
                    <div v-else class="uk-width-auto@m" style="padding:0.5em;width:100px;">
                        <img class="wps-card-logo" :src="client.detail.url_logo" alt="" style="">
                    </div>
                    <div class="uk-width-expand@m">
                        <div class="wps-card-body">
                            <div class="uk-grid-small" uk-grid>
                                <div class="uk-width-expand">
                                    <h4 class="uk-card-title wps-card-title uk-text-truncate">
                                        <a href="#" @click.prevent="selectWorkplace(client.client_id)" title="klik untuk memilih">{{client.detail.client_nama}}</a>
                                    </h4>
                                </div>
                                <div class="uk-width-auto">
                                    <a v-if="client.is_admin" href="#" class="wps-card-btn">
                                        <span uk-icon="more"></span>
                                    </a>
                                    <div style="border-radius:5px;margin:0;padding:1em;" uk-dropdown="mode: hover">
                                        <ul class="uk-list" style="margin:0;padding:0;">
                                            <li>
                                                <a href="#" @click.prevent="detailWorkplace(client.client_id)"  style="color:#333;font-size:14px;">
                                                    <span uk-icon="info" style="margin-right:5px;"></span> Info
                                                </a>
                                            </li>
                                            <li v-if="client.is_admin">
                                                <a href="#" @click.prevent="editWorkplace(client.client_id)"  style="color:#333;font-size:14px;">
                                                    <span uk-icon="pencil" style="margin-right:5px;"></span> Ubah
                                                </a>
                                            </li>
                                            <li v-if="client.is_admin">
                                                <a href="#" @click.prevent="deleteWorkplace(client.client_id)" style="color:#333;font-size:14px;">
                                                    <span uk-icon="trash" style="margin-right:5px;"></span> Hapus
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <p style="margin:0 0 1em 0; padding:0;font-size:12px;">
                                <span style="font-size:10px;" class="uk-label uk-label-default">{{client.detail.client_tipe}}</span>
                            </p>
                            <p class="wps-card-description">
                                {{client.detail.deskripsi}}
                            </p>                            
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <div v-else style="padding:0.5em 1em 0.5em 0; margin:0;">
            <div class="uk-alert-primary" uk-alert>
                <p>Anda belum memiliki tempat kerja. Terima undangan bergabung atau buat tempat kerja anda sendiri.</p>
            </div>
        </div>
        <preview-workplace ref="previewWp"/>
        <form-workplace  ref="formWp"  v-on:closed="getListWorkplace"/>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import formWorkplace from '@/Pages/Workplace/components/FormWorkplace.vue'
import previewWorkplace from '@/Pages/Workplace/components/PreviewWorkplace.vue'

export default {
    components : {
        formWorkplace,
        previewWorkplace
    },
    computed : {
        ...mapGetters(['errors','clients']),
    },
    created(){

    },
    mounted(){
        this.getListWorkplace();
    },
    methods : {
        ...mapActions(['listClient','dataClient','deleteClient']),
        ...mapMutations(['CLR_ERRORS','SET_SELECTED_CLIENT']),

        /**ambil daftar workspace yang dimiliki oleh pengguna */
        getListWorkplace(){
            this.CLR_ERRORS();
            this.listClient();
        },
        /**tampikan modal untuk membuat workspace baru */
        addWorkplace(){
            this.$refs.formWp.newWorkplace();        
        },
        /**tampilkan detail informasi workspace */
        detailWorkplace(clientId){
            this.$refs.previewWp.showWorkplace(clientId);
        },
        /**tampilkan modal untuk mengubah data */
        editWorkplace(clientId){
            this.$refs.formWp.updateWorkplace(clientId);
        },

        /**hapus workspace */
        deleteWorkplace(clientId){
            this.CLR_ERRORS();
            if(confirm("Proses ini akan menghapus seluruh data tempat kerja. Lanjutkan ?")){
                this.deleteClient(clientId).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.getListWorkplace();
                    }
                    else { alert (response.message) }
                });
            };
        },

        selectWorkplace(clientId) {     
            this.dataClient(clientId).then((response)=>{
                if (response.success == true) {
                    this.$store.commit('SET_SELECTED_CLIENT', JSON.stringify(response.data));   
                    this.$router.push({ name : 'dashboard' });
                } else {
                    alert(response.message)
                }
            })
        }
    }
}
</script>

<style>
.wps-card-logo img{
    background-image : url("@/Assets/rslogo.png");
}

.wps-card-body {
    padding:0 0.5em 0.5em 0.5em;
    margin:0;
}

.wps-card-title {
    margin:0 0 0.5em 0;
    padding:0;
    font-size:18px;
    font-weight:500;
    color:black;
}
.wps-card-title a {
    color:black;
}

.wps-card-description {
    margin:0; 
    padding:0; 
    font-size:12px;
    display: -webkit-box; 
    -webkit-line-clamp: 2; 
    -webkit-box-orient: vertical; 
    overflow: hidden;
}

.wps-card-btn {
    min-width:15px;
    /* border-radius:50px; */
    display:inline-block; 
    font-weight:400; 
    font-size:11px; 
    text-decoration:none; 
    line-height:25px;
    padding: 0 1em 0 1em;
    text-align:center;
    margin:3px;
    background-color: #f0f0f0;
    color:#333;
}

.wps-card-btn:hover {
    color:white;
    background-color:#39f;
}
</style>