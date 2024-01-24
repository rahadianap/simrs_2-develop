import axios from './httpReq';

export default {
    namespaced: true,

    state: () => ({
        spesialistikRef : [],
        caraKeluarRef : [],
        pascaPulangRef : [],
        
    }),

    mutations : {
        //set and clear selected board client
        SET_SPESIALISTIK_LISTS(state,payload) { 
            state.spesialistikRef = payload; 
        },
        CLR_SPESIALISTIK_LISTS(state) { 
            state.spesialistikRef = [];
        },

        SET_CARA_KELUAR_REF(state,payload) { 
            state.caraKeluarRef = payload; 
        },
        CLR_CARA_KELUAR_REF(state) { 
            state.caraKeluarRef = [];
        },

        SET_PASCA_PULANG_REF(state,payload) { 
            state.pascaPulangRef = payload; 
        },
        CLR_PASCA_PULANG_REF(state) { 
            state.pascaPulangRef = [];
        },
    },

    getters : {
        spesialistikRef : state => { 
            return state.spesialistikRef;
        },   
        caraKeluarRef : state => { 
            return state.caraKeluarRef;
        },
        pascaPulangRef : state => { 
            return state.pascaPulangRef;
        },
    },

    actions : {     
        updateBpjsMapping({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(`/bpjs/ref/mapping`, payload)
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

        removeBpjsMapping({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`/bpjs/ref/mapping`, payload)
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

        getBpjsCredential({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/credential`)
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

        updateBpjsCredential({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`/bpjs/credential`, payload)
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

        getJknCredential({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/jkn/credential`)
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

        updateJknCredential({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`/jkn/credential`, payload)
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

        refBpjsSpesialistik({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/ref/spesialistik`)
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

        refBpjsCaraKeluar({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/ref/cara/keluar`)
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

        refBpjsPascaPulang({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/ref/pasca/pulang`)
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

        refBpjsPropinsi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/ref/propinsi`)
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

        refBpjsKabupaten({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/ref/kabupaten/${payload}`)
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

        refBpjsKecamatan({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/ref/kecamatan/${payload}`)
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

        refBpjsCaraKeluar({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/ref/cara/keluar`)
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

        refBpjsPascaPulang({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/ref/pasca/pulang`)
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

        refBpjsKelas({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/ref/kelas`)
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

        refBpjsRuang({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/ref/ruang`)
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

        refBpjsPoli({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/ref/poli/${payload}`)
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
        refBpjsDokter({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/ref/dokter/${payload}`)
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

        refBpjsDiagnosaPrb({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/ref/prb/diagnosa`)
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

        refBpjsObatPrb({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/ref/prb/obat/${payload}`)
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

        refBpjsMonitoringKunjungan({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/monitoring/kunjungan/${payload.tglKunjungan}/jenis/${payload.jnsPelayanan}`)
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

        refBpjsMonitoringKlaim({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/monitoring/klaim/${payload.tglKunjungan}/jenis/${payload.jnsPelayanan}/status/${payload.status}`)
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

        refBpjsMonitoringPelayanan({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/monitoring/pelayanan/${payload.noka}/awal/${payload.tglMulai}/akhir/${payload.tglAkhir}`)
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

        refBpjsMonitoringJasaRaharja({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/bpjs/monitoring/jasaraharja/${payload.jenisPelayanan}/awal/${payload.tglMulai}/akhir/${payload.tglAkhir}`)
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