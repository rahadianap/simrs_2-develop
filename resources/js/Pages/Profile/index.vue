<template>
    <div class="uk-container uk-container-small">
        
        <div class="uk-card" uk-card style="min-height:400px;padding:1em;">
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-auto@m"  style="padding:0.5em;text-align:center;">                    
                    <div v-if="user.avatar == null || user.avatar == ''" class="uk-width-1-1"  style="margin:0 auto; overflow:hidden;height:80px;width:80px;padding:0;">
                        <img id="avatarWrapper" src="@/Assets/user.png" alt="" uk-img @click.prevent="browseAvatar">
                    </div>
                    <div v-else class="uk-width-1-1" :style=" {'background-image': `url(${user.avatar})` }">
                        <img id="avatarWrapper" :data-src="user.avatar" alt="" uk-img @click.prevent="browseAvatar">
                    </div> 
                </div>
                <div class="uk-width-expand@m" style="padding:0.8em 0.5em 1em 0.5em;">
                    <h4 class="uk-text-uppercase" style="font-weight:bold;margin:0;padding:0;">{{user.username}}</h4>
                    <p style="margin:0 0 0.2em 0; padding:0;font-size:14px;">{{user.email}}</p>
                    <p style="margin:0 0 1em 0; padding:0;font-size:10px;color:blue;font-style:italic;">klik foto untuk mengubah</p>
                </div>
                <div class="uk-width-1-1" style="margin:0;padding:0;">
                    <div uk-form-custom="target: true">
                        <input type="file" ref="file" @input="selectedFileAvatar" >
                    </div>
                </div>
            </div>
            <div>
                <div class="uk-card uk-card-default" style="padding:1em;border-radius:10px;min-height:400px;">
                    <ul uk-tab>
                        <li><a href="#" style="font-size:14px;font-weight:500;">DETAIL INFO</a></li>
                        <li><a href="#" style="font-size:14px;font-weight:500;">UNDANGAN BERGABUNG</a></li>
                        <li><a href="#" style="font-size:14px;font-weight:500;">UBAH PASSWORD</a></li>
                    </ul>
                    <ul class="uk-switcher">
                        <li>
                            <!--detail info pengguna-->
                            <user-profile ref="userProfile"></user-profile>
                        </li>
                        <li>
                            <!--undangan bergabung-->
                            <invitation-list></invitation-list>
                        </li>
                        <li>
                            <!--ubah password-->
                            <change-password></change-password>
                        </li>
                    </ul>
                </div>
            </div>
        </div>    
    </div>
</template>
<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import changePassword from '@/Pages/Profile/components/ChangePassword.vue';
import invitationList from '@/Pages/Profile/components/InvitationList.vue';
import userProfile from '@/Pages/Profile/components/UserProfile.vue';

export default {
    components : {
        changePassword,
        invitationList,
        userProfile,
    },
    data() {
        return {}
    },
    computed: {
        ...mapGetters(['user','errors']),
    },
    methods : {
        ...mapActions('profile',['uploadAvatar']),

        browseAvatar() {
            this.$refs.file.click();
        },

        selectedFileAvatar(){
            let input = this.$refs.file
            let file = input.files
            if(file && file[0]){
                let reader = new FileReader
                reader.onload = e => {
                    this.user.avatar = e.target.result
                }
                reader.readAsDataURL(file[0])
                this.$emit('input', file[0])
            }
            // this.selectedFiles = this.$refs.file.files[0];
            // this.CLR_ERRORS();
            // let formData = new FormData();
            // formData.append("image", this.selectedFiles);
            // if(this.isUpdate == false) { formData.append("dokter_id", ""); } 
            // else { formData.append("dokter_id", this.dokter.dokter_id); }

            // this.uploadDokterAvatar(formData).then((response)=>{
            //     if (response.success == true) {
            //         this.dokter.avatar = response.data.path_url;                    
            //     } else {
            //         alert(response.message)
            //     }
            // })
        },
    }
}
</script>