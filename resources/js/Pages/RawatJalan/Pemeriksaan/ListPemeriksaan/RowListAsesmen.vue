<template>
    <tr v-if="rowData" style="border-bottom:1px solid #ccc;"  v-bind:class="rowData.is_aktif != true ? 'inaktif':'' ">   
        <td style="background-color:whitesmoke;border:none;width:20px;padding:0.5em;">
            <a v-if="isExpandedReg" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowRegExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowRegExpand"></a>
        </td>      
        <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 500;">{{rowData.soap_id}}</p>
        </td>
        <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 400;">{{rowData.tgl_soap}}</p>
        </td>
        
        <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
            <p class="uk-text-uppercase" style="margin:0;padding:0;font-weight: 400;">{{rowData.dokter_nama}}</p>
        </td>
        <td style="width:80px;padding:1em; font-size: 12px; text-align:center; color:black;">{{rowData.status}}</td>
        <td style="font-size: 12px; padding:1em; text-align:center; color:black;">
            <button @click.prevent="linkCallback(rowData)" style="margin-right:5px;">Ubah</button>
            <button @click.prevent="hapusPemeriksaan(rowData)">Hapus</button>
        </td>
    </tr>
    <tr>
        <Transition>
            <td colspan="7" v-if="isExpandedReg" style="background-color:#fff;border-top:0px solid #000;padding:0;margin:0;">
                <div style="padding:0.5em;background-color:white;">
                    <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                        <thead>
                            <th class="tb-header-reg-sub" style="text-align:left;color:black;">JENIS</th>
                            <th class="tb-header-reg-sub" colspan="3" style="text-align:left;color:black;">DATA</th>
                        </thead>
                        <tbody>
                            <tr style="border-bottom:1px solid #ccc;" v-bind:class="rowData.is_aktif != true ? 'inaktif':'' ">   
                                <td style="width:120px; padding:1em; font-weight: 500; font-size: 12px; color:black;">PEMERIKSAAN FISIK</td>
                                     
                                <td style="min-width:180px; padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                    <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 500;"><strong>Suhu : </strong>{{rowData.suhu}} Celcius</p>
                                    <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 500;"><strong>Saturasi : </strong>{{rowData.saturasi_oksigen}}%</p>
                                </td>
                                <td style="min-width:180px; padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                    <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 500;"><strong>Sistole : </strong>{{rowData.sistole}} mmHg</p>
                                    <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 500;"><strong>Diastole : </strong>{{rowData.diastole}} mmHg</p>
                                </td>
                                <td style="min-width:180px; padding:1em; font-weight: 500; font-size: 12px; color:black;">
                                    <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 500;"><strong>Denyut Nadi : </strong>{{rowData.denyut_nadi}} /menit</p>
                                    <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 500;"><strong>Pernapasan : </strong>{{rowData.pernapasan}} /menit</p>                                
                                </td>
                            </tr>
                            <tr>
                                <td style="width:120px; padding:1em; font-weight: 500; font-size: 12px; color:black;">SOAP</td>
                                <td colspan="3" style="padding:1em; font-weight: 500; font-size: 12px; color:black;max-width:500px;">
                                    <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 400;"><strong>Subjective : </strong>{{rowData.subjective}}</p>
                                    <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 400;"><strong>Objective : </strong>{{rowData.objective}}</p>
                                    <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 400;"><strong>Assesment : </strong>{{rowData.assesment}}</p>
                                    <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 400;"><strong>Plan : </strong>{{rowData.plan}}</p>                                    
                                </td>
                            </tr>
                            <tr>
                                <td style="width:120px; padding:1em; font-weight: 500; font-size: 12px; color:black;">CATATAN</td>
                                <td colspan="3" style="min-width:200px;padding:1em; font-size: 12px; color:black;">
                                    <p class="uk-text-uppercase1" style="margin:0;padding:0;font-weight: 400;">{{rowData.catatan}}</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:120px; padding:1em; font-weight: 500; font-size: 12px; color:black;">DIAGNOSA</td>
                                <td colspan="3" style="padding:1em; font-size: 12px; color:black;">
                                    <template v-for="diag in rowData.diagnosa">
                                        <p style="margin:0;padding:0;color:black;">
                                            <strong>{{ diag.tipe }}</strong> 
                                            <span v-if="diag.kasus_lama" class="uk-label" style="font-size:10px;margin-left:5px;">Kasus Lama</span>
                                            <span v-else class="uk-label uk-label-warning" style="font-size:10px;margin-left:5px;">Kasus Baru</span>
                                        </p>
                                        <p style="margin:0;padding:0;color:black;"><strong style="margin-right:5px;">ICD : {{ diag.kode_icd }} -</strong> {{ diag.nama_icd }}</p>
                                        <p style="margin:0;padding:0;color:black;"><strong style="margin-right:5px;">Diagnosa :</strong> {{ diag.diagnosa }}</p>
                                    </template>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </Transition>
    </tr>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
export default {
    props : {
        rowData : { type:Object, required:false, default:null },
        linkCallback : { type:Function, required:false, default:null }
    },
    components : {},
    computed : {
        ...mapGetters('rawatJalan',['poliTransaction','poliExamination']),
        ...mapGetters('pemeriksaan',['examination']),
    },
    name : 'row-list-asesmen',
    data() {
        return {
            isExpandedReg : false,
            selectAll : true,
            buffer : [],
        }
    },
    methods : {        
        //...mapActions('resep',['deletePrescription']),
        //...mapActions('pemeriksaan',['dataPemeriksaan','deletePemeriksaan']),
        
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('rawatJalan',['dataTransaksiPoli']),
        ...mapActions('asesmen',['deleteAsesmen']),        

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        changeRowRegExpand() {
            this.isExpandedReg = !this.isExpandedReg;
            return false;
        },

        hapusPemeriksaan(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus data SOAP. Lanjutkan ?`)){
                this.deleteAsesmen(data.soap_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.retrieveTransaksiPoli();
                    }
                    else { alert (response.message) }
                });
            };
        },

        retrieveTransaksiPoli(){
            let trans = JSON.parse(JSON.stringify(this.poliTransaction));
            console.log(trans.trx_id);
            this.dataTransaksiPoli(trans.trx_id);
        }
    }
}
</script>
<style>
th.tb-header-reg-sub {
    padding:0.5em; 
    background-color:#fff; 
    color:black;
    font-weight: 500;
    font-size:14px;
}
</style>