<template>
  <div class="row d-flex align-items-center mb-4">
    <div class="col-md-11">
      <nav v-if="paginated.total > paginated.per_page">
        <ul class="pagination mt-4">
          <li class="page-item" :class="(paginated.current_page == 1)? 'disabled':''" aria-disabled="true">
            <a class="page-link" :class="(paginated.current_page == 1)? 'disabled':''" href="#" @click.prevent="getList(getNewLink(paginated.first_page_url))" rel="next"><<</a>
          </li>
          <template v-for="(link,index) in paginated.links">
            <li class="page-item" :class="(paginated.current_page == 1)? 'disabled':''" v-if="index === 0">
              <a class="page-link" :class="(paginated.current_page == 1)? 'disabled':''" href="#" @click.prevent="getList(getNewLink(link.url))" rel="next"><</a>
            </li>
            <template v-if="index !=0 && index!=paginated.links.length - 1">
              <li class="page-item active" aria-current="page" v-if="link.active">
                <span class="page-link">{{ link.label }}</span>
              </li>
              <li class="page-item" v-else>
                <a class="page-link" href="#" @click.prevent="getList(getNewLink(link.url))">
                  {{ link.label }}
                </a>
              </li>
            </template>
            <li class="page-item" v-if="index == paginated.links.length - 1">
              <a class="page-link" :class="(link.url === null)? 'disabled':''" href="#" @click.prevent="getList(getNewLink(link.url))" rel="next">></a>
            </li>
          </template>
          <li class="page-item">
            <a class="page-link" :class="(paginated.current_page === getNewLink(paginated.last_page_url))? 'disabled':''" href="#" @click.prevent="getList(getNewLink(paginated.last_page_url))" rel="next">>></a>
          </li>
        </ul>
      </nav>
    </div>
    <div class="col-md-1" v-if="counts">
      <select-items-page-component :count="paginated.per_page" @updateCount="setCounts"/>
    </div>
  </div>
</template>

<script lang="ts">
import SelectItemsPageComponent from "./selectItemsPageComponent.vue";

export default {
  name: "PaginationComponent",
  components: {SelectItemsPageComponent},
  props: {
    paginated: {},
    current_page: {},
    counts: {
      type: Boolean,
      default: false,
    }
  },
  methods: {
    getNewLink(link) {
      if (link !== null) {
        return link.substring(link.indexOf('=') + 1);
      }
    },
    getList(data) {
      this.$emit('updateList', data);
    },
    setCounts(data) {
      this.$emit('updateCounts', data);
    }
  }
}
</script>
