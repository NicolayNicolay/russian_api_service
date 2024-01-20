<template>
  <button @click="deleteUser">
    <i class="fa-solid fa-trash"></i>
  </button>
</template>

<script>
export default {
  name: "userDeleteComponent",
  props: ["user_id"],
  methods: {
    deleteUser()
    {
      if (confirm('Вы уверены, что хотите удалить данного пользователя ?')) {
        location.replace('/admin/user/' + this.user_id + '/delete');
      } else {
        return;
      }
    }
  }
}
</script>
