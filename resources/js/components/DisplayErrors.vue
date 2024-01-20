<template>
  <div class="alert alert-danger" id="validation-messages">
    <ul class="mb-0">
      <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
    </ul>
  </div>
</template>

<script lang="ts">
export default {
  name: "DisplayErrors",
  props: {
    errors: [Object, Array]
  }
}
</script>

