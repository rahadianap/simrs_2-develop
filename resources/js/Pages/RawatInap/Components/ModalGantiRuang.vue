<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="modalGantiRuang" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close @click.prevent=""></button>
            <div class="hims-form-container" style="padding:0; ">
                <form action="" @submit.prevent="submitRuang">                        
                    <h3 class="uk-text-uppercase" style="font-weight:bold;">Ganti Ruang Rawat</h3>
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-1-1">
                            <h5 style="font-weight: 500;padding:0;margin:0;">{{gantiRuang.no_rm}} - {{gantiRuang.nama_pasien}}</h5>
                            <p style="font-size: 11px;padding:0;margin:0;">{{gantiRuang.trx_id}}</p>
                            
                        </div>
                        <div class="uk-width-1-3@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>Unit </dt>
                                <dd>{{gantiRuang.unit_asal}}</dd>
                            </dl>                        
                        </div>
                        <div class="uk-width-1-3@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>Ruang </dt>
                                <dd>{{gantiRuang.ruang_asal}}</dd>
                            </dl>                        
                        </div>
                        <div class="uk-width-1-3@m">
                            <dl class="uk-description-list hims-description-list">
                                <dt>Bed</dt>
                                <dd> {{gantiRuang.no_asal_bed}}</dd>
                            </dl>                        
                        </div>
                        <div class="uk-width-1-1" style="border-top:1px solid #ccc;margin-top: 1em;margin-bottom: 0.5em;"></div>

                        <div class="uk-width-1-1@m">
                            <search-list
                                ref = "selectUnit"
                                :config = configSelect
                                :dataLists = unitLists
                                label = "Unit Tujuan*"
                                placeholder = "unit pelayanan"
                                captionField = "unit_nama"
                                valueField = "unit_nama"
                                :detailItems = unitDesc
                                :value = "gantiRuang.unit_nama"
                                v-on:item-select = "unitSelected"
                            ></search-list>
                        </div>

                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Ruang Tujuan*</label>
                            <div class="uk-form-controls">
                                <select v-model="gantiRuang.ruang_id" class="uk-select uk-form-small" @change="ruangSelected" required>
                                    <option v-if="roomLists" v-for="dt in roomLists" :value="dt.ruang_id">{{dt.ruang_nama}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Bed Tujuan*</label>
                            <div class="uk-form-controls">
                                <select v-model="gantiRuang.bed_id" class="uk-select uk-form-small" @change="bedSelected" required>
                                    <option v-if="bedLists" v-for="dt in bedLists" :value="dt.bed_id">{{dt.no_bed}} ({{dt.status}})</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Masuk*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" type="date" disabled v-model="gantiRuang.tgl_masuk" style="color:black;">
                            </div>
                        </div>

                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Masuk*</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" type="time" v-model="gantiRuang.jam_masuk" style="color:black;">
                            </div>
                        </div>

                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kelas Harga</label>
                            <div class="uk-form-controls">
                                <select v-model="gantiRuang.kelas_harga" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
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

export default {
    name : 'modal-ganti-ruang',
    components : { SearchList, },
    data() {
        return {
            unitDesc : [
                { colName : 'unit_nama', labelData : '', isTitle : true },
                { colName : 'unit_id', labelData : '', isTitle : false },
            ],
            configSelect : {
                required : true,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            gantiRuang : {
                trx_id : null,
                reg_id : null,
                pasien_id : null,
                no_rm : null,
                nama_pasien : null,
                
                unit_asal_id : null,
                unit_asal : null,
                ruang_asal_id : null,
                ruang_asal : null,
                bed_asal_id : null,
                no_asal_bed :null,
                
                unit_id : null,
                unit_nama : null,
                ruang_id : null,
                ruang_nama : null,
                bed_id : null,
                no_bed :null,

                jam_masuk : null,
                tgl_masuk : this.getTodayDate(),
                
                status : null,
                is_pulang : false,             
            },
            
            unitLists : [],
            roomLists : [],
            bedLists : [],
            kelasHargaLists : [],
        }
    },
    methods : {
        ...mapActions('rawatInap',['dataAdmisiInap','updateRuangInap']),
        ...mapActions('unitPelayanan',['listUnitPelayanan']),
        ...mapActions('kelas',['listKelas']),
        
        ...mapMutations(['CLR_ERRORS']),

        getTodayDate(){
            let dt = new Date();
            let year = dt.getFullYear();
            let month = dt.getMonth() + 1;
            if(month < 10) {month = `0${month}`}
            let date = dt.getDate();
            if(date < 10) {date = `0${date}`}
            let str_dt = `${year}-${month}-${date}`
            return str_dt;
        },

        modalGantiRuangClosed(){
            this.clearRuang();
            this.$emit('modalGantiRuangClosed',true);
            UIkit.modal('#modalGantiRuang').hide();
        },

        showModal() { UIkit.modal('#modalGantiRuang').show(); },

        editRuangInap(data){
            if(data){
                this.CLR_ERRORS();
                this.clearRuang();
                this.retrieveUnit();
                this.retrieveKelas();
                this.dataAdmisiInap(data.trx_id).then((response) => {
                    if (response.success == true) {
                        this.fillRuang(response.data);
                        this.showModal();
                    }
                    else { alert (response.message) }
                });
            }
        },

        clearRuang(){
            this.gantiRuang.trx_id = null;
            this.gantiRuang.reg_id = null;
            this.gantiRuang.pasien_id = null;
            this.gantiRuang.no_rm = null;
            this.gantiRuang.nama_pasien = null;

            this.gantiRuang.unit_id = null;
            this.gantiRuang.unit_nama = null;
            this.gantiRuang.ruang_id = null;
            this.gantiRuang.ruang_nama = null;
            this.gantiRuang.bed_id = null;
            this.gantiRuang.no_bed =null;

            this.gantiRuang.unit_asal_id = null;
            this.gantiRuang.unit_asal = null;
            this.gantiRuang.ruang_asal_id = null;
            this.gantiRuang.ruang_asal = null;
            this.gantiRuang.bed_asal_id = null;
            this.gantiRuang.no_asal_bed =null;

            this.gantiRuang.status = null;
            this.gantiRuang.is_pulang = false;

            this.gantiRuang.jam_masuk = null;
            this.gantiRuang.tgl_masuk = this.getTodayDate();
        },

        fillRuang(data){
            this.gantiRuang.trx_id = data.trx_id;
            this.gantiRuang.reg_id = data.reg_id;
            this.gantiRuang.pasien_id = data.pasien_id;            
            this.gantiRuang.no_rm = data.no_rm;
            this.gantiRuang.nama_pasien = data.nama_pasien;

            this.gantiRuang.unit_asal_id = data.unit_id;
            this.gantiRuang.unit_asal = data.unit_nama;
            this.gantiRuang.ruang_asal_id = data.ruang_id;
            this.gantiRuang.ruang_asal = data.ruang_nama;
            this.gantiRuang.bed_asal_id = data.bed_id;
            this.gantiRuang.no_asal_bed =data.no_bed;

            this.gantiRuang.unit_id = null;
            this.gantiRuang.unit_nama = null;
            this.gantiRuang.ruang_id = null;
            this.gantiRuang.ruang_nama = null;
            this.gantiRuang.bed_id = null;
            this.gantiRuang.no_bed =null;
            this.gantiRuang.jam_masuk = null;
            this.gantiRuang.tgl_masuk = this.getTodayDate();
        
            this.gantiRuang.status = data.status;
            this.gantiRuang.is_pulang = data.is_pulang;           
        },

        retrieveUnit() {
            this.CLR_ERRORS();
            this.listUnitPelayanan({per_page:'ALL','jenis_reg':'RAWAT_INAP'}).then((response) => {
                if (response.success == true) {
                    console.log(response.data.data);
                    this.unitLists = JSON.parse(JSON.stringify(response.data.data));
                }
                else { alert (response.message) }
            });
        },

        retrieveKelas() {
            this.listKelas({per_page:'ALL'}).then((response)=>{
                if (response.success == true) {
                    this.kelasHargaLists = response.data.data.filter(function (el) { return el.is_kelas_harga == true; });
                } 
                else { alert(response.message); }
            })
        },

        unitSelected(data) {
            if(data) {
                this.gantiRuang.unit_id = data.unit_id;
                this.gantiRuang.unit_nama = data.unit_nama;
                this.roomLists = data.ruang;
                this.bedLists = [];
                this.gantiRuang.ruang_id = null;
                this.gantiRuang.ruang_nama = null;  
            }
        },

        ruangSelected(){
            let room = this.roomLists.find( item => item.ruang_id == this.gantiRuang.ruang_id );
            if(room) {
                this.gantiRuang.ruang_id = room.ruang_id;
                this.gantiRuang.ruang_nama = room.ruang_nama;                
                this.bedLists = JSON.parse(JSON.stringify(room.beds));
                this.gantiRuang.bed_id = null;
                this.gantiRuang.no_bed = null;
            }
        },

        bedSelected(){
            let bed = this.bedLists.find( item => item.ruang_id == this.gantiRuang.ruang_id );
            if(bed) {
                this.gantiRuang.bed_id = bed.bed_id;
                this.gantiRuang.no_bed = bed.no_bed;         
            }
        },

        submitRuang(){            
            this.CLR_ERRORS();
            if(this.gantiRuang.bed_id == this.gantiRuang.bed_asal_id) {
                alert('ruang pengganti tidak boleh sama dengan Ruang sebelumnya.');
                return false;
            }
            else {
                if(confirm(`Proses ini akan mengubah ruang rawat pasien. Lanjutkan ?`)){
                    this.updateRuangInap(this.gantiRuang).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.modalGantiRuangClosed();
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