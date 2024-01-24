<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="modalFormPasien" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <!-- <form-pasien ref="formPasien" v-on:formPatientClosed="modalPasienClosed" ></form-pasien> -->
                <form-pasien ref="formPasien" v-on:formPatientClosed="modalPasienClosed" v-on:saveSucceed="modalPasienSaved"></form-pasien>
            </div>
        </div>
    </div>
</template>
<script>
import FormPasien from '@/Pages/MasterData/Pasien/components/FormPasien.vue';

export default {
    name : 'modal-form-pasien',
    components : { FormPasien, },
    methods : {
        createNewPasien(){
            this.$refs.formPasien.newPasien();
            UIkit.modal('#modalFormPasien').show();
        },
        editDataPasien(pasienId){
            this.$refs.formPasien.editPasien(pasienId);
            UIkit.modal('#modalFormPasien').show();
        },

        modalPasienClosed(data){
            this.$emit('modalPasienClosed',data);
            UIkit.modal('#modalFormPasien').hide();
        },

        modalPasienSaved(data){
            this.$emit('pasienSaved',data);
            UIkit.modal('#modalFormPasien').hide();
        }
        
    }
}
</script>