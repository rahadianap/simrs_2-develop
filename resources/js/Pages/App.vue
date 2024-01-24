<template>
    <component :is="layout">
        <router-view/>
    </component>
</template>

<script>

import AppLayout from '@/Layouts/Authenticated'
import GuestLayout from '@/Layouts/Guest'
import KioskLayout from '@/Layouts/Kiosk'

import router from '../Routers/index'

const defaultLayout = 'GuestLayout';

export default {
    
    name: 'App',
    components : {
        AppLayout,GuestLayout,KioskLayout
    },
    computed: {
        layout () {
            return (this.$route.meta.layout || defaultLayout);
        }
    },
}

</script>