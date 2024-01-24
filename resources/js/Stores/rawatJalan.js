import axios from './httpReq';
let baseUrl = '/outpatient/transactions';

export default {
    namespaced: true,

    state: () => ({
        filterTable : { 
            is_aktif : true, 
            keyword : '',
            per_page : 10,
            page : 1,
            tgl_periksa_start : null,
            tgl_periksa_end : null,
            unit : null,
            dokter : null,
            status : null,
            jns_penjamin : null,
        },

        poliTransactionLists : [],
        poliTransaction : { 
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
                           
                pemeriksaan : [],
                resep : [],
                pasien : {
                    pasien_id : null,
                    nama_pasien : null,
                    no_rm : null,
                    tempat_lahir : null,
                    tgl_lahir : null,
                    usia : null,
                },
                soap : [],
                inform : [],
        },        
    }),

    mutations : {
        SET_POLI_TRANSACTION_LISTS(state,payload) { 
            state.poliTransactionLists = payload; 
        },

        CLR_POLI_TRANSACTION_LISTS(state) { 
            state.poliTransactionLists = [];
        },

        SET_POLI_TRANSACTION(state,payload) { 
            state.poliTransaction = payload; 
            // state.poliTransaction = { 
            //     reg_id : payload.reg_id,
            //     trx_id : payload.trx_id,
            //     sub_trx_id : payload.sub_trx_id,
            //     client_id : payload.client_id,
            //     is_rujukan_int : payload.is_rujukan_int,
            //     jns_registrasi : payload.jns_registrasi,
            //     tgl_transaksi : payload.tgl_transaksi,
            //     tgl_periksa : payload.tgl_periksa,
            //     jam_periksa : payload.jam_periksa,
            //     no_antrian : payload.no_antrian,

            //     jadwal_id : payload.jadwal_id,
            //     dokter_id : payload.dokter_id,
            //     dokter_nama : payload.dokter_nama,
            //     unit_id : payload.unit_id,
            //     unit_nama : payload.unit_nama,
            //     ruang_id : payload.ruang_id,
            //     ruang_nama : payload.ruang_nama,
                
            //     dokter_pengirim_id: payload.dokter_pengirim_id,
            //     dokter_pengirim: payload.dokter_pengirim,
                
            //     cara_registrasi : payload.cara_registrasi,
            //     asal_pasien : payload.asal_pasien,
            //     ket_asal_pasien : payload.ket_asal_pasien,
                
            //     pasien_id : payload.pasien_id,
            //     no_rm : payload.no_rm,
            //     nama_pasien : payload.nama_pasien,
            //     tempat_lahir : payload.tempat_lahir,
            //     tgl_lahir : payload.tgl_lahir,
            //     nik : payload.nik,
            //     jns_kelamin : payload.jns_kelamin,
            //     is_pasien_baru : payload.is_pasien_baru,
            //     is_pasien_luar : payload.is_pasien_baru,                
            //     jns_penjamin : payload.jns_penjamin,
            //     penjamin_id : payload.penjamin_id,                
            //     penjamin_nama : payload.penjamin_nama,

            //     kelas_harga : payload.kelas_harga,
            //     buku_harga_id : payload.buku_harga_id,
            //     buku_harga : payload.buku_harga,
            //     total : payload.total,

            //     is_bpjs : payload.is_bpjs,
            //     status : payload.status,
            //     is_aktif : payload.is_aktif,
            //     client_id : payload.client_id,
            //     reff_trx_id : payload.reff_trx_id,
            //     is_realisasi : payload.is_realisasi,
            //     pemeriksaan : payload.pemeriksaan,
            //     pasien : {
            //         pasien_id : payload.pasien.pasien_id,
            //         nama_pasien : payload.pasien.nama_pasien,
            //         no_rm : payload.pasien.no_rm,
            //         tempat_lahir : payload.pasien.tempat_lahir,
            //         tgl_lahir : payload.pasien.tgl_lahir,
            //         usia : payload.pasien.usia,
            //     },
            // };
        },

        CLR_POLI_TRANSACTION(state) { 
            state.poliTransaction = { 
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
                pemeriksaan : [],
                pasien : {
                    pasien_id : null,
                    nama_pasien : null,
                    no_rm : null,
                    tempat_lahir : null,
                    tgl_lahir : null,
                    usia : null,
                },
            };
        },

        SET_FILTER_TABLE_POLI(state,payload) { 
            state.filterTable = payload; 
        },
        
        SET_ACTS_POLI(state,payload) { 
            state.poliTransaction.pemeriksaan.detail = payload; 
        },  
        
        SET_ITEM_USAGES(state,payload) { 
            state.poliTransaction.bhp.detail = payload; 
        },  

        SET_ITEM_PRESCRIPTS(state,payload) { 
            state.poliTransaction.resep.detail = payload; 
        },  


    },

    getters : {
        poliTransactionLists : state => { 
            if(state.poliTransactionLists.data) {
                return state.poliTransactionLists.data;
            }
            else {
                return state.poliTransactionLists;
            }
        },

        poliTransaction : state => {
            return state.poliTransaction;
        },

        poliActs : state => {
            if(state.poliTransaction.pemeriksaan) {
                return state.poliTransaction.pemeriksaan.detail;
            }
            else {
                return state.poliTransaction.pemeriksaan = { 
                    detail :[]
                };
            }
        },

        poliItemUsages : state => {
            // return state.poliTransaction.bhp.detail;
            return state.poliTransaction.bhp.detail;
        },

        poliItemPrescripts : state => {
            return state.poliTransaction.resep.detail;
        },
        
        poliFilterTable : state => {
            return state.filterTable;
        },

        poliResepLists : state => {
            return state.poliTransaction.resep;
        },

        poliBhpLists : state => {
            return state.poliTransaction.bhp;
        },

        poliExamination : state => {
            return state.poliTransaction.pemeriksaan;
        },

        poliAssesments : state => {
            return state.poliTransaction.soap;
        },

        poliLabLists : state => {
            return state.poliTransaction.penunjang.lab;
        },
        poliRadLists : state => {
            return state.poliTransaction.penunjang.rad;
        },

        poliInforms : state => {
            return state.poliTransaction.inform;
        },
        
    },

    actions : {
        
        listTransaksiPoli({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { 
                        commit('SET_POLI_TRANSACTION_LISTS',response.data); 
                    }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        dataTransaksiPoli({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else {
                        commit('SET_POLI_TRANSACTION',response.data.data);
                    }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        createTransaksiPoli({ commit }, payload ) {
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

        updateTransaksiPoli({ commit }, payload ) {
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

        deleteTransaksiPoli({ commit }, payload ) {
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

        

        

        // updateTransaksiPoli({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.put(`/inpatients/doctor/change`, payload)
        //         .then((response) => {
        //             if (response.data.success == false) {
        //                 commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //             } 
        //             resolve(response.data);
        //         })            
        //         .catch((error) => {
        //             commit('SET_ERRORS', error.response.data.error, { root: true });
        //             reject(error);
        //         })
        //     })
        // },

        // updateTransaksiPoli({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.put(`/inpatients/room/change`, payload)
        //         .then((response) => {
        //             if (response.data.success == false) {
        //                 commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //             } 
        //             resolve(response.data);
        //         })            
        //         .catch((error) => {
        //             commit('SET_ERRORS', error.response.data.error, { root: true });
        //             reject(error);
        //         })
        //     })
        // },

        // updatePenjaminInap({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.put(`/inpatients/guarantor/change`, payload)
        //         .then((response) => {
        //             if (response.data.success == false) {
        //                 commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //             } 
        //             resolve(response.data);
        //         })            
        //         .catch((error) => {
        //             commit('SET_ERRORS', error.response.data.error, { root: true });
        //             reject(error);
        //         })
        //     })
        // },

        // confirmTransaksiPoli({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.put(`${baseUrl}/confirm`,payload)
        //         .then((response) => {
        //             if (response.data.success == false) {
        //                 commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //             } 
        //             resolve(response.data);
        //         })            
        //         .catch((error) => {
        //             commit('SET_ERRORS', error.response.data.error, { root: true });
        //             reject(error);
        //         })
        //     })
        // },

        // pasienInapPulang({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.put(`/inpatients/discharges`,payload)
        //         .then((response) => {
        //             if (response.data.success == false) {
        //                 commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //             } 
        //             resolve(response.data);
        //         })            
        //         .catch((error) => {
        //             commit('SET_ERRORS', error.response.data.error, { root: true });
        //             reject(error);
        //         })
        //     })
        // },

        
    }
}