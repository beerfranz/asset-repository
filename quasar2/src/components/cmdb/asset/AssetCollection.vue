<template>
  <Collection
    title="Assets"
    :api="asset"
    :columns="columns"
    :actions="actions"
    :urlHistory="urlHistory"
  />
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

</script>
