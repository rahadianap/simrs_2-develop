import { createRouter,createWebHistory } from "vue-router"
import guest from './guest.router'
import authenticated from './authenticated.router'
import setting from './setting.router'
import master from './master.router'
import pendaftaran from './pendaftaran.router'
import pelayanan from './pelayanan.router'
import penunjang from "./penunjang.router"
import persediaan from "./persediaan.router"

import keuangan from "./keuangan.router"
import gizi from "./gizi.router"
import lab from "./lab.router"
import radiologi from "./radiologi.router"
import operasi from "./operasi.router"
import satusehat from "./satusehat.router"
import report from "./report.router"
import bpjs from "./bpjs.router"
import kiosk from "./kiosk.router"
import asset from "./asset.router"

import patologi from "./patologi.router"
import pembayaran from "./pembayaran.router"
import humanresource from "./humanresource.router"
import medrec from './medicalrecord.router'

import farmasi from "./farmasi.router"

import store from '@/Stores/index'
import praktekDokter from "./praktekdokter.router"

const router = createRouter({
    history : createWebHistory(),
    scrollBehavior(to, from, savedPosition) {
        const fromKeepScrollRoute = from.meta && from.meta.keepScroll
        const toKeepScrollRoute = to.meta && to.meta.keepScroll
        if (fromKeepScrollRoute || toKeepScrollRoute) return
    
        return savedPosition || { x: 0, y: 0 }
    },
    // Router untuk link active di navbar
    linkExactActiveClass: "active",

    routes : [
        ...guest,
        ...authenticated,
        ...setting,
        ...master,
        ...pendaftaran,
        ...pelayanan,
        ...penunjang,
        ...persediaan,
        ...keuangan,
        ...gizi,
        ...lab,
        ...radiologi,
        ...operasi,
        ...satusehat,
        ...report,
        ...bpjs,        
        ...kiosk,
        ...asset,
        ...patologi,
        ...pembayaran,
        ...humanresource,
        ...farmasi,
        ...medrec,
        ...praktekDokter
    ] 
});

//Navigation Guards
router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        let auth = store.getters.isAuth;
        if (!auth) { next({ name: 'login' }) } 
        else { next() }
    } 
    else { next() }
})

export default router