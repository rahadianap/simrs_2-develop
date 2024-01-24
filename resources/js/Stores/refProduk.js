import axios from './httpReq';

let baseUrl = '/products/groups';

export default {
    namespaced: true,

    state: () => ({
        klasifikasiProdukRefs : [],
        golonganProdukRefs : [],
        labelObatRefs : [],
        
        jenisProdukRefs : [],
        sediaanObatRefs : [],
        satuanProdukRefs : [],
        signaProdukRefs : [],
        statusVendorRefs : [],
        
        isRefProdukExist : false,

    }),

    mutations : {
        SET_PRODUCT_REFS(state,payload) { 
            if(payload) {
                payload.forEach(el => {
                    if(el.ref_id == 'KLASIFIKASI_PRODUK') { state.klasifikasiProdukRefs = el; }
                    else if(el.ref_id == 'GOLONGAN_PRODUK') { state.golonganProdukRefs = el; }
                    else if(el.ref_id == 'LABEL_OBAT') { state.labelObatRefs = el; }
                    else if(el.ref_id == 'SEDIAAN_OBAT') { state.sediaanObatRefs = el; } 
                    else if(el.ref_id == 'SIGNA_PRODUK') { state.signaProdukRefs = el; } 
                    else if(el.ref_id == 'SATUAN_PRODUK') { state.satuanProdukRefs = el; } 
                    else if(el.ref_id == 'JENIS_PRODUK') { state.jenisProdukRefs = el; } 
                });
                state.isRefProdukExist = true; 
            }            
        },
        CLR_PRODUCT_REFS(state) { 
            state.klasifikasiProdukRefs = [];
            state.golonganProdukRefs = [];
            state.jenisProdukRefs = [];
            state.labelObatRefs = [];
            state.sediaanObatRefs = [];
            state.satuanProdukRefs = [];
            state.signaProdukRefs = [];

            state.isRefProdukExist = false; 
        },
    },

    getters : {
        //return all referensi
        klasifikasiProdukRefs : state => { return state.klasifikasiProdukRefs; },    
        golonganProdukRefs : state => { return state.golonganProdukRefs; },
        jenisProdukRefs : state => { return state.jenisProdukRefs; },
        labelObatRefs : state => { return state.labelObatRefs; },
        sediaanObatRefs : state => { return state.sediaanObatRefs; },
        satuanProdukRefs : state => { return state.satuanProdukRefs; },
        signaProdukRefs : state => { return state.signaProdukRefs; },

        isRefProdukExist : state => { return state.isRefProdukExist; },
    },

    actions : {
        createRefProduk({ commit }, payload ) {
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

        updateRefProduk({ commit }, payload ) {
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

        deleteRefProduk({ commit }, payload ) {
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

        dataRefProduk({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/${payload}`)
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

        listRefProduk({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_PRODUCT_REFS',response.data.data); }
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