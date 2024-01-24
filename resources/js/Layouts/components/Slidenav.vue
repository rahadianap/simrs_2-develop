<template>
    <div id="offcanvas-menu" uk-offcanvas="mode: push; overlay: true;" style="height:100vh;overflow:hidden;">
        <div class="uk-offcanvas-bar" style="background-color:white;padding:0;margin:0;height:100vh;overflow:hidden;">
            <div class=" uk-grid-small" uk-grid style="height:40px;border-bottom:0px solid #eee;">
                <div style="background-color:#fff;height:40px; width:50px;color:white;text-align:center; margin:0;padding:0;">
                    <a href="#" class="uk-offcanvas-close" style="color:black;"><span uk-icon="icon: close; ratio:1" style="margin:0 0 0 0;"></span></a>
                </div>
            </div>
            <div style="padding:0;margin:0;height:calc(100vh - 50px);overflow:auto;">
                <navmenu :changePassword="ubahPassword"/>
            </div>
        </div>
    </div>
</template>

<script>
import navmenu from '@/Layouts/components/Navmenu.vue'
export default {
    props : {
        ubahPassword: { type:Function, required:false }
    },
    components : {
        navmenu
    },
}
</script>