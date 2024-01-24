<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formKomponenHarga" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitKomponenHarga" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">KOMPONEN HARGA</h5>
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
                        
                    <div class="uk-width-1-1@s uk-grid-small" uk-grid style="padding:0.5em;">                                 
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Komponen Harga*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="komponen.komp_harga" type="text" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea" style="font-size:14px;" v-model="komponen.deskripsi" type="text">{{komponen.deskripsi}}</textarea>
                            </div>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="komponen.is_jasa_dokter" style="margin-left:5px;"> Komponen jasa dokter</label>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="komponen.is_hitung_pajak" style="margin-left:5px;"> Komponen dikenakan pajak</label>
                        </div>
                        <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                            <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="komponen.is_aktif" style="margin-left:5px;"> Komponen harga aktif</label>
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
    name : 'form-komponen-harga', 
    data() {
        return {
            isUpdate : true,
            komponen : {
                client_id:null,
                komp_harga_id : null,
                komp_harga : null,
                deskripsi : null,
                is_jasa_dokter : false,
                is_hitung_pajak : false,
                is_auto_hitung : false,
                jenis_pajak : null,
                is_aktif : true,
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
    },
    
    methods : {
        ...mapActions('komponenHarga',['createKomponenHarga','dataKomponenHarga','updateKomponenHarga']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#formKomponenHarga').hide();
        },

        submitKomponenHarga(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createKomponenHarga(this.komponen).then((response) => {
                    if (response.success == true) {
                        alert("Komponen harga baru berhasil dibuat.");
                        this.clearKomponenHarga();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }    
            else {
                this.updateKomponenHarga(this.komponen).then((response) => {
                    if (response.success == true) {
                        alert("Komponen harga berhasil diubah.");
                        this.clearKomponenHarga();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModal();
                    }
                })
            }         
        },

        newKomponenHarga(){
            this.clearKomponenHarga();
            this.isUpdate = false;
            UIkit.modal('#formKomponenHarga').show();
        },  

        editKomponenHarga(id){
            this.clearKomponenHarga();            
            this.dataKomponenHarga(id).then((response)=>{
                if (response.success == true) {
                    this.fillKomponenHarga(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formKomponenHarga').show();
                } else {
                    alert(response.message);
                }
            })
        },   

        clearKomponenHarga(){
            this.komponen.client_id = null;
            this.komponen.komp_harga_id = null;
            this.komponen.komp_harga = null;
            this.komponen.deskripsi = null;
            this.komponen.is_jasa_dokter = false;
            this.komponen.is_hitung_pajak = false;
            this.komponen.is_auto_hitung = false;
            this.komponen.jenis_pajak = null;
            this.komponen.is_aktif = true;
        },

        fillKomponenHarga(data){
            this.komponen.client_id = null;
            this.komponen.komp_harga_id = data.komp_harga_id;
            this.komponen.komp_harga = data.komp_harga;
            this.komponen.deskripsi = data.deskripsi;
            this.komponen.is_jasa_dokter = data.is_jasa_dokter;
            this.komponen.is_hitung_pajak = data.is_hitung_pajak;
            this.komponen.is_auto_hitung = data.is_auto_hitung;
            this.komponen.jenis_pajak = data.jenis_pajak;
            this.komponen.is_aktif = data.is_aktif;
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