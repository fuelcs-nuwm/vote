<template>
    <div class="wrapper h-100 d-flex flex-column">
        <router-view></router-view>
    </div>
</template>

<script>
export default {
name: "Root"
}
</script>

<style scoped lang="scss">

.wrapper {
    background-image: url(/img/background.jpg);
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
}

</style>
