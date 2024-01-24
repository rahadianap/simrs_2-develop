<template>
    <div>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div> 
        <form action="" @submit.prevent="postProfile" >
            <!-- v-for="dt in this.datas" -->
            <div  class="uk-grid-small" uk-grid style="padding:0;margin:0.5em 0.5em 0.5em 0;">
                <div class="uk-width-1-1@m">
                    <label class="uk-form-label" for="form-stacked-text" style="font-weight:500;">Nama Lengkap</label>
                    <div class="uk-form-controls" style="margin:0.5em 0 0 0;">
                        <input v-model="datas" class="uk-input uk-form-small" type="text" placeholder="nama lengkap" required>
                    </div>
                </div>
                <div class="uk-width-1-3@m">
                    <label class="uk-form-label" for="form-stacked-text" style="font-weight:500;">Jenis Kelamin</label>
                    <div class="uk-form-controls" style="margin:0.5em 0 0 0;">
                        <select v-model="data.jenis_kelamin" class="uk-select uk-form-small" id="form-horizontal-select">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>
                <div class="uk-width-1-3@m">
                    <label class="uk-form-label" for="form-stacked-text" style="font-weight:500;">Tempat Lahir</label>
                    <div class="uk-form-controls" style="margin:0.5em 0 0 0;">
                        <input v-model="data.tempat_lahir" class="uk-input uk-form-small" type="text" placeholder="tempat lahir">
                    </div>
                </div>
                <div class="uk-width-1-3@m">
                    <label class="uk-form-label" for="form-stacked-text" style="font-weight:500;">Tanggal Lahir</label>
                    <div class="uk-form-controls" style="margin:0.5em 0 0 0;">
                        <input v-model="data.tanggal_lahir" class="uk-input uk-form-small" type="date" placeholder="tanggal lahir">
                    </div>
                </div>

                <div class="uk-width-1-3@m">
                    <label class="uk-form-label" for="form-stacked-text" style="font-weight:500;">No Telepon</label>
                    <div class="uk-form-controls" style="margin:0.5em 0 0 0;">
                        <input v-model="data.no_telepon" class="uk-input uk-form-small" type="text" placeholder="telepon">
                    </div>
                </div>
                <div class="uk-width-1-3@m">
                    <label class="uk-form-label" for="form-stacked-text" style="font-weight:500;">Instagram</label>
                    <div class="uk-form-controls" style="margin:0.5em 0 0 0;">
                        <input v-model="data.instagram" class="uk-input uk-form-small" type="text" placeholder="link instagram">
                    </div>
                </div>                            
                <div class="uk-width-1-3">
                    <label class="uk-form-label" for="form-stacked-text" style="font-weight:500;">NIK</label>
                    <div class="uk-form-controls" style="margin:0.5em 0 0 0;">
                        <input v-model="data.nik" class="uk-input uk-form-small" type="text" placeholder="no tanda pengenal">
                    </div>
                </div>
                <div class="uk-width-1-1">
                    <label class="uk-form-label" for="form-stacked-text" style="font-weight:500;">Biografi Singkat </label>
                    <div class="uk-form-controls" style="margin:0.5em 0 0 0;">
                        <textarea class="uk-textarea uk-form-small" v-model="data.bio_singkat" type="text" placeholder="biografi singkat"></textarea>
                    </div>
                </div>                
                <div style="padding:1.5em 0.5em 0.5em 1em;margin:0;text-align:left;">
                    <button  type="submit" class="uk-button uk-button-primary uk-box-shadow-medium" style="border-radius:5px;">Simpan Data</button>
                </div>    
            </div>                
        </form>        
        <div class="uk-width-1-1" style="margin:0;padding:0;">
            <div uk-form-custom="target: true">
                <input type="file" ref="file" @change="getFileAvatar" >
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
export default {
    name : 'user-profile',
    data() {
        return {
            data : {
                nama_lengkap : '',
                jenis_kelamin : '',
                tempat_lahir : '',
                tanggal_lahir : '',
                no_telepon : '',
                nik : '',
                instagram : '',
                avatar_url : '',
                avatar_path : '',
                bio_singkat : '',               
            }
        }
    },
    computed : {
        ...mapGetters(['user','errors']),
    },
    mounted(){
        this.retrieveProfile();
    },
    methods : {        
        ...mapActions('profile',['submitProfile','dataProfile','uploadAvatar']),
        ...mapMutations(['CLR_ERRORS']),

        retrieveProfile(){
            this.CLR_ERRORS();
            this.clearProfile();
            this.dataProfile().then((response)=>{
                if (response.success == true) {
                    this.fillProfile(response.data);
                } 
            })
        },

        postProfile(){
            this.CLR_ERRORS();
            this.submitProfile(this.data).then((response) => {
                if (response.success == true) {
                    alert("Profile berhasil diupdate.");
                    this.fillProfile(response.data);
                }
            })
        },

        clearProfile(){
            this.data.nama_lengkap = '';
            this.data.jenis_kelamin = '';
            this.data.tempat_lahir = '';
            this.data.tanggal_lahir = '';
            this.data.no_telepon = '';
            this.data.nik = '';
            this.data.instagram = '';
            this.data.avatar_url = '';
            this.data.avatar_path = '';
            this.data.bio_singkat = '';
        },

        fillProfile(data){
            this.data.nama_lengkap = data.nama_lengkap;
            this.data.jenis_kelamin = data.jenis_kelamin;
            this.data.tempat_lahir = data.tempat_lahir;
            this.data.tanggal_lahir = data.tanggal_lahir;
            this.data.no_telepon = data.no_telepon;
            this.data.nik = data.nik;
            this.data.instagram = data.instagram;
            this.data.avatar_url = data.avatar_url;
            this.data.avatar_path = data.avatar_path;
            this.data.bio_singkat = data.bio_singkat;
        },

        selectAvatar(){
            this.$refs.file.click();
        },

        getFileAvatar(){
            this.selectedFiles = this.$refs.file.files[0];
            this.CLR_ERRORS();
            let formData = new FormData();
            formData.append("pic_avatar", this.selectedFiles);

            this.uploadAvatar(formData).then((response)=> {
                if (response.success == true) {
                    this.data.avatar_path = response.data.path;
                    this.data.avatar_url = response.data.path_url;
                    this.updateUserData();
                } else {
                    alert(response.message);
                }
            })
        },

        updateUserData(){
            let userdata = {
                'username' : this.user.username,
                'email' : this.user.email,
                'is_aktif' : true,
                'pic_avatar' : this.data.avatar_url,
                'url_avatar' : this.data.avatar_url,
            };
            this.$store.commit('SET_USER', JSON.stringify(userdata));
        }
    }
}
</script>