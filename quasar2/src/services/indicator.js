import { Api } from './apiPlatformBackend/api'
import { useRouter } from "vue-router";

const indicator = new Api({ path: '/indicators', route_name_prefix: 'indicator' });

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
  { name: 'namespace', align: 'left', label: 'Namespace', field: 'namespace', sortable: true },
  { name: 'target', align: 'left', label: 'Target value', field: 'targetValue', sortable: true },
  { name: 'frequency', align: 'left', label: 'Frequency', field: row => row.frequency.description, sortable: false },
  { name: 'val1', label: 'Val1', field: row => row.valuesSample[0].value, sortable: false },
  { name: 'val2', label: 'Val2', field: row => row.valuesSample[1].value, sortable: false },
  { name: 'val3', label: 'Val3', field: row => row.valuesSample[2].value, sortable: false },
]

const actions = {
  canAdd: function(opts) {
    return true
  }
}


export {
	indicator,
	columns,
	actions,
}
