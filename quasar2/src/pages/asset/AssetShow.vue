<template>
	<AssetShow 
		:api="asset"
		:itemId="$route.params.id"
	/>
</template>

<script setup>
import AssetShow from 'components/cmdb/asset/AssetShow.vue'
import { asset } from 'src/services/asset'

defineOptions({
  name: 'AssetShow'
});

</script>
