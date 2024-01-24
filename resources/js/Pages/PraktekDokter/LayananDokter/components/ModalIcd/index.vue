<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="formIcdDiagnosa" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                <p>{{ errors.invalid }}</p>
            </div>
            
            <h2 class="uk-modal-title">MASTER ICD DIAGNOSA</h2>
            <button class="uk-modal-close-default" type="button" uk-close></button>

            <div class="uk-grid-small uk-flex uk-grid" uk-grid>
                <div class="uk-width-1-2@m">
                    <input type="text" style="line-height: 30px; width:100%;" v-model="filterTable.keyword" placeholder="kata pencarian" @change="submitFilter">
                </div>
                <div class="uk-width-1-2@m">
                    <button @click.prevent="submitFilter"  style="height: 35px;width:80px;">Cari</button>
                </div>                                        
            </div>
            <div style="margin-top:1em;">
                <table class="uk-table uk-table-striped">
                    <thead>
                        <tr>
                            <th style="width:80px;font-size: 14px; font-weight: 700;">Kode</th>
                            <th style="font-size: 14px; font-weight: 700;">Diagnosa</th>
                            <th style="width:70px; font-size: 14px; font-weight: 700; text-align: center;">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width:80px; padding:0.2em; margin:0;">
                                <input type="text" v-model="dataIcd.kode_icd" placeholder="kode ICD" style="height: 30px;width:80px;">
                            </td>                            
                            <td style="padding:0.2em; margin:0;">
                                <input type="text" v-model="dataIcd.nama_icd" placeholder="nama diagnosa" style="height: 30px;width:99%;">
                            </td>                            
                            <td style="width:70px; padding:0.2em; margin:0; text-align: center;">
                                <button @click.prevent="saveDiagnosa" style="height: 35px;width:60px;">Simpan</button>
                            </td>
                        </tr>

                        <tr v-if="icdDiagnoses" v-for="icd in icdDiagnoses.data">
                            <td style="width:80px; padding:0.2em;">{{ icd.kode_icd }}</td>
                            <td style="padding:0.2em;">{{ icd.nama_icd }}</td>
                            <td style="width:70px; text-align: center; padding:0.1em;">
                                <a href="#" @click.prevent="updateData(icd)" ><span uk-icon="pencil"></span></a>
                                <a href="#" @click.prevent="deleteData(icd)" style="margin-left:5px;"><span uk-icon="trash"></span></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import BaseTable from '@/Components/BaseTable';
import FormIcdDiagnosa from '@/Pages/MasterData/ICD/Components/FormIcdDiagnosa.vue';

export default {
    name : 'modal-icd',
    components : {
        BaseTable,
        FormIcdDiagnosa,
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('icd10',['icdDiagnoses']),
    },
    data() {
        return {
            dataIcd : {
                kode_icd : null,
                client_id : null,
                nama_icd : null,
                no_dtd : null,
                kata_kunci : null,
                is_valid_icd : true,
                is_aktif : true,
            },

            filterTable : {
                is_aktif : true,
                is_valid_icd : true,
                keyword : null,
                page : 1,
                per_page : 20,
            },

            searchUrl : '/icd10'
        }
    },
    methods : {
        ...mapActions('icd10',['listIcdDiagnosa','deleteIcdDiagnosa','dataIcdDiagnosa','createIcdDiagnosa','updateIcdDiagnosa']),
        ...mapActions('importExcel',['importExcelICD10']),
        ...mapMutations(['CLR_ERRORS']),

        showModal(){
            this.submitFilter();
            UIkit.modal('#formIcdDiagnosa').show();
        },

        /**tampikan modal untuk membuat dokter baru */
        addIcd10(){        
            this.CLR_ERRORS();
            this.$refs.formIcdDiagnosa.newIcdDiagnosa();        
        },

        refreshTable() {
            this.$refs.tableIcdDiagnosa.retrieveData();
        },

        updateData(data){
            this.CLR_ERRORS();
            this.fillDataIcd(data);
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus Kode ICD ${data.nama_icd}. Lanjutkan ?`)){
                this.deleteIcdDiagnosa(data.kode_icd).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.submitFilter();
                    }
                    else { alert (response.message) }
                });
            };
        },

        submitFilter(){
            this.listIcdDiagnosa(this.filterTable).then((response) => {
                if (response.success == true) {
                    //console.log(this.icdDiagnoses);
                }
                else { alert (response.message) }
            });
        },

        saveDiagnosa(){
            if(this.dataIcd.kode_icd == null || this.dataIcd.kode_icd == '' || this.dataIcd.nama_icd == null || this.dataIcd.nama_icd == '' ) {
                alert('kode / nama icd tidak boleh kosong');
                return false;
            }
            else {
                this.submitDataDiagnosa();
            }
        },

        submitDataDiagnosa(){
            this.CLR_ERRORS();
            if(this.isUpdate) {
                this.updateIcdDiagnosa(this.dataIcd).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.clrDataIcd();
                        this.submitFilter();
                        this.$emit('dataChanged',true);
                    }
                    else { alert (response.message) }
                });
            }
            else {
                this.createIcdDiagnosa(this.dataIcd).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.clrDataIcd();
                        this.submitFilter();
                        this.$emit('dataChanged',true);
                    }
                    else { alert (response.message) }
                });
            }
        },

        clrDataIcd(){
            this.isUpdate = false;
            this.dataIcd.kode_icd = null;
            this.dataIcd.client_id = null;
            this.dataIcd.nama_icd = null;
            this.dataIcd.no_dtd = null;
            this.dataIcd.kata_kunci = null;
            this.dataIcd.is_valid_icd = true;
            this.dataIcd.is_aktif = true;
        },

        fillDataIcd(data) {
            this.isUpdate = true;
            this.dataIcd.kode_icd = data.kode_icd;
            this.dataIcd.client_id = data.client_id;
            this.dataIcd.nama_icd = data.nama_icd;
            this.dataIcd.no_dtd = data.no_dtd;
            this.dataIcd.kata_kunci = data.kata_kunci;
            this.dataIcd.is_valid_icd = data.is_valid_icd;
            this.dataIcd.is_aktif = data.is_aktif;
        }
    }
    
}
</script>