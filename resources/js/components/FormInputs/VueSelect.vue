<template>
  <!--    <div class="form-control form-control-sm" :class="errors && errors.has(name) ? 'is-invalid' : ''">-->
  <select class="form-select" :multiple="multiple" :name="name" :id="id"
          :disabled="disable" :data-live-search="live_search" v-model="selected" >
    <option v-if="default_nothing" value="" :disabled="disableDefault">{{ default_nothing_text }}</option>
    <!--    <option v-for="(item,index) in items" :value="item.id" v-html="getName(item)" :key="index"></option>-->
    <option v-for="(item,index) in items" :value="item.value" :key="index">{{ item.name }}</option>
  </select>
  <!--    </div>-->
</template>

<script>
export default {
  name: "VueSelect",
  props: {
    items: {
      type: [Array, Object]
    },
    model: '',
    name: String,
    id: String,
    errors: Object,
    live_search: {
      type: Boolean,
      default: false,
    },
    default_nothing: {
      type: Boolean,
      default: true,
    },
    default_nothing_text: {
      type: String,
      default: 'Ничего не выбрано',
    },
    disable: {
      type: Boolean,
      default: false,
    },
    multiple: {
      type: Boolean,
      default: false,
    },
    disableDefault: {
      type: Boolean,
      default: true
    }
  },
  data()
  {
    return {
      selected: this.model
    }
  },
  watch: {
    'selected': function () {
      this.$emit('update:model', this.selected);
      this.$emit('selected', this.selected)
    }
  },
  methods: {}
}
</script>
