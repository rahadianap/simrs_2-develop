import axios from './httpReq';
let baseUrl = '/assesments';

export default {
    namespaced: true,

    state: () => ({
        asesmen : { 
            soap_id : null,
            reg_id : null,
            trx_id : null,
            reff_trx_id : null,
            tgl_asesmen : null,
            
            dokter_id : null,
            dokter_nama : null,
            unit_id : null,
            unit_nama : null,
            pasien_id : null,

            saturasi_oksigen : null,
            sistole : null,
            diastole : null,
            suhu : null,
            denyut_nadi : null,
            pernapasan : null,

            subjective : null,
            objective : null,
            assesment : null,
            plan : null,
            catatan : null,

            diagnosa : [],

            client_id : null,
            is_aktif : true,
            status : null,
            
        },        
    }),

    mutations : {
        SET_ASESMEN(state,payload) { 
            state.asesmen = payload; 
        },

        NEW_ASESMEN(state,payload){
            state.asesmen.soap_id = null;
            state.asesmen.reg_id = payload.reg_id;
            state.asesmen.trx_id = payload.trx_id;
            state.asesmen.reff_trx_id = payload.reff_trx_id;
            
            state.asesmen.dokter_id = payload.dokter_id;
            state.asesmen.dokter_nama = payload.dokter_nama;
            state.asesmen.unit_id = payload.unit_id;
            state.asesmen.unit_nama = payload.unit_nama;
            state.asesmen.pasien_id = payload.pasien_id;
            
            state.asesmen.tgl_asesmen = null;
            state.asesmen.saturasi_oksigen = null;
            state.asesmen.sistole = null;
            state.asesmen.diastole = null;
            state.asesmen.suhu = null;
            state.asesmen.denyut_nadi = null;
            state.asesmen.pernapasan = null;

            state.asesmen.subjective = null;
            state.asesmen.objective = null;
            state.asesmen.assesment = null;
            state.asesmen.plan = null;
            state.asesmen.catatan = null;

            state.asesmen.diagnosa = [];

            state.asesmen.client_id = null;
            state.asesmen.is_aktif = true;
            state.asesmen.status = null;
        },

        CLR_ASESMEN(state) { 
            state.asesmen.soap_id = null;
            state.asesmen.reg_id = null;
            state.asesmen.trx_id = null;
            state.asesmen.reff_trx_id = null;
            state.asesmen.tgl_asesmen = null;
            
            state.asesmen.dokter_id = null;
            state.asesmen.dokter_nama = null;
            state.asesmen.unit_id = null;
            state.asesmen.unit_nama = null;
            state.asesmen.pasien_id = null;

            state.asesmen.saturasi_oksigen = null;
            state.asesmen.sistole = null;
            state.asesmen.diastole = null;
            state.asesmen.suhu = null;
            state.asesmen.denyut_nadi = null;
            state.asesmen.pernapasan = null;

            state.asesmen.subjective = null;
            state.asesmen.objective = null;
            state.asesmen.assesment = null;
            state.asesmen.plan = null;
            state.asesmen.catatan = null;

            state.asesmen.diagnosa = [];

            state.asesmen.client_id = null;
            state.asesmen.is_aktif = true;
            state.asesmen.status = null;
        },
    },

    getters : {
        asesmenData : state => {
            return state.asesmen;
        },
    },

    actions : {        
        dataAsesmen({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_ASESMEN',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        updateAsesmen({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(baseUrl, payload)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    }
                    else { commit('SET_ASESMEN',response.data.data); } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        createAsesmen({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(baseUrl, payload)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    }
                    else { commit('SET_ASESMEN',response.data.data); } 
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        deleteAsesmen({ commit }, payload ) {
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

        createDiagnosis({ commit }, payload) {
            return new Promise((resolve, reject) => {
                axios.post('/assesments/diagnosis', payload)
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
        }
    }
}