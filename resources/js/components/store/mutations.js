let mutations = {
    set_layout (state, payload) {
        state.layout = payload
    },

    setIsShowSpinner (state, payload) {
        state.is_show_spinner = payload
    },
};

export default mutations
