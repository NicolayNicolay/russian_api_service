<template>
  <loading-component v-if="loading"></loading-component>
  <div v-else>
    <div :class="'alert alert-'+ alert.type" role="alert" v-if="alert.text">
      {{ alert.text }}
    </div>
    <div v-for="(error, i) in showErrors" :key="i" class="alert alert-danger mb-3" role="alert">
      {{ error[0] }}
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="mb-3">
          <label>Статус</label>
          <div :class="data.class" class="badge d-table fw-bolder mt-2">{{ data.status_name }}</div>
        </div>
        <div class="mb-3">
          <label>Текст сообщения</label>
          <textarea-component
            name="text"
            id="text"
            :model.sync="data.text"
            :disable="true"
            class="form-control form-control-sm"
          >
          </textarea-component>
        </div>
      </div>
      <div class="col-xl-6">
        <div class="row">
          <div class="col-6" v-if="!data.status">
            <button class="btn btn-primary btn-sm btn-block" @click.prevent="successInit">Выполнить рассылку</button>
          </div>

          <div class="col-6">
            <a class="btn btn-light btn-sm btn-block" :href="backurl">Назад</a>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-4">
      <div class="col-xl-12">
        <div class="parts-wrapper table-scroll">
          <table class="table table-parts table-sm table-bordered table-hover">
            <thead class="table-light">
            <tr>
              <th scope="col">Номер телефона</th>
              <th scope="col">Текст SMS</th>
              <th scope="col">Действия</th>
            </tr>
            </thead>
            <tbody class="parts">
            <template v-if="this.data.phones.length > 0">
              <tr v-for="(object, index) in this.data.phones" :key="index">
                <td>{{ object.phone }}</td>
                <td>{{ object.text }}</td>
                <td>
                  <a href="#" @click.prevent="removeItem(object.id)" title="Удалить">
                    <i class="fa-solid fa-trash"></i>
                  </a>
                </td>
              </tr>
            </template>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import InputTextComponent from "../../FormInputs/InputTextComponent.vue";
import LoadingComponent from "../../LoadingComponent.vue";
import DisplayErrors from "../../DisplayErrors.vue";
import InputCheckBox from "../../FormInputs/InputCheckBox.vue";
import TextareaComponent from "../../FormInputs/TextAreaComponent.vue";
import PaginationComponent from "../../PaginationComponent.vue";

export default {
  name: "SmsShow",
  components: {PaginationComponent, TextareaComponent, InputCheckBox, DisplayErrors, LoadingComponent, InputTextComponent},
  props: {
    id: {
      type: Number,
      default: 0,
    },
    backurl: '',
    message: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      loading: true,
      data: [],
      errors: null,
      updateErrors: 0,
      success: false,
      alert: {
        'type': 'success',
        'text': '',
      },
    }
  },
  mounted() {
    this.getData();
    this.alert.text = this.message;
  },
  computed: {
    showErrors: function () {
      return this.errors
    },
  },
  methods: {
    getData() {
      this.loading = true;
      axios.get('/admin/sms/list/get_data/' + this.id)
        .then((response) => {
          this.data = response.data;
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
    successInit() {
      if (confirm('Вы действительно хотите подтвердить отправку?')) {
        axios.get('/admin/sms/list/success/' + this.id)
          .then(() => {
            this.getData();
            this.alert.text = 'Начали выполнять рассылку';
            setTimeout(() => {
              this.alert.text = '';
            }, 3000);
          })
          .catch((error) => {
            if (error) {
              this.error = error.response;
            } else {
              alert('An error has occurred');
            }
            this.loading = false;
          });
      }
    },
    removeItem(id) {
      if (confirm('Вы действительно хотите удалить данный объект?')) {
        axios.post('/admin/sms/list/removeElement', {
          id: this.id,
          orderId: id,
        })
          .then(() => {
            this.getData();
            this.alert.text = 'Объект удален!';
            this.update++;
            this.loading = false;
          })
          .catch((error) => {
            if (error) {
              this.error = error.response;
              this.alert.text = '';
              console.log(error);
            } else {
              alert('An error has occurred');
            }
            this.loading = false;
          });
      }
    },
  }
}
</script>

<style scoped>
</style>
