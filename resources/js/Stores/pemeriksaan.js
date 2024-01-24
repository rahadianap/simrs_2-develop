import axios from './httpReq';
let baseUrl = '/examinations';

export default {
    namespaced: true,

    state: () => ({
        pemeriksaan : { 
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
            unit_id : null,
            unit_nama : null,
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

            kelas_harga_id : null,
            kelas_harga : null,
            
            total : 0,
            is_realisasi : false, 
            is_bayar : false, 
            is_aktif : true,
            status : null,
            items : [],
        },        
    }),

    mutations : {
        SET_EXAMINATION(state,payload) { 
            state.pemeriksaan = payload; 
        },

        NEW_EXAMINATION(state,payload){
            let dt = JSON.parse(JSON.stringify(payload));
            console.log(dt);
            
            state.pemeriksaan.reg_id = payload.reg_id;
            state.pemeriksaan.trx_id = null;
            state.pemeriksaan.reff_trx_id = payload.trx_id;
            state.pemeriksaan.dokter_id = payload.dokter_id;
            state.pemeriksaan.dokter_nama = payload.dokter_nama;
            state.pemeriksaan.client_id = null;
            state.pemeriksaan.tgl_transaksi = null;
            state.pemeriksaan.jns_transaksi = payload.jns_transaksi;
            state.pemeriksaan.no_antrian = null;
            state.pemeriksaan.tgl_resep = null;
            state.pemeriksaan.no_resep = null;
            state.pemeriksaan.unit_id = null;
            state.pemeriksaan.unit_nama = null;
            state.pemeriksaan.depo_id = null;
            state.pemeriksaan.depo_nama = null;
            state.pemeriksaan.peresep = payload.dokter_nama;        
            state.pemeriksaan.pasien_id = payload.pasien_id;
            state.pemeriksaan.no_rm = payload.no_rm;
            state.pemeriksaan.nama_pasien = payload.nama_pasien;
            state.pemeriksaan.tempat_lahir = payload.tempat_lahir;
            state.pemeriksaan.tgl_lahir = payload.tgl_lahir;
            state.pemeriksaan.jns_kelamin = payload.jns_kelamin;
            state.pemeriksaan.jns_penjamin = payload.jns_penjamin;
            state.pemeriksaan.penjamin_id = payload.penjamin_id;
            state.pemeriksaan.penjamin_nama = payload.penjamin_nama;
            state.pemeriksaan.total = 0;

            state.pemeriksaan.kelas_harga_id = payload.kelas_harga_id; 
            state.pemeriksaan.kelas_harga = payload.kelas_harga;
            state.pemeriksaan.unit_id = payload.unit_id; 
            state.pemeriksaan.unit_nama = payload.unit_nama;

            state.pemeriksaan.is_realisasi = false; 
            state.pemeriksaan.is_bayar = false; 
            state.pemeriksaan.is_aktif = true;
            state.pemeriksaan.status = null;
            state.pemeriksaan.items = [];
        },

        CLR_EXAMINATION(state) { 
            state.pemeriksaan = { 
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

                kelas_harga_id : payload.kelas_harga_id, 
                kelas_harga : payload.kelas_harga,
                unit_id : payload.unit_id, 
                unit_nama : payload.unit_nama,

                is_bpjs : false,
                status : null,
                is_aktif : true,
                client_id : null,
                reff_trx_id : null,
                is_realisasi : false, 
                items : [],
            };
        },

        SET_EXAMINATION_ITEMS(state,payload) {
            state.pemeriksaan.items = payload;
        }
    },

    getters : {
        examination : state => {
            return state.pemeriksaan;
        },

        examinationItems : state => {
            return state.pemeriksaan.items;
        },

        tindakanLists : state => {
            return state.pemeriksaan.items.filter(data => data.jenis == 'TINDAKAN');
        },

        bhpLists : state => {
            return state.pemeriksaan.items.filter(data => data.jenis == 'BHP');
        }
    },

    actions : {        
        dataPemeriksaan({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { 
                        commit('SET_EXAMINATION',response.data.data); 
                    }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        updatePemeriksaan({ commit }, payload ) {
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

        createPemeriksaan({ commit }, payload ) {
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

        deletePemeriksaan({ commit }, payload ) {
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

        confirmPemeriksaan({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(`${baseUrl}/confirm/${payload}`)
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

        unconfirmPemeriksaan({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(`${baseUrl}/unconfirm/${payload}`)
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