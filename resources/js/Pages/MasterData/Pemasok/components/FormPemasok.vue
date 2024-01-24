<template>
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="formPemasok" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="uk-container hims-form-container" style="min-height:50vh; background-color:#fff;">
                <form action="" @submit.prevent="submitPemasok" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA PEMASOK DAN PABRIKAN</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div>                  
                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                            <p>{{ errors.invalid }}</p>
                        </div>              
                        <div class="uk-grid-small hims-form-subpage" uk-grid >
                            <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">DATA UTAMA</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                    informasi utama terkait data pemasok dan pabrikan.
                                </p>
                            </div>
                            <div class="uk-width-3-4@s uk-grid-small" uk-grid> 
                                <div class="uk-width-2-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="pemasok.vendor_nama" type="text" placeholder="nama pemasok" required>
                                    </div>
                                </div>
                                
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Inisial*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="pemasok.inisial" type="text" placeholder="inisial" required>
                                    </div>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No. Kontrak</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="pemasok.no_kontrak" type="text" placeholder="no perjanjian">
                                    </div>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl. Kontrak</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="pemasok.tgl_kontrak" type="date">
                                    </div>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl. Akhir Kontrak</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="pemasok.tgl_akhir_kontrak" type="date">
                                    </div>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">NPWP*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="pemasok.npwp" type="text" placeholder="nomor pajak" required>
                                    </div>
                                </div>
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Status*</label>
                                    <div class="uk-form-controls">
                                        <select v-model="pemasok.status" class="uk-select uk-form-small">
                                            <option v-for="option in status_pemasok" v-bind:value="option.id" v-bind:key="option.id">{{option.text}}</option>
                                        </select>
                                    </div>
                                </div>                                      

                                <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                                    <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="pemasok.is_aktif" style="margin-left:5px;"> Pemasok / Pabrikan aktif</label>
                                </div>
                            </div>
                        </div>

                        <div class="uk-grid-small hims-form-subpage" uk-grid>
                            <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                                <h5 style="color:indigo;padding:0;margin:0;">INFO TAMBAHAN</h5>
                                <p style="font-size:12px;font-style:italic;padding:0;margin:0;">informasi tambahan terkait pemasok</p>
                            </div>
                            <div class="uk-width-3-4@s uk-grid-small" uk-grid>
                                
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Narahubung</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="pemasok.narahubung" type="text" placeholder="contact person">
                                    </div>
                                </div>
                                
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No. Telepon</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="pemasok.telepon" type="text" placeholder="nomor telepon">
                                    </div>
                                </div>
                                
                                <div class="uk-width-1-3@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Email</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="pemasok.email" type="email" placeholder="alamat email">
                                    </div>
                                </div>                                

                                <div class="uk-width-1-1@m">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Alamat</label>
                                    <div class="uk-form-controls">
                                        <textarea class="uk-textarea uk-form-small" rows="2" v-model="pemasok.alamat" type="text" placeholder="alamat pemasok atau pabrikan"></textarea>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                </form>                          
            </div>
        </div>
    </div> 
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';

export default {
    name : 'form-pemasok', 
    data() {
        return {
            isUpdate : true,
            pemasok : {
                client_id:null,
                vendor_id:null,
                vendor_nama : null,
                inisial : null,
                alamat : null,
                npwp : null,
                narahubung : null,
                telepon : null,
                email : null,
                is_aktif : true,
                no_kontrak : null,
                tgl_kontrak : null,
                tgl_akhir_kontrak : null,
                status : null,
            },

            status_pemasok : [
                { id:'Terverifikasi', text:'Terverifikasi' },
                { id:'Tidak Terverifikasi', text:'Tidak Terverifikasi' },
                { id:'Tidak Tahu', text:'Tidak Tahu' },
            ]
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
    },
    
    methods : {
        ...mapActions('vendor',['createVendor','updateVendor','dataVendor']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formPemasok').hide();
            return false;
        },

        submitPemasok(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createVendor(this.pemasok).then((response) => {
                    if (response.success == true) {
                        alert("Pemasok / Pabrikan baru berhasil dibuat.");
                        this.fillPemasok(response.data);
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        this.closeModal();
                    }
                })
            }
            else {
                this.updateVendor(this.pemasok).then((response) => {
                    if (response.success == true) {
                        this.fillPemasok(response.data);
                        alert("Pemasok / Pabrikan berhasil diubah.");
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        this.closeModal();
                    }
                })
            }
        },

        newPemasok(){
            this.clearPemasok();
            this.isUpdate = false;
            UIkit.modal('#formPemasok').show();
        },

        editPemasok(vendorId){
            this.clearPemasok();            
            this.dataVendor(vendorId).then((response)=>{
                if (response.success == true) {
                    this.fillPemasok(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formPemasok').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        clearPemasok(){
            this.pemasok.client_id = null;
            this.pemasok.vendor_id = null;
            this.pemasok.vendor_nama = null;
            this.pemasok.inisial = null;
            this.pemasok.npwp = null;
            this.pemasok.narahubung = null;
            this.pemasok.telepon = null;
            this.pemasok.email = null;
            this.pemasok.alamat = null;
            this.pemasok.is_aktif = true;
            this.pemasok.status = null;
            this.pemasok.no_kontrak = null;
            this.pemasok.tgl_kontrak = null;
            this.pemasok.tgl_akhir_kontrak = null;
        },

        fillPemasok(data){
            this.pemasok.client_id = null;
            this.pemasok.vendor_id = data.vendor_id;
            this.pemasok.vendor_nama = data.vendor_nama;
            this.pemasok.inisial = data.inisial;
            this.pemasok.npwp = data.npwp;
            this.pemasok.narahubung = data.narahubung;
            this.pemasok.telepon = data.telepon;
            this.pemasok.email = data.email;
            this.pemasok.alamat = data.alamat;
            this.pemasok.status = data.status;
            this.pemasok.is_aktif = data.is_aktif;
            this.pemasok.no_kontrak = data.no_kontrak;
            this.pemasok.tgl_kontrak = data.tgl_kontrak;
            this.pemasok.tgl_akhir_kontrak = data.tgl_akhir_kontrak;
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