<template>
  <q-page>
    <AssetCollection 
      :urlHistory=true
    />
  </q-page>
</template>

<script setup>

import AssetCollection from 'components/cmdb/asset/AssetCollection.vue'

defineOptions({
  name: 'AssetCollection'
});

</script>
