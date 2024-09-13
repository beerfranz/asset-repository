<template>

  <Toolbar v-if="canAdd === true" :actions="['add']" @add="actions.goToCreatePage">
  </Toolbar>

  <DataFilter v-if="Object.keys(props.filters).length > 0" @reset="resetFilters" @filter="updateFilters">
    <template v-slot:filter>
      <slot name="filter"></slot>
    </template>
  </DataFilter>
  
	<CollectionTable v-if="presentation === 'table'"
		  :title="title"
    	:columns="columns"
      :actions="actions"
      :selection="selection"
      :urlHistory="urlHistory"
      :rows="rows"
      :loading="loading"
      @onRequest="getRows"
      v-model:pagination="pagination"
      :classes="classes"
	/>

  <CollectionTable v-if="presentation === 'grid'"
      :title="title"
      :columns="columns"
      :api="api"
      :actions="actions"
      isGrid=true
  />



</template>


<script setup>
import Toolbar from 'components/common/CommonToolbar.vue'
import DataFilter from 'components/common/CommonDataFilter.vue'
import CollectionTable from 'components/common/collection/CollectionTable.vue'

import { useRoute, useRouter } from 'vue-router'
const router = useRouter()
const route = useRoute()

import { useQuasar } from 'quasar'
const $q = useQuasar()

const props = defineProps({
  title: String,
  columns: Array,
  api: Object,
  actions: {
    type: Object,
    default: {},
  },
  presentation: {
  	type: String,
  	default: 'table',
  },
  selection: {
    type: String,
    default: 'none', // none, single, multiple
  },
  urlHistory: {
    type: Boolean,
    default: false,
  },
  defaultFilters: {
    type: Object,
    default: {},
  },
  classes: {
    type: String,
    default: '', // Check classes in this file: css/table.scss
  }
})

const filters = defineModel('filters')

props.actions.goToUpdatePage = function(item) {
  router.push({ 'name': props.api.getRouteNameEdit(), params: { id: item['id'] } })
}

props.actions.goToShowPage = function(item) {
  router.push({ 'name': props.api.getRouteNameShow(), params: { id: item['id'] } })
}

props.actions.goToCreatePage = function() {
  router.push({ 'name': props.api.getRouteNameCreate() })
}

props.actions.deleteItem = function(item) {
  props.api.delete(item['id'])
    .then(response => {
      $q.notify({
        color: 'green-4',
        textColor: 'white',
        icon: 'cloud_done',
        message: 'Deleted'
      })

      router.go()
    })
    .catch(err => {
      $q.notify({
        color: 'red-4',
        textColor: 'white',
        icon: 'cloud_error',
        message: 'Error ' + err,
      })
    })
}

const canAdd = typeof props.actions.canAdd === 'function' ? props.actions.canAdd({}) : false

const api = props.api

import { ref, onUpdated, toRaw, watch } from 'vue'
const rows = ref([])
const loading = ref(false)

const defaultPagination = {
  sortBy: 'identifier',
  descending: false,
  page: 1,
  rowsPerPage: 10,
}

const pagination = ref({
  sortBy: props.urlHistory ? (route.query.sortBy||defaultPagination.sortBy) : defaultPagination.sortBy,
  descending: props.urlHistory ? (route.query.sortTo == 'DESC' ? true : defaultPagination.descending) : defaultPagination.descending,
  page: props.urlHistory ? (route.query.page||defaultPagination.page) : defaultPagination.page,
  rowsPerPage: props.urlHistory ? (route.query.itemsPerPage||defaultPagination.rowsPerPage) : defaultPagination.rowsPerPage,
})

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

function getRows (opts = { pagination: defaultPagination }) {
  const { page, rowsPerPage, sortBy, descending } = opts.pagination

  loading.value = true

  let objFilter = toRaw(filters.value)

  // Save page state in the URL
  if (props.urlHistory === true) {
    let urlParams = {};

    if (page !== defaultPagination.page)
      urlParams.page = page

    if (rowsPerPage !== defaultPagination.rowsPerPage)
      urlParams.itemsPerPage = rowsPerPage

    if (sortBy !== defaultPagination.sortBy)
      urlParams.sortBy = sortBy

    if (descending !== defaultPagination.descending)
      urlParams.sortTo = descending ? 'DESC' : 'ASC'

    for (let filter in objFilter) {
      const val = objFilter[filter]
      if (val !== props.defaultFilters[filter])
        urlParams[filter] = val
    }

    let newURL = location.protocol + '//' + location.host + location.pathname
    if (Object.keys(urlParams).length > 0)
      newURL += "?" + urlSerialize(urlParams)

    window.history.pushState(urlParams, 'JS filter', newURL)

  }

  const res = api.get({
    page: page,
    itemsPerPage: rowsPerPage,
    sortBy: sortBy,
    sortDesc: descending,
    filters: objFilter,
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

const watchFilters = watch(filters, (filters, prevFilters) => {
  getRows()
}, { once: false })

function updateFilters() {
  getRows({ pagination: toRaw(pagination.value) })
}

function resetFilters() {
  filters.value = { ...props.defaultFilters }
}

</script>
