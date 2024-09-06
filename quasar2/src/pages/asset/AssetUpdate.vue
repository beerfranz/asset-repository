<template>
	<AssetForm 
		:api="asset"
		:itemId="$route.params.id"
	/>
</template>

<script setup>
import AssetForm from 'components/cmdb/asset/AssetForm.vue'
import { asset } from 'src/services/asset'

defineOptions({
  name: 'AssetUpdate'
});

</script>
