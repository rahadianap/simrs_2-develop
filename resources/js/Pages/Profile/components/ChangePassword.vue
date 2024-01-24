<template>
    <div style="padding:0 1em 0.5em 1em;">
        <form class="uk-form-stacked" action="" @submit.prevent="submitChangePassword" style="margin:0;padding:0;">
            <div uk-alert class="uk-alert-primary" v-if="errors.invalid"> 
                {{ errors.invalid }}
            </div>
            <div>
                <label class="uk-form-label" style="font-weight:500;">Password Lama</label>
                <div class="uk-inline" style="margin:0.2em;width:400px;margin:0.2em 0 0 0;">
                    <span class="uk-form-icon uk-form-icon" uk-icon="icon: lock" ></span>
                    <input class="uk-input uk-form-small " :type="passwordType" placeholder="password" v-model="data.password" minlength="6" required pattern="\S(.*\S)?" style="color:black; background:rgba(255, 255, 255, 0.1);">
                </div>
            </div>
            <div style="margin:1em 0 1em 0;">
                <label class="uk-form-label" style="font-weight:500;">Password Baru</label>
                <div class="uk-inline" style="margin:0.2em;width:400px;margin:0.2em 0 0 0;">
                    <span class="uk-form-icon uk-form-icon" uk-icon="icon: lock"></span>
                    <input class="uk-input uk-form-small" :type="passwordType" placeholder="password" v-model="data.new_password" minlength="6" required pattern="\S(.*\S)?" style="color:black;background:rgba(255, 255, 255, 0.1);">
                </div>
            </div>
            <div class="uk-margin-small">
                <label style="font-size:14px;"><input class="uk-checkbox" @change="showPassword" type="checkbox" style="color:black;margin-left:5px;border:1px solid black;"> Tampilkan password</label>
            </div>    
            <div style="padding:1.5em 0.5em 0.5em 0;margin:0;text-align:left;">
                <button  type="submit" class="uk-button uk-button-primary uk-box-shadow-medium" style="border-radius:5px;">Ubah Password</button>
            </div>    
        </form>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
export default {
    name : 'change-password',
    data() {
        return {
            passwordType : 'password',
            data: {
                new_password : null,
                password: null,
            }
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },
    mounted(){
        this.CLR_ERRORS();
    },
    methods : {
        ...mapActions(['changePassword']),
        ...mapMutations(['CLR_ERRORS']),

        showPassword(){
            if(this.passwordType === 'password') {
                this.passwordType = 'text'
            } else {
                this.passwordType = 'password'
            }
        },
                
        submitChangePassword(){
            this.CLR_ERRORS();
            this.changePassword(this.data).then((response) => {
                if (response.success == true) {
                    this.data.new_password = null;
                    this.data.password = null;
                    alert("Password pengguna berhasil diubah.");
                }
            })
        },

    }
}
</script>