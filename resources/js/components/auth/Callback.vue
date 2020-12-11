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
            setIsShowSpinner: 'setIsShowSpinner'
        }),
        login (){

            this.setIsShowSpinner(true);

            this.$auth.login({
                params: {
                    authuser: this.$route.query.authuser,
                    code: this.$route.query.code,
                    hd: this.$route.query.hd,
                    prompt: this.$route.query.prompt,
                    scope: this.$route.query.scope,
                }
            })
                .then((res)=> {
                    console.log(res)
                })
                .catch((error)=> {
                    console.log(error.response.data.message);
                    alert (error.response.data.message) ;
                    this.$router.push({ name: 'home'}).catch(()=>{});
                })
                .finally(()=>{
                    this.setIsShowSpinner(false);
                });
        }
    }
}
</script>

<style scoped>

</style>
