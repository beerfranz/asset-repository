<template>
  <Collection
    title="Assets"
    :api="asset"
    :columns="columns"
    :actions="actions"
    :urlHistory="urlHistory"
    v-model:filters="filters"
    :defaultFilters="defaultFilters"
  >

    <template v-slot:filter>
      <div class="q-gutter-md" style="max-width: 300px">
        <q-input
          filled
          clearable
          dense
          v-model="filters.identifier"
          label="Identifier"
          hint="Identifier"
        />
      </div>
    </template>

  </Collection>
</template>

<script setup>

import Collection from 'components/common/collection/Collection.vue'
import { asset, columns, actions } from 'src/services/asset'

defineProps({
  urlHistory: Boolean,
})

defineOptions({
  name: 'AssetCollection'
});

const defaultFilters = {
  identifier: null
}

import { ref } from 'vue'
const filters = ref({...defaultFilters})

</script>
