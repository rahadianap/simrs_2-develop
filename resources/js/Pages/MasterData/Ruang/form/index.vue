<template>
    <div class="uk-container" style="padding:1em;">
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-auto">
                <a href="#" @click.prevent="returnMainPage" style="background-color:white;padding:0.3em;display:inline-block;">
                    <span uk-icon="icon:chevron-left;ratio:2"></span>
                </a>
            </div>
            <div class="uk-width-expand">
                <h4 style="font-weight:500;margin:0;padding:0;" class="uk-text-uppercase">
                    RUANG DAN BED
                </h4>
                <p style="font-weight:300;margin:0;padding:0;font-size:12px;font-style:italic;">
                    ubah dan buat master data ruang dan bed
                </p>
            </div>
        </div>
        
        <div class="uk-container-large" style="margin-top:1em;background-color:#fff;min-height:80vh; ">
            <form-ruang ref="formRuang"></form-ruang> 
        </div>
    </div>
</template>

<script>
import FormRuang from '@/Pages/MasterData/Ruang/components/formRuang.vue';
import headerPage from '@/Components/HeaderPage.vue';

export default {
    props : {
        ruangId : { type:String, required:true, default:'baru' },
    },
    components : {
        headerPage, FormRuang
    },
    mounted(){
        this.initialize();
    },
    methods : {
        initialize() {
            if(this.ruangId == '' || this.ruangId == 'baru' || this.ruangId == null) {
                this.$refs.formRuang.newRuang();
            }
            else {
                this.$refs.formRuang.editRuang(this.ruangId);
            }
        },

        returnMainPage(){
            this.$router.push('/master/ruang');
        }
    }
}
</script>