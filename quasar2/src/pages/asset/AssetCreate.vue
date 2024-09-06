<template>
	<AssetForm 
		:api="asset"
	/>
</template>

<script setup>
import AssetForm from 'components/cmdb/asset/AssetForm.vue'
import { asset } from 'src/services/asset'

defineOptions({
  name: 'AssetCreate'
});

</script>
