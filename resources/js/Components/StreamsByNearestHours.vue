<template>
  <ul>
    <li v-for="(item, index) in data">
      <div class="primary">
        <span>{{ index + 1 }}</span>
        <h6>{{ item.nearest_hour }}</h6>
      </div>

      <div class="secondary">
        <small>Streams started within:</small>
        <ins>{{ item.count }}</ins>
      </div>
    </li>
  </ul>
</template>

<script>
export default {
  name: "StreamsByNearestHours",
  props: {
    data: {
      type: Object,
      required: true,
    }
  },
}
</script>
