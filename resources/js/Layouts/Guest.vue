<template>
    <div class="guest-layout" >        
        <navbar v-if="isReady">
            <ul class="uk-navbar-nav">
                <li class="uk-visible@s nav-menu"><router-link to="/">Masuk</router-link></li>
                <li class="uk-visible@s nav-menu"><router-link to="/signup">Daftar</router-link></li>
                <li class="uk-visible@s nav-menu"><router-link to="/forget/password">Lupa Password</router-link></li>
                <li class="uk-hidden@s">
                    <a class="uk-navbar-toggle uk-navbar-toggle-animate" uk-navbar-toggle-icon href="#" style="color:black;display:block;line-height:50px;"></a>
                    <div class="uk-navbar-dropdown">
                        <ul class="uk-nav uk-navbar-dropdown-nav">
                            <li><router-link to="/" style="color:black;">Masuk</router-link></li>
                            <li><router-link to="/signup" style="color:black;">Daftar</router-link></li>
                            <li><router-link to="/forget/password" style="color:black;">Lupa Password</router-link></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </navbar>
        <div class="hero-image">
            <div class="uk-container"  style="width:100%;">
                <div class="hero-text">
                    <slot />  
                </div>
            </div>
        </div>        
    </div>
</template>

<script>
import navbar from './components/Navbar'
export default {
    components : {
        navbar,
    },
    data() {
        return { 
            isReady : false,
        }
    },
    mounted() {
        this.isReady = true;
    },
    methods : {
    }

}

</script>

<style>
    .guest-layout {
        background-color:white;
    }
    .nav-menu {
        height:60px;
        padding:0.5em 1em 0.5em 0.5em;
    }

    li.nav-menu a { 
        color:black;
        font-size:12px;
        font-weight:500;
        line-height:30px;
        border-top: 5px solid transparent !important;
        display:inline-block;
    }

    .hero-image {
        /* Use "linear-gradient" to add a darken background effect to the image (photographer.jpg). This will make the text easier to read */
        background-image: linear-gradient(rgba(245, 245, 245, 0.9), rgba(245, 245, 245, 0.9)), url("../Assets/bg.jpg");
        background-image: radial-gradient(rgba(245, 245, 245, 0.9), rgba(245, 245, 245, 0.9)), url("../Assets/bg.jpg"); 

        /* Set a specific height */
        height: calc(100vh - 52px);
        /* Position and center the image to scale nicely on all screens */
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
        position: relative;
        background-attachment:fixed;
        overflow :hidden;
        overflow-y: auto;
    }
    /* Place text in the middle of the image */
    .hero-text {
        position: relative;
        top: 5vh;
        color: black;
        margin: auto;
        height:calc(100vh - 52px);
    }

    @media only screen and (min-width: 640px) {
        .hero-text{
            width:80%;
            margin:auto;
        }
    }
    
</style>