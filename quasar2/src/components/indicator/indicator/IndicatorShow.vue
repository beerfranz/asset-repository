<template>
  <div class="q-pa-md page-show">
    <h1>Indicator {{ item['identifier'] }}</h1>

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

  </div>
</template>

<script setup>

import { ref } from 'vue'

import { indicator } from 'src/services/indicator'

const item = ref({})

const props = defineProps({
  itemId: String,
})

function onReset() {
  getServerData()
}

import { useQuasar } from 'quasar'
const $q = useQuasar()

function getServerData() {
  indicator.getOne(props.itemId)
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
