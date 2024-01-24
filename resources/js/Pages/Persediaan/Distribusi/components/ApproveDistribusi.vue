<template>
    <div class="uk-container hims-form-container" style="min-height:50vh;">        
        <div class="uk-grid uk-grid-small" style="padding:0 1em 0.5em 1em;">
            <div class="uk-width-auto" style="padding:0;">
                <a href="#" @click.prevent="closeModal" uk-icon="icon:arrow-left;ratio:1.5" style="display:block;width:30px;"></a>
            </div>
            <div class="uk-width-expand"  style="padding:0;margin:0;">
                <ul class="uk-breadcrumb" style="padding:0;margin:0;">
                    <li>
                        <a href="#" @click.prevent="closeModal" class="uk-text-uppercase">
                            <h4 style="font-weight:500;margin:0;padding:0;display: inline-block;" class="uk-text-uppercase">PERSETUJUAN DISTRIBUSI</h4>
                        </a> 
                    </li>
                </ul>
            </div>
        </div>        
        <div class="uk-container" style="background-color:#fff;padding:1em;margin-top:1em;">
            <form action="" @submit.prevent="persetujuanDistribusi">
                <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid>
                    <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                        <h5 class="uk-text-uppercase">DATA DISTRIBUSI</h5>
                    </div>                                
                    <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                        <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;border:none;">Tutup</button>                  
                    </div>
                    <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                        <button type="submit" class="uk-button hims-button-primary uk-button-default uk-button-medium uk-box-shadow-large" style="font-size:12px;">SETUJUI DAN KIRIM</button>                  
                    </div>
                </div>
                <div> 
                    <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                        <div uk-spinner="ratio : 2"></div>
                        <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil data...</span>
                    </div>                         
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>              
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA UTAMA</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                informasi utama distribusi persediaan.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid>
                            <div class="uk-width-1-1@m uk-grid-small" uk-grid>
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Asal Permintaan**</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="distribusi.depo_penerima" type="text" disabled style="color:black;">
                                    </div>
                                </div>
                                <div v-if="isUpdate" class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tujuan Permintaan*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="distribusi.depo_pengirim" type="text" disabled style="color:black;">
                                    </div>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Dibutuhkan</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="distribusi.tgl_dibutuhkan" type="date" disabled style="color:black;">
                                    </div>
                                </div>
                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan*</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" v-model="distribusi.catatan" type="text" disabled style="color:black;">{{distribusi.catatan}}</textarea>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
            </form>
            <div class="uk-grid-small hims-form-subpage" uk-grid>
                <div class="uk-width-1-4@m" style="padding:0 0.5em 0.2em 0.5em;">
                    <h5 style="color:indigo;padding:0;margin:0;">ITEM DISTRIBUSI</h5>
                    <p style="font-size:12px;font-style:italic;padding:0;margin:0;">item distribusi persediaan.</p>
                </div>                    
                <div class="uk-width-3-4@m uk-grid-small" uk-grid style="padding:0 0.5em 0.2em 0.5em;">     
                    <div class="uk-width-1-1">
                        <table class="uk-table hims-table uk-table-responsive uk-box-shadow-small">
                            <thead>
                                <tr>
                                    <th>ITEM</th>
                                    <th style="width:80px;text-align:center;">DIMINTA</th>
                                    <th style="width:80px;text-align:center;">DIKIRIM</th>
                                    <th style="text-align:center;">SATUAN</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr v-for="itm in distribusi.items" style="border-top:1px solid #eee;" :class=" !itm.is_aktif ? 'data-inactive' : ''">
                                    <td style="padding:0.5em;font-weight:500;">
                                        {{itm.produk_nama}}
                                        <p style="font-weight: 200;font-size:10px; padding:0;margin:0; font-style: italic;">{{itm.produk_id}}</p>
                                    </td>
                                    <td style="width:80px;text-align:center;">
                                        {{itm.jml_diminta}}
                                    </td>
                                    <td style="width:80px;text-align:center;padding:0.5em 0.2em 0.5em 0.2em;">
                                        <input class="uk-input uk-form-small" v-model="itm.jml_dikirim" type="number" min="0" :max="itm.jml_diminta" style="text-align:center;">
                                    </td>
                                    <td style="width:100px;text-align:center;">{{itm.satuan}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';

export default {
    name : 'approve-distribusi', 
    data() {
        return {
            isLoading : true,
            isUpdate : true,
            distribusi : {
                client_id : null,
                distribusi_id : null,
                depo_penerima_id : null,
                depo_pengirim_id : null,
                depo_penerima : null,
                depo_pengirim : null,
                
                tgl_dibuat : null,
                tgl_dibutuhkan : null,
                tgl_realisasi : null,
                
                status : null,
                catatan : null,
                is_aktif : true,
                items : [],
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),    
    },

    mounted() {
    },
    
    methods : {
        ...mapActions('distribusi',['dataDistribusi','approveDistribusi']),
        ...mapMutations(['CLR_ERRORS']),


        closeModal(){
            this.clearDistribusi();
            this.$emit('closed',true);
        },

        persetujuanDistribusi(){            
            this.CLR_ERRORS();
            this.approveDistribusi(this.distribusi).then((response) => {
                if (response.success == true) {
                    this.fillDistribusi(response.data);
                    alert("Data produksi berhasil disetujui.");
                    this.$emit('saveSucceed',true);
                    this.isUpdate = true;
                    this.closeModal();
                }
            })
        },

        refreshData(data){
            this.clearDistribusi();
            this.isLoading = true;
            this.dataDistribusi(data.distribusi_id).then((response)=>{
                if (response.success == true) {
                    this.fillDistribusi(response.data);
                    this.isUpdate = true;
                } 
                else { alert(response.message); }
                this.isLoading = false;
            })
        },   

        clearDistribusi(){
            this.distribusi.client_id = null;
            this.distribusi.distribusi_id = null;
            this.distribusi.depo_penerima_id = null;
            this.distribusi.depo_pengirim_id = null;
            this.distribusi.depo_penerima = null;
            this.distribusi.depo_pengirim = null;
                
            this.distribusi.tgl_dibuat = null;
            this.distribusi.tgl_dibutuhkan = null;
            this.distribusi.tgl_realisasi = null;
                
            this.distribusi.status = null;
            this.distribusi.catatan = null;
            this.distribusi.is_aktif = true;
            this.distribusi.items = [];
        },

        fillDistribusi(data){
            this.distribusi.client_id = null;
            this.distribusi.distribusi_id = data.distribusi_id;
            this.distribusi.depo_penerima_id = data.depo_penerima_id;
            this.distribusi.depo_pengirim_id = data.depo_pengirim_id;
            this.distribusi.depo_penerima = data.depo_penerima;
            this.distribusi.depo_pengirim = data.depo_pengirim;
                
            this.distribusi.tgl_dibuat = data.tgl_dibuat;
            this.distribusi.tgl_dibutuhkan = data.tgl_dibutuhkan;
            this.distribusi.tgl_realisasi = data.tgl_realisasi;
                
            this.distribusi.status = data.status;
            this.distribusi.catatan = data.catatan;
            this.distribusi.is_aktif = data.is_aktif;
            this.distribusi.items = data.items;
        },
    }
}
</script>

<style>
/* tr.data-inactive td {
    text-decoration: line-through;
    font-style: italic;
}
.uk-input, .uk-textarea, .uk-checkbox, .uk-select {
    border: 1px solid #999;
    color: black;
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