<template>
  <loading-component v-if="loading"></loading-component>
  <div class="card" v-else>
    <div class="card-body">
      <div v-if="success" class="alert alert-success mb-3" role="alert">
        Данные успешно изменены
      </div>
      <div class="alert alert-info mb-3">
        Список макропеременных
        <ul>
          <li v-for="(item, index) in description" :key="index">
            {{ index }}: {{ item }}
          </li>
        </ul>
      </div>
      <div v-for="(error, i) in showErrors" :key="i" class="alert alert-danger mb-3" role="alert">
        {{ error[0] }}
      </div>
      <form @submit.prevent="submitForm">
        <div class="row">
          <div class="col-lg-12" v-for="(fields,indexField) in this.form" :key="indexField">
            <div class="mb-3" v-if="fields.id">
              <label :for="fields.id" class="form-label" v-if="fields.type !== 'checkbox'">{{ fields.label }}</label>
              <input-text-component
                v-if="fields.type === 'text'"
                :name="fields.name"
                :id="fields.id"
                :model.sync="fields.value"
                class="form-control form-control-sm"
              ></input-text-component>
              <textarea-component
                v-if="fields.type === 'textarea'"
                :name="fields.name"
                :id="fields.id"
                :model.sync="fields.value"
                class="form-control form-control-sm"
              >
              </textarea-component>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="row">
              <div class="col-6">
                <button type="submit" class="btn btn-primary btn-sm btn-block">Сохранить</button>
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
import InputTextComponent from "../../FormInputs/InputTextComponent.vue";
import LoadingComponent from "../../LoadingComponent.vue";
import DisplayErrors from "../../DisplayErrors.vue";
import InputCheckBox from "../../FormInputs/InputCheckBox.vue";
import TextareaComponent from "../../FormInputs/TextAreaComponent.vue";

export default {
  name: "TemplateForm",
  components: {TextareaComponent, InputCheckBox, DisplayErrors, LoadingComponent, InputTextComponent},
  props: {
    id: {
      type: Number,
      default: 0,
    },
    backurl: '',
  },
  data() {
    return {
      loading: true,
      form: [],
      errors: null,
      updateErrors: 0,
      success: false,
      description: [],
      action: '',
      type: '',
    }
  },
  mounted() {
    this.getForm();
  },
  computed: {
    showErrors: function () {
      return this.errors
    },
  },
  methods: {
    getForm() {
      this.loading = true;
      axios.get('/admin/sms/templates/get_form' + (this.id !== 0 ? '/' + this.id : ''))
        .then((response) => {
          this.action = response.data.action;
          this.form = response.data.form;
          this.type = response.data.type;
          this.description = response.data.description;
        })
        .catch((error) => {
          if (error) {
            this.errors = error.response;
          } else {
            alert('An error has occurred');
          }
        })
        .finally(() => {
          this.loading = false;
        });
    },
    submitForm() {
      this.loading = true;
      axios.post(this.action, this.form)
        .then(() => {
          this.errors = null;
          if (this.type === 'add') {
            location.replace(this.backurl)
          } else {
            this.success = true;
            this.loading = false;
          }
        })
        .catch(error => {
          this.errors = error.response.data.msg;
          this.success = false;
          this.loading = false;
        }).finally(() => {
        this.updateErrors++;
      });
    }
  }
}
</script>

<style scoped>

</style>
