<template>
    <div uk-sticky style="border-bottom:1px solid #f0f0f0;">
        <nav class="uk-navbar-container" uk-navbar style="background-color:#fff;height:70px;">
            <div v-if="isAuth" class="uk-navbar-left uk-width-auto">
                <div class="uk-hidden@m" style="background-color:#fff;height:50px; width:50px;color:black;text-align:center;">
                    <a href="#" uk-toggle="target: #offcanvas-menu" style="color:black;">
                        <span uk-icon="icon: menu; ratio:1.2" style="margin:1em 0 0 0;"></span>
                    </a>
                </div>
                <div class="uk-visible@m" style="background-color:#fff;height:50px; width:50px;color:white;text-align:center;">
                    <a href="#" @click="toggleSidenav" style="color:black;"><span uk-icon="icon: menu; ratio:1.2" style="margin:1em 0 0 0;"></span></a>
                </div>
            </div>
            <div class="uk-navbar-left" style="margin-left:0.2em;">
                <p style="margin:0.4em 0.2em 0.2em 0.2em;font-weight:bold;color:#cc0202;font-size:18px;">HIMS</p>
            </div>
            <div class="uk-navbar-right">
                <slot/>
            </div>
        </nav>
    </div>
</template>

<script>
import { mapGetters} from 'vuex';
export default {
    props : {
        navbarType : { type: Number, required:false, default:1 },
    },
    data() {
        return { }
    },
    computed : {
        ...mapGetters(['isAuth']),
    },
    methods:{
        toggleSidenav(){
            let comp = document.getElementById('sidenav');
            if(comp.style.width == '0px') { comp.style.width = '250px'; }
            else { comp.style.width = '0px'; }
            return false;
        },

        toggleTopnav(){
            UIkit.drop('#topnav').show();
        }
    }

}
</script>
