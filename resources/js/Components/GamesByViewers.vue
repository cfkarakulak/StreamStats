<template>
  <ul>
    <li v-for="(item, index) in data">
      <div class="primary">
        <span>{{ index + 1 }}</span>
        <h6>{{ item.game_name }}</h6>
      </div>

      <div class="secondary">
        <small>Number of viewers:</small>
        <ins>{{ item.number_of_viewers }}</ins>
      </div>
    </li>
  </ul>
</template>

<script>
export default {
  name: "GamesByViewers",
  props: {
    data: {
      type: Object,
      required: true,
    }
  },
}
</script>
