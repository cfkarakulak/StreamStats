<template>
  <table-lite
    :is-static-mode="true"
    :columns="table.columns"
    :rows="table.rows"
    :total="table.totalRecordCount"
    :sortable="table.sortable"
  ></table-lite>
</template>

<script>
import { defineComponent, reactive, computed } from "vue";
import TableLite from "../Addons/TableLite.vue";

export default {
  name: "StreamsByViewers",
  components: {
    TableLite,
  },
  props: {
    data: {
      type: Object,
      required: true,
    }
  },
  setup(props) {
    const data = reactive([]);

    for (const key in props.data) {
        data.push({
          select: '<div class="select-this"><input type="checkbox" data-id="b4babce1babe5a453511d212"></div>',
          title: props.data[key].stream_title,
          viewers: props.data[key].number_of_viewers,
          game_name: props.data[key].game_name,
        });
    }

    const table = reactive({
      columns: [
        {
          label: "<div class='select-all inventory-checkbox'><input type='checkbox'></div>",
          field: "select",
          width: "0%",
          sortable: false,
          isKey: true,
        },
        {
          label: "Title",
          field: "title",
          width: "50%",
          sortable: true,
          isKey: true,
        },
        {
          label: "Game",
          field: "game_name",
          width: "30%",
          sortable: true,
        },
        {
          label: "Viewers",
          field: "viewers",
          width: "20%",
          sortable: true,
        },
      ],
      rows: data,
      totalRecordCount: computed(() => {
        return table.rows.length;
      }),
      sortable: {
        order: "id",
        sort: "asc",
      },
    });

    return {
      table,
    };
  },
}
</script>
