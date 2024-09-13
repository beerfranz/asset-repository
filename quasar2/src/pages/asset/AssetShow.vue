<template>
	<AssetShow 
		:itemId="$route.params.id"
	/>
</template>

<script setup>
import AssetShow from 'components/cmdb/asset/AssetShow.vue'

defineOptions({
  name: 'AssetShow'
});

</script>
