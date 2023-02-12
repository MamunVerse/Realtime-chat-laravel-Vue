import Axios from "axios"

export default {
    state: {
        userList : [],
        userMessage : [],
    },

    getters: {
        userList(state){
             return state.userList;
        },
        userMessage(state){
             return state.userMessage;
        }
    },
    actions: {
        userList(contex){
            Axios.get('/userlist')
            .then(res => {
                contex.commit("userList", res.data);
            })
        },
        userMessage(contex, payload){
            Axios.get('/usermessage/'+payload)
            .then(res => {
                contex.commit("userMessage", res.data);
            })
        }
    },
    mutations: {
        userList(state, payload){
            state.userList = payload;
        },
        userMessage(state, payload){
            state.userMessage = payload;
        }
    },
}