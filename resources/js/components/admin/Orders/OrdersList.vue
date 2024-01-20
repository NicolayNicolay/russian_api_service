<template>
  <div class="row">
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
      <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
      </symbol>
    </svg>
    <div class="col-lg-12" v-if="!loading">
      <div class="row mt-4" v-if="alert.text">
        <div class="col-lg-8">
          <div :class="'alert alert-'+ alert.type" role="alert">
            {{ alert.text }}
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-6 col-lg-2">
          <div class="filter-container">
            <div class="show-filter">
              <button class="btn btn-block btn-light btn-sm" @click.prevent="filterShow = !filterShow" data-bs-toggle="collapse" data-bs-target="#filter-panel" aria-expanded="true" aria-controls="filter-panel">
                <i class="fas fa-filter me-1"></i>Фильтр
              </button>
            </div>
          </div>
        </div>
        <div class="col-6 col-lg-3 ms-auto">
          <div class="d-flex">
            <a class="btn btn-primary btn-sm w-100 text-nowrap" href="/admin/orders/add" style="min-width: 90px">
              <i class="fas fa-plus me-1"></i>
              Импорт
            </a>
            <button class="btn btn-block btn-primary btn-sm ms-2 text-nowrap" @click.prevent="downloadOrders()" style="min-width: 90px">
              <i class="fas fa-file-download me-1"></i>Выгрузить
            </button>
          </div>
        </div>
      </div>
      <div class="row mt-4" v-if="ready_download">
        <div class="col-4">
          <div class="alert alert-success d-flex align-items-center justify-content-between mb-0" role="alert">
            <div class="d-flex align-items-center justify-content-between">
              <svg class="bi flex-shrink-0 me-3" width="24" height="24" role="img" aria-label="Success:">
                <use xlink:href="#check-circle-fill"/>
              </svg>
              Выгруженный файл готов к скачиванию
              <a :href="download_link" class="ms-1">Скачать</a>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <div id="filter-panel" class="accordion-collapse collapse mt-3" :class="filterShow ? 'show': ''" aria-labelledby="panelsStayOpen-headingOne">
            <div class="card">
              <div class="card-body">
                <form action="">
                  <div class="row">
                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label for="season" class="form-label">Сезон</label>
                        <select class="form-select form-select-sm" id="season" name="season" v-model="filter.season_id">
                          <option value="">Ничего не выбрано</option>
                          <option v-for="season in seasons" :value="season.value">{{ season.name }}</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label for="filterPart" class="form-label">Номер партии</label>
                        <input type="number" class="form-control form-control-sm" id="lot_number" name="lot_number" v-model="filter.lot_number">
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label for="filterOrderID" class="form-label">Номер заказа</label>
                        <input type="number" class="form-control form-control-sm" id="code" name="code" v-model="filter.code">
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label for="name" class="form-label">ФИО</label>
                        <input type="text" class="form-control form-control-sm" id="fio" name="fio" v-model="filter.fio">
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label for="phone" class="form-label">Номер телефона</label>
                        <input type="text" class="form-control form-control-sm" id="phone_relatives" name="phone_relatives" v-model="filter.phone_relatives">
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label for="filterOrderPrice1" class="form-label">Трек номер</label>
                        <input type="number" class="form-control form-control-sm" id="track" name="track" v-model="filter.track">
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="filter-title p-0 pb-2 mt-2 mb-2 border-bottom">
                        <h4 class="fw-bolder">Почтовое отправление</h4>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="mb-3">
                        <label for="stage" class="form-label">Стадия</label>
                        <select class="form-select form-select-sm" id="stage" name="stage" v-model="filter.stage">
                          <option value="">Ничего не выбрано</option>
                          <option v-for="stage in stages" :value="stage">{{ stage }}</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="mb-3">
                        <label for="state" class="form-label">Состояние</label>
                        <select v-if="filter.stage" class="form-select form-select-sm" id="state" name="state" v-model="filter.state">
                          <option value="">Ничего не выбрано</option>
                          <option v-for="status in statuses" v-if="filter.stage==status.stage && status.state" :value="status.state">{{ status.state }}</option>
                        </select>
                        <select v-else disabled class="form-select form-select-sm" id="state" name="state" v-model="filter.state">
                          <option value="">Ничего не выбрано</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-12">
                      <div class="mb-3">
                        <div class="row">
                          <div class="col-lg-6">
                            <label for="filterUploadDateStart" class="form-label">Дата операции</label>
                            <input type="date" class="form-control form-control-sm" id="created_at_start" name="created_at_start" v-model="filter.date_operation_start">
                          </div>
                          <div class="col-lg-6">
                            <label for="filterUploadDateEnd" class="form-label">&nbsp;</label>
                            <input type="date" class="form-control form-control-sm" id="created_at_end" name="created_at_end" v-model="filter.date_operation_end">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="filter-title p-0 pb-2 mt-2 mb-2 border-bottom">
                        <h4 class="fw-bolder">Наложенный платеж</h4>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="mb-3">
                        <label for="stage" class="form-label">Статус</label>
                        <select class="form-select form-select-sm" id="stage" name="stage" v-model="filter.payment_state">
                          <option value="">Ничего не выбрано</option>
                          <option v-for="state in payment_state" :value="state.id">{{ state.name }}</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="mb-3">
                        <div class="row">
                          <div class="col-lg-6">
                            <label for="filterUploadDateStart" class="form-label">Дата операции</label>
                            <input type="date" class="form-control form-control-sm" id="created_at_start" name="created_at_start" v-model="filter.payment_date_start">
                          </div>
                          <div class="col-lg-6">
                            <label for="filterUploadDateEnd" class="form-label">&nbsp;</label>
                            <input type="date" class="form-control form-control-sm" id="created_at_end" name="created_at_end" v-model="filter.payment_date_end">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12">
                      <div class="filter-title p-0 pb-2 mt-2 mb-2 border-bottom">
                        <h4 class="fw-bolder">Дополнительно</h4>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-12">
                      <div class="mb-3">
                        <div class="row">
                          <div class="col-lg-6">
                            <label for="filterUploadDateStart" class="form-label">Дата загрузки</label>
                            <input type="date" class="form-control form-control-sm" id="created_at_start" name="created_at_start" v-model="filter.created_at_start">
                          </div>
                          <div class="col-lg-6">
                            <label for="filterUploadDateEnd" class="form-label">&nbsp;</label>
                            <input type="date" class="form-control form-control-sm" id="created_at_end" name="created_at_end" v-model="filter.created_at_end">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-12">
                      <div class="mb-3">
                        <div class="row">
                          <div class="col-lg-6">
                            <label for="filterUploadDateStart" class="form-label">Дата изменения</label>
                            <input type="date" class="form-control form-control-sm" id="updated_at_start" name="updated_at_start" v-model="filter.updated_at_start">
                          </div>
                          <div class="col-lg-6">
                            <label for="filterUploadDateEnd" class="form-label">&nbsp;</label>
                            <input type="date" class="form-control form-control-sm" id="updated_at_end" name="updated_at_end" v-model="filter.updated_at_end">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-lg-6">
                      <input type="checkbox" id="filterShowAdd" name="showAdd" v-model="showUser">
                      <label for="filterShowAdd" class="form-label">Показывать дополнительную информацию</label>
                    </div>
                    <div class="col-lg-6">
                      <input type="checkbox" id="filterEmptyTrack" name="EmptyTrack" v-model="filter.empty_track">
                      <label for="filterEmptyTrack" class="form-label">Без трек-номера</label>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="row">
                        <div class="col-6 col-lg-6">
                          <button class="btn btn-primary btn-block" type="submit" @click.prevent="filterObject">
                            Показать
                          </button>
                        </div>
                        <div class="col-6 col-lg-6">
                          <button class="btn btn-light btn-block" @click.prevent="clearFilter">
                            Сбросить
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="parts-wrapper table-scroll overflow-x-scroll mt-4">
        <table class="table table-parts table-sm table-bordered table-hover">
          <thead class="table-light">
          <tr>
            <th scope="col" rowspan="2">Сезон</th>
            <th scope="col" rowspan="2" width="2%">
              <table-sort-title
                :name="'Номер партии'"
                :sort_field="'lot_number'"
                :sort_current="sorting.sort_field"
                :sort_direction="sorting.sort_direction"
                @change="changeSort">
              </table-sort-title>
            </th>
            <th scope="col" rowspan="2" width="2%">
              <table-sort-title
                :name="'Номер заказа'"
                :sort_field="'code'"
                :sort_current="sorting.sort_field"
                :sort_direction="sorting.sort_direction"
                @change="changeSort">
              </table-sort-title>
            </th>
            <th scope="col" rowspan="2">ФИО</th>
            <th scope="col" rowspan="2" v-if="showUser">Индекс</th>
            <th scope="col" rowspan="2" v-if="showUser">Область, край, республика</th>
            <th scope="col" rowspan="2" v-if="showUser">Район</th>
            <th scope="col" rowspan="2" v-if="showUser">Адрес (улица, дом,кв, нас. пункт)</th>
            <th scope="col" rowspan="2" v-if="showUser">Кому (ФИО родителей)</th>
            <th scope="col" rowspan="2">Номер телефона</th>
            <th scope="col" rowspan="2">Трек номер</th>
            <th scope="col" colspan="3">Почтовое отправление</th>
            <th scope="col" colspan="4">Наложенный платеж</th>
            <th scope="col" rowspan="2">
              <table-sort-title
                :name="'Загружено'"
                :sort_field="'created_at'"
                :sort_current="sorting.sort_field"
                :sort_direction="sorting.sort_direction"
                @change="changeSort">
              </table-sort-title>
            </th>
            <th scope="col" rowspan="2">
              <table-sort-title
                :name="'Обновлено'"
                :sort_field="'updated_at'"
                :sort_current="sorting.sort_field"
                :sort_direction="sorting.sort_direction"
                @change="changeSort">
              </table-sort-title>
            </th>
            <th scope="col" rowspan="2" v-if="showUser">Загрузил</th>
            <th scope="col" rowspan="2" v-if="showUser">Изменил</th>
            <th scope="col" rowspan="2">Действия</th>
          </tr>
          <tr>
            <th scope="col">Стадия</th>
            <th scope="col">Состояние</th>
            <th scope="col">
              <table-sort-title
                :name="'Дата операции'"
                :sort_field="'date_operation'"
                :sort_current="sorting.sort_field"
                :sort_direction="sorting.sort_direction"
                @change="changeSort">
              </table-sort-title>
            </th>
            <th scope="col">Статус</th>
            <th scope="col">Сумма</th>
            <th scope="col">
              <table-sort-title
                :name="'Дата операции'"
                :sort_field="'payment_date'"
                :sort_current="sorting.sort_field"
                :sort_direction="sorting.sort_direction"
                @change="changeSort">
              </table-sort-title>
            </th>
            <th scope="col">Индекс отделения</th>
          </tr>
          </thead>
          <tbody class="parts">
          <template v-if="this.data.data.length > 0">
            <tr v-for="(object, index) in this.data.data" :key="index" :style="object.table_color ? 'background-color:' + object.table_color : ''">
              <td>{{ object.season.name }}</td>
              <td>{{ object.lot_number }}</td>
              <td>{{ object.code }}</td>
              <td>{{ object.fio }}</td>
              <td v-if="showUser">{{ object.index }}</td>
              <td v-if="showUser">{{ object.geo }}</td>
              <td v-if="showUser">{{ object.district }}</td>
              <td v-if="showUser">{{ object.address }}</td>
              <td v-if="showUser">{{ object.fio_relatives }}</td>
              <td>{{ object.phone_relatives }}</td>
              <td>{{ object.track }}</td>
              <td>
                {{ object.status_name ? object.status_name.stage : '' }}
              </td>
              <td>
                {{ object.status_name ? object.status_name.state : '' }}
              </td>
              <td>
                {{ object.date_operation }}
              </td>
              <td>
                <span v-if="object.avail_payment">
                  {{ object.avail_payment.name }}
                </span>
                <span v-else-if="object.payment_status_name">
                      {{ object.payment_status_name.name }}
                </span>
              </td>
              <td>
                {{ object.payment_sum }}
              </td>
              <td>
                 <span v-if="object.avail_payment">
                  {{ object.avail_payment.date }}
                </span>
                <span v-else>
                  {{ object.payment_date }}
                </span>
              </td>
              <td>
                {{ object.payment_place_index }}
              </td>
              <td>{{ object.created_at }}</td>
              <td>{{ object.updated_at }}</td>
              <td v-if="showUser">{{ object.user_created.name }}</td>
              <td v-if="showUser">{{ object.user_updated.name }}</td>
              <td>
                <a class="me-2" target="_blank" :href="'https://www.pochta.ru/tracking?barcode='+object.track" title="Просмотр">
                  <i class="fa-solid fa-ellipsis"></i>
                </a>
                <a href="#" @click.prevent="removeItem(object.id)" title="Удалить">
                  <i class="fa-solid fa-trash"></i>
                </a>
              </td>
            </tr>
          </template>
          <tr v-else>
            <td colspan="16">
              Заказы не найдены
            </td>
          </tr>
          </tbody>
        </table>
        <pagination-component v-if="this.data.data.length > 0" :paginated="data" :current-page="page" :counts="true" @updateList="updateObjectsList" @updateCounts="setCounts"></pagination-component>
      </div>
    </div>
    <loading-component v-else></loading-component>
  </div>
</template>

<script>
import LoadingComponent from "../../LoadingComponent.vue";
import PaginationComponent from "../../PaginationComponent.vue";
import TableSortTitle from "../../TableSortTitle.vue";
import {isEmpty} from "lodash";

export default {
  name: "OrdersList",
  components: {PaginationComponent, LoadingComponent, TableSortTitle},
  props: {
    message: {
      type: String,
      default: ''
    },
    seasons: {
      type: Array,
      default: ''
    },
    statuses: {
      type: Array,
      default: ''
    },
  },
  data() {
    return {
      data: [],
      loading: true,
      page: 1,
      counts: 100,
      update: 0,
      filterShow: false,
      showUser: false,
      alert: {
        'type': 'success',
        'text': '',
      },
      stages: [],
      filter: {
        season_id: '',
        code: '',
        lot_number: '',
        fio: '',
        phone_relatives: '',
        track: '',
        status: '',
        stage: '',
        state: '',
        created_at_start: '',
        created_at_end: '',
        updated_at_start: '',
        updated_at_end: '',
        date_operation_start: '',
        date_operation_end: '',
        payment_date_start: '',
        payment_date_end: '',
        payd: '',
        payment_state: '',
        empty_track: '',
        empty_np: '',
      },
      sorting: {
        sort_field: 'code',
        sort_direction: 'asc',
      },
      download_link: '',
      ready_download: false,
      payment_state: [],
    }
  },
  mounted() {
    this.alert.text = this.message;
    this.getObjectsList();
    this.getStatusesStages();
  },
  methods: {
    filterObject() {
      this.filterShow = !this.filterShow;
      this.getObjectsList();
    },
    downloadOrders() {
      this.clearDownload();
      this.loading = true;
      axios.post('/admin/orders/getOrdersXls', {
        'filter': this.filter,
      })
        .then((response) => {
          if (response.data.status) {
            this.ready_download = true;
            this.download_link = response.data.path;
            this.alert.type = 'success';
            this.alert.text = '';
          } else {
            this.alert.type = 'danger';
            this.alert.text = 'При выгрузке заказов произошла ошибка: ' + response.data.errors;
          }
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
    clearDownload() {
      this.ready_download = false;
      this.download_link = '';
    },
    getObjectsList(page = 1) {
      this.page = page;
      axios.post('/admin/orders/ordersList',
        {
          'page': page,
          'counts': this.counts,
          'filter': this.filter,
          'sorting': this.sorting,
        })
        .then((response) => {
          this.data = response.data.orders;
          if (isEmpty(this.payment_state)) {
            this.payment_state = response.data.payment_state;
          }
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
    setCounts(data) {
      this.counts = data;
      this.getObjectsList();
    },
    clearFilter() {
      this.filter = {
        season_id: '',
        code: '',
        lot_number: '',
        fio: '',
        phone_relatives: '',
        track: '',
        status: '',
        stage: '',
        state: '',
        created_at_start: '',
        created_at_end: '',
        updated_at_start: '',
        updated_at_end: '',
        date_operation_start: '',
        date_operation_end: '',
        payment_date_start: '',
        payment_date_end: '',
        payd: '',
        payment_state: '',
      };
      this.showUser = false;
      this.getObjectsList();
    },
    removeItem(id) {
      if (confirm('Вы действительно хотите удалить данный объект?')) {
        axios.get('/admin/orders/remove/' + id)
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
    getDate(data) {
      let date = new Date(data);
      return date.toLocaleString("ru");
    },
    formatPhone(data) {
      return data.split(',');
    },
    /**
     * Установка сортировки
     * @param sort_field
     * @param sort_direction
     */
    changeSort(sort_field, sort_direction) {
      this.sorting.sort_field = sort_field;
      this.sorting.sort_direction = sort_direction;
      this.getObjectsList(1);
    },
    getStatusesStages() {
      let stages = [];
      this.statuses.forEach(status => {
        stages.push(status.stage);
      });
      this.stages = new Set(stages);
    },
    getPaymentState(value) {
      if (value == 3) {
        return 'Да';
      } else {
        return 'Нет';
      }
    }
  }
}
</script>
