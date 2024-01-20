<template>
  <div class="form-signin">
    <form @submit="submitForm">
      <img class="mb-4" :src="logo">
      <h1 class="h3 fw-bold">Регистрация</h1>
      <div class="mb-3">
        <small class="fw-bold">Пожалуйста, авторизуйтесь</small>
      </div>

      <div v-for="(error, i) in showErrors" :key="i" class="alert alert-danger" role="alert">
        {{ error[0] }}
      </div>

      <div class="form-floating mb-3">
        <input v-model="name" type="text" class="form-control" id="floatingInput">
        <label for="floatingInput">FIO</label>
      </div>

      <div class="form-floating mb-3">
        <input v-model="email" type="email" class="form-control" id="floatingInput">
        <label for="floatingInput">E-mail</label>
      </div>
      <div class="form-floating">
        <input v-model="password" type="password" class="form-control" id="floatingPassword">
        <label for="floatingPassword">Пароль</label>
      </div>

      <div class="checkbox mb-3">
        <label class="fw-bold" for="rememberInput">
          <input type="checkbox" id="rememberInput" @change="switchRemember" :value="remember">
          Запомнить меня
        </label>
      </div>
      <button class="w-100 btn btn-primary" type="submit">
        Войти
      </button>
    </form>
  </div>
</template>

<script>
export default {
  name: "loginComponent",
  props: ['logo'],
  data()
  {
    return {
      name: '',
      email: '',
      password: '',
      errors: {},
      remember: 0
    }
  },
  computed: {
    showErrors: function () {
      return this.errors
    }
  },
  methods: {
    switchRemember()
    {
      if (this.remember === 0) this.remember = 1
      else this.remember = 0
    },
    submitForm(event)
    {
      event.preventDefault();
      event.stopPropagation();

      axios.post('/admin/register', {name: this.name, email: this.email, password: this.password, remember: this.remember})
        .then(response => {
          location.replace(response.data.redirect)
        })
        .catch(error => {
          this.errors = error.response.data.msg;
        });
    }
  }
}
</script>

<style scoped>

</style>
