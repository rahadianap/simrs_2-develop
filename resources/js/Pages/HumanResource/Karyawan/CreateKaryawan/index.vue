<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-expand">
                <h4 style="font-weight:500;margin:0;padding:0;" class="uk-text-uppercase">
                    MASTER KARYAWAN
                </h4>
                <p style="font-weight:300;margin:0;padding:0;font-size:12px;font-style:italic;">
                    master data dan detail info karyawan 
                </p>
            </div>
        </div>
        <div style="margin-top:1em;background-color:#fff;">
            <form-karyawan-baru ref="formKaryawanBaru" :karyawanId="karyawanId"></form-karyawan-baru>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import FormKaryawanBaru from './component/FormKaryawanBaru.vue';

export default {
    name : 'tambahKaryawan',
    props : {
        karyawanId : { type:String, required:true, default:'baru' },
    },
    components : {
        SearchSelect,
        FormKaryawanBaru,
    },
    mounted() {
        this.initialize();
    },

    methods : {
        initialize() {
            if (this.karyawanId == '' || this.karyawanId == 'baru' || this.karyawanId == null) {
                this.$refs.formKaryawanBaru.newKaryawan();
            } 
            else {
                this.$refs.formKaryawanBaru.editKaryawan(this.karyawanId);
            }
        }
    }
}
</script>