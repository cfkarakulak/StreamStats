<template>
  <ul>
    <li v-for="item in data">
      <div class="following">
        <span>{{ item.game_name }}</span>
        <h6>{{ item.stream_title }}</h6>
      </div>
    </li>
  </ul>
</template>

<script>
export default {
  name: "StreamsFollowedByUser",
  props: {
    data: {
      type: Object,
      required: true,
    }
  },
}
</script>
