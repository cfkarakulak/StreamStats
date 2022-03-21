<template>
  <div class="number">
    <span>{{ minimumCount }}</span>
  </div>
</template>

<script>
export default {
  name: "MinimumViewerCount",
  props: {
    data: {
      type: Number,
      required: true,
    }
  },

  computed: {
    minimumCount() {
        return this.data < 0 ? `Already ${Math.abs(this.data)} more` : this.data;
    }
  }
}
</script>
