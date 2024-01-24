<template>
    <div class="kiosk-layout" style="width:100%;margin:0;padding:0;">        
        <!-- <navbar>
            <div class="uk-inline" style="margin:0 1em 0 1em;">
                <a href="#" style="color:black; font-weight:400; padding:0; text-decoration: none; font-size:14px;">
                    Halo, {{ username }} 
                    <img id="img-avatar" class="uk-border-circle" :src="avatar" style="margin:0 0 0 1em;padding:0;width:30px;">
                </a>
                <div uk-dropdown="mode: click;offset:24px;">
                    <ul class="uk-nav user-menu" style="padding:0; margin:0;">
                        <li><router-link :to = "{ name:'profile' }" style="color:black;font-size:14px;font-weight:400;">Profil saya</router-link></li>
                        <li><a href="#" @click.prevent="changePassword" style="color:black;font-size:14px;font-weight:400;">Ubah Password</a></li>
                        <li><a href="#" @click.prevent="doLogout" style="color:black;font-size:14px;font-weight:400;">Keluar</a></li>
                    </ul>
                </div>
            </div>      
        </navbar> -->
        
        <!-- <slidenav :ubahPassword="changePassword"></slidenav>         -->
        <div id="kiosk-container" class="tm-main uk-section uk-section-default uk-grid uk-grid-small" uk-grid >            
            <!-- <div class="uk-width-auto uk-visible@m" style="margin:0;padding:0;">
                <sidenav  v-if="isReady" :ubahPassword="changePassword"/>
            </div> -->
            <div id="div-kiosk" class="uk-width-expand uk-container uk-container-xlarge">
                <slot/>
            </div>
        </div>
        <!-- <changepassword ref = "modalChangePassword"/> -->
    </div>
</template>

<script>
import navbar from './components/Navbar'
import slidenav from './components/Slidenav'
import sidenav from './components/Sidenav'
import changepassword from './components/ChangePassword.vue'

import { mapGetters, mapActions, mapMutations,mapState } from 'vuex';

export default {    
    data(){
        return {
            isReady : false,
        }
    },
    components : {
        navbar,    
        slidenav,
        sidenav,
        changepassword
    },
    computed : {
        ...mapGetters(['errors','user','clientId']), 

        avatar() { 
            if(this.user !== null) { return this.user.url_avatar; } 
            else { return require('@/Assets/user.png'); }
        },
        username() {
            if(this.user !== null) { return this.user.username; } 
            else { return ''; }
        },
    },
    mounted(){
        this.initialize();
    },
    methods : {
        ...mapActions(['logout','clientMenus']),
        ...mapMutations(['CLR_ERRORS','CLR_USER','CLR_MENUS','CLR_CLIENTS','CLR_SELECTED_CLIENT','CLR_TOKEN']),
        
        initialize(){            
            this.CLR_ERRORS();
            this.CLR_MENUS();
            this.isReady = true;
            if(this.clientId) {
                this.clientMenus();
            }
        },

        doLogout() {
            this.CLR_ERRORS();
            this.logout(this.data).then((response) => {
                if (response.success == true) {
                    this.CLR_USER();
                    this.CLR_MENUS();
                    this.CLR_CLIENTS();
                    this.CLR_SELECTED_CLIENT();
                    this.CLR_TOKEN(); 
                    this.$router.push({ name: 'index' });      
                }
                else {
                    alert(`tidak berhasil logout ${Response.message}`);
                }
            });
        },
        
        changePassword(){
            this.$refs.modalChangePassword.showModal();
        }
    }
}

</script>

<style>
.tm-main, .all-container {
    padding:0;
    background-color: #fefefe;
} 

#kiosk-container {
    overflow:hidden;
    height:100vh;
    margin:0;
    padding:0; 
}

#div-kiosk {
    padding:0;
    background-color:white;
    overflow:auto;
    height:100vh;
    border-style: inset;
    border:2px solid #f0f0f0;
}

</style>