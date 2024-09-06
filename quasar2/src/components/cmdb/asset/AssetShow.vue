<template>
  <div class="q-pa-md page-show">
    <h1>Asset {{ item.kind?.identifier || '' }} {{ item['identifier'] }}</h1>

    <h2>Attributes</h2>
    <q-markup-table>
      <thead>
        <tr>
          <th>Attribute</th>
          <th>Value</th>
        </tr>
      </thead>
      <tbody>
        <template v-for="(value, attr) in item.attributes">
          <tr><td>{{ attr }}</td><td>{{ value }}</td></tr>
        </template>
      </tbody>
    </q-markup-table>

    <h2>Relations</h2>

    <h2>Instances</h2>

    <h2>Audits</h2>
    <q-markup-table>
      <thead>
        <tr>
          <th>Datetime</th>
          <th>Actor</th>
          <th>Action</th>
          <th>Data</th>
        </tr>
      </thead>
      <tbody>
        <template v-for="audit in item.assetAudits">
          <tr><td>{{ audit.datetime }}</td><td>{{ audit.actor }}</td><td>{{ audit.action }}</td><td>{{ audit.data }}</td></tr>
        </template>
      </tbody>
    </q-markup-table>

  </div>
</template>

<script setup>

import { ref } from 'vue'

const item = ref({})

const props = defineProps({
  api: Object,
  itemId: String,
})

function onReset() {
  getServerData()
}

import { useQuasar } from 'quasar'
const $q = useQuasar()

function getServerData() {
  props.api.getOne(props.itemId)
  .then(response => {
    item.value = response.data
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

import { onMounted } from 'vue'
onMounted(() => {
  onReset()
})

</script>
