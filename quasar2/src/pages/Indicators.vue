<template>
  <q-page>
    <Table
      :columns="columns"
      title="Indicators"
      :api="api"
    />
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Table from 'components/collection/Table.vue';
import { Api } from '../services/apiPlatformBackend/api'

defineOptions({
  name: 'Indicators'
});

const columns = [
  {
    name: 'identifier',
    required: true,
    label: 'Identifier',
    align: 'left',
    field: row => row.identifier,
    format: val => `${val}`,
    sortable: true
  },
  { name: 'description', align: 'left', label: 'Description', field: 'description', sortable: false },
  { name: 'namespace', label: 'Namespace', field: 'namespace  ', sortable: true },
  { name: 'target', label: 'Target value', field: 'targetValue', sortable: true },
  { name: 'frequency', label: 'Frequency', field: row => row.frequency.description, sortable: false },
  { name: 'val1', label: 'Val1', field: row => row.valuesSample[0].value, sortable: false },
  { name: 'val2', label: 'Val2', field: row => row.valuesSample[1].value, sortable: false },
  { name: 'val3', label: 'Val3', field: row => row.valuesSample[2].value, sortable: false },
]

const api = new Api({ path: '/indicators' });

</script>
