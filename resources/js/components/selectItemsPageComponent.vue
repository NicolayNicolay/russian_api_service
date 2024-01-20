<template>
  <select class="form-select form-select-sm" name="count" @change="showCountItems" v-model="countItems">
    <option v-for="(s, v) in resCountingItems" :value="v">{{ v }}</option>
  </select>
</template>

<script>
export default {
  name: "selectItemsPageComponent",
  props: {
    count: {
      type: Number,
      default: 100,
    }
  },
  data()
  {
    return {
      countingItems: {
        100: false,
        200: false,
        500: false
      },
      countItems: (this.count !== 0) ? this.count : 100
    }
  },
  computed: {
    resCountingItems()
    {
      if (this.count !== 0) {
        this.countingItems[this.count] = true;
      }

      return this.countingItems;
    }
  },
  methods: {
    showCountItems()
    {
      this.$emit('updateCount', this.countItems);
    }
  }
}
</script>
