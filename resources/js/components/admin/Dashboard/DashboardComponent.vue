<template>
  <loading-component v-if="loading"></loading-component>
  <div class="content" v-else>
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
    </div>
    <div class="row mt-2">
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
                      <label for="lot_number" class="form-label">Номер партии</label>
                      <input type="text" class="form-control form-control-sm" id="lot_number" name="lot_number" v-model="filter.lot_numbers">
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="mb-3">
                      <label for="geo" class="form-label">Пункт назначения</label>
                      <input type="text" class="form-control form-control-sm" id="geo" name="geo" v-model="filter.geo">
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="mb-3">
                      <label for="address" class="form-label">Адрес</label>
                      <input type="text" class="form-control form-control-sm" id="address" name="address" v-model="filter.address">
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="mb-3">
                      <label for="fio" class="form-label">ФИО</label>
                      <input type="text" class="form-control form-control-sm" id="fio" name="fio" v-model="filter.fio">
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="mb-3">
                      <label for="phone_relatives" class="form-label">Телефон</label>
                      <input type="text" class="form-control form-control-sm" id="phone_relatives" name="phone_relatives" v-model="filter.phone_relatives">
                    </div>
                  </div>
                  <div class="col-lg-12">
                    <div class="mb-3">
                      <div class="row">
                        <div class="col-lg-6">
                          <label for="date_start" class="form-label">Дата</label>
                          <input type="date" class="form-control form-control-sm" id="date_start" name="date_start" v-model="filter.date_start">
                        </div>
                        <div class="col-lg-6">
                          <label for="date_end" class="form-label">&nbsp;</label>
                          <input type="date" class="form-control form-control-sm" id="date_end" name="date_end" v-model="filter.date_end">
                        </div>
                      </div>
                    </div>
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
    <div class="row mt-4">
      <div class="col-12 col-xl-10">
        <div class="parts-wrapper">
          <div class="col-sm-12 border-bottom pb-2 mb-4">
            <h5>
              <strong>Выплаты, руб.</strong>
            </h5>
          </div>
          <div class="row">
            <div class="col-sm-6 col-lg-3">
              <div class="card shadow-sm mb-4">
                <div class="card-body py-3">
                  <div class="text-muted">Всего</div>
                  <div class="dashboard-link">{{ getFormatPrice(data.payments.total) }}</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card shadow-sm mb-4">
                <div class="card-body py-3">
                  <div class="text-muted">Индекс 344000</div>
                  <div class="dashboard-link">{{ getFormatPrice(data.payments.index_344000) }}</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card shadow-sm mb-4">
                <div class="card-body py-3">
                  <div class="text-muted">
                    Индекс 3444999
                  </div>
                  <div class="dashboard-link">{{ getFormatPrice(data.payments.index_344999) }}</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card shadow-sm mb-4">
                <div class="card-body py-3">
                  <div class="text-muted">Другой индекс</div>
                  <div class="dashboard-link">{{ getFormatPrice(data.payments.index_another) }}</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card shadow-sm mb-4">
                <div class="card-body py-3">
                  <div class="text-muted">
                    Прием
                  </div>
                  <div class="dashboard-link">{{ getFormatPrice(data.payments.reception) }}</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card shadow-sm mb-4">
                <div class="card-body py-3">
                  <div class="text-muted">Другая стадия</div>
                  <div class="dashboard-link">{{ getFormatPrice(data.payments.another_state) }}</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-12 border-bottom pb-2 mb-4">
            <h5>
              <strong>Не оплаченные, шт.</strong>
            </h5>
          </div>
          <div class="row">
            <div class="col-sm-6 col-lg-3">
              <div class="card shadow-sm mb-4">
                <div class="card-body py-3">
                  <div class="text-muted">Всего</div>
                  <div class="dashboard-link">{{ data.not_payment.total }}</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card shadow-sm mb-4">
                <div class="card-body py-3">
                  <div class="text-muted">Лежат на почте</div>
                  <div class="dashboard-link">{{ data.not_payment.on_post }}</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card shadow-sm mb-4">
                <div class="card-body py-3">
                  <div class="text-muted">В пути</div>
                  <div class="dashboard-link">{{ data.not_payment.on_way }}</div>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="card shadow-sm mb-4">
                <div class="card-body py-3">
                  <div class="text-muted">Другое</div>
                  <div class="dashboard-link">{{ data.not_payment.on_another }}</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-12 border-bottom pb-2 mb-4">
            <h5>
              <strong>
                Процент выкупа</strong>
            </h5>
          </div>
          <div class="row">
            <div class="col-sm-6 col-lg-3">
              <div class="card shadow-sm mb-4">
                <div class="card-body py-3">
                  <div class="dashboard-link">{{ data.percentage }}%</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import LoadingComponent from "../../LoadingComponent.vue";

export default {
  name: "DashboardComponent",
  components: {LoadingComponent},
  props: {
    seasons: {
      type: Array,
      default: [],
    },
  },
  data() {
    return {
      filterShow: false,
      loading: true,
      errors: null,
      data: [],
      filter: {
        season_id: '',
        lot_numbers: '',
        date_start: '',
        date_end: '',
        geo: '',
        address: '',
        fio: '',
        phone_relatives: '',

      },
    }
  },
  mounted() {
    this.getData()
  },
  methods: {
    filterObject() {
      this.filterShow = !this.filterShow;
      this.getData();
    },
    clearFilter() {
      this.filter = {
        season_id: '',
        lot_numbers: '',
        date_start: '',
        date_end: '',
        geo: '',
        address: '',
        fio: '',
        phone_relatives: '',

      };
      this.getData();
    },
    getData() {
      axios.post('/admin/dashboard/get_data', {
        'filter': this.filter,
      })
        .then((response) => {
          this.data = response.data;
          console.log(this.data);
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
    getFormatPrice(data) {
      let price = Math.round(data)
      if (Number.isInteger(price)) {
        return parseFloat(String(price)).toLocaleString('en').replaceAll(',', ' ');
      } else {
        return parseFloat(Number(price).toFixed(2)).toLocaleString('en').replaceAll(',', ' ');
      }
    },
  }
}
</script>
