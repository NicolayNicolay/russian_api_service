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
              <input type="text" class="form-control form-control-sm" id="inputUserName" v-model="form.name">
            </div>
          </div>

          <div class="col-lg-12">
            <div class="mb-3">
              <label for="inputUserEmail" class="form-label">Email</label>
              <input type="text" class="form-control form-control-sm" id="inputUserEmail" v-model="form.email">
            </div>
          </div>

          <div class="col-lg-12">
            <div class="mb-3">
              <label for="inputUserNewPassword" class="form-label">Новый пароль</label>
              <input type="text" class="form-control form-control-sm" id="inputUserNewPassword" v-model="form.password">
            </div>
          </div>

          <div class="col-lg-12">
            <div class="mb-3">
              <label for="selectUserRole" class="form-label">Роль</label>
              <select class="form-select form-select-sm" id="selectUserRole" v-model="role" v-bind:disabled="admin">
                <option v-for="(role,i) in roles" :key="i" v-bind:value="role.id">
                  {{ role.display_name ? role.display_name : role.name }}
                </option>
              </select>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="row">
              <div class="col-6">
                <button class="btn btn-primary btn-sm btn-block">Сохранить</button>
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
  name: "userEditComponent",
  props: ["user", "roles", "backurl"],
  data()
  {
    return {
      form: {
        'id': this.user.id,
        'name': this.user.name,
        'email': this.user.email,
        'password': '',
      },
      role: this.user.role_id,
      base_role: 0,
      admin: false,
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
  mounted() {
    this.admin = this.form.id === 1;
    if (this.admin) {
      this.base_role = this.role;
    }
  },
  methods: {
    submitForm(event)
    {
      if (!this.admin || this.base_role === this.role) {
        axios.post('/admin/user/save', {
          form: this.form,
          role_id: this.role,
        })
          .then(response => {
            // console.log(response.data);
            this.success = response.data.msg;
            // location.replace(response.data.redirect)
          })
          .catch(error => {
            // console.error('Ошибка Login submitForm Vue!', error);
            this.errors = error.response.data.msg;
          });
      } else {
        this.errors = {'errors': ['Невозможно изменить роль у администратора']};
        this.role = this.base_role;
      }
    }
  }
}
</script>

<style scoped>

</style>
