import axios from './httpReq';

let baseUrl = '/billings';

export default {
    namespaced: true,

    state: () => ({
        filterTable : { 
            is_aktif : true, 
            keyword : '',
            per_page : 10,
            page : 1,
            tgl_start : null,
            tgl_end : null,
            unit : null,
            dokter : null,
            status : null,
            jns_penjamin : null,
        },
        billingLists : [],
        billingData : {
            reg_id : null,
            no_rm: null,
            pasien_id :null,
            nama_pasien : null,
            nama_penanggung : null,
            penjamin_nama : null,
            penjamin_id : null,
            tgl_registrasi : null,
            grand_total : 0,
            total_bayar : 0,
            total_pembulatan : 0,
            transaksi : [],
            pembayaran :[],
            histori_bayar : [],

            all_trx_total : null,
            all_trx_bayar : null,
            all_trx_sisa : null,
        },
        poliLists : null,
    }),

    mutations : {
        SET_BILLING_LISTS(state,payload) {
            state.billingLists = payload; 
        },
        CLR_BILLING_LISTS(state) { 
            state.billingLists = [];
        },
        SET_BILLING_DATA(state,payload) {
            state.billingData.tgl_registrasi = payload.tgl_registrasi;
            state.billingData.reg_id = payload.reg_id;
            state.billingData.nama_pasien = payload.nama_pasien;
            state.billingData.pasien_id = payload.pasien_id;
            state.billingData.no_rm = payload.no_rm;
            state.billingData.nama_penanggung = payload.nama_penanggung;
            state.billingData.penjamin_nama = payload.penjamin_nama;
            state.billingData.penjamin_id = payload.penjamin_id;
            state.billingData.grand_total = payload.grand_total;
            state.billingData.total_bayar = payload.total_bayar;
            state.billingData.total_pembulatan = payload.total_pembulatan;
            state.billingData.transaksi = payload.transaksi;    
            state.billingData.pembayaran = payload.pembayaran;
            state.billingData.histori_bayar = payload.histori_bayar;

            state.billingData.all_trx_total = payload.grand_total;
            state.billingData.all_trx_bayar = payload.total_bayar;
            state.billingData.all_trx_sisa = payload.grand_total - payload.total_bayar;
        }
    },

    getters : {
        //return all user client data
        billingLists : state => { 
            if(state.billingLists.data) { return state.billingLists.data; }
            else { return state.billingLists; }
        },
        billingFilterTable : state => {
            return state.filterTable;
        },
        billingData : state => {
            return state.billingData;
        }
    },

    actions : {        
        createPayment({ commit }, payload ) {
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

        updatePayment({ commit }, payload ) {
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

        deletePayment({ commit }, payload ) {
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

        dataBilling({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_BILLING_DATA',JSON.parse(JSON.stringify(response.data))); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        listBilling({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_BILLING_LISTS',response.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        // cetakPembayaran({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.get(baseUrl+`/cetak/pembayaran/${payload[0]['jnsCetakan']}/${payload[0]['ids']}`)
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