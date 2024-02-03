<template>
  <div class="row">
    <div class="col-lg-8" v-if="!loading">
      <div class="row mt-4" v-if="alert.text">
        <div class="col-lg-8">
          <div :class="'alert alert-'+ alert.type" role="alert">
            {{ alert.text }}
          </div>
        </div>
      </div>
      <div class="parts-wrapper table-scroll">
        <table class="table table-parts table-sm table-bordered table-hover">
          <thead class="table-light">
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Текст</th>
            <th scope="col">Статус</th>
            <th scope="col">Создан</th>
            <th scope="col">Действия</th>
          </tr>
          </thead>
          <tbody class="parts">
          <template v-if="this.data.data.length > 0">
            <tr v-for="(object, index) in this.data.data" :key="index">
              <td>{{ object.id }}</td>
              <td>{{ object.short_text }}</td>
              <td>
                <div :class="object.status_class" class="badge fs-7 fw-bolder">{{ object.status_name }}</div>
              </td>
              <td>{{ object.created_at }}</td>
              <td>
                <div class="dropdown">
                  <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                     aria-expanded="false">
                    <i class="fa-solid fa-ellipsis"></i>
                  </a>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" :href="'/admin/sms/list/show/'+object.id">Просмотр</a>
                    <a class="dropdown-item" href="#" @click.prevent="successItem(object.id)" v-if="!object.status">Подтвердить отправку</a>
                    <a class="dropdown-item" href="#" @click.prevent="removeItem(object.id)">Удалить отправку</a>
                  </div>
                </div>
              </td>
            </tr>
          </template>
          <tr v-else>
            <td colspan="5">
              Список пуст
            </td>
          </tr>
          </tbody>
        </table>
        <pagination-component :paginated="data" :current-page="page" @updateList="updateObjectsList"></pagination-component>
      </div>
    </div>
    <loading-component v-else></loading-component>
  </div>
</template>

<script>
import LoadingComponent from "../../LoadingComponent.vue";
import PaginationComponent from "../../PaginationComponent.vue";

export default {
  name: "SmsList",
  components: {PaginationComponent, LoadingComponent},
  props: {
    message: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      data: [],
      loading: true,
      page: 1,
      alert: {
        'type': 'success',
        'text': '',
      },
      update: 0,
    }
  },
  mounted() {
    this.getObjectsList();
    this.alert.text = this.message;
  },
  methods: {
    getObjectsList(page = 1) {
      axios.get('/admin/sms/list/apiList?page=' + page)
        .then((response) => {
          this.data = response.data;
        })
        .catch((error) => {
          if (error) {
            this.error = error.response;
            console.log(error);
          } else {
            alert('An error has occurred');
          }
        })
        .finally(() => {
          this.loading = false;
        });
    },
    updateObjectsList(data) {
      this.getObjectsList(data);
    },
    removeItem(id) {
      if (confirm('Вы действительно хотите удалить данный объект?')) {
        axios.get('/admin/sms/list/remove/' + id)
          .then(() => {
            this.getObjectsList();
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
    successItem(id) {
      if (confirm('Вы действительно хотите подтвердить отправку?')) {
        axios.get('/admin/sms/list/success/' + id)
          .then(() => {
            this.getObjectsList();
            this.alert.text = 'Начали выполнять рассылку!';
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
.dropdown-toggle::after {
  content: none;
}
</style>
