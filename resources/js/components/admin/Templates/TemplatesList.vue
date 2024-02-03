<template>
  <div class="row mt-4">
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
            <th scope="col" width="5%">ID</th>
            <th scope="col" width="20%">Название</th>
            <th scope="col" width="45%">Текст</th>
            <th scope="col" width="20%">Создан</th>
            <th scope="col" width="10%">Действия</th>
          </tr>
          </thead>
          <tbody class="parts">
          <template v-if="this.data.data.length > 0">
            <tr v-for="(object, index) in this.data.data" :key="index">
              <td>{{ object.id }}</td>
              <td>{{ object.name }}</td>
              <td>{{ object.short_text }}</td>
              <td>{{ object.created_at }}</td>
              <td>
                <a class="me-2" :href="'/admin/sms/templates/edit/'+object.id" title="Редактировть">
                  <i class="fa-solid fa-user-pen"></i>
                </a>
                <a href="#" @click.prevent="removeItem(object.id)" title="Удалить">
                  <i class="fa-solid fa-trash"></i>
                </a>
              </td>
            </tr>
          </template>
          <tr v-else>
            <td colspan="5">
              Шаблоны не найдены
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
  name: "TemplatesList",
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
      axios.get('/admin/sms/templates/apiList?page=' + page)
        .then((response) => {
          this.data = response.data;
        })
        .catch((error) => {
          if (error) {
            this.error = error.response;
            console.log(error);
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
        axios.get('/admin/sms/templates/remove/' + id)
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
  }
}
</script>
