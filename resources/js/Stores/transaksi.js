import axios from './httpReq';
let baseUrl = '';

export default {
    namespaced: false,
    state: () => ({
        //resultLists : [],
        //**simpan data transaksi*/
        // transaksi : {
        //     reg_id : null,
        //     trx_id : null,
        //     sub_trx_id : null,
        //     is_bpjs : false,
        //     status : null,
        //     is_aktif : true,
        //     client_id : null,
        //     reff_trx_id : null,
        //     is_realisasi : false,

        //     detail : {
        //         reg_id : null,
        //         trx_id : null,
        //         sub_trx_id : null,
        //         jns_transaksi : null,
        //         tgl_transaksi : null,
        //         client_id : null,
        //         is_rujukan_int : false,
        //         jns_registrasi : null,
        //         tgl_transaksi : null,
        //         tgl_periksa : null,
        //         jam_periksa : null,
        //         no_antrian : null,
        //         jadwal_id : null,
        //         dokter_id : null,
        //         dokter_nama : null,
        //         unit_id : null,
        //         unit_nama : null,
        //         ruang_id : null,
        //         ruang_nama : null,
        //         dokter_pengirim_id: null,
        //         dokter_pengirim: null,
                    
        //         cara_registrasi : null,
        //         asal_pasien : null,
        //         ket_asal_pasien : null,
                    
        //         pasien_id : null,
        //         no_rm : null,
        //         nama_pasien : null,
        //         tempat_lahir : null,
        //         tgl_lahir : null,
        //         nik : null,
        //         jns_kelamin : null,
        //         is_pasien_baru : null,
        //         is_pasien_luar : false,                
        //         jns_penjamin : null,
        //         penjamin_id : null,                
        //         penjamin_nama : null,
    
        //         kelas_harga : null,
        //         buku_harga_id : null,
        //         buku_harga : null,
        //         total : 0,
        //     },
        //     pasien : {
        //         pasien_id : null,
        //         nama_pasien : null,
        //         no_rm : null,
        //         tempat_lahir : null,
        //         tgl_lahir : null,
        //         usia : null,
        //     },
            
        //     tindakan : [],
        //     resep : [],
        //     bhp : [],
        //     soap : [],            
        // },
        transaksi : null,

    }),

    mutations : {
        //set and clear selected board client
        // SET_RESULT_LISTS(state,payload) { 
        //     state.resultLists = payload; 
        // },
        // CLR_RESULT_LISTS(state) { 
        //     state.resultLists = [];
        // },
        SET_URL_SEARCH(payload) {
            this.baseUrl = payload;
        }
    },

    getters : {
        //return all user client data
        resultlists : state => { 
            return state.resultLists;
        },
    },

    actions : {
        searchList({ commit }, payload ) {            
            return new Promise((resolve, reject) => {
                axios.get(baseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_RESULT_LISTS',response.data.data); }
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