<template>
    <div id="sidenav" class="his-sidenav">
        <div class="menu-container" uk-card style="height:calc(100vh - 50px);overflow:auto;">
            <navmenu :changePassword="ubahPassword"/>
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
    }

}
</script>
<style>
.menu-container {
    height:100vh;
    background-color:transparent !important;
    overflow:hidden;
    padding: 0;
}

.his-sidenav {
    width:250px;
    background-color: transparent !important;
    height: 100vh;
    overflow:hidden;
    padding:0;
    border-right:1px solid #fff;
}

/* width */
::-webkit-scrollbar {
    width: 5px;
}

/* Track */
::-webkit-scrollbar-track {
    background: #f1f1f1; 
}

/* Handle */
::-webkit-scrollbar-thumb {
    background: #aaa; 
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
    background: #ccc; 
}
</style>