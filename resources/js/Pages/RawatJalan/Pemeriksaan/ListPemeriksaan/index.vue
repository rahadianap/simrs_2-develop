<template>
    <div style="padding:0;min-height:70vh;">
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-auto">
                <a href="#" @click.prevent="formClosed" style="background-color:white;padding:0.3em;display:inline-block;">
                    <span uk-icon="icon:chevron-left;ratio:1"></span>
                </a>
            </div>
            <div class="uk-width-expand">
                <h4 style="font-weight:500;margin:0.2em 0 0 0;padding:0;" class="uk-text-uppercase">PEMERIKSAAN PASIEN</h4>
            </div>
        </div>
        <div style="background-color:#fff;margin-top:1em;">
            <div class="" style="margin:0;padding:0;">   
                <div style="margin:0;padding:0;">
                    <form action="" @submit.prevent="submisiRegPoli" style="margin:0;padding:0;">
                        <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                            <div uk-spinner="ratio : 2"></div>
                            <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil/memproses data...</span>
                        </div>
                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                            <p>{{ errors.invalid }}</p>
                        </div>
                        <div v-if="poliTransaction.reg_id" class="uk-container uk-container-large">                    
                            <div class="hims-form-container" style="padding:1.5em 1em 0 1em; margin:0;"> 
                                <div class="uk-grid-small hims-form-subpage" uk-grid style="border:none;padding:0;background-color:#fff;">
                                    <div class="uk-width-1-1@m">
                                        <p style="font-size:12px;padding:0;margin:0;">ID.{{ poliTransaction.pasien_id }}</p>
                                        <h5 style="color:black;padding:0;margin:0;font-weight: 500;">{{ poliTransaction.nama_pasien }} ({{ poliTransaction.no_rm }})</h5>                                        
                                    </div>
                                    <div class="uk-width-1-3@m">
                                        <dl class="uk-description-list hims-description-list">
                                            <dt>Dokter</dt>
                                            <dd>{{poliTransaction.dokter_nama}}</dd>
                                        </dl>                                        
                                    </div>
                                    <div class="uk-width-1-3@m">                                
                                        <dl class="uk-description-list hims-description-list">
                                            <dt>Unit</dt>
                                            <dd>{{poliTransaction.unit_nama}}</dd>
                                        </dl>
                                    </div>
                                    <div class="uk-width-1-3@m">                                        
                                        <dl class="uk-description-list hims-description-list">
                                            <dt>No.Registrasi</dt>
                                            <dd>{{poliTransaction.trx_id}}</dd>
                                        </dl>
                                    </div>
                                    <div class="uk-width-1-1">
                                        <button type="button" @click.prevent="RefreshPemeriksaan">Refresh Pemeriksaan</button>
                                    </div>                        
                                </div> 
                            </div>
                            
                            <div style="padding:0.5em 1em 2em 1em; margin:0;">
                                <ul uk-tab>
                                    <li><a href="#">SOAP</a></li>
                                    <li><a href="#">Assesmen</a></li>
                                    <li><a href="#">Pemeriksaan & BHP</a></li>
                                    <li><a href="#">Resep</a></li>
                                    <li><a href="#">Penunjang</a></li>
                                </ul>
                                <ul class="uk-switcher uk-margin">
                                    <li>
                                        <div style="background-color:white;padding:0;margin:0;">
                                            <sub-list-soap
                                                :fnEditCallback="EditSoap" 
                                                :fnAddCallback="TambahSoap"
                                                :fnRefresh = "RefreshPemeriksaan">
                                            </sub-list-soap>
                                        </div>
                                    </li>
                                    <li>
                                        <div style="background-color:white;padding:0;margin:0;">
                                            <sub-list-inform
                                                :fnEditCallback="EditAsesmen" 
                                                :fnAddCallback="TambahAsesmen"
                                                :fnRefresh = "RefreshPemeriksaan">
                                            </sub-list-inform>
                                        </div>
                                    </li>
                                    <li>
                                        <div style="background-color:white;padding:0;margin:0;">
                                            <sub-list-pemeriksaan 
                                                :fnEditCallback="EditPemeriksaan" 
                                                :fnAddCallback="TambahPemeriksaan">
                                            </sub-list-pemeriksaan>
                                        </div>                                        
                                    </li>
                                    <li>
                                        <div style="background-color:white;padding:0;margin:0;">
                                            <sub-list-resep 
                                                :fnEditCallback="editDataResep" 
                                                :fnAddCallback="tambahResep"
                                                :fnRefresh = "RefreshPemeriksaan">
                                            </sub-list-resep>
                                        </div>
                                    </li>
                                    <li>
                                        <div style="padding:0 0.5em 0.2em 0.5em;">            
                                            <sub-list-laboratorium
                                                :trxId = this.trxId
                                                :data="poliLabLists" 
                                                :fnAddCallback="tambahPemeriksaanLab"
                                                v-on:refreshLab = "retrieveData">
                                            </sub-list-laboratorium>
                                            
                                            <sub-list-radiologi 
                                                :trxId = this.trxId
                                                :data="poliRadLists"
                                                :fnAddCallback = "tambahPemeriksaanRad"
                                                v-on:refreshRad = "retrieveData">
                                            </sub-list-radiologi>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>                        
                   </form>
                </div> 
            </div>
        </div>
        <modal-resep ref="modalResep" modalId="modalResepPoli"></modal-resep>
        <modal-pemeriksaan ref="modalPemeriksaan" modalId="modalPemeriksaanPoli"></modal-pemeriksaan>
        <modal-asesmen ref="modalAsesmen" modalId="modalAsesmen"></modal-asesmen>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SubFormResep from '@/Pages/RawatJalan/Pemeriksaan/SubFormResep';
import SubListPemeriksaan from '@/Pages/RawatJalan/Pemeriksaan/ListPemeriksaan/SubListPemeriksaan.vue';
import SubListResep from '@/Pages/RawatJalan/Pemeriksaan/ListPemeriksaan/SubListResep.vue';
import SubListSoap from '@/Pages/RawatJalan/Pemeriksaan/ListPemeriksaan/SubListSoap.vue';
import SubListInform from '@/Pages/RawatJalan/Pemeriksaan/ListPemeriksaan/SubListInform.vue';

import SubListLaboratorium from '@/Pages/RawatJalan/Pemeriksaan/SubListLaboratorium/SubListLaboratorium.vue';
import SubListRadiologi from '@/Pages/RawatJalan/Pemeriksaan/SubListRadiologi/SubListRadiologi.vue';

import ModalResep from '@/Pages/RawatJalan/Pemeriksaan/components/ModalResep.vue';
import ModalPemeriksaan from '@/Pages/RawatJalan/Pemeriksaan/components/ModalPemeriksaan.vue';
import ModalAsesmen from '@/Pages/RawatJalan/Pemeriksaan/components/ModalAsesmen.vue';

export default {
    props : {
        trxId : { type:String, required:true },
    },
    name : 'list-pemeriksaan',
    components : { 
        SubFormResep, 
        SubListPemeriksaan,
        ModalResep,
        ModalPemeriksaan, 
        SubListResep, 
        SubListSoap,
        SubListInform,
         
        ModalAsesmen, 
        SubListLaboratorium,
        SubListRadiologi,
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatJalan',['poliTransactionLists','poliFilterTable','poliTransaction','poliLabLists','poliRadLists']),  
        ...mapGetters('informedConsent',['informedListUnit']),
    },

    data(){
        return {
            isLoading : false,
        }
    },
    
    mounted() {
        this.retrieveData();
    },
    
    methods : {        
        ...mapMutations(['CLR_ERRORS']),        
        ...mapMutations('rawatJalan',[
            'CLR_POLI_TRANSACTION',
            'SET_POLI_TRANSACTION',
            'SET_POLI_TRANSACTION_LISTS',
            'CLR_POLI_TRANSACTION_LISTS',
            'SET_FILTER_TABLE_POLI'
        ]),

        ...mapActions('rawatJalan',['dataTransaksiPoli','updateTransaksiPoli','createTransaksiPoli']),
        ...mapActions('pemeriksaan',['createPemeriksaan','updatePemeriksaan','dataPemeriksaan','confirmPemeriksaan','unconfirmPemeriksaan']),
        ...mapActions('pasien',['dataPasien']),
        ...mapActions('informedConsent',['listMapInformUnit']),


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
        /**modal call from other component for update data entry */
        // retrieveData(refData) {
        retrieveData() {
            this.CLR_ERRORS();
            this.isUpdate = true;
            this.isLoading = true;
            if(this.trxId) {
                this.dataTransaksiPoli(this.trxId).then((response) => {
                    if (response.success == true) {
                        this.SET_POLI_TRANSACTION(response.data);
                        this.isUpdate = true;
                        this.isLoading = false;
                        this.retrieveListInformConsent();
                    }
                    else { 
                        this.CLR_POLI_TRANSACTION();
                        alert(response.message); 
                        this.isLoading = false; 
                    }
                })
            }
        },


        retrieveListInformConsent() {
            console.log(this.poliTransaction.unit_id);
            this.listMapInformUnit(this.poliTransaction.unit_id);
        },

        TambahPemeriksaan(){
            //this.$refs.modalPemeriksaan.pemeriksaanBaru();
            this.$router.push({ name: 'dataTindakanPoli', params: { trxId: this.trxId, actId: 'baru' } });
        },

        EditPemeriksaan(data){
            if(data.status.toUpperCase == 'DRAFT') {
                this.$router.push({ name: 'dataTindakanPoli', params: { trxId: this.trxId, actId: data.trx_id } });
            }
            else {
                this.needUnconfirmData(data);
            }
        },
        

        needUnconfirmData(data) {  
            this.CLR_ERRORS();

            if(confirm(`Proses ini akan Mengubah status data pemeriksaan ${data.trx_id} kembali ke DRAFT. Lanjutkan ?`)){
                this.isLoading = true;
                this.unconfirmPemeriksaan(data.trx_id).then((response) => {
                    if (response.success == true) {
                        this.isLoading = false; 
                        this.isUpdate = false;
                        alert('konfirmasi pemeriksaan dibatalkan. Anda dapat mengubah kembali data pemeriksaan ini.');
                        this.$router.push({ name: 'dataTindakanPoli', params: { trxId: this.trxId, actId: data.trx_id } });
                    }
                    else { 
                        this.isLoading = false;
                        alert('pembatalan konfirmasi pemeriiksaan tidak berhasil.'); 
                    }
                })
            }
        },

        tambahResep(){
            //this.$refs.modalResep.resepBaru(this.poliTransaction);
            this.$router.push({ name: 'dataResepPoli', params: { trxId: this.trxId, resepId: 'baru' } });
        },

        editDataResep(data){
            console.log(data);
            this.$router.push({ name: 'dataResepPoli', params: { trxId: this.trxId, resepId: data.trx_id } });
        },

        formClosed(){
            this.$router.push({ name: 'listPasienPoli' });
        },

        EditSoap(data){
            if(data){ this.$refs.modalAsesmen.editAsesmen(data); }
        },
        
        TambahSoap(){
            this.$refs.modalAsesmen.addAsesmen(this.poliTransaction);
        },

        tambahPemeriksaanLab(){
            this.$router.push({ name: 'dataLabPoli', params: { trxId: this.trxId, labId: 'baru' } });
        },

        tambahPemeriksaanRad(){
            this.$router.push({ name: 'dataRadPoli', params: { trxId: this.trxId, radId: 'baru' } });
        },

        EditAsesmen(data){

        },

        TambahAsesmen(data){
            
        }
    }
}
</script>