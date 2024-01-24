<template>
    <div class="uk-modal-full" uk-modal="bg-close:false;" id="modalws" style="height:100vh; padding:0;margin:0;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container uk-container-small" style="min-height:100vh;">
                <preview-workplace v-if="isPreview" :workplace = "workplace" v-on:modeUpdate="changeModeToEdit"></preview-workplace>
                <form-workplace v-else :workplace = "workplace" :isUpdate="isUpdate"></form-workplace>
            </div>            
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import FormWorkplace  from '@/Pages/Workplace/components/FormWorkplace.vue';
import PreviewWorkplace  from '@/Pages/Workplace/components/PreviewWorkplace.vue';

export default {
    name : 'modal-workplace',
    components : {
        FormWorkplace,
        PreviewWorkplace
    },
    data() {
        return {
            isPreview : true,
            isUpdate : false,
            selectedFiles : null,
            onProgress : false,
            is_loading : 1,
            welcome: 'This is ID',
            WsTipe :[
                {value:'RUMAH SAKIT', caption:'Rumah Sakit' },
                {value:'KLINIK', caption:'Klinik' },
                {value:'APOTEK', caption:'Apotek' },                
            ],
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
    computed : {
        ...mapGetters(['errors'])
    },
    methods : {
        ...mapActions(['createClient','updateClient','deleteClient','dataClient','listClient','uploadLogo',]),
        ...mapMutations(['CLR_ERRORS']),

        changeModeToEdit(){
            this.isPreview = false;
            this.isUpdate = true;
        },

        closeModal(){
            alert('close modal');
            UIkit.modal('#modalws').hide();
        },

        showModal(){
            UIkit.modal('#modalws').show();
        },

        browseLogo() {
            this.$refs.file.click();
        },

        newWorkplace(){
            this.clearWorkplace();
            this.isPreview = false;
            this.isUpdate = false;
            UIkit.modal('#modalws').show();
        },

        showWorkplace(clientId){
            this.clearWorkplace();            
            this.dataClient(clientId).then((response)=>{
                if (response.success == true) {
                    this.fillWorkplace(response.data);
                    this.isPreview = true;
                    this.isUpdate = false;
                    UIkit.modal('#modalws').show();
                } else {
                    alert(response.message)
                }
            })
        },

        updateWorkplace(clientId){
            this.clearWorkplace();            
            this.dataClient(clientId).then((response)=>{
                if (response.success == true) {
                    this.fillWorkplace(response.data);
                    this.isPreview = false;
                    this.isUpdate = true;
                    UIkit.modal('#modalws').show();
                } else {
                    alert(response.message)
                }
            })
        },        

        selectedFileLogo(){
            this.selectedFiles = this.$refs.file.files[0];
            this.CLR_ERRORS();
            let formData = new FormData();
            formData.append("image", this.selectedFiles);
            if(this.isUpdate == false) { formData.append("client_id", ""); } 
            else { formData.append("client_id", this.workplace.client_id); }

            this.uploadLogo(formData).then((response)=>{
                if (response.success == true) {
                    this.workplace.path_logo = response.data.path;
                    this.workplace.url_logo = response.data.path_url;                    
                } else {
                    alert(response.message)
                }
            })
        },

        getSourceMap(){
            let comp = document.getElementsByTagName('iframe')[0];
            let embedSource = comp.getAttribute("src");
            this.workplace.loc_embed_src = embedSource;
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
            this.selectedFiles = null;
            this.onProgress = false;
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
            this.workplace.is_admin = data.is_admin;
        }
    }
}
</script>

<style>
</style>