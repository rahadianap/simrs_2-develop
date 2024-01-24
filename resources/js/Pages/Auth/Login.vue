<template>
    <div>
        <div class="uk-align-center uk-width-large">
            <form action="" @submit.prevent = "submitLogin">            
                <div class="uk-card" uk-card >
                    <div style="margin:0 0.2em 2em 0.2em;">
                        <h4 style="margin:0;font-weight:bold;">LOGIN USER</h4>
                        <h5 style="margin:0.2em 0 0 0;font-size:14px;">gunakan akun pengguna anda untuk masuk kedalam aplikasi.</h5>
                    </div> 
                    <div uk-alert class="uk-alert-primary" v-if="errors.invalid"> 
                        {{ errors.invalid }}
                    </div>
                    <div>
                        <div class="uk-inline" :class="{'has-error': errors.username}" style="width:100%;margin:0.2em;">
                            <span class="uk-form-icon" uk-icon="icon: user" style="color:#39f;"></span>
                            <input class="uk-input" type="text" placeholder="username / email" v-model="data.username" minlength="6" required pattern="\S(.*\S)?" style="color:black; border:1px solid #39f; border-radius:0px;background:rgba(255, 255, 255, 0.1);">
                        </div>
                        <div style="text-align:left;color:red; font-style:italic;">
                            <p v-if="errors.username">{{ errors.username[0] }}</p>
                        </div>
                    </div>
                    <div>
                        <div class="uk-inline"  :class="{'has-error': errors.password}" style="width:100%;margin:0.2em;">
                            <span class="uk-form-icon uk-form-icon" uk-icon="icon: lock" style="color:#39f;"></span>
                            <input class="uk-input" :type="passwordType" placeholder="password" v-model="data.password" minlength="6" required pattern="\S(.*\S)?" style="color:black; border:1px solid #39f; border-radius:0px;background:rgba(255, 255, 255, 0.1);">
                        </div>
                        <div style="text-align:left;color:red; font-style:italic;">
                            <p v-if="errors.password">{{ errors.password[0] }}</p>
                        </div>
                    </div>
                    <div class="uk-margin-small">
                        <label><input class="uk-checkbox" @change="showPassword" type="checkbox" style="margin-left:5px;border:1px solid black;"> Tampilkan password</label>
                    </div>
                    <div class="uk-margin-small">
                        <label><input class="uk-checkbox" type="checkbox" v-model="data.remember_me" style="margin-left:5px;border:1px solid black;"> Ingat saya</label>
                    </div>
                    <div class="uk-margin-small" style="padding:1em 0 0 0;">
                        <div class="uk-grid-small" uk-grid>                            
                            <div class="uk-width-1-1@s">
                                <button type="submit" class="uk-button uk-button-primary" style="border-radius:50px;">Masuk</button>
                            </div>
                            <div class="uk-width-expand@s">
                                <p style="font-size:14px;font-style:italic;margin:0;">belum memiliki akun? 
                                    <router-link to="/signup" style="color:blue;text-decoration: underline;">daftar disini</router-link>
                                </p>
                                
                                <p style="font-size:14px;font-style:italic;margin:0;"> lupa password?
                                    <router-link to="/forget/password" style="color:blue;text-decoration: underline;">klik disini</router-link>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';

export default{
    data() {
        return {
            passwordType : 'password',
            data: {
                username : '',
                password: '',
                remember_me: false
            }
        }
    },
    mounted(){
        this.CLR_ERRORS();
    },    
    computed : {
        ...mapGetters(['errors']),
    },
    created(){
        if (this.isAuth) {
            this.$router.push({ name: 'home' })
        }
        this.CLR_ERRORS()
    },
    methods : {
        ...mapActions(['login']),
        ...mapMutations(['CLR_ERRORS']),
        ...mapGetters(['isAuth']),

        showPassword() {
            if(this.passwordType === 'password') {
                this.passwordType = 'text'
            } else {
                this.passwordType = 'password'
            }
        },
        
        submitLogin() {
            this.CLR_ERRORS();
            this.login(this.data).then((response) => {
                if (response.success == true) {
                    this.CLR_ERRORS();
                    if(this.isAuth) {
                        this.$router.push({ name: 'home' })
                    }
                }
            })
        },
    }
}
</script>