<template>
  <div>
      <p>Admin</p>
      <p>{{ $auth.user().name }}</p>
      <p>
          <img :src="$auth.user().avatar_original" alt="avatar" rel="noreferrer">
      </p>
      <a href="#" @click.prevent="$auth.logout()">Logout</a>

  </div>
</template>

<script>
export default {

}
</script>

<style scoped>

</style>
