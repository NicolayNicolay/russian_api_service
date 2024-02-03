<template>
    <textarea
      v-if="disable"
      :id="id"
      :name="name"
      v-html="model_value"
      :maxlength="max"
      disabled="disabled"
      style="width: 100%; min-height: 80px;"
    >{{ model_value }}</textarea>
  <textarea
    v-else
    :id="id"
    :name="name"
    v-model="model_value"
    :maxlength="max"
    style="width: 100%; min-height: 80px;"
  >{{ model_value }}</textarea>
</template>

<script lang="ts">
export default {
  name: "TextareaComponent",
  props: {
    name: String,
    id: String,
    disable: {
      type: Boolean,
      default: false,
    },
    model: {
      type: [Number, String],
      default: ''
    },
    max: {
      type: Number,
      default: 1000,
    }
  },
  emits: ['update:model'],
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
}
</script>
