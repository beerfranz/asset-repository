<template>
  
  <div class="q-pa-md">
    <q-table
      flat bordered
      class=""
      :grid="isGrid === 'true'"
      ref="tableRef"
      :title="title"
      :rows="rows"
      :columns="columns"
      row-key="identifier"
      v-model:pagination="pagination"
      :loading="loading"
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
  actions: Object,
  isGrid: String,
  selection: String,
  urlHistory: Boolean,
  loading: Boolean,
  rows: Array,
  classes: String,
})

const pagination = defineModel('pagination')

const emit = defineEmits(['onRequest'])
function onRequest(opts) {
  emit('onRequest', opts)
}

import { ref, onMounted } from 'vue'

const tableRef = ref()

// Selection
const selected = ref([])
function getSelectedString() {
  return selected.value.length === 0 ? '' : `${selected.value.length} record${selected.value.length > 1 ? 's' : ''} selected of ${pagination.value.rowsNumber}`
}

onMounted(() => {
  // get initial data from server (1st page)
  tableRef.value.requestServerInteraction()
})

</script>
