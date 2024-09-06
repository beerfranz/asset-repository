<template>
  
  <div class="q-pa-md">
    <q-table
      flat bordered
      :grid="isGrid === 'true'"
      ref="tableRef"
      :title="title"
      :rows="rows"
      :columns="columns"
      row-key="identifier"
      v-model:pagination="pagination"
      :loading="loading"
      :filter="filter"
      binary-state-sort
      @request="onRequest"
      :selection="selection"
      :selected-rows-label="getSelectedString"
      v-model:selected="selected"
    >
      <template #body-cell-actions="{ row }">
        <ActionCell
          :actions="['show', 'update', 'delete']"
          @show="actions.goToShowPage(row)"
          @update="actions.goToUpdatePage(row)"
          @delete="actions.deleteItem(row)"
        />
      </template>

<!--       <template v-slot:top-right>
        <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </template> -->

      <template v-slot:body-cell-labels="props">
        <q-td :props="props">
          <div v-for="label in props.value">
            <q-badge rounded color="purple" :label="label" />
          </div>
        </q-td>
      </template>

    </q-table>
  </div>
</template>

<script setup>
import ActionCell from 'components/common/CommonActionCell.vue';


const props = defineProps({
  title: String,
  columns: Array,
  api: Object,
  actions: Object,
  isGrid: String,
  selection: String,
})

import { ref, onMounted } from 'vue'

const tableRef = ref()
const rows = ref([])
const filter = ref('')
const loading = ref(false)
const pagination = ref({
  sortBy: 'identifier',
  descending: false,
  page: 1,
  rowsPerPage: 10,
})

// Selection
const selected = ref([])
function getSelectedString() {
  return selected.value.length === 0 ? '' : `${selected.value.length} record${selected.value.length > 1 ? 's' : ''} selected of ${pagination.value.rowsNumber}`
}

const api = props.api

function onRequest (props) {
  const { page, rowsPerPage, sortBy, descending } = props.pagination
  const filter = props.filter

  loading.value = true

  const res = api.get({
    page: page,
    itemsPerPage: rowsPerPage,
    sortBy: sortBy,
    sortDesc: descending,
  })

  res.then((response) => {
    pagination.value.rowsNumber = api.getTotalItems(response)
    rows.value = api.getData(response)

    pagination.value.page = page
    pagination.value.rowsPerPage = rowsPerPage
    pagination.value.sortBy = sortBy
    pagination.value.descending = descending

    loading.value = false
  })
}

onMounted(() => {
  // get initial data from server (1st page)
  tableRef.value.requestServerInteraction()
})

</script>
