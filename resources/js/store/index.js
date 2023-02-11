import Axios from "axios"

export default {
    state: {
        userList : []
    },

    getters: {
        userList(state){
             return state.userList;
        }
    },
    actions: {
        userList(contex){
            Axios.get('/userlist')
            .then(res => {
                contex.commit("userList", res.data);
            })
        }
    },
    mutations: {
        userList(state, payload){
            state.userList = payload;
        }
    },
}