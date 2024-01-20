<template>
  <loading-component v-if="loading"></loading-component>
  <div class="content" v-else>
    <div class="row">
      <div class="col-12 col-md-8 table-scroll">
        <table class="table table-order table-sm table-bordered">
          <thead class="table-light">
          <tr>
            <th scope="col">Сезон</th>
            <th scope="col">Выгрузил</th>
            <th scope="col">Обработано строк</th>
            <th scope="col">Начало выгрузки</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>{{ data.season.name }}</td>
            <td>{{ data.user.name }}</td>
            <td>{{ data.processed }}</td>
            <td>{{ data.start_work }}</td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
<script>
import LoadingComponent from "../../LoadingComponent.vue";

export default {
  name: "HistoryComponent",
  components: {LoadingComponent},
  props: {
    id: {
      type: Number,
      default: 0,
    },
  },
  data() {
    return {
      loading: true,
      errors: null,
      data: [],
    }
  },
  mounted() {
    this.getData()
    this.startTimer();
  },
  methods: {
    getData() {
      axios.get('/admin/history/get_data' + (this.id !== 0 ? '/' + this.id : ''))
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
    startTimer() {
      this.interval = window.setInterval(() => {
        this.getData();
      }, 3000)
    },
  }
}
</script>
