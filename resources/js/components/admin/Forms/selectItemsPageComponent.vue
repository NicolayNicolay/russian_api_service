<template>
  <select class="form-select form-select-sm" name="count" @change="showCountItems" v-model="countItems">
    <option v-for="(s, v) in resCountingItems" :value="v">{{ v }}</option>
  </select>
</template>

<script>
export default {
  name: "selectItemsPageComponent",
  props: {
    "currentCountItems": Number
  },
  data()
  {
    return {
      countingItems: {
        100: false,
        200: false,
        500: false
      },
      countItems: (this.currentCountItems !== 0) ? this.currentCountItems : 100
    }
  },
  computed: {
    resCountingItems()
    {
      if (this.currentCountItems !== 0) {
        this.countingItems[this.currentCountItems] = true;
      }

      return this.countingItems;
    }
  },
  methods: {
    showCountItems()
    {
      let url = new URL(window.location.href);

      if (url.searchParams.has('count')) {
        url.searchParams.set('count', this.countItems);
      } else {
        url.searchParams.append('count', this.countItems);
      }
      window.location.href = url.href;
    }
  }
}
</script>
