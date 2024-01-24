import axios from './httpReq';

let baseUrl = '/references';

export default {
    namespaced: true,

    state: () => ({
        groupLabNormalRefs : [],
        satuanLabNormalRefs : [],
        klasifikasiItemLabRefs : [],
        radModalityRefs : [],
        mediaHasilRefs : [],
        bentukMakananRefs : [],
        kelompokMakananRefs : [],
        waktuPenyajianRefs : [],
        jenisFotoRefs : [],
        sequenceRefs : [],
        monitoringGiziRefs : [],
        // jenisOperasiRefs : [],
        // skalaOperasiRefs : [],
        isRefPenunjangExist : false,
    }),

    mutations : {
        SET_ANCILLARIES_REFS(state,payload) { 
            if(payload) {
                payload.forEach(el => {
                    if(el.ref_id == 'GROUP_NILAI_NORMAL') { state.groupLabNormalRefs = el; }
                    else if(el.ref_id == 'SATUAN_NILAI_NORMAL') { state.satuanLabNormalRefs = el; }
                    else if(el.ref_id == 'KLASIFIKASI_ITEM_LAB') { state.klasifikasiItemLabRefs = el; }
                    else if(el.ref_id == 'RAD_MODALITY') { state.radModalityRefs = el; }
                    else if(el.ref_id == 'MEDIA_HASIL') { state.mediaHasilRefs = el; } 
                    else if(el.ref_id == 'BENTUK_MAKANAN') { state.bentukMakananRefs = el; }
                    else if(el.ref_id == 'KELOMPOK_MAKANAN') { state.kelompokMakananRefs = el; }
                    else if(el.ref_id == 'WAKTU_PENYAJIAN') { state.waktuPenyajianRefs = el; }
                    else if(el.ref_id == 'JENIS_FOTO') { state.jenisFotoRefs = el; }
                    else if(el.ref_id == 'SEQUENCE_NO') { state.sequenceRefs = el; }
                    else if(el.ref_id == 'MONITORING_GIZI') { state.monitoringGiziRefs = el; }
                    // else if(el.ref_id == 'JENIS_OPERASI') { state.jenisOperasiRefs = el; }
                    // else if(el.ref_id == 'SKALA_OPERASI') { state.skalaOperasiRefs = el; }
                });
                state.isRefPenunjangExist = true; 
            }            
        },
        CLR_ANCILLARIES_REFS(state) { 
            state.groupLabNormalRefs = [];
            state.satuanLabNormalRefs = [];
            state.klasifikasiItemLabRefs = [];

            state.radModalityRefs = [];
            state.mediaHasilRefs = [];
            state.bentukMakananRefs = [];
            state.kelompokMakananRefs = [];
            state.waktuPenyajianRefs = [];
            state.jenisFotoRefs = [];
            state.sequenceRefs = [];
            state.monitoringGiziRefs = [];            
            // state.jenisOperasiRefs = [];
            // state.skalaOperasiRefs = [];
            state.isRefPenunjangExist = false; 
        },
    },

    getters : {
        //return all referensi
        groupLabNormalRefs : state => { return state.groupLabNormalRefs; },    
        satuanLabNormalRefs : state => { return state.satuanLabNormalRefs; },
        klasifikasiItemLabRefs : state => { return state.klasifikasiItemLabRefs; },
        radModalityRefs : state => { return state.radModalityRefs; },
        mediaHasilRefs : state => { return state.mediaHasilRefs; },
        bentukMakananRefs: state=> { return state.bentukMakananRefs; },
        kelompokMakananRefs: state => { return state.kelompokMakananRefs; },
        waktuPenyajianRefs: state => { return state.waktuPenyajianRefs; },
        jenisFotoRefs: state => { return state.jenisFotoRefs; },
        sequenceRefs: state => { return state.sequenceRefs; },
        monitoringGiziRefs: state => { return state.monitoringGiziRefs; },
        // jenisOperasiRefs: state => { return state.jenisOperasiRefs; },
        // skalaOperasiRefs: state => { return state.skalaOperasiRefs; },
        isRefPenunjangExist : state => { return state.isRefPenunjangExist; },
    },

    actions : {
        createRefPenunjang({ commit }, payload ) {
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

        updateRefPenunjang({ commit }, payload ) {
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

        deleteRefPenunjang({ commit }, payload ) {
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

        dataRefPenunjang({ commit }, payload ) {
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

        listRefPenunjang({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`ancillary${baseUrl}`, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_ANCILLARIES_REFS',response.data.data); }
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