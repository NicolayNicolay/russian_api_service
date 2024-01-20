<template>

    <div>
      <div class="sortable table-order-button d-flex align-items-center" style="cursor: pointer" @click="change">
        <span class="lh-1">{{ name }}</span>
        <span style="margin-left: 3px" v-if="sort_field == sort_current">
            <i v-if="sort_direction == 'desc'" class="fa-solid fa-arrow-down"></i>
            <i v-if="sort_direction == 'asc'" class="fa-solid fa-arrow-up"></i>
        </span>
      </div>
    </div>
  
  </template>
  
  <script>
  export default {
    props: {
      name: {
        default: 'Номер заказа',
        type: String
      },
      sort_current: {
        default: 'code',
        type: String
      },
      sort_field: {
        default: 'code',
        type: String
      },
      sort_direction: {
        default: 'asc',
        type: String
      }
    },
    methods: {
      change()
      {
        let sort_direction = this.sort_direction === 'asc' ? 'desc' : 'asc';
        this.$emit('change', this.sort_field, sort_direction);
      }
    },
    name: "SortComponent"
  }
  </script>
  