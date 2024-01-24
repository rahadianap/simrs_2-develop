import axios from './httpReq';

let baseUrl = '/cetakan';

export default {
    namespaced: true,

    state: () => ({
        cBillingLists : [],
        cDistLists : [],
        cStockLists : [],
        cDoctorsLists : [],
        cLabLists : [],
        PatientLists : [],
        RegLists : [],
        cPOLists : [],
        cPORLists : [],
        cPRLists : [],
        resepLists : [],
        cRawatInapists : [],
    }),

    mutations : {
        // Cetakan Billing
        SET_CBILLING_LISTS(state,payload) { 
            state.cBillingLists = payload; 
        },
        CLR_CBILLING_LISTS(state) { 
            state.cBillingLists = [];
        },

        // Cetakan Persediaan
        SET_CDIST_LISTS(state,payload) { 
            state.cDistLists = payload; 
        },
        CLR_CDIST_LISTS(state) { 
            state.cDistLists = [];
        },
        SET_CSTOCK_LISTS(state,payload) { 
            state.cStockLists = payload; 
        },
        CLR_CSTOCK_LISTS(state) { 
            state.cStockLists = [];
        },
        SET_CPO_LISTS(state,payload) { 
            state.cPOLists = payload; 
        },
        CLR_CPO_LISTS(state) { 
            state.cPOLists = [];
        },
        SET_CPOR_LISTS(state,payload) { 
            state.cPORLists = payload; 
        },
        CLR_CPOR_LISTS(state) { 
            state.cPORLists = [];
        },
        SET_CDOCTOR_LISTS(state,payload) { 
            state.cPRLists = payload; 
        },
        CLR_CDOCTOR_LISTS(state) { 
            state.cPRLists = [];
        },

        // Cetakan Laboratorium
        SET_CLAB_LISTS(state,payload) { 
            state.cLabLists = payload; 
        },
        CLR_CLAB_LISTS(state) { 
            state.cLabLists = [];
        },

        // Cetakan Data Master
        SET_CDOCTOR_LISTS(state,payload) { 
            state.cDoctorsLists = payload; 
        },
        CLR_CDOCTOR_LISTS(state) { 
            state.cDoctorsLists = [];
        },
        SET_REG_LISTS(state,payload) { 
            state.PatientLists = payload; 
        },
        CLR_REG_LISTS(state) { 
            state.PatientLists = [];
        },

        // Cetakan Pendaftaran
        SET_REG_LISTS(state,payload) { 
            state.RegLists = payload; 
        },
        CLR_REG_LISTS(state) { 
            state.RegLists = [];
        },

        // Cetakan Resep
        SET_RESEP_LISTS(state,payload) { 
            state.resepLists = payload; 
        },
        CLR_RESEP_LISTS(state) { 
            state.resepLists = [];
        },

        // Cetakan Rawat Inap
        SET_CRI_LISTS(state,payload) { 
            state.cRawatInapists = payload; 
        },
        CLR_CRI_LISTS(state) { 
            state.cRawatInapists = [];
        },
    },

    getters : {
        // Cetakan Billing
        cBillingLists : state => { 
            return state.cBillingLists;
        },   

        // Cetakan Distribusi
        cDistLists : state => { 
            return state.cDistLists;
        },  

        // Cetakan Persediaan
        cStockLists : state => { 
            return state.cStockLists;
        },
        cPOLists : state => { 
            return state.cPOLists;
        },
        cPORLists : state => { 
            return state.cPORLists;
        },
        cPRLists : state => { 
            return state.cPRLists;
        },

        // Cetakan Laboratorium
        cLabLists : state => { 
            return state.cLabLists;
        },

        // Cetakan Data Master
        cDoctorsLists : state => { 
            return state.cDoctorsLists;
        },
        PatientLists : state => { 
            return state.PatientLists;
        },

        // Cetakan Pendaftaran
        RegLists : state => { 
            return state.RegLists;
        },

        // Cetakan Resep
        resepLists : state => { 
            return state.resepLists;
        },

        // Cetakan Rawat Inap
        cRawatInapists : state => { 
            return state.cRawatInapists;
        },
    },

    actions : {
        // Cetakan Billing
        cetakPembayaran({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+`/billing/${payload[0]['jnsCetakan']}/${payload[0]['ids']}`)
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

        cetakUangMukaPasien({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/billing/uangMuka', {params:payload[0]})
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

        // Cetakan Persediaan
        dataDist({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/distribusi/${payload}`)
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

        dataKartuStock({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/kartustock/${payload}`)
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

        dataCetakanPO({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/podata/${payload}`)
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

        dataCetakanPOR({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/pordata/${payload}`)
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

        dataPR({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/prdata/${payload}`)
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

        // Cetakan Laboratorium
        dataCetakanLab({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/lab/${payload}`)
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
        
        dataCetakanHasilLab({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/lab/result/${payload}`)
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

        // Cetakan Data Master
        dataDokter({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/dokter/${payload}`)
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

        dataRiwayat({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/pasien/history/${payload}`)
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

        // Cetakan Pendaftaran
        dataCetakanPendaftaran({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/register/${payload}`)
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

        dataCetakanTracer({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/tracer/${payload}`)
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

        dataGelangDewasa({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/gelangDewasa', {params:payload[0]})
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

        dataFormInformasiPasien({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/form', {params:payload[0]})
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

        dataRegistrasiLabel({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl+'/registrasi', {params:payload[0]})
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

        // Cetakan Resep
        dataResep({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/etiket/${payload}`)
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

        // Cetakan Rawat Inap
        cetakanFormPenempatanKelasRI({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/ri/penempatan/kelas/${payload}`)
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