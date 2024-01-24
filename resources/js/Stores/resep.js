import axios from './httpReq';
let baseUrl = '/prescriptions';

export default {
    namespaced: true,

    state: () => ({
        depoResepLists : [],
        resep : { 
            reg_id : null,
            trx_id : null,
            reff_trx_id : null,
            dokter_id : null,
            dokter_nama : null,
            client_id : null,
            tgl_transaksi : null,
            jns_transaksi : null,
            no_antrian : null,
            tgl_resep : null,
            no_resep : null,
            unit_id : null,
            unit_nama : null,
            depo_id : null,
            depo_nama : null,
            peresep : null,        
            pasien_id : null,
            no_rm : null,
            nama_pasien : null,
            tempat_lahir : null,
            tgl_lahir : null,
            jns_kelamin : null,
            jns_penjamin : null,
            penjamin_id : null,
            penjamin_nama : null,
            total : 0,
            is_realisasi : false, 
            is_bayar : false, 
            is_aktif : true,
            status : null,
            items : [],
        },        
    }),

    mutations : {
        SET_PRESCRIPTION(state,payload) { 
            state.resep = payload; 
        },

        NEW_PRESCRIPTION(state,payload){
            state.resep.reg_id = payload.reg_id;
            state.resep.trx_id = null;
            state.resep.reff_trx_id = payload.trx_id;
            state.resep.dokter_id = payload.dokter_id;
            state.resep.dokter_nama = payload.dokter_nama;
            state.resep.client_id = null;
            state.resep.tgl_transaksi = null;
            state.resep.jns_transaksi = payload.jns_transaksi;
            state.resep.no_antrian = null;
            state.resep.tgl_resep = null;
            state.resep.no_resep = null;
            state.resep.unit_id = null;
            state.resep.unit_nama = null;
            state.resep.depo_id = null;
            state.resep.depo_nama = null;
            state.resep.peresep = payload.dokter_nama;        
            state.resep.pasien_id = payload.pasien_id;
            state.resep.no_rm = payload.no_rm;
            state.resep.nama_pasien = payload.nama_pasien;
            state.resep.tempat_lahir = payload.tempat_lahir;
            state.resep.tgl_lahir = payload.tgl_lahir;
            state.resep.jns_kelamin = payload.jns_kelamin;
            state.resep.jns_penjamin = payload.jns_penjamin;
            state.resep.penjamin_id = payload.penjamin_id;
            state.resep.penjamin_nama = payload.penjamin_nama;
            state.resep.total = 0;
            state.resep.is_realisasi = false; 
            state.resep.is_bayar = false; 
            state.resep.is_aktif = true;
            state.resep.status = null;
            state.resep.items = [];
        },

        CLR_PRESCRIPTION(state) { 
            state.resep = { 
                reg_id : null,
                trx_id : null,
                sub_trx_id : null,
                client_id : null,
                is_rujukan_int : false,
                jns_registrasi : null,
                tgl_transaksi : null,
                tgl_periksa : null,
                jam_periksa : null,
                no_antrian : null,
                jadwal_id : null,
                dokter_id : null,
                dokter_nama : null,
                unit_id : null,
                unit_nama : null,
                ruang_id : null,
                ruang_nama : null,
                dokter_pengirim_id: null,
                dokter_pengirim: null,
                cara_registrasi : null,
                asal_pasien : null,
                ket_asal_pasien : null,
                
                pasien_id : null,
                no_rm : null,
                nama_pasien : null,
                tempat_lahir : null,
                tgl_lahir : null,
                nik : null,
                jns_kelamin : null,
                is_pasien_baru : null,
                is_pasien_luar : false,                
                jns_penjamin : null,
                penjamin_id : null,                
                penjamin_nama : null,

                kelas_harga : null,
                buku_harga_id : null,
                buku_harga : null,
                total : 0,

                is_bpjs : false,
                status : null,
                is_aktif : true,
                client_id : null,
                reff_trx_id : null,
                is_realisasi : false, 
                items : [],
            };
        },

        SET_PRESCRIPTION_ITEMS(state,payload) {
            state.resep.items = payload;
        },

        SET_DEPO_LISTS(state,payload) {
            state.depoResepLists = payload;
        }
    },

    getters : {
        prescription : state => {
            return state.resep;
        },

        prescriptionItems : state => {
            return state.resep.items;
        },

        depoResepList : state => {
            return state.depoResepLists;
        }  
    },

    actions : {
        
        dataPrescription({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { 
                        commit('SET_PRESCRIPTION',response.data.data); 
                    }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        updatePrescription({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(baseUrl, payload)
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

        createPrescription({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(baseUrl, payload)
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

        deletePrescription({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.delete(`${baseUrl}/${payload}`)
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

        realisasiResep({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`${baseUrl}/${payload}/realizations`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true }); reject(error);
                })
            })
        },

        listDepoResep({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/depos`, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_DEPO_LISTS',response.data.data); }
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