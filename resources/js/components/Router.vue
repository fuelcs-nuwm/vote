<template>
    <div></div>
</template>

<script>
export default {
    created() {
        if (this.$auth.check(1)) {
            this.$router.push({ name: 'admin'}).catch(()=>{});
        } else {
            this.$router.push({ name: 'vote'}).catch(()=>{});
        }
    }
}
</script>

<style scoped>

</style>
