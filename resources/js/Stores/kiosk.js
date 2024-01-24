import axios from './httpReq';

let baseUrl = '';

export default {
    namespaced: true,

    state: () => ({
        kioskData : {
            unit_id : null,
            dokter_id : null,
            unit_nama : null,
            dokter_nama: null,
            kode_booking : null,
            jadwal_id : null,
            no_antrian : null,
            angka_antrian : null,
            tanggal_registrasi : null,
            tgl_periksa : null,
            jam_periksa : null,

            pasien_id : null,
            no_rm : null,
            nama_pasien : null,
            nama_lengkap : null,
            tempat_lahir : null,
            tgl_lahir : null,
            nik : null,
            no_kk : null,
            no_telepon : null,
            jadwal : null,
        },
        poliLists : [],
    }),

    mutations : {
        /**penyimpanan data pasien */
        SET_PASIEN(state,payload) { 
            state.kioskData.pasien_id = payload.pasien_id;
            state.kioskData.no_rm = payload.no_rm;
            state.kioskData.nama_pasien = payload.nama_pasien;
            state.kioskData.nama_lengkap = payload.nama_lengkap;
            state.kioskData.tempat_lahir = payload.tempat_lahir;
            state.kioskData.tgl_lahir = payload.tgl_lahir;
            state.kioskData.nik = payload.nik;
            state.kioskData.no_kk = payload.no_kk;
            state.kioskData.no_telepon = payload.no_telepon;
        },

        SET_JADWAL(state,payload) {
            state.kioskData.unit_id = payload.unit_id;
            state.kioskData.dokter_id = payload.dokter_id;
            state.kioskData.unit_nama = payload.unit_nama;
            state.kioskData.dokter_nama = payload.dokter_nama;
            state.kioskData.jadwal_id = payload.jadwal_id;
            state.kioskData.jadwal = payload;
        },

        CLR_DATA(state) { 
            state.kioskData.unit_id = null;
            state.kioskData.dokter_id = null;
            state.kioskData.unit_nama = null;
            state.kioskData.dokter_nama= null;
            state.kioskData.kode_booking = null;
            state.kioskData.jadwal_id = null;
            state.kioskData.no_antrian = null;
            state.kioskData.angka_antrian = null;
            state.kioskData.tanggal_registrasi = null;
            state.kioskData.tgl_periksa = null;
            state.kioskData.jam_periksa = null;

            state.kioskData.pasien_id = null;
            state.kioskData.no_rm = null;
            state.kioskData.nama_pasien = null;
            state.kioskData.nama_lengkap = null;
            state.kioskData.tempat_lahir = null;
            state.kioskData.tgl_lahir = null;
            state.kioskData.nik = null;
            state.kioskData.no_kk = null;
            state.kioskData.no_telepon = null;
        },

        SET_POLI_LISTS(state,payload) {
            state.poliLists = payload;
        },
        CLR_POLI_LISTS(state) {
            state.poliLists = [];
        },     

        /** penyimpanan data pasien BPJS */
        SET_PASIEN_BPJS(state,payload) {
            state.pasienBpjs.no_rujukan = payload.no_rujukan;
            state.pasienBpjs.no_kepesertaan = payload.no_kepesertaan;
            state.pasienBpjs.nik = payload.nik;
            state.pasienBpjs.pasien_id = payload.pasien_id;
            state.pasienBpjs.no_rm = payload.no_rm;
            state.pasienBpjs.nama_pasien = payload.nama_pasien;
            state.pasienBpjs.tempat_lahir = payload.tempat_lahir;
            state.pasienBpjs.tanggal_lahir = payload.tanggal_lahir;
            state.pasienBpjs.unit_id = payload.unit_id;
            state.pasienBpjs.dokter_id = payload.dokter_id;
            state.pasienBpjs.jadwal_id = payload.jadwal_id;
            state.pasienBpjs.unit_nama = payload.unit_nama;
            state.pasienBpjs.dokter_nama = payload.dokter_nama;
            state.pasienBpjs.no_antrian = payload.no_antrian;
            state.pasienBpjs.angka_antrian = payload.angka_antrian;
            state.pasienBpjs.tanggal_registrasi = payload.tanggal_registrasi;
            state.pasienBpjs.tgl_periksa = payload.tgl_periksa;
            state.pasienBpjs.jam_periksa = payload.jam_periksa;
            state.pasienBpjs.kode_booking = payload.kode_booking;
        },
        CLR_PASIEN_BPJS(state) {
            state.pasienBpjs.pasien_id = null;
            state.pasienBpjs.no_rm = null;
            state.pasienBpjs.nama_pasien = null;
            state.pasienBpjs.tempat_lahir = null;
            state.pasienBpjs.tanggal_lahir = null;
            state.pasienBpjs.unit_id = null;
            state.pasienBpjs.dokter_id = null;
            state.pasienBpjs.jadwal_id = null;
            state.pasienBpjs.unit_nama = null;
            state.pasienBpjs.dokter_nama = null;
            state.pasienBpjs.no_antrian = null;
            state.pasienBpjs.angka_antrian = null;
            state.pasienBpjs.tanggal_registrasi = null;
            state.pasienBpjs.tgl_periksa = null;
            state.pasienBpjs.jam_periksa = null;
            state.pasienBpjs.no_rujukan = null;
            state.pasienBpjs.kode_booking = null;
            state.pasienBpjs.no_kepesertaan = null;
            state.pasienBpjs.nik = null;
        }
    },

    getters : {
        kioskData : state => { 
            return state.kioskData;
        }, 
        poliLists : state => {
            return state.poliLists;
        },
        pasienBpjs : state => { 
            return state.pasienBpjs;
        }, 
        pasienJkn : state => { 
            return state.pasienJkn;
        }, 
        pasienAsuransi : state => { 
            return state.pasienAsuransi;
        }, 
    },

    actions : {
        // getTodayDate: function getTodayDate() {
        //     var dt = new Date();
        //     var year = dt.getFullYear();
        //     var month = dt.getMonth() + 1;
        //     if (month < 10) { month = "0".concat(month); }        
        //     var date = dt.getDate();
        //     if (date < 10) { date = "0".concat(date); }
        //     var str_dt = "".concat(year, "-").concat(month, "-").concat(date);
        //     return str_dt;
        // },
        
        jknAntrianData({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/kiosk/jkn/antrean/data/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        pasienData({ commit }, payload ){
            return new Promise((resolve, reject) => {
                axios.get(`/kiosk/pasien/data/${payload.tipe}/no/${payload.id}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        listUnitPoli({ commit }, payload ){
            return new Promise((resolve, reject) => {
                axios.get(`/kiosk/list/poli/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_POLI_LISTS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        // Route::GET('/kiosk/rujukan/nomor/{noRujukan}','AntrianKioskController@GetRujukanBpjsByNomor');
        // Route::GET('/kiosk/rujukan/kartu/{noKartu}','AntrianKioskController@GetRujukanBpjsByNoka');

        dataRujukanBpjsByNomor({ commit }, payload ){
            return new Promise((resolve, reject) => {
                axios.get(`/kiosk/rujukan/bpjs/nomor/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        dataRujukanBpjsByKartu({ commit }, payload ){
            return new Promise((resolve, reject) => {
                axios.get(`/kiosk/rujukan/bpjs/nokartu/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        dataJadwal({ commit }, payload ){
            return new Promise((resolve, reject) => {
                axios.get(`/kiosk/jadwal/${payload.jadwal_id}/tanggal/${payload.tanggal}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        bookingAntrian({ commit }, payload ){
            return new Promise((resolve, reject) => {
                axios.post(`/kiosk/booking`, payload)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },
        
        dataBooking({ commit }, payload ){
            return new Promise((resolve, reject) => {
                axios.get(`/kiosk/booking/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        verifikasiBooking({ commit }, payload ){
            return new Promise((resolve, reject) => {
                axios.post(`/kiosk/booking/verifikasi`,payload)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        noAntrianKasir({ commit }, payload ){
            return new Promise((resolve, reject) => {
                axios.post(`/kiosk/antrian/kasir`,payload)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        createPasienBaru({ commit }, payload ){
            return new Promise((resolve, reject) => {
                axios.post(`/kiosk/pasien/baru`,payload)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },
    }
}