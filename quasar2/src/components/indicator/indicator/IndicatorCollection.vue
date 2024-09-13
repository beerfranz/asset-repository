<template>
  <Collection
    title="Indicators"
    :api="indicator"
    :columns="columns"
    :actions="actions"
    :urlHistory="urlHistory"
  />
</template>

<script setup>

import Collection from 'components/common/collection/Collection.vue'
import { indicator, columns, actions } from 'src/services/indicator'

defineProps({
  urlHistory: Boolean,
})

defineOptions({
  name: 'IndicatorCollection'
});

</script>
