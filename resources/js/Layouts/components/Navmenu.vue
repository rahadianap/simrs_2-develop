<template>
    <div style="padding:0;margin:0;background-color:transparent !important;" >  
        <div class="uk-grid-small" uk-grid style="margin:0; padding:1em 0.5em 1em 0.5em;border-bottom:1px solid #f3f3f3;">
            <div class="uk-width-auto">
                <a href="#"><img id="img-avatar" class="uk-border-circle" src = "@/Assets/user.png" style="margin:0;padding:0;width:35px;" /></a>
            </div>
            <div class="uk-width-expand">
                <p style="font-size:14px; font-weight:bold;padding:0;margin:0;"><a style="text-decoration: none;color:#333;">{{username}}</a></p>
                <p style="font-size:12px; padding:0;margin:0;"><a style="text-decoration: none;color:#333;">{{usermail}}</a></p>
            </div>
        </div>
        <div style="overflow:auto;">
            <ul class="his-accordion">
                <li key="mainmenu" class="his-accordion-tab">
                    <a class="his-accordion-header" uk-toggle="target: #mymenu">
                        <span uk-icon="grid" class="his-accordion-title-icon"></span>
                        <span class="his-accordion-title" style="font-size:14px;font-weight:500;">Menu Pengguna</span>
                    </a>
                    <div id="mymenu" class="his-accordion-content" style="margin:0;background-color:#fff;">
                        <ul class="uk-list">
                            <li key="mymenu001" >
                                <router-link :to = "{ name:'home' }" style="color:black;font-size:12px;">Beranda</router-link>
                            </li>
                            <li key="mymenu002" >
                                <router-link :to = "{ name:'profile' }" style="color:black;font-size:12px;">Profil Saya</router-link>
                            </li>
                            <li key="mymenu004">
                                <a href="#" @click.prevent="changePassword" style="color:black;font-size:12px;">Ubah Password</a>
                            </li>
                            <li key="mymenu005">
                                <a href="#" @click.prevent="doLogout" style="color:black;font-size:12px;">Keluar Aplikasi</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li v-if="menus.length > 0" v-for="menu in menus" :key="menu.menu_id" class="his-accordion-tab">
                    <a class="his-accordion-header" :uk-toggle="'target: #' + menu.group_menu">
                        <span :uk-icon="menu.menu_icon" class="his-accordion-title-icon"></span>
                        <span class="his-accordion-title" style="font-size:14px;font-weight:500;">{{menu.menu_title}}</span>
                    </a>
                    <div :id="menu.group_menu" class="his-accordion-content" style="margin:0;background-color:#ffffff;">
                        <ul class="uk-list">
                            <li v-for="item in menu.items" :key="item.menu_id" class="menu-list-item" v-on:click="setActiveClass(item.menu_id)" :id="item.menu_id" :ref="item.menu_id">
                                <router-link :to = "{ path:item.menu_link }" style="color:black;font-size:12px;">{{item.menu_title}}</router-link>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul> 
        </div> 
    </div>
    
</template>

<script>

import { mapActions, mapMutations, mapGetters, mapState } from 'vuex';

export default {
    props : {
        changePassword : { type: Function, required:false },
    },
    data() {
        return {}
    },
    mounted(){
        
    },
    watched(){

    },
    computed: {
        ...mapGetters(['user','userMenus','errors']),

        menus() { 
            if(this.userMenus) { return this.userMenus }
            else {return null };
        },
        avatar() { 
            if(this.user !== null) { return this.user.url_avatar; } 
            else { return require('@/Assets/user.png'); }
        },
        username() {
            if(this.user !== null) { return this.user.username; } 
            else { return ''; }
        },        
        usermail() {
            if(this.user !== null) { return this.user.email; } 
            else { return ''; }
        },
    },

    methods : { 
        ...mapActions(['logout']),
        ...mapMutations(['CLR_ERRORS','CLR_USER','CLR_MENUS','CLR_CLIENTS','CLR_SELECTED_CLIENT','CLR_TOKEN']),

        // Function Nav Active
        setActiveClass() {
            $(document).ready(function () {
               click(function (){
                    $(this).addClass("active").siblings().removeClass("active");
                })
            })
        },
        //  End Nav Active

        doLogout(){
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
    }
}
</script>

<style scoped>
    /* .icon-menu-sidebar {
        padding:0;
        margin:0;
        background-color:white;
        height:calc(100vh - 52px); 
        width:50px;
        text-align:center;
    }

    .icon-menu-sidebar button {
        width:50px;
        height:50px;
        padding:0;
        margin:0;
        border:none;
        background-color:#1e87f0;
    } */

   .accordion-title {
       vertical-align: center;
    }

    .menu-item:hover {
        background-color:whitesmoke;
    }

    li.tm-nav-parent {
        color:white;
        font-size:12px;
        padding:0.2em;
        margin:0;
        background-color: #39f;
    }

    li.tm-nav-parent a {
        color:black;
        font-size:12px;
        padding:0.2em;
        margin:0;
    }

    .tm-nav-parent ul.tm-nav-sub li a {
        color : black;
        font-size:12px;
    }    

    .his-accordion {
        list-style-type: none;
        margin: 0;
        padding: 0.8em;
    }
    
    .his-accordion-header {
        background-color: #fafafa;
        /* background-color: #cc0202; */
        display:block;
        width: 100%;
        line-height:40px;
        text-decoration: none;
        color:black;
        /* color:white; */
        margin-bottom:0.5em;
        font-weight:400;
        box-shadow : 1px 1px #aaaaaa;
    }

    .his-accordion-header:hover {
        background-color: #cc0202;
        color:white;
        /* background-color: #fafafa;
        color:black; */
    }

    .his-accordion-title {
        margin: 0.5em 1em 0 1em;
        font-size: 14px;
        font-weight: bold;
    }
    .his-accordion-title-icon {
        margin: 0.2em 0.5em 0.2em 0.5em;
        font-size: 12px;
        font-weight: bold;
    }

    ul.his-accordion-content {
        margin:0;
        padding:1em;
    }

    .his-accordion-content ul li {
        margin:0;
        padding:0.4em 0.4em 0.4em 2em;
        font-size:14px;        
    }

    .his-accordion-content ul li a {
        color:#666;
        text-decoration: none;
        font-weight:400;
        display:block;
        width:100%;
    }

    .his-accordion-content ul li a:hover {
        color:#000;
        font-weight: 500;
        text-decoration: none;
    }
    .active {
        /* background: #dfe0e5; */
        background: rgb(223, 224, 229, 0.5);
        overflow: hidden;
        border-left: 3px solid #cc0202;
        padding: 5px 0px 5px 6px;
    }

</style>