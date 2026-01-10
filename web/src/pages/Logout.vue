<template>
  <div class="fullscreen">&nbsp;</div>
</template>

<script>
import {useUsersStore} from 'stores/users';

export default {
  name: 'LogoutPage',
  mounted() {
    useUsersStore().logout().finally(() => {
      window.location.href = '/';
    });
  },
}
</script>
