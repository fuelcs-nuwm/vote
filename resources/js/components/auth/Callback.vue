<template>
    <div>callback</div>
</template>

<script>
export default {
    created() {
        this.init();
        this.login();
    },
    methods: {
        init () {
            console.log(this.$route)
        },

        login (){
            this.$auth.login({
                params: {
                    authuser: this.$route.query.authuser,
                    code: this.$route.query.code,
                    hd: this.$route.query.hd,
                    prompt: this.$route.query.prompt,
                    scope: this.$route.query.scope,
                },
                success: function(res) {
                },
                error: function(error) {
                    console.log(error)
                },
            });
        }
    }
}
</script>

<style scoped>

</style>
