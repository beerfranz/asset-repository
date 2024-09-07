<template>
  <Collection
    title="Assets"
    :api="asset"
    :columns="columns"
    :actions="actions"
    :urlHistory="urlHistory"
    :filters="filters"
  >

    <template v-slot:filter>
      <q-input
        filled
        v-model="filters.identifier"
        label="Identifier"
        hint="Identifier"
      />
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

import { ref } from 'vue'
const filters = ref({
  identifier: null
})

</script>
