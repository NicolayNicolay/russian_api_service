<template>
  <div class="form-signin">
    <div class="card">
      <div class="card-body">
        <form @submit="submitForm">
          <div class="logo">
            <a href="/public">Военкор<br/>
              <span style="font-size: 18px;">Трек-номера</span>
            </a>
          </div>
          <hr>
          <div class="text-center">
            <h1 class="h2 fw-bold">Авторизация</h1>
            <div class="mb-3">
              Пожалуйста, авторизуйтесь
            </div>
          </div>

          <div v-for="(error, i) in showErrors" :key="i" class="alert alert-danger" role="alert">
            {{ error }}
          </div>

          <div class="form-floating">
            <input v-model="email" type="email" class="form-control" id="floatingEmail">
            <label for="floatingEmail">E-mail</label>
          </div>
          <div class="form-floating">
            <input v-model="password" type="password" class="form-control" id="floatingPassword">
            <label for="floatingPassword">Пароль</label>
          </div>

          <div class="checkbox mb-3">
            <label for="rememberInput">
              <input type="checkbox" id="rememberInput" name="remember" @change="switchRemember" :value="remember">
              Запомнить меня
            </label>
          </div>

          <button class="w-100 btn btn-lg btn-primary" type="submit">
            Войти
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "loginComponent",
  props: ['logo'],
  data()
  {
    return {
      email: '',
      password: '',
      errors: [],
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

      axios.post('/admin/login', {email: this.email, password: this.password, remember: this.remember})
        .then(response => {
          location.replace(response.data.redirect)
        })
        .catch(error => {
          // console.error('Ошибка Login submitForm Vue!', error);
          this.errors.push(error.response.data.msg)
        });
    }
  }
}
</script>

<style scoped>

</style>
