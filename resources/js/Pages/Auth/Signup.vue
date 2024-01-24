<template>
    <div>
        <div v-if="!isSuccessRegistered"  class="uk-align-center uk-width-large">
            <form action="" @submit.prevent="submitSignup"> 
                <div  class="uk-card" uk-card >
                    <div style="margin:0 0.2em 2em 0.2em;text-align:left;">
                        <h4 style="margin:0;font-weight:bold;">PENDAFTARAN PENGGUNA BARU</h4>
                        <h5 style="margin:0.2em 0 0 0;font-size:14px;">proses pendaftaran pengguna baru.</h5>
                    </div>
                    <div uk-alert class="uk-alert-primary" v-if="errors.invalid"> 
                        <p>{{ errors.invalid }}</p>
                    </div>
                    <div>
                        <div class="uk-inline" :class="{'has-error': errors.email}" style="width:100%;margin:0.2em;">
                            <span class="uk-form-icon" uk-icon="icon: mail" style="color:#39f;"></span>
                            <input class="uk-input" type="email" placeholder="email" v-model="data.email" required style="color:black; border:1px solid #39f; background:rgba(255, 255, 255, 0.1);">
                        </div>
                        <div style="text-align:left;color:red; font-style:italic;">
                            <p v-if="errors.username">{{ errors.username[0] }}</p>
                        </div>
                    </div>
                    <div>
                        <div class="uk-inline" :class="{'has-error': errors.username}" style="width:100%;margin:0.2em;">
                            <span class="uk-form-icon" uk-icon="icon: user" style="color:#39f;"></span>
                            <input class="uk-input" type="text" placeholder="username" v-model="data.username" minlength="6" required pattern="\S(.*\S)?" style="color:black; border:1px solid #39f; border-radius:0px;background:rgba(255, 255, 255, 0.1);">
                        </div>
                        <div style="text-align:left;color:red; font-style:italic;">
                            <p v-if="errors.username">{{ errors.username[0] }}</p>
                        </div>
                    </div>
                    <div>
                        <div class="uk-inline" :class="{'has-error': errors.password}" style="width:100%;margin:0.2em;">
                            <span class="uk-form-icon uk-form-icon" uk-icon="icon: lock" style="color:#39f;"></span>
                            <input class="uk-input" :type="passwordType" placeholder="password" v-model="data.password" minlength="6" required pattern="\S(.*\S)?" style="color:black; border:1px solid #39f; border-radius:0px;background:rgba(255, 255, 255, 0.1);">
                        </div>
                        <div style="text-align:left;color:red; font-style:italic;">
                            <p v-if="errors.password">{{ errors.password[0] }}</p>
                        </div>
                    </div>
                    <div class="uk-margin-small">
                        <label><input @change="showPassword" class="uk-checkbox" type="checkbox" style="margin-left:5px;border:1px solid black;"> Tampilkan password</label>
                    </div>
                    <div class="uk-margin-small" style="padding:1em 0 0 0;">
                        <div class="uk-grid-small" uk-grid>                            
                            <div class="uk-width-1-1@s">
                                <button type="submit" class="uk-button uk-button-primary" style="border-radius:50px;">Daftar</button>
                            </div>
                            <div class="uk-width-expand@s">
                                <p style="font-size:14px;font-style:italic;margin-top:10px;">sudah memiliki akun? 
                                    <router-link to="/" style="color:blue;text-decoration: underline;">masuk disini</router-link>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div v-else class="uk-align-center uk-width-large">            
            <div  class="uk-card-primary uk-shadow-large" uk-card  style="padding:2em;border-radius:10px;">
                <div style="margin:0 0.2em 1em 0.2em; text-align:center;">
                    <h4 style="margin:1em;font-weight:bold; ">Registrasi-mu berhasil</h4>
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
            passwordType : 'password',
            successMessage : null,
            isSuccessRegistered : false,
            data: {
                username : '',
                email: '',
                password: '',
                remember_me: false
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
        ...mapActions(['signup']),
        ...mapMutations(['CLR_ERRORS']),

        showPassword() {
            if(this.passwordType === 'password') {
                this.passwordType = 'text'
            } else {
                this.passwordType = 'password'
            }
        },

        submitSignup() {
            this.CLR_ERRORS();
            this.signup(this.data).then((response) => {
                
                if (response.success == true) {
                    this.isSuccessRegistered = true;
                    this.successMessage = response.message;
                    this.CLR_ERRORS();
                }
            })
        }
    }

}
</script>