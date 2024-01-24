import axios from './httpReq';

let baseUrl = '/registrations';
let baseUrlAntrian = '/praktek/dokter/queue';

let baseUrlPasien = '/praktek/dokter/pasien';
let baseUrlDokter = '/praktek/dokter/masterdokter';
let baseUrlMasterTindakan = '/praktek/dokter/mastertindakan';

let baseUrlPayment = '/praktek/dokter/pembayaran';
let baseUrlMedrec = '/praktek/dokter/medrec';
let baseUrlAct = '/praktek/dokter/pemeriksaan';
let baseUrlResep = '/praktek/dokter/resep';

let baseUrlDashboard = '/praktek/dokter/dashboard';

let baseUrlLaporan = '/praktek/dokter/laporan';

export default {
    namespaced: true,

    state: () => ({
        totalPasienBaru: 0,
        antrianLists : [],
        paymentLists : [],
        actLists : null,
        antrianDashboardLists : [],
        transaksiDashboardLists : [],
        
        filterTable : { 
            is_aktif : true, 
            keyword : '',
            per_page : 10,
            page : 1,
            
            tgl_periksa_start : null,
            tgl_periksa_end : null,

            tgl_registrasi_start : null,
            tgl_registrasi_end : null,
            
            dokter : null,
            status : null,
            jns_penjamin : null,
        },
        doctorLists : null,
        
        // registrationLists : [],
        // //bookingLists : [],
        // poliLists : null,
        
        jenisPenjamin : [
            { value : 'PRIBADI', text : 'PRIBADI' },
            { value : 'BPJS', text : 'BPJS' },
            { value : 'ASURANSI', text : 'ASURANSI' }
        ],

        statusAntrian : [
            { value : 'BOOKING', text : 'Booking', is_periksa : false },
            { value : 'ANTRI', text : 'Antri', is_periksa : false },
            { value : 'PERIKSA', text : 'Periksa', is_periksa : true },
            // { value : 'BATAL', text : 'Batal', is_periksa : false },
            // { value : 'SELESAI', text : 'SELESAI', is_periksa : true }, 
            // { value : 'BAYAR', text : 'BAYAR', is_periksa : true }, 
        ],

        newAntrian : {
            reg_id : null,
            tgl_registrasi : null,
            tgl_periksa : null,
            pasien_id : null,
            nama_pasien : null,
            no_rm : null,
            jns_kelamin : null,
            tgl_lahir : null,
            tempat_lahir : null,
            alamat : null,
            is_pasien_baru : false,
            dokter_id : null,
            dokter_nama : null,
            penjamin_id : null,
            penjamin_nama : null,
            jns_penjamin : null,
            catatan : null,
            status : null,
            is_periksa : false,
            is_aktif : true,
        },

        filterAntrian : { 
            is_aktif : true, 
            keyword : '',
            per_page : 10,
            page : 1,
            tgl_periksa : null,
            status : null,
        },

        filterPayment : { 
            is_aktif : true, 
            keyword : '',
            per_page : 10,
            page : 1,
            tgl_periksa : null,
            status : null,
        },

        filterAntrianDashboard : { 
            per_page : 10,
            page : 1,
            is_aktif : true, 
            keyword : null,
            date_start : null,
            date_end : null,
            doctorLists : "",
        },

        filterTransaksiDashboard : { 
            per_page : 10,
            page : 1,
            is_aktif : true, 
            keyword : null,
            date_start : null,
            date_end : null,
            doctorLists : "",
        },

        filterLapKunjungan : { 
            is_aktif : true, 
            keyword : '',
            per_page : 10,
            page : 1,
            tgl_periksa : null,
            status : null,
            start_date : null,
            end_date:null,
            periode : null,
        },

        soap_data : {
            soap_id : null,
            reg_id : null,
            reff_trx_id : null,
            tgl_soap :null,
            unit_id : '',
            unit_nama : '',
            dokter_id : null,
            dokter_nama : null,
            pasien_id : null,
            nama_pasien : null,
            no_rm : null,
            saturasi_oksigen : null,
            sistole : null,
            diastole : null,
            suhu : null,
            denyut_nadi : null,
            pernapasan : null,
            tinggi_badan : null,
            berat_badan : null,
            note_assesmen : null,
            subjective : null,
            objective : null,
            intervention : null,
            evaluation : null,
            assesment : null,
            plan : null,
            catatan : null,
            status : null,
            is_aktif : true,
            is_bayar : false,
            diagnosa : [],            
        },

        pemeriksaan : {
            reg_id : null,
            trx_id : null,
            items : [],
        },

        resep : {
            reg_id : null,
            trx_id : null,
            items : [],
        },

        dataRegistrasi : null,
        pasienMedrec : null,
        visitationLists : null,
        revenueLists : null,

        filterRevenueReport : { 
            is_aktif : true, 
            keyword : '',
            per_page : 10,
            page : 1,
            tgl_periksa : null,
            status : null,
            start_date : null,
            end_date:null,
            periode : null,
        },
        
    }),

    mutations : {
        incrementTotalPasienBaru(state) {
            state.totalPasienBaru += 1;
        },

        SET_ANTRIAN_LISTS(state,payload) {
            state.antrianLists = payload; 
        },
        CLR_REGISTRATION_LISTS(state) { 
            state.registrationLists = [];
        },

        SET_DOCTOR_LISTS(state,payload){
            state.doctorLists = payload;
        },

        SET_PAYMENT_LISTS(state,payload) {
            state.paymentLists = payload; 
        },

        

        SET_ACT_LISTS(state,payload) {
            state.actLists = payload; 
        },

        SET_SOAP_DATA(state,payload) {
            state.soap_data.soap_id = payload.soap_id;
            state.soap_data.reg_id = payload.reg_id;
            state.soap_data.reff_trx_id = payload.reff_trx_id;
            state.soap_data.tgl_soap =payload.tgl_soap;
            state.soap_data.unit_id = '';
            state.soap_data.unit_nama = '';
            state.soap_data.dokter_id = payload.dokter_id;
            state.soap_data.dokter_nama = payload.dokter_nama;
            state.soap_data.pasien_id = payload.pasien_id;
            state.soap_data.nama_pasien = payload.nama_pasien;
            state.soap_data.no_rm = payload.no_rm;
            state.soap_data.saturasi_oksigen = payload.saturasi_oksigen;
            state.soap_data.sistole = payload.sistole;
            state.soap_data.diastole = payload.diastole;
            state.soap_data.suhu = payload.suhu;
            state.soap_data.denyut_nadi = payload.denyut_nadi;
            state.soap_data.tinggi_badan = payload.tinggi_badan;
            state.soap_data.berat_badan = payload.berat_badan;
            state.soap_data.pernapasan = payload.pernapasan;
            state.soap_data.note_assesmen = payload.note_assesmen;
            
            state.soap_data.subjective = payload.subjective;
            state.soap_data.objective = payload.objective;
            state.soap_data.assesment = payload.assesment;
            state.soap_data.plan = payload.plan;
            state.soap_data.intervention = payload.intervention;
            state.soap_data.evaluation = payload.evaluation;
            
            state.soap_data.catatan = payload.catatan;
            state.soap_data.status = payload.status;
            state.soap_data.is_aktif = payload.is_aktif;
            state.soap_data.is_bayar = payload.is_bayar;

            state.soap_data.diagnosa = JSON.parse(JSON.stringify(payload.diagnosa));

        },

        SET_DATA_REG(state,payload) {
            state.dataRegistrasi = JSON.parse(JSON.stringify(payload));
        },

        SET_PATIENT_MEDREC(state,payload) {
            state.pasienMedrec = JSON.parse(JSON.stringify(payload));
        },

        SET_PEMERIKSAAN_DATA(state, payload) {
            state.pemeriksaan = JSON.parse(JSON.stringify(payload));
        },

        SET_RESEP_DATA(state,payload) {
            state.resep = JSON.parse(JSON.stringify(payload));
        },

        // SET_BOOKING_LISTS(state,payload) {
        //     state.bookingLists = payload; 
        // },

        // SET_POLI_LISTS(state,payload) { 
        //     state.poliLists = payload; 
        // },
        // CLR_POLI_LISTS(state) { 
        //     state.poliLists = null;
        // },

        SET_ANTRIAN_DASHBOARD_LISTS(state,payload) { 
            state.antrianDashboardLists = payload; 
        },
        
        SET_TRANSAKSI_DASHBOARD_LISTS(state,payload) { 
            state.transaksiDashboardLists = payload; 
        },

        SET_VISIT_LISTS(state,payload) {
            state.visitationLists = payload;
        },

        SET_REVENUE_LISTS(state,payload) {
            state.revenueLists = payload;
        },
    },

    getters : {
        //return all user client data
        actLists : state => { 
            return state.actLists;
        },
        //return all user client data
        antrianLists : state => { 
            if(state.antrianLists.data) { return state.antrianLists.data; }
            else { return state.antrianLists; }
        },

        paymentLists : state => { 
            if(state.paymentLists.data) { return state.paymentLists.data; }
            else { return state.paymentLists; }
        },

        regFilterTable : state => {
            return state.filterTable;
        },
        statusAntrian : state => {
            return state.statusAntrian;
        },
        jenisPenjamin : state => {
            return state.jenisPenjamin;
        },
        doctorLists : state => {
            return state.doctorLists;
        },

        newDataAntrian : state => {
            return state.newAntrian;
        },
        antrianFilterTable : state => {
            return state.filterAntrian;
        },

        paymentFilter : state => {
            return state.filterPayment;
        },

        soapData : state => {
            return state.soap_data;
        },
        diagnosaLists : state => {
            if(state.soap_data.diagnosa == null) {
                return [];
            }
            else {
                return state.soap_data.diagnosa;
            }
        },

        dataPemeriksaan : state => {
            return state.pemeriksaan;
        },

        resepData : state => {
            return state.resep;
        },

        antrianDashboardLists : state => {
            return state.antrianDashboardLists;
            // if(state.antrianDashboardLists.data) { return state.antrianDashboardLists.data; }
            // else { return state.antrianDashboardLists; }
        },
        
        filterDataAntrianDashboard : state => {
            return state.filterAntrianDashboard;
        },

        transaksiDashboardLists : state => {
            if(state.transaksiDashboardLists.data) { return state.transaksiDashboardLists.data; }
            else { return state.transaksiDashboardLists; }
        },
        
        filterDataTransaksiDashboard : state => {
            return state.filterTransaksiDashboard;
        },

        visitFilter : state => {
            return state.filterLapKunjungan;
        },

        revenueFilter : state => {
            return state.filterRevenueReport;
        },

        revenueList : state => {
            if(state.revenueLists.data) { return state.revenueLists.data; }
            else { return state.revenueLists; }
        },
        
        visitationList : state => {
            if(state.visitationLists.data) { return state.visitationLists.data; }
            else { return state.visitationLists; }
        },

        medrecPasienList : state => {
            return state.pasienMedrec;
        },

        registrasiData : state => {
            return state.dataRegistrasi;
        }
    },

    actions : { 
        /**
         * Data master pasien 
         */
        createPasien({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(baseUrlPasien, payload)
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

        updatePasien({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(baseUrlPasien, payload)
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

        deletePasien({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.delete(`${baseUrlPasien}/${payload}`)
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

        dataPasien({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlPasien}/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_PATIENT_DATA',response.data.data);}
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        listPasien({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrlPasien, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_PATIENT_LISTS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        /**master dokter */
        createDokter({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(baseUrlDokter, payload)
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

        updateDokter({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(baseUrlDokter, payload)
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

        deleteDokter({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.delete(`${baseUrlDokter}/${payload}`)
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

        dataDokter({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlDokter}/${payload}`)
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

        listDokter({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrlDokter, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_DOCTOR_LISTS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        /**antrian */
        createAntrian({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(baseUrlAntrian, payload)
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

        updateAntrian({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(baseUrlAntrian, payload)
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

        deleteAntrian({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`${baseUrlAntrian}/delete`, payload)
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

        dataAntrian({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlAntrian}/${payload}`)
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

        listAntrian({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrlAntrian, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_ANTRIAN_LISTS',response.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        updateStatusAntrian({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`${baseUrlAntrian}/status`, payload)
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

        pasienMedrec({ commit }, payload ) {
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

        /**payload adalah pasien Id */
        listMedrec({ commit }, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlMedrec}/${payload.pasien}/lists?dokter=${payload.dokter}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_PATIENT_MEDREC',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        dataMedrec({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlMedrec}/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { 
                        commit('SET_DATA_REG',response.data.data);
                        commit('SET_SOAP_DATA',response.data.data.soap); 
                    }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        createMedrec({ commit }, payload ) {
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

        updateMedrec({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`${baseUrlMedrec}/${payload.reg_id}`, payload)
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

        cetakPendaftaran({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlMedrec}/pendaftaran/cetak/${payload.reg_id}`, {payload})
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

        cetakHistory({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlMedrec}/history/cetak/${payload.reg_id}`, {payload})
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

        // deleteSoapMedrec({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.delete(`${baseUrl}/${payload}`)
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

        // deleteDiagnosaMedrec({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.delete(`${baseUrl}/${payload}`)
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

        /**PEMERIKSAAN */
        dataPemeriksaanPasien({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlAct}/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_PEMERIKSAAN_DATA',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        updatePemeriksaanPasien({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(baseUrlAct, payload)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_PEMERIKSAAN_DATA',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        cetakSuratPasien({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlAct}/surat/pasien/cetak/${payload['jns_surat']}/${payload['reg_id']}`)
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

        createSuratPasien({ commit }, payload ) {
            console.log(payload);
            return new Promise((resolve, reject) => {
                axios.post(`${baseUrlAct}/surat/pasien/${payload['jnsSurat']}`,payload)
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

        updateSuratPasien({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(`${baseUrlAct}/surat/pasien/${payload['jnsSurat']}`,payload)
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

        dataSuratPasien({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlAct}/surat/pasien/${payload['jns_surat']}/${payload['reg_id']}`,payload)
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
        
        /**
         * PAYMENT
         */
        createPayment({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(baseUrlPayment, payload)
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
                axios.put(baseUrlPayment, payload)
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
                axios.put(`${baseUrlPayment}/delete`, payload)
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

        dataPayment({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlPayment}/${payload}`)
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

        listPayment({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrlPayment, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_PAYMENT_LISTS',response.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        cetakKwitansiPDM({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlPayment}/cetak/kwitansi/${payload}`)
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

        /**
         * DASHBOARD
         */
        dashboardSummaryKunjungan({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlDashboard}/summary/kunjungan`, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_PAYMENT_LISTS',response.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        dashboardSummary({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlDashboard}/summary/all`, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_PAYMENT_LISTS',response.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        dashboardMonitoringStatus({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlDashboard}/monitoring/status`, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_PAYMENT_LISTS',response.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        listAntrianDashboard({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlDashboard}/monitoring/antrian`, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_ANTRIAN_DASHBOARD_LISTS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        listTransaksiDashboard({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlDashboard}/monitoring/transaksi`, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_TRANSAKSI_DASHBOARD_LISTS',response.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        /**
         * MASTER PEMERIKSAAN
         */
        createAct({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.post(baseUrlMasterTindakan, payload)
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

        updateAct({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(baseUrlMasterTindakan, payload)
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

        deleteAct({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.delete(`${baseUrlMasterTindakan}/${payload}`)
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

        listAct({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrlMasterTindakan, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_ACT_LISTS',response.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },


        /**
         * LAPORAN
         */
        lapPendapatan({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlLaporan}/pendapatan`, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_REVENUE_LISTS',response.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        lapKunjungan({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlLaporan}/kunjungan`, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_VISIT_LISTS',response.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        lapKas({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlLaporan}/kas`, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_PAYMENT_LISTS',response.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        cetakPendapatan({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlLaporan}/cetakan/pendapatan`, {params:payload})
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

        cetakKunjungan({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlLaporan}/cetakan/kunjungan`, {params:payload})
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

        cetakKas({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlLaporan}/cetakan/kas`, {params:payload})
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

        /**RESEP */
        dataResep({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlResep}/${payload}`)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_RESEP_DATA',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        updateResep({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.put(baseUrlResep, payload)
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_RESEP_DATA',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        cetakResep({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlResep}/cetak/${payload}`)
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