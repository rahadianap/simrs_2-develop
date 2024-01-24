import axios from './httpReq';

let baseUrl = '/references';

export default {
    namespaced: true,

    state: () => ({
        golDarahRefs : [],
        rhesusRefs : [],
        salutRefs : [],
        agamaRefs : [],
        pekerjaanRefs : [],
        pendidikanRefs : [],
        jenisPenjaminRefs : [],
        hubKeluargaRefs : [],
        statusKawinRefs : [],
        sukuBangsaRefs : [],
        kebangsaanRefs : [],
        caraPulangRefs : [],
        statusPulangRefs : [],
        asalPasienRefs : [],
        jenisAlergiRefs : [],
        jenisPelayananRefs : [],
        jenisRegistrasiRefs : [],
        statusBedInapRefs : [], 
        kasusIgdRefs : [],
        namaHariRefs : [],
        jenisProdukRefs : [],
        caraRegistrasiRefs : [],
        jenisFotoRefs : [],
        jenisJawabanRefs : [],

        groupDarahRefs : [],
        asalDarahRefs : [],
        jenisOperasiRefs : [],
        skalaOperasiRefs : [],     
        timOperasiRefs : [],        
        isRefExist : false,
    }),

    mutations : {
        SET_REFS(state,payload) { 
            if(payload) {
                payload.forEach(el => {
                    if(el.ref_id == 'GOL_DARAH') { state.golDarahRefs = el; }
                    else if(el.ref_id == 'RHESUS') { state.rhesusRefs = el; }
                    else if(el.ref_id == 'GROUP_DARAH') { state.groupDarahRefs = el; }
                    else if(el.ref_id == 'ASAL_DARAH') { state.asalDarahRefs = el; }
                    
                    else if(el.ref_id == 'SALUT') { state.salutRefs = el; }
                    else if(el.ref_id == 'AGAMA') { state.agamaRefs = el; }

                    else if(el.ref_id == 'PEKERJAAN') { state.pekerjaanRefs = el; }
                    else if(el.ref_id == 'PENDIDIKAN') { state.pendidikanRefs = el; }
                    else if(el.ref_id == 'JENIS_PENJAMIN') { state.jenisPenjaminRefs = el; }
                    else if(el.ref_id == 'HUB_KELUARGA') { state.hubKeluargaRefs = el; }
                    else if(el.ref_id == 'STATUS_KAWIN') { state.statusKawinRefs = el; }
                    else if(el.ref_id == 'SUKU_BANGSA') { state.sukuBangsaRefs = el; }
                    else if(el.ref_id == 'KEBANGSAAN') { state.kebangsaanRefs = el; }
                    else if(el.ref_id == 'CARA_REGISTRASI') { state.caraRegistrasiRefs = el; }
                    else if(el.ref_id == 'CARA_PULANG') { state.caraPulangRefs = el; }
                    else if(el.ref_id == 'STATUS_PULANG') { state.statusPulangRefs = el; }
                    else if(el.ref_id == 'ASAL_PASIEN') { state.asalPasienRefs = el; }
                    else if(el.ref_id == 'JENIS_ALERGI') { state.jenisAlergiRefs = el; }
                    else if(el.ref_id == 'JNS_PELAYANAN') { state.jenisPelayananRefs = el; }
                    else if(el.ref_id == 'JNS_REGISTRASI') { state.jenisRegistrasiRefs = el; }
                    else if(el.ref_id == 'STATUS_BED_INAP') { state.statusBedInapRefs = el; }
                    else if(el.ref_id == 'KASUS_IGD') { state.kasusIgdRefs = el; }
                    else if(el.ref_id == 'NAMA_HARI') { state.namaHariRefs = el; }    
                    else if(el.ref_id == 'JENIS_PRODUK') { state.jenisProdukRefs = el; }                 
                    else if(el.ref_id == 'JENIS_FOTO') { state.jenisFotoRefs = el; }
                    else if(el.ref_id == 'JENIS_OPERASI') { state.jenisOperasiRefs = el; }
                    else if(el.ref_id == 'SKALA_OPERASI') { state.skalaOperasiRefs = el; }
                    else if(el.ref_id == 'TIM_OPERASI') { state.timOperasiRefs = el; }
                    else if(el.ref_id == 'JENIS_JAWABAN') { state.jenisJawabanRefs = el; }
                });
                state.isRefExist = true; 
            }            
        },
        CLR_REFS(state) { 
            state.golDarahRefs = [];
            state.rhesusRefs = [];
            state.salutRefs = [];
            state.agamaRefs = [];
            state.pekerjaanRefs = [];
            state.pendidikanRefs = [];
            state.jenisPenjaminRefs = [];
            state.hubKeluargaRefs = [];
            state.statusKawinRefs = [];
            state.sukuBangsaRefs = [];
            state.kebangsaanRefs = [];
            state.caraPulangRefs = [];
            state.statusPulangRefs = [];
            state.asalPasienRefs = [];
            state.jenisAlergiRefs = [];
            state.jenisPelayananRefs = [];
            state.jenisRegistrasiRefs = [];
            state.statusBedInapRefs = [];
            state.kasusIgdRefs = [];
            state.namaHariRefs = [];
            state.jenisProdukRefs = [];
            state.caraRegistrasiRefs = [];
            state.jenisFotoRefs = [];
            state.jenisOperasiRefs = [];
            state.skalaOperasiRefs = [];
            state.timOperasiRefs = [];
            state.groupDarahRefs = [];
            state.asalDarahRefs = [];
            state.jenisJawabanRefs = [];
        },
    },

    getters : {
        //return all referensi
        golDarahRefs : state => { return state.golDarahRefs; },
        groupDarahRefs : state => { return state.groupDarahRefs; },
        asalDarahRefs : state => { return state.asalDarahRefs; },
        
        rhesusRefs : state => { return state.rhesusRefs; },
        salutRefs : state => { return state.salutRefs; },
        agamaRefs : state => { return state.agamaRefs; },
        pekerjaanRefs : state => { return state.pekerjaanRefs; },
        pendidikanRefs : state => { return state.pendidikanRefs; },
        jenisPenjaminRefs : state => { return state.jenisPenjaminRefs; },
        hubKeluargaRefs : state => { return state.hubKeluargaRefs; },
        statusKawinRefs : state => { return state.statusKawinRefs; },
        sukuBangsaRefs : state => { return state.sukuBangsaRefs; },
        kebangsaanRefs : state => { return state.kebangsaanRefs; },
        caraPulangRefs : state => { return state.caraPulangRefs; },
        statusPulangRefs : state => { return state.statusPulangRefs; },
        asalPasienRefs : state => { return state.asalPasienRefs; },
        jenisAlergiRefs : state => { return state.jenisAlergiRefs; },
        jenisPelayananRefs : state => { return state.jenisPelayananRefs; },
        jenisRegistrasiRefs : state => { return state.jenisRegistrasiRefs; },
        caraRegistrasiRefs : state => { return state.caraRegistrasiRefs; },
        statusBedInapRefs : state => { return state.statusBedInapRefs; },
        kasusIgdRefs : state => { return state.kasusIgdRefs; },
        namaHariRefs : state => { return state.namaHariRefs; },
        jenisProdukRefs : state => { return state.jenisProdukRefs; },
        jenisFotoRefs : state => { return state.jenisFotoRefs; },

        jenisOperasiRefs: state => { return state.jenisOperasiRefs; },
        skalaOperasiRefs: state => { return state.skalaOperasiRefs; },
        timOperasiRefs: state => { return state.timOperasiRefs; },
        jenisJawabanRefs: state => { return state.jenisJawabanRefs; },

        isRefExist : state => { return state.isRefExist; },
    },

    actions : {
        createReferensi({ commit }, payload ) {
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

        updateReferensi({ commit }, payload ) {
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

        deleteReferensi({ commit }, payload ) {
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

        dataReferensi({ commit }, payload ) {
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

        listReferensi({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_REFS',response.data.data); }
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