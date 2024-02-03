<template>
  <div class="row d-flex align-items-center mb-4">
    <div :class="find? 'col-md-10': 'col-md-11'">
      <nav v-if="paginated.total > paginated.per_page">
        <ul class="pagination mt-4">
          <li class="page-item" :class="(paginated.current_page === 1)? 'disabled':''" aria-disabled="true">
            <a class="page-link" :class="(paginated.current_page === 1)? 'disabled':''" href="#" @click.prevent="getList(getNewLink(paginated.first_page_url))" rel="next"><<</a>
          </li>
          <template v-for="(link,index) in paginated.links">
            <li class="page-item" :class="(paginated.current_page === 1)? 'disabled':''" v-if="index === 0">
              <a class="page-link" :class="(paginated.current_page === 1)? 'disabled':''" href="#" @click.prevent="getList(getNewLink(link.url))" rel="next"><</a>
            </li>
            <template v-if="index !==0 && index!==paginated.links.length - 1">
              <li class="page-item active" aria-current="page" v-if="link.active">
                <span class="page-link">{{ link.label }}</span>
              </li>
              <li class="page-item" v-else>
                <a class="page-link" href="#" @click.prevent="getList(getNewLink(link.url))">
                  {{ link.label }}
                </a>
              </li>
            </template>
            <li class="page-item" v-if="index === paginated.links.length - 1">
              <a class="page-link" :class="(link.url === null)? 'disabled':''" href="#" @click.prevent="getList(getNewLink(link.url))" rel="next">></a>
            </li>
          </template>
          <li class="page-item">
            <a class="page-link" :class="(paginated.current_page === getNewLink(paginated.last_page_url))? 'disabled':''" href="#" @click.prevent="getList(getNewLink(paginated.last_page_url))" rel="next">>></a>
          </li>
        </ul>
      </nav>
    </div>
    <div class="col-md-1" v-if="find">
      <div class="d-flex align-items-center">
        <label>Страница</label>
        <input
          name="page"
          id="page"
          class="form-control form-control-sm ms-2"
          @input="changedPage"
          v-model="page"
        >
      </div>
    </div>
    <div class="col-md-1" v-if="counts">
      <select-items-page-component :count="paginated.per_page" @updateCount="setCounts"/>
    </div>
  </div>
</template>

<script>
import SelectItemsPageComponent from "./selectItemsPageComponent.vue";
import VueInputText from "./FormInputs/InputTextComponent.vue";
import InputTextComponent from "./FormInputs/InputTextComponent.vue";
import _ from "lodash";

export default {
  name: "PaginationComponent",
  components: {InputTextComponent, VueInputText, SelectItemsPageComponent},
  props: {
    paginated: {},
    current_page: {},
    counts: {
      type: Boolean,
      default: false,
    },
    find: {
      type: Boolean,
      default: false,
    }
  },
  data() {
    return {
      page: 1,
    }
  },
  methods: {
    getNewLink(link) {
      if (link !== null) {
        return link.substring(link.indexOf('=') + 1);
      }
    },
    getList(data) {
      this.page = data;
      this.$emit('updateList', data);
    },
    setCounts(data) {
      this.$emit('updateCounts', data);
    },
    changedPage: _.debounce(function () {
      this.$emit('updateList', this.page);
    }, 500),
  }
}
</script>
