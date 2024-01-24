<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formKelompokHarga" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitKelompokHarga" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">KELOMPOK HARGA</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModalBukuHarga" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
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
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kelompok Harga*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small uk-text-uppercase" v-model="bukuHarga.buku_nama" type="text" required :disabled="isUpdate">
                            </div>
                        </div>

                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Deskripsi*</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea uk-form-small" v-model="bukuHarga.deskripsi" type="text" required></textarea>
                            </div>
                        </div>      

                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Berlaku*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="bukuHarga.tgl_berlaku" type="date" required>
                            </div>
                        </div>

                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Berlaku*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="bukuHarga.jam_berlaku" type="time" required>
                            </div>
                        </div>

                        <div class="uk-width-1-1@m" style="margin:0;padding:1em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:14px;font-weight: 400;">
                                    <input class="uk-checkbox" type="checkbox" value="1" v-model="bukuHarga.is_standar_sistem" > Standar Sistem
                                </label>
                                <span style="font-size:12px; color:black; padding:0.2em 0.2em 0.2em 0.5em; margin:0; font-style: italic;">kelompok harga standar sistem.</span>                                        
                            </div>
                        </div>

                        <div class="uk-width-1-1@m" style="margin:0;padding:0.5em 0.2em 0.2em 1.2em;">
                            <div class="uk-form-controls">
                                <label style="color:black; font-size:14px;font-weight: 400;">
                                    <input class="uk-checkbox" type="checkbox" value="1" v-model="bukuHarga.is_aktif" disabled> Data Aktif
                                </label>
                                <span style="font-size:12px; color:black; padding:0.2em 0.2em 0.2em 0.5em; margin:0; font-style: italic;">kelompok harga aktif.</span>                                        
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
    name : 'modal-kelompok-harga', 
    data() {
        return {
            isUpdate : true,
            bukuHarga : {
                client_id : null,
                buku_harga_id : null,
                buku_nama : null,
                deskripsi : null,
                is_standar_sistem : false,
                tgl_berlaku : null,
                jam_berlaku : null,
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
        ...mapActions('bukuHarga',['createBukuHarga','dataBukuHarga','updateBukuHarga','deleteBukuHarga']),
        ...mapMutations(['CLR_ERRORS']),
        
        closeModalBukuHarga(){
            this.$emit('GroupTariffClosed',true);
            UIkit.modal('#formKelompokHarga').hide();
        },

        submitKelompokHarga(){
            this.CLR_ERRORS();

            if(!this.isUpdate) {
                this.createBukuHarga(this.bukuHarga).then((response) => {
                    if (response.success == true) {
                        alert("kelompok harga baru berhasil dibuat.");
                        this.clearBukuHarga();
                        this.isUpdate = false;
                        this.closeModalBukuHarga();
                    }
                })
            }    
            else {
                this.updateBukuHarga(this.bukuHarga).then((response) => {
                    if (response.success == true) {
                        alert("Kelompok harga berhasil diubah.");
                        this.clearBukuHarga();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModalBukuHarga();
                    }
                })
            }         
        },  

        newBukuHarga(){
            this.clearBukuHarga();
            this.isUpdate = false;
            UIkit.modal('#formKelompokHarga').show();
        },  

        editBukuHarga(id){
            this.clearBukuHarga();            
            this.dataBukuHarga(id).then((response)=>{
                if (response.success == true) {
                    this.fillBukuHarga(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formKelompokHarga').show();
                } else {
                    alert(response.message);
                }
            })
        },   

        clearBukuHarga(){
            this.bukuHarga.client_id = null;
            this.bukuHarga.buku_harga_id = null;
            this.bukuHarga.buku_nama = null;
            this.bukuHarga.deskripsi = null;
            this.bukuHarga.jam_berlaku = null;
            this.bukuHarga.tgl_berlaku = null;
            this.bukuHarga.is_standar_sistem = false;
            this.bukuHarga.is_aktif = true;
        },

        fillBukuHarga(data){
            this.bukuHarga.client_id = data.client_id;
            this.bukuHarga.buku_harga_id = data.buku_harga_id;
            this.bukuHarga.buku_nama = data.buku_nama;
            this.bukuHarga.deskripsi = data.deskripsi;
            this.bukuHarga.jam_berlaku = data.jam_berlaku;
            this.bukuHarga.tgl_berlaku = data.tgl_berlaku;
            this.bukuHarga.is_standar_sistem = data.is_standar_sistem;
            this.bukuHarga.is_aktif = data.is_aktif;
        },
    }
}
</script>

<style>
</style>