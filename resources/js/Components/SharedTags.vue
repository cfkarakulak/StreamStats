<template>
  <ul>
    <li v-for="item in data">
      <div class="tags">
        <span>{{ item.tag_id }}</span>
        <h6>{{ item.localization_names['en-us'] }}</h6>
        <p>{{ item.localization_descriptions['en-us'] }}</p>
      </div>
    </li>
  </ul>
</template>

<script>
export default {
  name: "SharedTags",
  props: {
    data: {
      type: Object,
      required: true,
    }
  },
}
</script>
