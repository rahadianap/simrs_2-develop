<template>
    <div class="uk-container uk-container-xlarge" style="padding:1em;">
        <div style="padding:0;margin:0 0 0 0;background-color:#fff;">
            <ul id="switcherPasienPoli" class="uk-tab hims-penunjang-tab" uk-switcher style="padding:0;margin:0;">
                <li style="padding:0;"  :class=" tabName.name == 'listPendaftaran' ? 'uk-active' : ''">
                    <div>
                        <router-link :to = "{ name:'listPendaftaran' }"><h5>Antrian Poli</h5></router-link>
                    </div>
                </li>
                <li style="padding:0;"  :class=" tabName.name == 'listBookingPoli' ? 'uk-active' : ''">
                    <div>
                        <router-link :to = "{ name:'listBookingPoli' }"><h5>Booking Poli</h5></router-link>
                    </div>
                </li>
                <li style="padding:0;"  :class=" tabName.name == 'listMasterPasien' ? 'uk-active' : ''">
                    <div>
                        <router-link :to = "{ name:'listMasterPasien' }"><h5>Master Pasien</h5></router-link>
                    </div>
                </li>                
            </ul>
            <div style="padding:1em;min-height:40vh;">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';

export default {
    computed : {
        ...mapGetters(['errors']),        
    },

    data() {
        return {
            tabName : null,
        }
    },
    watch:{
        'tabName': function(newVal, oldVal) {
            this.tabName = newVal;
        },
    },
    created() {
        this.tabName = this.$router.currentRoute;
    },
    mounted() {
        this.init();
    },

    methods : {
        ...mapActions('referensi',['listReferensi']),
        ...mapActions('unitPelayanan',['listUnitPelayanan']),
        ...mapActions('dokter',['listDokter']),
        ...mapMutations(['CLR_ERRORS']),

        init(){
            if(!this.isRefExist) { this.listReferensi({per_page:'ALL'}); }  
            if(this.unitLists.length < 1 ){
                let param = {'per_page':'ALL','is_aktif':true };
                this.listUnitPelayanan(param).then((response) => {
                    if (response.success == true) {}
                });
            }
            //ambil daftar dokter
            if(this.doctorLists.length < 1) {
                this.listDokter().then((response) => {
                    if (response.success == true) {}
                });
            }
        },
    }
}
</script>
<style>

ul.hims-poli-tab li {
    margin:0;
    padding:0;
}

ul.hims-poli-tab li h5{
    padding:0 1em 0 1em;
    margin:0;
}

ul.hims-poli-tab li div a {
    padding:0;
    margin:0;
    color:#000; 
    display:block; 
    padding:0.5em;
    text-decoration: none;
    font-weight: 500;
}

ul.hims-poli-tab li div {
    margin:0; 
    background-color: #eee; 
    color:#000;
    padding:0;
}

ul.hims-poli-tab li.uk-active div {
    margin:0; 
    background-color: #fff; 
    color:#000;
    padding:0;
}

ul.hims-poli-tab li div a h5 {
    color:#666;
    font-weight: 500;
}

ul.hims-poli-tab li.uk-active div a h5 {
    color:#000;
    font-weight: 500;
}
</style>