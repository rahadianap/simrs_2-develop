<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="modalPersalinan" style="min-height:50vh;padding:0;margin:0;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form-persalinan ref="formPersalinan" v-on:formPatientClosed="modalPersalinanClosed"></form-persalinan>
            </div>
        </div>
    </div>
</template>

<script>
import FormPersalinan from '@/Pages/Penunjang/Operasi/components/FormPersalinan.vue';

export default {
    name : 'modal-persalinan',
    components : { FormPersalinan, },
    methods : {
        createNewPersalinan(){
            this.$refs.formPersalinan.newPasien();
            UIkit.modal('#modalPersalinan').show();
        },
        editDataPasien(pasienId){
            this.$refs.formPersalinan.editPasien(pasienId);
            UIkit.modal('#modalPersalinan').show();
        },

        modalPersalinanClosed(data){
            this.$emit('modalPersalinanClosed',data);
            UIkit.modal('#modalPersalinan').hide();
        }
        
    }
}
</script>