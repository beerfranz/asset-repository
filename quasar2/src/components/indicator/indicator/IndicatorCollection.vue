<template>
  <Collection
    title="Indicators"
    :api="indicator"
    :columns="columns"
    :actions="actions"
  />
</template>

<script setup>

import Collection from 'components/common/collection/Collection.vue'
import { indicator, columns, actions } from 'src/services/indicator'

defineOptions({
  name: 'IndicatorCollection'
});

</script>
