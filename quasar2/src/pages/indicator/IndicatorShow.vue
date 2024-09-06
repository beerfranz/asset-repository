<template>
  <IndicatorShow 
    :itemId="$route.params.id"
  />
</template>

<script setup>
import IndicatorShow from 'components/indicator/indicator/IndicatorShow.vue'

defineOptions({
  name: 'IndicatorShow'
});

</script>
