import axios from './httpReq';

let baseUrl = '';

export default {
  namespaced: true,

  state: () => ({
    // coaLists : [],
  }),

  mutations : {
    //set and clear coa
    // SET_COA_LISTS(state,payload) { 
    //     state.coaLists = payload; 
    // },
  },

  getters : {
    // coaLists : state => { 
    //     return state.coaLists;
    // },
  },

  actions : {
    importExcelCOA({ commit }, payload ) {
      return new Promise((resolve, reject) => {
        axios.post('/coa/importExcel', payload)
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

    importExcelICD9({ commit }, payload ) {
      return new Promise((resolve, reject) => {
        axios.post('/icd9/importExcel', payload)
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

    importExcelICD10({ commit }, payload ) {
      return new Promise((resolve, reject) => {
        axios.post('/icd10/importExcel', payload)
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

    importExcelDtd({ commit }, payload ) {
      return new Promise((resolve, reject) => {
        axios.post('/dtd/importExcel', payload)
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