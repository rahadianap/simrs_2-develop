<template>
    <div>
        <div style="margin-top:0;">
            <store-table ref="tablePendaftaran"
                :columns = "tbColumns" 
                :buttons = "buttons"
                :config = "configTable"
                :storeData = "bookingLists"
                :searchCallback = "listBooking"
                :paramFilter = "bookingFilterTable">
                <template v-slot:tbl-body>
                    <tbody style="min-weight:50vh;">
                        <row-booking  v-if="bookingLists"
                            v-for="dt in bookingLists.data" 
                            :rowData="dt"
                            :linkCallback = updateData>
                        </row-booking>
                    </tbody>
                </template>
                <template v-slot:form-filter>                    
                    <filter-booking></filter-booking>
                </template>
            </store-table>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseTable from '@/Components/BaseTable';
import StoreTable from '@/Components/StoreTable';
import RowBooking from '@/Pages/RawatJalan/Pendaftaran/BookingPoli/RowBooking.vue';
import FilterBooking from '@/Pages/RawatJalan/Pendaftaran/BookingPoli/FilterBooking.vue';

export default {
    name : 'booking-poli',
    components : {
        headerPage,
        StoreTable,
        RowBooking,
        FilterBooking,
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('booking',['bookingFilterTable','bookingLists']),
    },
    data() {
        return {
            isExpanded : false,
            configTable : {
                isExpandable : false,
                filterType : 'ADVANCED', 
            },

            tbColumns : [
                { 
                    name : 'kode_booking', 
                    title : 'Kode Booking', 
                    colType : 'linkmenus',
                },   
                { 
                    name : 'no_antrian', 
                    title : 'Antrian', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'nama_pasien', 
                    title : 'PASIEN', 
                    colType : 'linkmenus', 
                },
                { 
                    name : 'dokter_nama', 
                    title : 'Dokter Unit', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'penjamin_nama', 
                    title : 'Penjamin', 
                    colType : 'text', 
                    isSearchable : true,
                },
                
                { 
                    name : 'status_reg', 
                    title : 'STATUS', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'jns_registrasi', 
                    title : 'JENIS REG.', 
                    colType : 'text', 
                    isSearchable : true,
                },
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    isSearchable : false,
                }                
            ],

            buttons : [
                { title : 'Booking Baru', icon:'calendar', 'callback' : this.AddBooking },
            ],
            searchUrl : '/outpatient/booking',
            regBookingLists : null,
        }
    },
    mounted() {
    },

    methods : {
        ...mapActions('booking',['deleteBooking','listBooking']),
        ...mapMutations(['CLR_ERRORS']),

        AddBooking(){
            this.$router.push({ name: 'formBookingPoli', params: { tipe:'form', dataId: 'baru' } });
        },

        // updateListDataBooking(data){
        //     this.regBookingLists = null;
        //     if(data){ this.regBookingLists = data.data; }
        // },

        refreshTable() {
            this.$refs.tableBooking.retrieveData();
        },

        updateData(data) {
            this.CLR_ERRORS();
            if(data) {
                this.$router.push({ name: 'formBookingPoli', params: { tipe:'form',dataId: data.reg_id } });
            }
        },

        deleteData(data) {
            this.CLR_ERRORS();
            if(confirm(`Proses ini akan menghapus registrasi ${data.nama_pasien}. Lanjutkan ?`)){
                this.deleteRegistrasi(data.reg_id).then((response) => {
                    if (response.success == true) {
                        alert(response.message);
                        this.$refs.tablePendaftaran.retrieveData();
                    }
                    else { alert (response.message) }
                });
            };
        }
    }
}
</script>
<style>

ul.hims-poli-tab li {
    margin:0;
    padding:0;
}

ul.hims-poli-tab li h5{
    padding:0 1em 0 1em;
    margin:0;
}

ul.hims-poli-tab li div a {
    padding:0;
    margin:0;
    color:#000; 
    display:block; 
    padding:0.5em;
    text-decoration: none;
    font-weight: 500;
}

ul.hims-poli-tab li div {
    margin:0; 
    background-color: #eee; 
    color:#000;
    padding:0;
}

ul.hims-poli-tab li.uk-active div {
    margin:0; 
    background-color: #fff; 
    color:#000;
    padding:0;
}

ul.hims-poli-tab li div a h5 {
    color:#666;
    font-weight: 500;
}

ul.hims-poli-tab li.uk-active div a h5 {
    color:#000;
    font-weight: 500;
}
</style>

