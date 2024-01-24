<template>
    <div class="uk-container uk-container" style="padding:1em;">
        <header-page title="CREDENTIAL BPJS" subTitle="data credential bridging BPJS"></header-page>
        <div style="margin-top:1em;padding:1em;" class="uk-card-default">
            <form action="" @submit.prevent="updateCredential">
                <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                    <p>{{ errors.invalid }}</p>
                </div>                
                    
                <div class="uk-width-1-1@s uk-grid-small1" uk-grid1 style="padding:0.5em;">  

                    <div class="uk-width-1-1@m">
                        <h4>{{credBpjs.client_nama}}</h4>
                    </div>                       
                    <div class="uk-width-1-1@m">
                        <h5 style="font-weight:500;">VCLAIM BPJS</h5>
                    </div>                       
                    <div class="uk-width-1-2@m">
                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Cons ID*</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-form-small" v-model="credBpjs.bpjs_cons_id" type="text" required>
                        </div>
                    </div>
                    <div class="uk-width-1-2@m">
                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Secret Key*</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-form-small" v-model="credBpjs.bpjs_secret" type="text" required>
                        </div>
                    </div>
                    <div class="uk-width-1-2@m">
                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">User Key*</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-form-small" v-model="credBpjs.bpjs_user_key" type="text" required>
                        </div>
                    </div>
                    
                    <div class="uk-width-1-1" style="padding:1em 1em 0.2em 0.2em;margin:0;">
                        <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="credBpjs.is_bpjs_aktif" style="margin-left:5px;"> Bridging BPJS Aktif</label>
                    </div>

                    <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.2em;margin:0;">
                        <button>SIMPAN</button>
                    </div>
                </div>

            </form>
        </div>     
        
        <div style="margin-top:1em;padding:1em;" class="uk-card-default">
            <form action="" @submit.prevent="updateAntrianCredential">
                <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                    <p>{{ errors.invalid }}</p>
                </div>                
                    
                <div class="uk-width-1-1@s uk-grid-small1" uk-grid1 style="padding:0.5em;">       
                    <div class="uk-width-1-1@m">
                        <h5 style="font-weight:500;">ANTREAN BPJS</h5>
                    </div>                       
                    <div class="uk-width-1-2@m">
                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Username*</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-form-small" v-model="credAntrian.jkn_username" type="text" disabled>
                        </div>
                    </div>
                    <div class="uk-width-1-2@m">
                        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Passkey*</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-form-small" v-model="credAntrian.jkn_password" type="text" disabled>
                        </div>
                    </div>
                    <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.2em;margin:0;">
                        <button>BUAT CREDENTIAL</button>
                    </div>
                </div>
            </form>
        </div>  
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
export default {
    components : {
        headerPage,
    },
    data() {
        return {
            credBpjs : {
                client_nama : null,
                bpjs_cons_id : null,
                bpjs_secret : null,
                bpjs_user_key : null,
                is_bpjs_aktif : false,
            },

            credAntrian : {
                jkn_password : null,
                jkn_password : null,
            }
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },
    mounted() {
        this.retrieveData();
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('bpjsReferensi',['getBpjsCredential','updateBpjsCredential','getJknCredential','updateJknCredential']),

        retrieveData(){
            this.getBpjsCredential().then((response) => {
                if (response.success == true) {
                    this.fillData(response.data);
                    this.retrieveDataJkn();
                }
                else { 
                    alert(response.message); 
                }
            })
        },

        fillData(data) {
            this.credBpjs.client_nama = data.client_nama;
            this.credBpjs.bpjs_cons_id = data.bpjs_cons_id;
            this.credBpjs.bpjs_secret = data.bpjs_secret;
            this.credBpjs.bpjs_user_key = data.bpjs_user_key;
            this.credBpjs.is_bpjs_aktif = data.is_bpjs_aktif;
        },

        updateCredential() {
            this.CLR_ERRORS();
            if(confirm(`Update data credential BPJS ?`)){
                this.updateBpjsCredential(this.credBpjs).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.retrieveData();
                    }
                    else { alert (response.message) }
                });
            }
        },

        retrieveDataJkn(){
            this.getJknCredential().then((response) => {
                if (response.success == true) {
                    this.fillDataJkn(response.data);
                }
                else { 
                    alert(response.message); 
                }
            })
        },

        fillDataJkn(data) {
            this.credAntrian.jkn_username = data.jkn_username;
            this.credAntrian.jkn_password = data.jkn_password;
        },

        updateAntrianCredential(){
            this.CLR_ERRORS();
            if(confirm(`Update data credential Antrian JKN Mobile akan membuat data otomatis dan mengganti data lama. Lanjutkan?`)){
                this.updateJknCredential(this.credAntrian).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.retrieveDataJkn();
                    }
                    else { alert (response.message) }
                });
            }
        }
    }
}
</script>