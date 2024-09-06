import { Api } from './apiPlatformBackend/api'
import { useRouter } from "vue-router";

const asset = new Api({ path: '/entity/assets', route_name_prefix: 'asset' });

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
      } catch(e) { return 'none' }
    },
  },
  {
    name: 'actions',
    label: 'Actions',
  }
]

const actions = {
  canAdd: function(opts) {
    return true
  }
}


export {
	asset,
	columns,
	actions,
}
