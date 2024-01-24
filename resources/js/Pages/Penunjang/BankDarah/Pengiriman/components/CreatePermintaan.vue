<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="modalCreateDarah" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <template v-if="!isUpdate">
                <div>
                    <ul class="uk-tab hims-darah-tab" uk-switcher style="padding:0;margin:0;">
                        <li><div><a href="#"><h3>1. Pilih Pasien</h3></a></div></li>
                        <li style="padding:0;"><div><a href="#"><h3 >2. Form Permintaan</h3></a></div></li> 
                    </ul>
                    <ul class="uk-switcher">
                        <li>test</li>
                        <li style="padding:0;margin:0;">
                            <form-permintaan ref="formPengiriman" v-on:formPermintaanDarahClosed="modalClosed" v-on:retrievePermintaanSuccess="showModal"></form-permintaan>
                        </li>
                    </ul>
                </div>
            </template>
            <template v-else>
                <div><form-permintaan ref="formPengiriman" v-on:formPermintaanDarahClosed="modalClosed" v-on:retrievePermintaanSuccess="showModal"></form-permintaan></div>
            </template>
        </div>
    </div>
</template>

<script>
import FormPermintaan from '@/Pages/Penunjang/BankDarah/Pengiriman/components/FormPermintaan.vue';

export default {
    name : 'create-permintaan',
    components : { FormPermintaan },
    data() {
        return {
            isUpdate : true,
            isLoading : true,
        }
    },

    methods : {        
        showModal(){
            UIkit.modal('#modalCreateDarah').show();
        },
        /** function called before modal closed */
        modalClosed(){
            this.$emit('modalCreateDarahClosed',true);
            UIkit.modal('#modalCreateDarah').hide();            
        },

        /**modal call from other component for new data entry */
        newData(){
            this.isUpdate = false;
            UIkit.modal('#modalCreateDarah').show();
        },
        
        /**modal call from other component for update data entry */
        editData(refData) {
            this.isUpdate = true;
            this.$refs.formPengiriman.editData(refData);
        },
    }
}

</script>
<style>
ul.hims-darah-tab li {
    margin:0;padding:0;
}

ul.hims-darah-tab li h3{
    padding:0 1em 0 1em;margin:0;
}

ul.hims-darah-tab li div a {
    padding:0;margin:0;color:#000; display:block; padding:0.5em;
    text-decoration: none;
}

ul.hims-darah-tab li div {
    margin:0; background-color: #eee; color:#000;padding:0;
}

ul.hims-darah-tab li.uk-active div {
    margin:0; background-color: #cc0202; color:#fff;padding:0;
}

ul.hims-darah-tab li div a h3 {
    color:#000;
}

ul.hims-darah-tab li.uk-active div a h3 {
    color:#fff;
}

</style>