<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:true;" :id="modalId" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <h2 class="uk-modal-title">Asesmen</h2>
            <div style="padding:0;margin:0;">
                <form action="" @submit.prevent="submitAsesmen">
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">PEMERIKSAAN FISIK</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                catatan pemeriksaan fisik.
                            </p>
                        </div>
                        <div class="uk-width-3-4@s uk-grid-small" uk-grid> 
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Saturasi Oksigen(%)</label>
                                <div class="uk-form-controls">
                                    <input type="number" step="0.01" v-model="asesmenData.saturasi_oksigen" class="uk-input uk-form-small">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Sistole(mmHg)</label>
                                <div class="uk-form-controls">
                                    <input type="number" step="0.01" v-model="asesmenData.sistole" class="uk-input uk-form-small">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diastole(mmHg)</label>
                                <div class="uk-form-controls">
                                    <input type="number" step="0.01" v-model="asesmenData.diastole" class="uk-input uk-form-small">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Suhu(Celcius)</label>
                                <div class="uk-form-controls">
                                    <input type="number" step="0.01" v-model="asesmenData.suhu" class="uk-input uk-form-small">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nadi(per menit)</label>
                                <div class="uk-form-controls">
                                    <input type="number" step="0.01" v-model="asesmenData.denyut_nadi" class="uk-input uk-form-small">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Pernafasan(per menit)</label>
                                <div class="uk-form-controls">
                                    <input type="number" step="0.01" v-model="asesmenData.pernapasan" class="uk-input uk-form-small">
                                </div>
                            </div>
                        </div>
                    </div>
                            
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">SOAP DAN DIAGNOSA</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                catatan SOAP dan diagnosa.
                            </p>
                        </div>
                        <div class="uk-width-3-4@s uk-grid-small" uk-grid> 
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;font-weight: 500;">Subjective*</label>
                                <div class="uk-form-controls">
                                    <textarea type="textarea" rows="6" v-model="asesmenData.subjective" class="uk-textarea uk-form-small" required></textarea>
                                </div>
                            </div>
                        
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;font-weight: 500;">Objective*</label>
                                <div class="uk-form-controls">
                                    <textarea type="textarea" rows="6" v-model="asesmenData.objective" class="uk-textarea uk-form-small" required></textarea>
                                </div>
                            </div>
                        
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;font-weight: 500;">Assesment*</label>
                                <div class="uk-form-controls">
                                    <textarea type="textarea" rows="6" v-model="asesmenData.assesment" class="uk-textarea uk-form-small" required></textarea>
                                </div>
                            </div>
                        
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;font-weight: 500;">Plan*</label>
                                <div class="uk-form-controls">
                                    <textarea type="textarea" rows="6" v-model="asesmenData.plan" class="uk-textarea uk-form-small" required></textarea>
                                </div>
                            </div>

                            <div class="uk-width-1-1@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                <div class="uk-form-controls">
                                    <input type="text" v-model="asesmenData.catatan" class="uk-input uk-form-small">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 0 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">ICD Diagnosa</h5>
                            <!-- <p style="font-size:12px;font-style:italic;padding:0;margin:0;">catatan ICD Diagnosa.</p> -->
                        </div>
                        <div class="uk-width-1-1@s uk-grid-small" uk-grid> 
                            <div class="uk-width-1-1@m">
                                <div style="padding:0 0 0.2em 0;">
                                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                                        <thead>
                                            <th class="tb-header-reg" style="text-align:center;color:white;">Tipe</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;">ICD</th>
                                            <th class="tb-header-reg" style="text-align:left;color:white;">Diagnosa</th>
                                            <th class="tb-header-reg" style="width:100px;text-align:left;color:white;">Kasus Lama</th>
                                            <th class="tb-header-reg" style="width:80px;text-align:center;color:white;">...</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="padding:0.4em;">
                                                    <select class="uk-select uk-form-small" v-model="diagData.tipe">
                                                        <option></option>
                                                        <option value="PRIMER">PRIMER</option>
                                                        <option value="SEKUNDER">SEKUNDER</option>
                                                    </select>
                                                </td>
                                                <td style="padding:0.4em;">
                                                    <search-select
                                                        :config = configSelect
                                                        searchUrl = '/icd10'
                                                        placeholder = "icd diagnosa"
                                                        captionField = "nama_icd"
                                                        valueField = "kode_icd"
                                                        :itemListData = icdDesc
                                                        :value = "diagData.kode_icd"
                                                        v-on:item-select = "icdSelected"
                                                    ></search-select>
                                                </td>
                                                <td style="padding:0.4em;">
                                                    <input type="text" v-model="diagData.diagnosa" class="uk-input uk-form-small">
                                                </td>
                                                <td style="padding:0.4em;text-align:center;">
                                                    <input type="checkbox" class="uk-checkbox" v-model="diagData.kasus_lama">
                                                </td>
                                                <td style="padding:0.4em;text-align:center;">
                                                    <button type="button" @click.prevent="insertDiagnosa">tambahkan</button>
                                                </td>
                                            </tr>
                                            <template v-for="dt in asesmenData.diagnosa">
                                                <tr v-if="dt.is_aktif">
                                                    <td style="padding:1em;color:black;font-size:12px;">{{ dt.tipe }}</td>
                                                    <td style="padding:1em;color:black;font-size:12px;"><strong>{{ dt.kode_icd }}</strong> - {{ dt.nama_icd }}</td>
                                                    <td style="padding:1em;color:black;font-size:12px;">{{ dt.diagnosa }}</td>
                                                    <td style="padding:0.4em;text-align:center;color:black;"><input type="checkbox" class="uk-checkbox" v-model="dt.kasus_lama" disabled></td>
                                                    <td style="padding:0.4em;text-align:center;"><button type="button" @click.prevent="removeDiagnosa(dt)">hapus</button></td>
                                                </tr>
                                            </template>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid>
                        <div class="uk-width-1-1" style="text-align:right;padding:0 1em 0.5em 1em;">
                            <button type="button" class="uk-button uk-modal-close" style="border:none; background-color: whitesmoke; color:#000;">TUTUP</button>
                            <button type="submit" class="uk-button" style="border:none; background-color: #cc0202; color:#fff;margin-left:0.5em;">SIMPAN</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
export default {
    name : 'modal-asesmen',
    props : { 
        modalId : { type:String, required:false, default:'modalAsesmen' }, 
    },
    components : {
        SearchSelect
    },
    data() {
        return {
            isUpdate : false,

            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            icdDesc: [
                { colName : 'kode_icd', labelData : '', isTitle : false },
                { colName : 'nama_icd', labelData : '', isTitle : true },
            ],
            diagData : {
                soap_diag_id : null,
                tipe : null,
                kode_icd :null,
                nama_icd : null,
                diagnosa : null,
                kasus_lama : false,
                is_aktif: true,
            },
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatJalan',['poliTransaction','poliAssesments']),
        ...mapGetters('asesmen',['asesmenData']),
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('asesmen',['NEW_ASESMEN','SET_ASESMEN']),
        ...mapActions('asesmen',['dataAsesmen','updateAsesmen','createAsesmen','createDiagnosis']),
        ...mapActions('pemeriksaan',['createPemeriksaan','updatePemeriksaan','dataPemeriksaan','confirmPemeriksaan']),
        ...mapActions('rawatJalan',['dataTransaksiPoli']),

        icdSelected(data){
            //console.log(data);
            this.diagData.kode_icd = data.kode_icd;
            this.diagData.nama_icd = data.nama_icd;
        },
        showModal(){
            UIkit.modal(`#${this.modalId}`).show();
        },
        closeModal(){
            UIkit.modal(`#${this.modalId}`).hide();
        },
        addAsesmen(data) {
            if(data) {
                console.log(data);
                let val = {
                    reg_id : data.reg_id,
                    trx_id : data.trx_id,
                    reff_trx_id : data.reff_trx_id,
                    dokter_id : data.dokter_id,
                    dokter_nama : data.dokter_nama,
                    unit_id : data.unit_id,
                    unit_nama : data.unit_nama,
                    pasien_id : data.pasien_id,
                };
                this.NEW_ASESMEN(val);
                this.isUpdate = false;
                this.showModal();
            }
        },

        editAsesmen(data){
            this.CLR_ERRORS();
            this.isUpdate = true;
            this.isLoading = true;
            if(data) {
                this.dataAsesmen(data.soap_id).then((response) => {
                    if (response.success == true) {
                        this.SET_ASESMEN(response.data);
                        console.log(this.asesmenData);
                        this.isUpdate = true;
                        this.isLoading = false;
                        this.showModal();
                    }
                    else { 
                        this.CLR_ASESMEN();
                        alert(response.message); 
                        this.isLoading = false; 
                        this.closeModal();
                    }
                })
            }
        },
        
        submitAsesmen(){
            this.CLR_ERRORS();
            if(this.isUpdate) {
                if(confirm(`Proses ini akan mengubah data SOAP. Lanjutkan ?`)){
                    this.updateAsesmen(this.asesmenData).then((response) => {
                        if (response.success == true) {
                            this.isUpdate = true;
                            alert(response.message);
                            this.RefreshPemeriksaan();
                            this.closeModal();
                        }
                        else { 
                            alert (response.message); 
                        }
                    });
                };
            }
            else {
                if(confirm(`Proses ini akan membuat data baru SOAP. Lanjutkan ?`)){
                    this.createAsesmen(this.asesmenData).then((response) => {
                        if (response.success == true) {
                            this.isUpdate = true;
                            alert(response.message);
                            this.RefreshPemeriksaan();
                            this.closeModal();
                        }
                        else { 
                            alert (response.message); 
                        }
                    });
                    this.createDiagnosis(this.asesmenData).then((response) => {
                        if (response.success == true) {
                            this.isUpdate = true;
                            alert(response.message);
                            this.RefreshPemeriksaan();
                            this.closeModal();
                        }
                        else { 
                            alert (response.message); 
                        }
                    });
                };
            }
        },

        RefreshPemeriksaan(){
            let trxId = this.poliTransaction.trx_id;
            this.CLR_ERRORS();
            this.isLoading = true;
            if(trxId) {
                this.dataTransaksiPoli(trxId).then((response) => {
                    if (response.success == true) {
                        this.SET_POLI_TRANSACTION(response.data);
                        this.isLoading = false;
                    }
                    else { 
                        this.CLR_POLI_TRANSACTION();
                        alert(response.message); 
                        this.isLoading = false; 
                    }
                })
            }
        },

        insertDiagnosa(){
            if(this.diagData.tipe == null || this.diagData.tipe == '' || this.diagData.diagnosa == null || this.diagData.diagnosa == '' || this.diagData.nama_icd == null || this.diagData.nama_icd == '' ) {
                alert('data ICD / diagnosa masih kosong atau belum lengkap');
                return false;
            }
            else {
                let val = JSON.parse(JSON.stringify(this.diagData));
                this.asesmenData.diagnosa.push(val);
                this.diagData.soap_diag_id = null;
                this.diagData.tipe = null;
                this.diagData.kode_icd = null;
                this.diagData.nama_icd = null;
                this.diagData.diagnosa = null;
                this.diagData.kasus_lama = null;
                this.diagData.is_aktif = true;
            }
        },

        removeDiagnosa(data){
            data.is_aktif = false;
            //console.log(this.asesmenData.diagnosa);
        }
    }
}
</script>