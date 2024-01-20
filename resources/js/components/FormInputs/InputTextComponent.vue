<template>
  <input :type="type"
         :id="id"
         :name="name"
         @input="input"
         v-model="model_value"
         :disabled="disable"
  />
</template>

<script>

export default {
  name: "VueInputText",
  props: {
    model: '',
    facadeMask: {
      type: String,
      default: '',
    },
    is_phone: {
      type: Boolean,
      default: false
    },
    legacy_mode: {
      type: Boolean,
      default: false
    },
    classes: {
      type: String,
      default: ''
    },
    autocomplete: {
      type: String,
      default: ''
    },
    type: {
      type: String,
      default: 'text'
    },
    name: String,
    label: String,
    id: String,
    errors: Object,
    /**
     * To disable the input
     */
    disable: {
      type: Boolean
    },
    readonly: {
      type: Boolean
    },
  },
  data() {
    return {
      model_value: this.model
    }
  },
  watch: {
    'model_value': function () {
      this.$emit('update:model', this.model_value);
    }
  },
  methods: {
    input() {
      this.$emit('oninput', this.model_value);
    },
  }
}
</script>
