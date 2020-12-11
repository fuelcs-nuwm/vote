<template>
    <div class="content container flex-grow-1">

        <p>{{ $auth.check('admin') }}</p>
    </div>
</template>

<script>
export default {
    mounted() {
        console.log(this.$auth.user())
        console.log(this.$auth.check('admin'))

        console.log( this.parseJwt (this.$auth.token() ) );
    },

    methods: {
        parseJwt (token) {
            var base64Url = token.split('.')[1];
            var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            var jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));

            return JSON.parse(jsonPayload);
        }
    }
}
</script>

<style scoped lang="scss">

.content {
    background-color: rgb(241, 241, 241);
}

</style>
