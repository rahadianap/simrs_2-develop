<template>
    <div>        
        <div style="margin:0 0.2em 2em 0.2em;">
            <h4 style="margin:0;font-weight:bold;">VERIFIKASI PENGGUNA BARU</h4>
            <h5 style="margin:0.2em 0 0 0;font-size:14px;">proses verifikasi pendaftaran pengguna baru.</h5>
        </div>
        <div uk-alert class="uk-alert-primary" v-if="errors.invalid"> 
            {{ errors.invalid }}
        </div>
        <div style="margin-bottom:30px;">
            <table>
                <tr>
                    <td style="font-size:14px;font-weight:bold;margin:0.2em;">Code</td>
                    <td style="font-size:14px;font-weight:bold;margin:0.2em;">:</td>
                    <td style="font-size:14px;margin:0.2em;font-weight:200;font-style:italic;">{{verifParam.id}}-{{verifParam.token}}</td>
                </tr>     
                <tr>
                    <td style="font-size:14px;font-weight:bold;margin:0.2em;">Username</td>
                    <td style="font-size:14px;font-weight:bold;margin:0.2em;">:</td>
                    <td style="font-size:14px;margin:0.2em;font-weight:200;font-style:italic;">{{data.username}}</td>
                </tr>
                <tr>
                    <td style="font-size:14px;font-weight:bold;margin:0.2em;">Email</td>
                    <td style="font-size:14px;font-weight:bold;margin:0.2em;">:</td>
                    <td style="font-size:14px;margin:0.2em;font-weight:200;font-style:italic;">{{data.email}}</td>
                </tr>
                <tr v-if="isVerified">
                    <td colspan="3" style="text-align:center;font-size:18px;font-weight:200;padding:1em;background-color:#39f;color:white;">{{successMessage}}</td>
                </tr>
            </table>
        </div>
        <div v-if="isProcess" class="uk-card uk-text-left" uk-card style="width:100%;">
            <div uk-spinner="ratio: 3.5"></div>
        </div>
        <div v-else class="uk-grid-small" uk-grid>
            <div v-if="isVerified" class="uk-width-auto@m uk-margin-small">
                <router-link to="/" class="uk-button uk-button-primary" style="border-radius:50px;">MASUK APLIKASI</router-link>
            </div>
            <div v-else class="uk-width-auto@m uk-margin-small">
                <button type="submit" @click.prevent="submitSignupVerification" class="uk-button uk-button-primary" style="border-radius:50px;">VERIFIKASI ULANG</button>
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
            isVerified : false,
            isProcess : true,
            verifParam : {
                token: null,
                id : null,
            },
            data: {
                email : '',
                username: null,
            }
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },
    created() {
        /**
         * format : 
         * http://localhost:8000/signup/verification?id=xxxx&&token=xxxx
         */
        this.verifParam.token = this.$route.query.token;
        this.verifParam.id = this.$route.query.id;        
    },
    mounted(){
        this.submitSignupVerification();
    },
    methods : {
        ...mapActions(['signupVerification']),
        ...mapMutations(['CLR_ERRORS']),
        
        submitSignupVerification() {
            this.CLR_ERRORS();
            this.signupVerification(this.verifParam).then((response) => {
                if (response.success == true) {
                    this.successMessage = response.message;
                    this.data.username = response.data.username;
                    this.data.email = response.data.email;                    
                    this.CLR_ERRORS();
                    this.isVerified = true;
                    this.isProcess = false;
                }
                else {
                    this.isVerified = false;
                    this.isProcess = false;
                }
            })
        }
    }
}
</script>