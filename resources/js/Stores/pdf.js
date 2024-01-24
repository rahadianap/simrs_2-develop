import axios from './httpReq';

let baseUrlReg = '/test/pdf';

export default {
    namespaced: true,

    state: () => ({
        radiologiLists : [],
        radTansaction : null,
        radTableFilter : { 
            keyword : '', 
            is_aktif : true, 
            per_page:10 
        },
    }),

    mutations : {
        SET_REGISTRATION_LISTS(state,payload) { 
            state.radiologiLists = payload; 
        },
        CLR_REGISTRATION_LISTS(state) { 
            state.radiologiLists = [];
        },
        SET_RAD_TRANSACTION(state,payload) { 
            state.radTansaction = payload; 
        },
        CLR_RAD_TRANSACTION(state,payload) { 
            state.radTansaction = null; 
        },
    },

    getters : {
        //return all user client data
        radiologiLists : state => { 
            return state.radiologiLists;
        },
        radTransaction : state => {
            return state.radTansaction;
        }
    },

    actions : {
        dataPatologi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlReg}/patologi`)
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
        dataRawatInap({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlReg}/rawat_inap`)
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
        dataPasienAntiHIV({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlReg}/rekap/pasienAntiHIV`)
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
        dataPasienRujukan({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlReg}/rekap/pasienRujukan`)
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
        dataLapPendapatanFarmasi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlReg}/laporan/pendapatan_farmasi`)
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
        dataLapPenggunaanObatKaryawan({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlReg}/laporan/penggunaan_obat/karyawan`)
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
        dataLapJumlahResep({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlReg}/laporan/jumlah_resep`)
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
        dataRender({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlReg}/render_view`)
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