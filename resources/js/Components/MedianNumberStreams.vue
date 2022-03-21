<template>
  <div class="number">
    <span>{{ data }}</span>
  </div>
</template>

<script>
export default {
  name: "MedianNumberStreams",
  props: {
    data: {
      type: Number,
      required: true,
    }
  },
}
</script>
