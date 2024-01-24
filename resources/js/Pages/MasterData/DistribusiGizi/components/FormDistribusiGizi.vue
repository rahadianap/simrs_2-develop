<template>
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="formDistribusiGizi" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitDistribusiGizi" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA DISTRIBUSI GIZI</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>                
                        
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA DISTRIBUSI GIZI</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                data utama distribusi gizi.
                            </p>
                        </div>
                        <div class="uk-width-3-4@s uk-grid-small" uk-grid>              
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Permintaan</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="gizi.tgl_permintaan" type="date" placeholder="tanggal permintaan" required :disabled="isUpdate">
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Dibutuhkan</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="gizi.tgl_dibutuhkan" type="date" placeholder="tanggal dibutuhkan" required>
                                </div>
                            </div>
                            <div class="uk-width-1-1@m">
                                <search-select
                                    :config = configSelect
                                    searchUrl = "/units"
                                    label = "Unit"
                                    placeholder = "Poli Gigi, Poli Mata, etc.."
                                    captionField = "unit_id"
                                    valueField = "unit_id"
                                    :itemListData = unitDesc
                                    :value = "gizi.unit_id"
                                    v-on:item-select = "unitSelected"
                                ></search-select>
                            </div>
                            <div class="uk-width-1-1@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                <div class="uk-form-controls">
                                    <textarea class="uk-textarea" v-model="gizi.catatan" type="text" required>{{gizi.catatan}}</textarea>
                                </div>
                            </div> 
                            <div v-if="isUpdate" class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                                <label style="padding:0;margin:0;font-size:14px;color:black;"><input class="uk-checkbox" type="checkbox" v-model="gizi.is_aktif" style="margin-left:5px;"> Data gizi aktif</label>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-1@s" style="padding:0 0.5em 0 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DETAIL DISTRIBUSI GIZI</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                data detail distribusi gizi.
                            </p>
                        </div>
                        <div class="uk-width-1-1@s" style="padding:0 0.5em 0 0.5em;">
                            <table class="uk-table uk-table-striped">
                                <thead>
                                    <th style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:80px;">Transaksi ID</th>
                                    <th style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:80px;">Registrasi ID</th>                            
                                    <th style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:80px;">Nama Pasien</th>
                                    <!-- <th style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:80px;text-align:center;">...</th> -->
                                </thead>
                                <tbody>    
                                    <tr>                            
                                        <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:100px;">
                                            <div class="uk-form-controls">
                                                <search-select
                                                    :config = configSelect
                                                    searchUrl = "/units"
                                                    placeholder = "TRX2022-0001"
                                                    captionField = "unit_id"
                                                    valueField = "unit_id"
                                                    :itemListData = unitDesc
                                                    :value = "gizi.unit_id"
                                                    v-on:item-select = "unitSelected"
                                                ></search-select>
                                            </div>                        
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:11px;">
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="text" v-model="detailGizi.reg_id">
                                            </div>                        
                                        </td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:11px;max-width:100px;">
                                            <div class="uk-form-controls">
                                                <input class="uk-input uk-form-small" type="text" v-model="detailGizi.nama_pasien">
                                            </div>                        
                                        </td>
                                        <!-- <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;">
                                            <a href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="addDataAlergi" style="padding:0;margin:0;color:blue;display:inline-block;"></a>
                                        </td> -->
                                    </tr>

                                    <!-- <tr v-for="dt in gizi.detailGizi" v-bind:class="dt.is_aktif != true ? 'inaktif':'' ">
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:80px;font-weight:500;">{{dt.jns_alergi}}</td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;">{{dt.catatan_alergi}}</td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;">{{dt.tgl_kejadian_awal}}</td>
                                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;text-align:center;">
                                            <input class="uk-checkbox" type="checkbox" v-model="dt.is_aktif">
                                        </td>   
                                    </tr> -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>                          
            </div>
        </div>
    </div> 
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';

export default {
    name : 'form-distribusi-gizi', 
    components : { SearchSelect },
    data() {
        return {
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            unitDesc : [
                { colName : 'unit_nama', labelData : '', isTitle : true },
                { colName : 'unit_id', labelData : '', isTitle : false },
            ],

            isUpdate : true,
            gizi : {
                client_id:null,
                dist_gizi_id:null,
                status : 'DRAFT',
                tgl_permintaan: null,
                tgl_dibutuhkan: null,
                unit_id: null,
                catatan:null,
                is_aktif : true,
            },
            detailGizi : {
                trx_id : '',
                reg_id : '',
                nama_pasien : ''
            }
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('unitPelayanan',['unitLists']),
        
    },

    mounted() {
        this.init();
    },
    
    methods : {
        ...mapActions('distribusiGizi',['createDGizi','dataDGizi','updateDGizi']),
        ...mapActions('unitPelayanan',['listUnitPelayanan']),
        ...mapMutations(['CLR_ERRORS']),

        init() {
            this.listUnitPelayanan();
        },

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formDistribusiGizi').hide();
            return false;
        },

        unitSelected(data){
            if(data) {
                this.gizi.unit_id = data.unit_id;                
            }
        },

        editDistribusiGizi(id){
            this.clearGizi();            
            this.dataDGizi(id).then((response)=>{
                if (response.success == true) {
                    this.fillGizi(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formDistribusiGizi').show();
                } else {
                    alert(response.message);
                }
            })
        },

        submitDistribusiGizi(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createDGizi(this.gizi).then((response) => {
                    if (response.success == true) {
                        alert(" data distribusi gizi baru berhasil dibuat.");
                        this.clearGizi();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        UIkit.modal('#formDistribusiGizi').hide();
                    }
                })
            }   
            else {
                this.updateDGizi(this.gizi).then((response) => {
                    if (response.success == true) {
                        alert(" data distribusi gizi berhasil diubah.");
                        this.clearGizi();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModal();
                        UIkit.modal('#formDistribusiGizi').hide();
                    }
                })
            }         
        },

        newDistribusiGizi(){
            this.clearGizi();
            this.isUpdate = false;
            UIkit.modal('#formDistribusiGizi').show();
        },  

        clearGizi(){
            this.gizi.client_id = null;
            this.gizi.dist_gizi_id = null;
            this.gizi.tgl_permintaan = null;
            this.gizi.tgl_dibutuhkan = null;
            this.gizi.unit_id = null;
            this.gizi.status = null;
            this.gizi.catatan = null;
            this.gizi.is_aktif = true;
        },

        fillGizi(data){
            this.gizi.client_id = null;
            this.gizi.dist_gizi_id = data.dist_gizi_id;
            this.gizi.tgl_permintaan = data.tgl_permintaan;
            this.gizi.tgl_dibutuhkan = data.tgl_dibutuhkan;
            this.gizi.unit_id = data.unit_id;
            this.gizi.status = data.status;
            this.gizi.catatan = data.catatan;
            this.gizi.is_aktif = true;
        },
    }
}
</script>

<style>
/* .uk-input, .uk-textarea, .uk-checkbox {
    border: 1px solid #999;
}

.hims-form-container label {
    color:#333;
    font-size:12x;
}

.hims-button-primary {
    background-color: #cc0202;
    color: #fafafa;
    border:2px solid #cc0202;
}

.uk-button {
    border-radius:50px;
    border:2px solid #aaa;
    font-weight:400;
}

.hims-button-primary {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}

.hims-button-primary:hover {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}

.hims-accordion-title {
    border-bottom:1px solid #cc0202; 
    font-size:14px; 
    font-weight:500; 
    background-color:#cc0202; 
    color:white; 
    padding:0.5em 1em 0.5em 1em;
    border-radius:5px;
}

.hims-accordion-title:hover {
    color:white; 
}
.hims-accordion-title::before {
    color:white;
}

.hims-form-header {
    padding:0.5em 0 0 0; 
    margin:1em 0 0 0;
    position:sticky;
    top:0;
    background-color:white;
    z-index:99;
    color:#cc0202;
}

.hims-form-header h5 {
    color:#333;
    font-weight:500;
    font-size:18px;
}


.hims-form-subpage {
    padding:1em;
    margin:0 0.5em 0.5em 0.5em;
    border-top:1px solid #cc0202;
} */

</style>