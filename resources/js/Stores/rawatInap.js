import axios from './httpReq';

let baseUrlAdmission = '/inpatients/admissions';
let baseUrlInpatient = '/inpatient/transactions';


export default {
    namespaced: true,

    state: () => ({
        filterTable : { 
            is_aktif : true, 
            keyword : '',
            per_page : 10,
            page : 1,
            tgl_masuk_start : null,
            tgl_masuk_end : null,
            unit : null,
            dokter : null,
            status : 'RAWAT',
        },

        inpatientLists : [],
        inpatientTransaction : {
            reg_id : null,
            nama_pasien : null,
        },
    }),

    mutations : {
        SET_INPATIENT_LISTS(state,payload) { 
            state.inpatientLists = payload; 
        },
        CLR_INPATIENT_LISTS(state) { 
            state.inpatientLists = [];
        },

        SET_INPATIENT_TRANSACTION(state,payload) {
            state.inpatientTransaction = payload; 
        },

        CLR_INPATIENT_TRANSACTION(state) {
            state.inpatientTransaction =  {
                reg_id : null,
                nama_pasien : null,
            };
        },

        SET_FILTER_TABLE_INAP(state,payload) { 
            state.filterTable = payload; 
        },
        
        SET_ACTS(state,payload) { 
            state.inpatientTransaction.pemeriksaan.detail = payload; 
        },  
        
        SET_ITEM_USAGES(state,payload) { 
            state.inpatientTransaction.bhp.detail = payload; 
        },  

        SET_ITEM_PRESCRIPTS(state,payload) { 
            state.inpatientTransaction.resep.detail = payload; 
        },  

    },

    getters : {
        inpatientLists : state => { 
            if(state.inpatientLists.data) { return state.inpatientLists.data; }
            else { return state.inpatientLists; }
        },

        inpatientTransaction : state => {
            return state.inpatientTransaction;
        },

        inpatientAssesments : state => {
            return state.inpatientTransaction.soap;
        },

        inpatientInforms : state => {
            return state.inpatientTransaction.inform;
        },

        inpatientExamination : state => {
            return state.inpatientTransaction.pemeriksaan;
        },

        inpatientLabLists : state => {
            return state.inpatientTransaction.penunjang.lab;
        },
        
        inpatientRadLists : state => {
            return state.inpatientTransaction.penunjang.rad;
        },

        inpatientItemPrescripts : state => {
            return state.inpatientTransaction.resep.detail;
        },

        inpatientResepLists : state => {
            return state.inpatientTransaction.resep;
        },

        inapFilterTable : state => { 
            return state.filterTable; 
        },
        
        inpatientInforms : state => {
            return state.inpatientTransaction.inform;
        },
    },

    actions : {
        listAdmisiInap({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrlAdmission, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_INPATIENT_LISTS',response.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        createAdmisiInap({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(baseUrlAdmission, payload)
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

        updateAdmisiInap({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(baseUrlAdmission, payload)
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

        deleteAdmisiInap({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.delete(`${baseUrlAdmission}/${payload}`)
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

        dataAdmisiInap({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlAdmission}/${payload}`)
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
        
        confirmAdmisiInap({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`${baseUrlAdmission}/confirm`,payload)
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
        

        updateDokterInap({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`/inpatients/doctor/change`, payload)
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

        updateRuangInap({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`/inpatients/room/change`, payload)
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

        updatePenjaminInap({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`/inpatients/guarantor/change`, payload)
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

        pasienInapPulang({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`/inpatients/discharges`,payload)
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


        dataTransaksiInap({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlInpatient}/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else {
                        commit('SET_INPATIENT_TRANSACTION',response.data.data);
                    }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        // cetakGelang
        cetakGelang({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`/inpatient/cetak/gelang/${payload}`)
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