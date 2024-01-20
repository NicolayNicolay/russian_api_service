<template>
  <div class="row">
    <div class="col-md-12 col-lg-6">
      <div class="card">
        <div class="card-body">
          <template v-if="!loading">
            <ul class="alert alert-danger ps-4" v-if="this.errors.length > 0">
              <li v-for="(error,i) in this.errors" :key="i">{{error}}</li>
            </ul>

            <form @submit="formSubmit">

              <div class="form-group mb-4">
                <label for="role_name">Название роли</label>
                <input
                  type="text"
                  class="form-control form-control-sm r-25"
                  id="role_name"
                  v-model="form.name"
                  v-bind:disabled="disabled"
                  required
                >
              </div>

              <div class="form-group mb-4">
                <label for="display_name">Псевдоним</label>
                <input
                  type="text"
                  class="form-control form-control-sm r-25"
                  id="display_name"
                  v-model="form.display_name"
                  v-bind:disabled="disabled"
                >
              </div>

              <div class="form-group mb-4">
                <label for="description">Описание</label>
                <textarea
                  class="form-control form-control-sm r-25"
                  id="description"
                  rows="3"
                  v-model="form.description"
                  v-bind:disabled="disabled"
                ></textarea>
              </div>

              <div class="form-group mb-4" v-for="(permission,i) in permissions" :key="i">
                <input
                  type="checkbox"
                  class="form-check-input"
                  :id="permission.name"
                  v-model="permission.value"
                  v-bind:disabled="disabled"
                >
                <label :for="permission.name">{{ permission.display_name }}</label>
              </div>

              <div class="row">
                <div class="col-4">
                  <input
                    type="submit"
                    class="btn btn-primary btn-sm w-100"
                    value="Сохранить"
                    v-bind:disabled="disabled"
                  >
                </div>
                <div class="col-4">
                  <button class="btn btn-light btn-sm w-100" @click.prevent="goBack()">Назад</button>
                </div>
              </div>
            </form>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "RoleFormComponent",
  props: ["role_id"],
  data()
  {
    return {
      form: [],
      permissions: [],
      loading: false,
      errors: [],
      disabled: false,
    };
  },
  mounted() {
    this.setForm();
  },
  methods: {
    setForm() {
      this.loading = true;
      axios.get('/admin/role/get-form/' + (this.role_id ? this.role_id : ''))
        .then((response) => {
          this.form = response.data.form;
          this.permissions = response.data.permissions;
          this.disabled = this.form.name === 'admin';
        })
        .catch((error) => {
          this.errors = [];
          this.errors.push(error.response.data.msg)
          //console.error('Ошибка setForm Vue!', error.response.data.msg);
        })
        .finally(() => {
          this.loading = false;
        });
    },
    formSubmit(event)
    {
      event.preventDefault();
      if (!this.disabled) {
        axios.post('/admin/role/submit', {
          form: this.form,
          permissions: this.permissions
        })
          .then(response => {
            location.replace(response.data.redirect)
          })
          .catch(error => {
            //console.error('Ошибка formSubmit Vue!', error);
            this.errors = [];
            error.response.data.msg.forEach((error) => {
              this.errors.push(error);
            })
          });
      } else {
        this.errors.push('Невозможно редактировать роль Администратор');
      }
    },
    goBack() {
      window.history.back();
    },
  }
}
</script>
