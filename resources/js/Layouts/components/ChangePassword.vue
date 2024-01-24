<template>
    <div id="modalChangePassword" uk-modal>
        <div class="uk-modal-dialog uk-modal-body">
            <h4 class="uk-modal-title" style="margin:0;padding:0;">Ubah Password</h4>
            <form action="" @submit.prevent="submitChangePassword" style="margin:0;padding:0;">
                <p style="margin:0;padding:0 0 1em 0;color:black;">Masukkan password lama dan password baru anda.</p>
                <div uk-alert class="uk-alert-primary" v-if="errors.invalid"> 
                    {{ errors.invalid }}
                </div>
                <div>
                    <div class="uk-inline" style="width:100%;margin:0.2em;">
                        <span class="uk-form-icon uk-form-icon" uk-icon="icon: lock" style="color:#39f;"></span>
                        <input class="uk-input" :type="passwordType" placeholder="password" v-model="data.password" minlength="6" required pattern="\S(.*\S)?" style="color:black; border:1px solid #39f; border-radius:0px;background:rgba(255, 255, 255, 0.1);">
                    </div>
                </div>
                <div>
                    <div class="uk-inline" style="width:100%;margin:0.2em;">
                        <span class="uk-form-icon uk-form-icon" uk-icon="icon: lock" style="color:#39f;"></span>
                        <input class="uk-input" :type="passwordType" placeholder="password" v-model="data.new_password" minlength="6" required pattern="\S(.*\S)?" style="color:black; border:1px solid #39f; border-radius:0px;background:rgba(255, 255, 255, 0.1);">
                    </div>
                </div>
                <div class="uk-margin-small">
                    <label><input class="uk-checkbox" @change="showPassword" type="checkbox" style="color:black;margin-left:5px;border:1px solid black;"> Tampilkan password</label>
                </div>    
                <p class="uk-text-right">
                    <button class="uk-button uk-button-default uk-modal-close" type="button">Batal</button>
                    <button class="uk-button uk-button-primary" type="submit">Simpan</button>
                </p>
            </form>
            
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
export default {
    data() {
        return {
            passwordType : 'password',
            data: {
                new_password : null,
                password: null,
            }
        }
    },
    mounted(){
        this.CLR_ERRORS();
    },           
    computed : {
        ...mapGetters(['errors']),
    },
    methods :{
        ...mapActions(['changePassword','logout']),
        ...mapMutations(['CLR_ERRORS','CLR_USER','CLR_MENUS','CLR_CLIENTS','CLR_SELECTED_CLIENT','CLR_TOKEN']),

        showPassword(){
            if(this.passwordType === 'password') {
                this.passwordType = 'text'
            } else {
                this.passwordType = 'password'
            }
        },
        showModal(){
            this.CLR_ERRORS();
            UIkit.modal('#modalChangePassword').show();
        },
                
        submitChangePassword(){
            this.CLR_ERRORS();
            this.changePassword(this.data).then((response) => {
                if (response.success == true) {
                    UIkit.modal('#modalChangePassword').hide();
                    alert("Password berhasil diubah. Silahkan login ulang.");
                    this.doLogout();
                }
            })
        },

        doLogout(){
            this.CLR_ERRORS();
            this.logout(this.data).then((response) => {                
                this.CLR_USER();
                this.CLR_MENUS();
                this.CLR_CLIENTS();
                this.CLR_SELECTED_CLIENT();
                this.CLR_TOKEN(); 
                this.$router.push({ name: 'index' });
            });
        }
    }
}
</script>