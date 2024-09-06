<template>
  <Collection
    title="Assets"
    :api="asset"
    :columns="columns"
    :actions="actions"
  />
</template>

<script setup>

import Collection from 'components/common/collection/Collection.vue'
import { asset, columns, actions } from 'src/services/asset'

defineOptions({
  name: 'AssetCollection'
});

</script>
