<template>
    <div></div>
</template>

<script>
import { mapMutations } from 'vuex'
export default {
    created() {
        this.login();
    },
    methods: {
        ...mapMutations({
            set_is_show_spinner: 'set_is_show_spinner'
        }),
        login (){

            this.set_is_show_spinner(true);

            this.$auth.login({
                params: {
                    authuser: this.$route.query.authuser,
                    code: this.$route.query.code,
                    hd: this.$route.query.hd,
                    prompt: this.$route.query.prompt,
                    scope: this.$route.query.scope,
                }
            })
                .then()
                .catch()
                .finally(()=>{
                    this.set_is_show_spinner(false);
                });
        }
    }
}
</script>

<style scoped>

</style>
