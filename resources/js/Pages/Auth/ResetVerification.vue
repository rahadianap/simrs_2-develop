<template>
    <div>
        <div  class="uk-align-center uk-width-large">
            
            <div v-if="isProcess">
                <div style="margin:0 0.2em 2em 0.2em;">
                    <h4 style="margin:0;font-weight:bold;">VERIFIKASI RESET PASSWORD</h4>
                    <h5 style="margin:0.2em 0 0 0;font-size:14px;">sedang memproses data reset password....</h5>
                </div>
                <div uk-alert class="uk-alert-primary" v-if="errors.invalid"> 
                    {{ errors.invalid }}
                </div>                
            </div>

            <div v-else>
                <div v-if="isValid">
                    <div v-if="!isSuccess">
                        <form action="" @submit.prevent = "submitResetVerification">            
                            <div style="margin:0 0.2em 2em 0.2em;">
                                <h4 style="margin:0;font-weight:bold;">VERIFIKASI RESET PASSWORD</h4>
                                <h5 style="margin:0.2em 0 0 0;font-size:14px;">masukkan kode token dan password baru Anda.</h5>
                            </div> 
                            <div uk-alert class="uk-alert-primary" v-if="errors.invalid"> 
                                {{ errors.invalid }}
                            </div>                        
                            <div>
                                <div>
                                    <div class="uk-inline" style="width:100%;margin:0.2em 0.2em 0 0.2em;">
                                        <span class="uk-form-icon" uk-icon="icon: code" style="color:#39f;"></span>
                                        <input class="uk-input" type="text" placeholder="kode reset password" v-model="data.token" required style="color:black; border:1px solid #39f; font-weight:bold; border-radius:0px;background:rgba(255, 255, 255, 0.1);">
                                    </div>
                                    <p style="font-size:10px;font-style:italic;color:#333;margin:0.1em 0.2em 1em 0.2em;">*kode token terisi otamatis (jika tidak gunakan kode token yang dikirim ke email anda)</p>
                                </div>
                                <div>
                                    <div class="uk-inline" style="width:100%;margin:0.2em;">
                                        <span class="uk-form-icon uk-form-icon" uk-icon="icon: lock" style="color:#39f;"></span>
                                        <input class="uk-input" :type="passwordType" placeholder="password baru anda" v-model="data.new_password" required minlength="6" pattern="\S(.*\S)?" style="color:black; border:1px solid #39f; border-radius:0px;background:rgba(255, 255, 255, 0.1);">
                                    </div>
                                </div>
                                <div class="uk-margin-small">
                                    <label><input class="uk-checkbox" @change="showPassword" type="checkbox" style="margin-left:5px;border:1px solid black;"> Tampilkan password</label>
                                </div>
                                <div>
                                    <div class="uk-grid-small" uk-grid style="width:100%;margin-top:1.5em;">
                                        <div class="uk-width-expand@m">
                                            <button type="submit" class="uk-button uk-button-primary" style="border-radius:50px;">Reset Password</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div v-else>
                        <div style="margin:0 0.2em 2em 0.2em;">
                            <h4 style="margin:0;font-weight:bold;">RESET PASSWORD</h4>
                            <h5 style="margin:1em 0 0 0;font-size:18px; font-weight:400;">{{successMessage}}</h5>
                        </div>
                        <div style="margin:0 0.2em 2em 0.2em;">
                            <p style="margin-top:2em;">
                                <router-link to="/" class="uk-button uk-button-primary" style="border-radius:50px;">LANJUT MASUK APLIKASI</router-link>
                            </p>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div style="margin:0 0.2em 2em 0.2em;">
                        <h4 style="margin:0;font-weight:bold;">VERIFIKASI RESET PASSWORD</h4>
                        <h5 style="margin:1em 0 0 0;font-size:18px; color:red;">tautan tidak berhasil divalidasi. Silahkan ulangi proses kembali.</h5>
                    </div>
                    <div>
                        <div class="uk-grid-small" uk-grid style="width:100%;margin-top:1.5em;">
                            <div class="uk-width-expand@m">
                                <button type="button" @click.prevent="getDataResetPassword" class="uk-button uk-button-primary" style="border-radius:50px;">VERIFIKASI ULANG</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="isProcess" class="uk-card uk-text-left" uk-card style="width:100%;">
                <div uk-spinner="ratio: 3.5"></div>
            </div>
            
        </div>   
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';

export default{
    data() {
        return {
            successMessage : null,
            isValid : false,
            isProcess : true,
            isSuccess : false,
            passwordType : 'password',
            data: {
                token : '',
                id: '',
                new_password: '',
            }
        }
    },
    
    computed : {
        ...mapGetters(['errors']),
    },
    
    created() {        
        /**
         * http://localhost:8000/reset/verification?id=123e4567-e89b-12d3-a456-426614174000&&token=9087645361222
         */
        this.data.token = this.$route.query.token;
        this.data.id = this.$route.query.id;        
    },
    mounted(){
        this.getDataResetPassword();
    },
    methods : {
        ...mapActions(['resetVerification','resetPasswordData']),
        ...mapMutations(['CLR_ERRORS']),

        showPassword() {
            if(this.passwordType === 'password') {
                this.passwordType = 'text'
            } else {
                this.passwordType = 'password'
            }
        },

        getDataResetPassword(){
            isValid : false;
            this.isProcess = true;
            this.CLR_ERRORS();
            this.resetPasswordData(this.data).then((response) => {   
                this.isProcess = false;

                if (response.success == true) {
                    this.CLR_ERRORS();
                    this.isValid = true;
                } 
                else { this.isValid = false;  }
            })
        },
        
        submitResetVerification() {
            this.isProcess = true;
            this.isSuccess = false;
            this.successMessage = null;
            this.CLR_ERRORS();
            
            this.resetVerification(this.data).then((response) => {
                this.isProcess = false;
                if (response.success == true) {
                    this.isSuccess = true;
                    this.successMessage = response.message;
                    this.CLR_ERRORS();
                }
            })
        },
    }
}
</script>