<template>
  <Toolbar :actions="['add']" @add="$emit('goToCreatePage')">
  </Toolbar>

  <div class="q-pa-md">
    <q-table
      flat bordered
      ref="tableRef"
      :title="title"
      :rows="rows"
      :columns="columns"
      row-key="id"
      v-model:pagination="pagination"
      :loading="loading"
      :filter="filter"
      binary-state-sort
      @request="onRequest"
    >
      <template #body-cell-actions="{ row }">
        <ActionCell
          :actions="['show', 'update', 'delete']"
          @show="$emit('goToShowPage(row)')"
          @update="goToUpdatePage(row)"
          @delete="deleteItem(row)"
        />
      </template>

      <template v-slot:top-right>
        <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </template>

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
import Toolbar from '../common/CommonToolbar.vue';
import Breadcrumb from '../common/CommonBreadcrumb.vue';
import ActionCell from '../common/CommonActionCell.vue';


const props = defineProps({
  title: String,
  columns: Array,
  api: Object,
  canCreate: Boolean,
  canShow: Boolean,
  canUpdate: Boolean,
  canDelete: Boolean,
})

const emit = defineEmits([
  'goToCreatePage',
  'goToShowPage',
  'goToUpdatePage',
  'deleteItem',
])

import { ref, onMounted } from 'vue'

const tableRef = ref()
const rows = ref([])
const filter = ref('')
const loading = ref(false)
const pagination = ref({
  sortBy: 'desc',
  descending: false,
  page: 1,
  rowsPerPage: 3,
  rowsNumber: 10
})

const api = props.api

function onRequest (props) {
  const { page, rowsPerPage, sortBy, descending } = props.pagination
  const filter = props.filter

  loading.value = true

  const res = api.get({
    page: page,
    itemsPerPage: rowsPerPage,
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
