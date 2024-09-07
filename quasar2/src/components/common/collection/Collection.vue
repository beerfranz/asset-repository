<template>

  <Toolbar v-if="canAdd === true" :actions="['add']" @add="actions.goToCreatePage">
  </Toolbar>

  <DataFilter v-if="Object.keys(props.filters).length > 0">
    <template v-slot:filter>
      <slot name="filter"></slot>
    </template>
  </DataFilter>
  
	<CollectionTable v-if="presentation === 'table'"
		  :title="title"
    	:columns="columns"
    	:api="api"
      :actions="actions"
      :selection="selection"
      :urlHistory="urlHistory"
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

import { useRouter } from 'vue-router'
const router = useRouter()

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
  filters: {
    type: Object,
    default: {},
  }
})

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

</script>
