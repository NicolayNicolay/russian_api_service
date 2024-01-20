<template>
  <div class="form-check">
    <input class="form-check-input"
           type="checkbox"
           :id="id"
           :name="name"
           v-model="model_value"
           :disabled="disable"
           :checked="model_value"
    >
    <label class="form-check-label" :for="id">
      {{ this.label }}
    </label>
  </div>
</template>


<script lang="ts">
export default {
  name: "InputCheckBox",
  props: {
    name: String,
    id: String,
    model: {
      type: [Boolean, Number],
      default: false
    },
    disable: {
      type: Boolean,
      default: false
    },
    label: {
      type: String
    }
  },
  emits: ['update:model'],
  data() {
    return {
      model_value: !!(this.model === 1 || this.model),
    }
  },
  watch: {
    'model_value': function () {
      this.$emit('update:model', this.model_value);
    }
  },
}
</script>
