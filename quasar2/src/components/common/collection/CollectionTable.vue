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
  urlHistory: Boolean,
})

import { ref, onMounted } from 'vue'

import { useRoute } from 'vue-router'
const route = useRoute()

const tableRef = ref()
const rows = ref([])
const filter = ref('')
const loading = ref(false)

const pagination = ref({
  sortBy: props.urlHistory ? (route.query.sortBy||'identifier') : 'identifier',
  descending: props.urlHistory ? (route.query.sortTo == 'DESC' ? true : false) : false,
  page: props.urlHistory ? (route.query.page||1) : 1,
  rowsPerPage: props.urlHistory ? (route.query.itemsPerPage||10) : 10,
})

// Selection
const selected = ref([])
function getSelectedString() {
  return selected.value.length === 0 ? '' : `${selected.value.length} record${selected.value.length > 1 ? 's' : ''} selected of ${pagination.value.rowsNumber}`
}

const api = props.api

function urlSerialize (obj) {
  var str = [];
  for (var p in obj)
    if (obj.hasOwnProperty(p)) {
      if (typeof(obj[p]) === 'object')
        for (var n in obj[p]) {
          str.push(encodeURIComponent(p) + "[" + n + "]=" + encodeURIComponent(obj[p][n]))
        }
      else {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
      }
    }
  return str.join("&");
}

function onRequest (opts) {
  const { page, rowsPerPage, sortBy, descending } = opts.pagination
  const filter = opts.filter

  loading.value = true

  // Save page state in the URL
  if (props.urlHistory === true) {
    var url = new URL(window.location);

    var params = {
      itemsPerPage: rowsPerPage,
      page: page,
      sortBy: sortBy,
      sortTo: descending ? 'DESC' : 'ASC'
    };

    // Add filters
    // $('.dataTableFilters').serializeArray().forEach(filter => {
    //   // console.log(filter);

    //   if (filter.value != '') {
    //     urlParams.set(filter.name, filter.value);
    //     params[filter.name] = filter.value;
    //   }
      
    //   // If multiple values:
    //   // urlParams.append('id', '101');
    //   // urlParams.append('id', '102');
    // });


    var newURL = location.protocol + '//' + location.host + location.pathname + "?" + urlSerialize(params)
    window.history.pushState(params, 'JS filter', newURL)

  }

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
