<template>
  <div class="card">
    <div class="card-body">
      <div v-for="(success, i) in showSuccess" :key="i" class="alert alert-success mb-3" role="alert">
        {{ success[0] }}
      </div>

      <div v-for="(error, i) in showErrors" :key="i" class="alert alert-danger mb-3" role="alert">
        {{ error[0] }}
      </div>

      <form @submit.prevent="submitForm">
        <div class="row">
          <div class="col-lg-12">
            <div class="mb-3">
              <label for="inputUserName" class="form-label">ФИО</label>
              <input type="text" class="form-control form-control-sm" id="inputUserName" v-model="form.name" required>
            </div>
          </div>

          <div class="col-lg-12">
            <div class="mb-3">
              <label for="inputUserEmail" class="form-label">Email</label>
              <input type="text" class="form-control form-control-sm" id="inputUserEmail" v-model="form.email" required>
            </div>
          </div>

          <div class="col-lg-12">
            <div class="mb-3">
              <label for="inputUserPassword" class="form-label">Пароль</label>
              <input type="text" class="form-control form-control-sm" id="inputUserPassword" v-model="form.password" required>
            </div>
          </div>

          <div class="col-lg-12">
            <div class="mb-3">
              <label for="selectUserRole" class="form-label">Роль</label>
              <select class="form-select form-select-sm" id="selectUserRole" v-model="role_id">
                <option v-for="(role,i) in roles" :key="i" v-bind:value="role.id">
                  {{ role.display_name ? role.display_name : role.name }}
                </option>
              </select>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="row">
              <div class="col-6">
                <button class="btn btn-primary btn-sm btn-block">Добавить</button>
              </div>

              <div class="col-6">
                <a class="btn btn-light btn-sm btn-block" :href="backurl">Назад</a>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: "userAddComponent",
  props: ["roles", "backurl"],
  data()
  {
    return {
      form: {
        'name': '',
        'email': '',
        'password': '',
      },
      role_id: 0,
      errors: {},
      success: {}
    }
  },
  computed: {
    showErrors: function () {
      return this.errors
    },
    showSuccess: function () {
      return this.success
    },
  },
  methods: {
    submitForm(event)
    {
      axios.post('/admin/user/add', {
        form: this.form,
        role_id: this.role_id,
      })
        .then(response => {
          // console.log(response.data);
          this.success = response.data.msg;

          setTimeout(() => {
            location.replace(response.data.redirect)
          }, 1000);
        })
        .catch(error => {
          // console.error('Ошибка Login submitForm Vue!', error);
          this.errors = error.response.data.msg;
        });
    }
  }
}
</script>

<style scoped>

</style>
