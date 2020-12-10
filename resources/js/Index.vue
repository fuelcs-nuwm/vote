<template>
    <div class="h-100">
        <template v-if="$auth.ready()">
            <component v-bind:is="layout"></component>
        </template>
        <template v-if="!$auth.ready()">
            <div class="h-100 d-flex justify-content-center align-items-center">
                <Spinner></Spinner>
            </div>
        </template>
        <div v-if="$auth.ready() && isShowSpinner" class="spinner-wrapper d-flex justify-content-center align-items-center">
            <Spinner v-if="isShowSpinner"></Spinner>
        </div>
    </div>
</template>

<script>

import Spinner from "./components/common/Spinner";
import Root from "./components/layouts/Root";
import Admin from "./components/layouts/Admin";
import Empty from "./components/layouts/Empty";
import {mapGetters} from "vuex";

export default {
    computed: {
        ...mapGetters({
            isShowSpinner: 'get_is_show_spinner',
            getLayout: 'get_layout'
        }),
        layout() {
            return this.$store.getters.get_layout;
            // return this.getLayout;
        },
    },
    components: {
        Spinner,
        Root,
        Admin,
        Empty
    }
}
</script>

<style>

html,
body,
#app {
    height: 100%;
    margin: 0;
}

</style>

<style scoped lang="scss">
    .spinner-wrapper {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 2000;
    }

    *:root {
        --orange-color: #ED6E00;
        --orange-bg-color: #ED6E00;
        --blue-color: #256589;
        --blue-bg-color: #256589;
        --gley-color: rgb(241, 241, 241);
        --gley-bg-color: rgb(241, 241, 241);
        --light-blue-color: #358cbd;
        --light-blue-bg-color: #358cbd;

    }
</style>
