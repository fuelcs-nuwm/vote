let mutations = {
    set_layout (state, payload) {
        state.layout = payload
    },

    set_is_show_spinner (state, payload) {
        state.is_show_spinner = payload
    },
};

export default mutations
