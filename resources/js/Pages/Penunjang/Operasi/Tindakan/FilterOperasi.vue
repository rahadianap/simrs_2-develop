<template>
    <div class="uk-width-1-1@m">
        <p style="padding:0;margin:0;font-size:10px;color:black;font-style:italic;">*kata pencarian berdasar no RM, nama pasien, dan id transaksi</p>
    </div>
    <div class="uk-width-1-2@m">
        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tgl Operasi</label>
        <div class="uk-form-controls">
            <input type="date" name="tgl_operasi_start" v-model="filter.tgl_operasi_start" class="uk-input uk-form-small" style="color:black;">
        </div>
    </div>
    <div  class="uk-width-1-2@m">               
        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">s/d</label>         
        <div class="uk-form-controls">
            <input type="date" name="tgl_operasi_end" v-model="filter.tgl_operasi_end" class="uk-input uk-form-small" style="color:black;">
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
            :value = "filter.unit"
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
            :value = "filter.dokter"
            v-on:item-select = "dokterSelected"
        ></search-list>
    </div>
    <div class="uk-width-1-1">
        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Status</label>
        <div class="uk-form-controls">
            <select name="status" class="uk-select uk-form-small">
                <option></option>
                <option value="DAFTAR">DAFTAR</option>
                <option value="ANTRI">PROSES</option>
                <option value="PERIKSA">SELESAI</option>
            </select>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import SearchList from '@/Components/SearchList.vue';
export default {
    name : 'filter-operasi',
    components : {
        SearchList,
    },
    data() {
        return {
            filter : {
                tgl_operasi_start : this.getTodayDate(),
                tgl_operasi_end : this.getTodayDate(),
                unit : null,
                dokter : null,
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
        ...mapGetters('unitPelayanan',['unitLists']),
        ...mapGetters('dokter',['dokterLists']),
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
            let param = {'jenis_reg':'OPERASI','per_page':'ALL','is_aktif':true }
            this.listUnitPelayanan(param).then((response) => {
                if (response.success == true) {
                    this.unitServiceLists = response.data.data;
                }
            });
            this.listDokter().then((response) => {
                if (response.success == true) {
                    this.dokLists = response.data.data;
                }
            });;
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
            this.filter.unit = null;
            if(data) {
                this.filter.unit = data.unit_id;
            }
        },
        dokterSelected(data) {
            this.filter.dokter = null;
            if(data) {
                this.filter.dokter = data.dokter_id;
            }
        }
    }
}
</script>