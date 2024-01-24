<template>
    <div class="uk-width-1-1@m">
        <p style="padding:0;margin:0;font-size:10px;color:black;font-style:italic;">*kata pencarian berdasar no RM, nama pasien, dan id transaksi</p>
    </div>
    <div class="uk-width-1-2@m">
        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Masuk Mulai</label>
        <div class="uk-form-controls">
            <input type="date" name="tgl_masuk_start" v-model="inapFilterTable.tgl_masuk_start" class="uk-input uk-form-small" style="color:black;">
        </div>
    </div>
    <div  class="uk-width-1-2@m">               
        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">s/d</label>         
        <div class="uk-form-controls">
            <input type="date" name="tgl_masuk_end" v-model="inapFilterTable.tgl_masuk_end" class="uk-input uk-form-small" style="color:black;">
        </div>
    </div>
    <div class="uk-width-1-1">          
        <search-list
            :config = configSelect
            :dataLists = unitServiceLists
            searchName = "unit"
            label = "Unit Pelayanan"
            placeholder = "pilih unit"
            captionField = "unit_nama"
            valueField = "unit_id"
            :detailItems = unitDesc
            :value = "inapFilterTable.unit"
            v-on:item-select = "unitSelected"
        ></search-list>
    </div>
    <div class="uk-width-1-1">          
        <search-list
            :config = configSelect
            :dataLists = dokLists
            searchName = "dokter"
            label = "Dokter"
            placeholder = "pilih dokter"
            captionField = "dokter_nama"
            valueField = "dokter_id"
            :detailItems = dokterDesc
            :value = "inapFilterTable.dokter"
            v-on:item-select = "dokterSelected"
        ></search-list>
    </div>
    <div class="uk-width-1-1">
        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Status</label>
        <div class="uk-form-controls">
            <select name="status" v-model="inapFilterTable.status" class="uk-select uk-form-small">
                <option></option>
                <option value="DAFTAR">DAFTAR</option>
                <option value="RAWAT">RAWAT</option>
                <option value="PULANG">PULANG</option>
            </select>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchList from '@/Components/SearchList.vue';
export default {
    name : 'filter-pasien-inap',
    components : {
        SearchList,
    },
    data() {
        return {
            filter : {
                tgl_masuk_start : null,
                tgl_masuk_end : null,
                unit : null,
                dokter : null,
                status : 'RAWAT',
            },
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },
            unitDesc: [
                { colName : 'unit_nama', labelData : '', isTitle : true },
                { colName : 'unit_id', labelData : '', isTitle : false },
            ],
            dokterDesc: [
                { colName : 'dokter_nama', labelData : '', isTitle : true },
                { colName : 'dokter_id', labelData : '', isTitle : false },
            ],
            unitServiceLists:[],
            dokLists:[],
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('rawatInap',['inapFilterTable']),
        ...mapGetters('unitPelayanan',['unitLists']),
        ...mapGetters('dokter',['doctorLists']),
        ...mapGetters('referensi',['isRefExist','jenisPelayananRefs','jenisRegistrasiRefs','asalPasienRefs',
            'caraRegistrasiRefs','salutRefs','jenisPenjaminRefs','hubKeluargaRefs',]),    
    },
    mounted(){
        this.init();
    },
    methods : {
        ...mapActions('referensi',['listReferensi']),
        ...mapActions('unitPelayanan',['listUnitPelayanan']),
        ...mapActions('dokter',['listDokter']),

        init(){
            if(!this.isRefExist) { this.listReferensi({per_page:'ALL'}); } 

            if(!this.unitLists || this.unitLists.length < 1) {
                let param = {'per_page':'ALL','is_aktif':true }
                this.listUnitPelayanan(param).then((response) => {
                    if (response.success == true) { 
                        this.unitServiceLists = response.data.data.filter(function (el) { return el.jenis_registrasi == 'RAWAT_INAP'; }); 
                    }
                });
            }            
            else { 
                this.unitServiceLists = this.unitLists.data.filter(function (el) { return el.jenis_registrasi == 'RAWAT_INAP'; }); 
            }
            
            if(!this.doctorLists || this.doctorLists.length < 1) {
                this.listDokter().then((response) => { if (response.success == true) { this.dokLists = response.data.data; } });
            }
            else { 
                this.dokLists = this.doctorLists.data; 
            }

            if(this.filter.tgl_masuk_start == null) {
                this.filter.tgl_masuk_start = this.getTodayDate();
                this.filter.tgl_masuk_end = this.getTodayDate();
            }
        },
        /**
         * get date today for default value input date 
         */
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

        unitSelected(data) {
            this.inapFilterTable.unit = null;
            if(data) { this.inapFilterTable.unit = data.unit_id; }
        },
        dokterSelected(data) {
            this.inapFilterTable.dokter = null;
            if(data) { this.inapFilterTable.dokter = data.dokter_id; }
        }
    }
}
</script>