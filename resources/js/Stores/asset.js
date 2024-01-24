import axios from './httpReq';

let baseUrl = '/assets';
let baseUrlOverview='/asset/overview';
// let baseUrlTicket='/asset/maintenanceticket';
// let baseUrlTicketSchedule='/asset/maintenanceschedule';

export default {
    namespaced: true,

    state: () => ({
        lists : [],
        checked_all: false
    }),

    mutations : {
        //set and clear selected board client
        SET_LISTS(state,payload) { 
            state.lists = payload; 
        },
        CLR_LISTS(state) { 
            state.lists = [];
        },
    },

    getters : {
        //return all user client data
        lists : state => { 
            return state.lists;
        },
        checked_all : state => { 
            return state.checked_all;
        },  
    },

    actions : {
        // Master Asset
        createAsset({ commit }, payload) {
            return new Promise((resolve, reject) => {
                const data = {...payload, nilai_asset: payload.nilai_asset.replace(/Rp\. /g, "")}

                axios.post(baseUrl, data)
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

        updateAsset({ commit }, payload ) {
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

        deleteAsset({ commit }, payload ) {
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

        dataAsset({ commit }, payload ) {
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

        listAsset({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrl, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_ASSET_LISTS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        // AssetOverview
        dataOverview({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrlOverview}/${payload}`)
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

        listOverview({ commit }, payload ) {
            return new Promise((resolve, reject) => {
                axios.get(baseUrlOverview, {params:payload})
                .then((response) => {
                    if (response.data.success == false) {
                        commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
                    } 
                    else { commit('SET_ASSET_LISTS',response.data.data); }
                    resolve(response.data);
                })            
                .catch((error) => {
                    commit('SET_ERRORS', error.response.data.error, { root: true });
                    reject(error);
                })
            })
        },

        // master maintenance ticket
        // createTicket({ commit }, payload) {
        //     return new Promise((resolve, reject) => {
        //         // const data = {...payload, nilai_asset: payload.nilai_asset.replace(/Rp\. /g, "")}

        //         axios.post(baseUrlTicket, data)
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

        // updateTicket({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.put(baseUrlTicket, payload)
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

        // deleteTicket({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.delete(`${baseUrlTicket}/${payload}`)
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

        // dataTicket({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.get(`${baseUrlTicket}/${payload}`)
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

        // listTicket({ commit }, payload ) {
        //     return new Promise((resolve, reject) => {
        //         axios.get(baseUrlTicket, {params:payload})
        //         .then((response) => {
        //             if (response.data.success == false) {
        //                 commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //             } 
        //             else { commit('SET_TICKET_LISTS',response.data.data); }
        //             resolve(response.data);
        //         })            
        //         .catch((error) => {
        //             commit('SET_ERRORS', error.response.data.error, { root: true });
        //             reject(error);
        //         })
        //     })
        // },

        // //  maintenance schedule
        //         // master maintenance ticket
        //         createSchedule({ commit }, payload) {
        //             return new Promise((resolve, reject) => {
        //                 // const data = {...payload, nilai_asset: payload.nilai_asset.replace(/Rp\. /g, "")}
        
        //                 axios.post(baseUrlTicketSchedule, data)
        //                 .then((response) => {
        //                     if (response.data.success == false) {
        //                         commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //                     } 
        //                     resolve(response.data);
        //                 })            
        //                 .catch((error) => {
        //                     commit('SET_ERRORS', error.response.data.error, { root: true });
        //                     reject(error);
        //                 })
        //             })
        //         },
        
        //         updatSchedule({ commit }, payload ) {
        //             return new Promise((resolve, reject) => {
        //                 axios.put(baseUrlTicketSchedule, payload)
        //                 .then((response) => {
        //                     if (response.data.success == false) {
        //                         commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //                     } 
        //                     resolve(response.data);
        //                 })            
        //                 .catch((error) => {
        //                     commit('SET_ERRORS', error.response.data.error, { root: true });
        //                     reject(error);
        //                 })
        //             })
        //         },
        
        //         deleteSchedule({ commit }, payload ) {
        //             return new Promise((resolve, reject) => {
        //                 axios.delete(`${baseUrlTicketSchedule}/${payload}`)
        //                 .then((response) => {
        //                     if (response.data.success == false) {
        //                         commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //                     } 
        //                     resolve(response.data);
        //                 })            
        //                 .catch((error) => {
        //                     commit('SET_ERRORS', error.response.data.error, { root: true });
        //                     reject(error);
        //                 })
        //             })
        //         },
        
        //         dataSchedule({ commit }, payload ) {
        //             return new Promise((resolve, reject) => {
        //                 axios.get(`${baseUrlTicketSchedule}/${payload}`)
        //                 .then((response) => {
        //                     if (response.data.success == false) {
        //                         commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //                     } 
        //                     resolve(response.data);
        //                 })            
        //                 .catch((error) => {
        //                     commit('SET_ERRORS', error.response.data.error, { root: true });
        //                     reject(error);
        //                 })
        //             })
        //         },
        
        //         listSchedule({ commit }, payload ) {
        //             return new Promise((resolve, reject) => {
        //                 axios.get(baseUrlTicketSchedule, {params:payload})
        //                 .then((response) => {
        //                     if (response.data.success == false) {
        //                         commit('SET_ERRORS', { invalid: response.data.message }, { root: true });
        //                     } 
        //                     else { commit('SET_SCHEDULE_LISTS',response.data.data); }
        //                     resolve(response.data);
        //                 })            
        //                 .catch((error) => {
        //                     commit('SET_ERRORS', error.response.data.error, { root: true });
        //                     reject(error);
        //                 })
        //             })
        //         },
    }
}