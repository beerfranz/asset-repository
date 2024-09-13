<template>
  <IndicatorForm 
    :itemId="$route.params.id"
  />
</template>

<script setup>
import IndicatorForm from 'components/indicator/indicator/IndicatorForm.vue'

defineOptions({
  name: 'IndicatorUpdate'
});

</script>
