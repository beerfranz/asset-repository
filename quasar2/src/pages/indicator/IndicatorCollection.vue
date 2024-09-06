<template>
  <q-page>
    <IndicatorCollection
      :urlHistory=true
    />
  </q-page>
</template>

<script setup>

import IndicatorCollection from 'components/indicator/indicator/IndicatorCollection.vue'

defineOptions({
  name: 'IndicatorCollection'
});

</script>
