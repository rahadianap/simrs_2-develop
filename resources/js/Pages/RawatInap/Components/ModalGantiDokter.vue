<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="modalGantiDokter" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close @click.prevent=""></button>
            <div class="hims-form-container" style="padding:0; ">
                <form action="" @submit.prevent="submitGantiDokter">                        
                    <h3 class="uk-text-uppercase" style="font-weight:bold;">Ganti Dokter Penanggung Jawab</h3>
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-1-1">
                            <h5 style="font-weight: 500;padding:0;margin:0;">{{gantiDokter.no_rm}} - {{gantiDokter.nama_pasien}}</h5>
                            <p style="font-size: 11px;padding:0;margin:0;">{{gantiDokter.trx_id}}</p>
                            
                        </div>
                        <div class="uk-width-1-3@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>Unit </dt>
                                <dd>{{gantiDokter.unit_nama}}</dd>
                            </dl>                        
                        </div>
                        <div class="uk-width-1-3@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>Ruang </dt>
                                <dd>{{gantiDokter.ruang_nama}}</dd>
                            </dl>                        
                        </div>
                        <div class="uk-width-1-3@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>Bed</dt>
                                <dd> {{gantiDokter.no_bed}}</dd>
                            </dl>                        
                        </div>
                        <div class="uk-width-1-1" style="border-top:1px solid #ccc;margin-top: 1em;margin-bottom: 0.5em;"></div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Dokter DPJP Sekarang*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="gantiDokter.dokter_asal" type="text" disabled style="color:black;">
                            </div>
                        </div>

                        <div class="uk-width-1-1@m">
                            <search-list
                                :config = configSelect
                                :dataLists = dokterUnitLists
                                label = "DPJP Pengganti*"
                                placeholder = "pilih dokter penanggung jawab"
                                captionField = "dokter_nama"
                                valueField = "dokter_nama"
                                :detailItems = dokterDesc
                                :value = "gantiDokter.dokter_nama"
                                v-on:item-select = "dpjpSelected"
                            ></search-list>
                        </div>
                        <div class="uk-width-1-1" style="text-align:right;margin-top:1.5em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>
                        </div>
                    </div>                    
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'modal-ganti-dokter',
    components : { SearchList, },
    data() {
        return {
            dokterDesc : [
                { colName : 'dokter_nama', labelData : '', isTitle : true },
                { colName : 'dokter_id', labelData : '', isTitle : false },
            ],
            configSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            gantiDokter : {
                trx_id : null,
                reg_id : null,
                pasien_id : null,
                no_rm : null,
                nama_pasien : null,
                unit_id : null,
                unit_nama : null,
                ruang_id : null,
                ruang_nama : null,
                bed_id : null,
                no_bed :null,
                dokter_id : null,
                dokter_nama : null,  
                dokter_asal_id : null,
                dokter_asal : null,  
                status : null,
                is_pulang : false,             
            },
            
            dokterUnitLists : [],
        }
    },
    methods : {
        ...mapActions('rawatInap',['dataAdmisiInap','updateDokterInap']),
        ...mapActions('unitPelayanan',['dataUnitPelayanan']),
        
        ...mapMutations(['CLR_ERRORS']),

        modalGantiDokterClosed(){
            this.clearDokterPasien();
            this.$emit('modalGantiDokterClosed',true);
            UIkit.modal('#modalGantiDokter').hide();
        },

        showModal() { UIkit.modal('#modalGantiDokter').show(); },

        editDokterPasien(data){
            if(data){
                this.CLR_ERRORS();
                this.clearDokterPasien();
                this.dataAdmisiInap(data.trx_id).then((response) => {
                    if (response.success == true) {
                        this.fillDokterPasien(response.data);
                        this.showModal();
                    }
                    else { alert (response.message) }
                });
            }

        },

        clearDokterPasien(){
            this.gantiDokter.trx_id = null;
            this.gantiDokter.reg_id = null;
            this.gantiDokter.pasien_id = null;
            this.gantiDokter.no_rm = null;
            this.gantiDokter.nama_pasien = null;
            this.gantiDokter.unit_id = null;
            this.gantiDokter.unit_nama = null;
            this.gantiDokter.ruang_id = null;
            this.gantiDokter.ruang_nama = null;
            this.gantiDokter.bed_id = null;
            this.gantiDokter.no_bed =null;

            this.gantiDokter.dokter_asal_id = null;
            this.gantiDokter.dokter_asal = null;

            this.gantiDokter.dokter_id = null;
            this.gantiDokter.dokter_nama = null;
            this.gantiDokter.status = null;
            this.gantiDokter.is_pulang = false;            
        },

        fillDokterPasien(data){
            this.retrieveDokterUnit(data.unit_id);
            this.gantiDokter.trx_id = data.trx_id;
            this.gantiDokter.reg_id = data.reg_id;
            this.gantiDokter.pasien_id = data.pasien_id;            
            this.gantiDokter.no_rm = data.no_rm;

            this.gantiDokter.nama_pasien = data.nama_pasien;
            this.gantiDokter.unit_id = data.unit_id;
            this.gantiDokter.unit_nama = data.unit_nama;
            this.gantiDokter.ruang_id = data.ruang_id;
            this.gantiDokter.ruang_nama = data.ruang_nama;
            this.gantiDokter.bed_id = data.bed_id;
            this.gantiDokter.no_bed =data.no_bed;

            this.gantiDokter.dokter_asal_id = data.dokter_id;
            this.gantiDokter.dokter_asal = data.dokter_nama;

            this.gantiDokter.status = data.status;
            this.gantiDokter.is_pulang = data.is_pulang;           
        
            this.gantiDokter.dokter_id = null;
            this.gantiDokter.dokter_nama = null;
            
        },

        retrieveDokterUnit(unitId) {
            this.CLR_ERRORS();
            this.dataUnitPelayanan(unitId).then((response) => {
                if (response.success == true) {
                    this.dokterUnitLists = JSON.parse(JSON.stringify(response.data.dokter));
                }
                else { alert (response.message) }
            });
        },

        dpjpSelected(data) {
            if(data) {
                if(data.dokter_id == this.gantiDokter.dokter_asal_id) {
                    alert('dokter pengganti masih sama dengan dokter DPJP sebelumnya.');
                    this.gantiDokter.dokter_nama = null;
                    this.gantiDokter.dokter_id = null;
                    return false;
                }
                this.gantiDokter.dokter_id = data.dokter_id;
                this.gantiDokter.dokter_nama = data.dokter_nama;
            }
        },

        submitGantiDokter(){
            if(this.gantiDokter.dokter_id == this.gantiDokter.dokter_asal_id) {
                alert('dokter pengganti tidak boleh sama dengan DPJP sebelumnya.');
                return false;
            }
            else {
                if(confirm(`Proses ini akan mengubah data dokter DPJP. Lanjutkan ?`)){
                    this.updateDokterInap(this.gantiDokter).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.modalGantiDokterClosed();
                        }
                        else { 
                            alert (response.message); 
                        }
                    });
                };
            }
        }
    }
}
</script>