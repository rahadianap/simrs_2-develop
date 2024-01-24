<template>
    <!-- Kop Laporan -->
    <div class="uk-container uk-flex uk-flex-middle uk-card-body" ref="kop-surat">
        <div class="uk-width-1-4">
            <div class="uk-flex uk-flex-center uk-flex-middle">
                <img src="../../../../../Assets/aviat-logo.webp" class="logo-pdf uk-text-center uk-responsive-width">
            </div>
        </div>
        <div class="uk-width-1-2">
            <div class="uk-flex-center uk-text-center">
                <p style="font-size: 20px;font-family: verdana-bold;font-weight:bold;letter-spacing:1.1px;">{{namaInstansi}}</p>
                <p class="uk-margin-small" style="font-size: 16px;font-family: verdana;letter-spacing:1.3px;">{{alamatInstansi}}</p>
                <p class="uk-margin-small" style="font-size: 16px;font-family: verdana;letter-spacing:1.3px;">{{kontakInstansi}}</p>
            </div>
        </div>
    </div>
    <hr style="border:0.2px solid #cc0202;margin-top:7px;">                                                               
</template>
<script>
import "./style.css"
import { mapGetters, mapActions, mapMutations } from 'vuex';
export default {
    name: 'kop',
    props : {
        kopSurat : { type:Boolean, require:false}, 
        namaInstansi : { type:String , required:true, default:'' },
        alamatInstansi : { type:String , required:true, default:'' },    
        kontakInstansi : { type:String, default:'', required : false },    
    },
    computed: {

    },
    methods: {
 
    },
}


</script>