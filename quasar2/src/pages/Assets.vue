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
  name: 'Assets'
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
  {
    name: 'kindIdentifier',
    align: 'left',
    label: 'Kind',
    field: row => { 
      try { return row.kind.identifier }
      catch(e) { return '' }
    },
    sortable: false
  },
  {
    name: 'labels',
    align: 'left',
    label: 'Labels',
    field: row => {
      try {
        var labels = [];
        Object.keys(row.labels).forEach((label) => labels.push(label + '=' + row.labels[label]));
        return labels;
        // return labels.join(' ');
        var result = '';

        labels.forEach(function(d) {
          result += '<span class="badge badge-secondary">' + d + '</span>';
        });
        return result;
      } catch(e) { return 'none' }
    },

  }
]

const api = new Api({ path: '/entity/assets' });

</script>
