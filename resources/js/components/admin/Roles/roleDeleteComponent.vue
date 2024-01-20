<template>
  <a href="#" @click="deleteAction" title="Удалить">
    <i class="fa-solid fa-trash"></i>
  </a>
</template>

<script>
export default {
  name: "roleDeleteComponent",
  props: ["role_id"],
  methods: {
    deleteAction()
    {
      if (confirm('Вы уверены, что хотите удалить данную роль ?')) {
        location.replace('/admin/role/' + this.role_id + '/delete');
      } else {
        return;
      }
    }
  }
}
</script>
