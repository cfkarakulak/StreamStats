<template>
  <ul>
    <li v-for="(item, index) in data">
      <div class="primary">
        <span>{{ index + 1 }}</span>
        <h6>{{ item.stream_title }}</h6>
      </div>

      <div class="secondary">
        <div class="rounded-hour">
          <span>{{ item.nearest_hour }}</span>
        </div>

        <div class="count">
          <small>Count:</small>
          <ins>{{ item.count }}</ins>
        </div>
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
