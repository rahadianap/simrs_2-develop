import axios from './httpReq';

let baseUrl = '/templatemaster';

export default {
  namespaced: true,

  state: () => ({
  }),

  mutations : {
  },

  getters : {
  },

  actions : {
    exportCoa({ commit }, payload ) {
      return new Promise((resolve, reject) => {
        axios.get('/templatemaster/coa', payload, {responseType: 'blob'})
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

    listTemplate({ commit }, payload ) {
      return new Promise((resolve, reject) => {
          axios.get(baseUrl, {params:payload})
          .then((response) => {
              if (response.data.success == false) {
                  commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
              } 
              else {
                  commit('SET_ROLE_LISTS',response.data.data);
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