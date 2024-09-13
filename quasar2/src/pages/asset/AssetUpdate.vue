<template>
	<AssetForm 
		:itemId="$route.params.id"
	/>
</template>

<script setup>
import AssetForm from 'components/cmdb/asset/AssetForm.vue'

defineOptions({
  name: 'AssetUpdate'
});

</script>
