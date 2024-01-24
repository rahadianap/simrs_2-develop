<template>
    <div>        
        <div v-if="!isSuccess"  class="uk-align-center uk-width-large">
            <form action="" @submit.prevent="submitResetPassword">            
                <div>
                    <div style="margin:0 0.2em 2em 0.2em;">
                        <h4 style="margin:0;font-weight:bold;">RESET PASSWORD</h4>
                        <h5 style="margin:0.2em 0 0 0;font-size:14px;">masukkan alamat email terdaftar Anda untuk kami kirimkan link (tautan) reset password.</h5>
                    </div>
                    <div uk-alert class="uk-alert-primary" v-if="errors.invalid"> 
                        {{ errors.invalid }}
                    </div>
                    <div>
                        <div class="uk-inline uk-width-1-1" style="width:100%;">
                            <span class="uk-form-icon" uk-icon="icon: mail" style="color:#39f;"></span>
                            <input class="uk-input uk-width-1-1" type="email" placeholder="email terdaftar" v-model="data.email" required style="color:black; border:1px solid #39f; border-radius:0px;background:rgba(255, 255, 255, 0.1);">
                        </div>
                    </div>
                    <div class="uk-width-auto@m uk-margin-small" style="padding-top:1em;">
                        <div class="uk-width-1-1">
                            <button type="submit" class="uk-button uk-button-primary" style="border-radius:50px;">Reset Password</button>
                        </div>
                        <div class="uk-width-expand@s">
                            <p style="font-size:14px;font-style:italic;margin-top:20px;">kembali ke halaman utama 
                                <router-link to="/" style="color:blue;text-decoration: underline;">klik disini</router-link>
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div v-else class="uk-align-center uk-width-large">            
            <div  class="uk-card-primary uk-shadow-large" uk-card  style="padding:2em;border-radius:10px;">
                <div style="margin:0 0.2em 1em 0.2em; text-align:center;">
                    <h4 style="margin:1em;font-weight:bold; ">Reset Password berhasil</h4>
                    <h5 style="margin:0;" v-html="successMessage"></h5>
                    <p style="font-size:16px;font-style:italic;margin-top:2em;">
                        <router-link to="/" class="uk-button uk-button-primary">OK</router-link>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
export default {
    data() {
        return {
            isSuccess : false,
            successMessage : null,
            data: {
                email: '',
            }
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapActions(['resetPassword']),
        ...mapMutations(['CLR_ERRORS']),

        submitResetPassword(){
            this.successMessage = null;
            this.isSuccess = false;
            this.CLR_ERRORS();

            this.resetPassword(this.data).then((response) => {
                if (response.success == true) {
                    this.successMessage = response.message;
                    this.isSuccess = true;
                    this.CLR_ERRORS();
                }
            })
        }
    }
}
</script>