<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="modalGantiPenjamin" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close @click.prevent=""></button>
            <div class="hims-form-container" style="padding:0; ">
                <form action="" @submit.prevent="submitGantiPenjamin">                        
                    <h3 class="uk-text-uppercase" style="font-weight:bold;">Ganti Penjamin</h3>
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-1-1">
                            <h5 style="font-weight: 500;padding:0;margin:0;">{{gantiPenjamin.no_rm}} - {{gantiPenjamin.nama_pasien}}</h5>
                            <p style="font-size: 11px;padding:0;margin:0;">{{gantiPenjamin.trx_id}}</p>
                            
                        </div>
                        <div class="uk-width-1-3@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>Unit </dt>
                                <dd>{{gantiPenjamin.unit_nama}}</dd>
                            </dl>                        
                        </div>
                        <div class="uk-width-1-3@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>Ruang </dt>
                                <dd>{{gantiPenjamin.ruang_nama}}</dd>
                            </dl>                        
                        </div>
                        <div class="uk-width-1-3@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>Bed</dt>
                                <dd> {{gantiPenjamin.no_bed}}</dd>
                            </dl>                        
                        </div>
                        <div class="uk-width-1-1" style="border-top:1px solid #ccc;margin-top: 1em;margin-bottom: 0.5em;"></div>
                        <div class="uk-width-1-1@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>Penjamin Sekarang </dt>
                                <dd>{{gantiPenjamin.penjamin_asal}}</dd>
                            </dl>                        
                        </div>
                        <div class="uk-width-1-3@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>No Kepesertaan </dt>
                                <dd>{{gantiPenjamin.no_kepesertaan_asal}}</dd>
                            </dl>                        
                        </div>
                        <div class="uk-width-1-3@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>Kelas Penjamin</dt>
                                <dd> {{gantiPenjamin.kls_asal_nama}}</dd>
                            </dl>                        
                        </div>
                        
                        <div class="uk-width-1-1" style="border-top:1px solid #ccc;margin-top: 1em;margin-bottom: 0.5em;"></div>
                        <!-- <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Penjamin Sekarang*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="gantiPenjamin.penjamin_asal" type="text" disabled style="color:black;">
                            </div>
                        </div> -->

                        <div class="uk-width-1-1@m">
                            <search-select
                                :config = configSelect
                                searchUrl = "/guarantors"
                                label = "Penjamin pengganti*"
                                placeholder = "pilih penjamin"
                                captionField = "penjamin_nama"
                                valueField = "penjamin_nama"
                                :itemListData = penjaminDesc
                                :value = "gantiPenjamin.penjamin_nama"
                                v-on:item-select = "penjaminSelected"
                            ></search-select>
                        </div>

                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No Kepesertaan*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" type="text" v-model="gantiPenjamin.no_kepesertaan" style="color:black;">
                            </div>
                        </div>

                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kelas Penjamin*</label>
                            <div class="uk-form-controls">
                                <select v-model="gantiPenjamin.kelas_penjamin" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                    <option v-for="option in kelasHargaLists" v-bind:value="option.kelas_id">{{option.kelas_nama}}</option>
                                </select>
                            </div>
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
import SearchSelect from '@/Components/SearchSelect.vue';

export default {
    name : 'modal-ganti-penjamin',
    components : { SearchList, SearchSelect},
    data() {
        return {
            penjaminDesc : [
                { colName : 'penjamin_nama', labelData : '', isTitle : true },
                { colName : 'penjamin_id', labelData : '', isTitle : false },
            ],
            configSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            gantiPenjamin : {
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

                penjamin_id : null,
                penjamin_nama : null,  
                no_kepesertaan : null,
                kelas_penjamin : null,
            
                penjamin_asal_id : null,
                penjamin_asal : null,     
                no_kepesertaan_asal : null,
                kelas_penjamin_asal : null,
                kls_asal_nama : null,
                
            },
            
            kelasHargaLists : [],
        }
    },
    computed : {
        ...mapGetters('kelas',['classLists']),        
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapActions('rawatInap',['dataAdmisiInap','updatePenjaminInap']),
        ...mapActions('kelas',['listKelas']),
        ...mapMutations(['CLR_ERRORS']),

        modalGantiPenjaminClosed(){
            this.clearPenjamin();
            this.$emit('modalGantiPenjaminClosed',true);
            UIkit.modal('#modalGantiPenjamin').hide();
        },

        showModal() { UIkit.modal('#modalGantiPenjamin').show(); },

        initialize(){
            this.CLR_ERRORS();     
            this.listKelas({per_page:'ALL'}).then((response)=>{
                if (response.success == true) {
                    this.kelasHargaLists = response.data.data.filter(function (el) { return el.is_kelas_harga == true; });
                } 
                else { alert(response.message); }
            })  
        },

        editPenjamin(data){
            if(data){
                this.CLR_ERRORS();
                this.initialize();
                this.clearPenjamin();
                this.dataAdmisiInap(data.trx_id).then((response) => {
                    if (response.success == true) {
                        this.fillPenjamin(response.data);
                        this.showModal();
                    }
                    else { alert (response.message) }
                });
            }

        },

        clearPenjamin(){
            this.gantiPenjamin.trx_id = null;
            this.gantiPenjamin.reg_id = null;
            this.gantiPenjamin.pasien_id = null;
            this.gantiPenjamin.no_rm = null;
            this.gantiPenjamin.nama_pasien = null;
            this.gantiPenjamin.unit_id = null;
            this.gantiPenjamin.unit_nama = null;
            this.gantiPenjamin.ruang_id = null;
            this.gantiPenjamin.ruang_nama = null;
            this.gantiPenjamin.bed_id = null;
            this.gantiPenjamin.no_bed =null;
            this.gantiPenjamin.penjamin_id = null;
            this.gantiPenjamin.penjamin_nama = null;  
            this.gantiPenjamin.penjamin_asal_id = null;
            this.gantiPenjamin.penjamin_asal = null;     
            this.gantiPenjamin.no_kepesertaan = null;
            this.gantiPenjamin.kelas_penjamin = null;   
            this.gantiPenjamin.kls_asal_nama = null;
        },

        fillPenjamin(data){
            //this.retrieveDokterUnit(data.unit_id);
            this.gantiPenjamin.trx_id = data.trx_id;
            this.gantiPenjamin.reg_id = data.reg_id;
            this.gantiPenjamin.pasien_id = data.pasien_id;
            this.gantiPenjamin.no_rm = data.no_rm;
            this.gantiPenjamin.nama_pasien = data.nama_pasien;
            this.gantiPenjamin.unit_id = data.unit_id;
            this.gantiPenjamin.unit_nama = data.unit_nama;
            this.gantiPenjamin.ruang_id = data.ruang_id;
            this.gantiPenjamin.ruang_nama = data.ruang_nama;
            this.gantiPenjamin.bed_id = data.bed_id;
            this.gantiPenjamin.no_bed = data.no_bed;
            
            this.gantiPenjamin.penjamin_asal_id = data.penjamin_id;
            this.gantiPenjamin.penjamin_asal = data.penjamin_nama;
            this.gantiPenjamin.no_kepesertaan_asal = data.no_kepesertaan;
            this.gantiPenjamin.kelas_penjamin_asal = data.kelas_penjamin;                
        
            this.gantiPenjamin.penjamin_id = null;
            this.gantiPenjamin.penjamin_nama = null;
            this.gantiPenjamin.no_kepesertaan = null;
            this.gantiPenjamin.kelas_penjamin = null;  
            
            let valKelas = this.kelasHargaLists.find(item => item.kelas_id == data.kelas_penjamin);
            if(valKelas) { this.gantiPenjamin.kls_asal_nama = valKelas.kelas_nama; }
        },

        // retrieveDokterUnit(unitId) {
        //     this.CLR_ERRORS();
        //     this.dataUnitPelayanan(unitId).then((response) => {
        //         if (response.success == true) {
        //             this.dokterUnitLists = JSON.parse(JSON.stringify(response.data.dokter));
        //         }
        //         else { alert (response.message) }
        //     });
        // },

        penjaminSelected(data) {
            if(data) {
                if(data.penjamin_id == this.gantiPenjamin.penjamin_asal_id) {
                    alert('Penjamin pengganti masih sama dengan penjamin sebelumnya.');
                    this.gantiPenjamin.penjamin_nama = null;
                    this.gantiPenjamin.penjamin_id = null;
                    return false;
                }
                this.gantiPenjamin.penjamin_id = data.penjamin_id;
                this.gantiPenjamin.penjamin_nama = data.penjamin_nama;
            }
        },

        submitGantiPenjamin(){
            if(this.gantiPenjamin.penjamin_id == this.gantiPenjamin.penjamin_asal_id) {
                alert('Penjamin pengganti tidak boleh sama dengan penjamin sebelumnya.');
                return false;
            }
            else {
                if(confirm(`Proses ini akan mengubah data Penjamin dan menyesuaikan harga. Lanjutkan ?`)){
                    this.updatePenjaminInap(this.gantiPenjamin).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.modalGantiPenjaminClosed();
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