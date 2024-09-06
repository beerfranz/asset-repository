<template>
	<q-input
		filled
		v-model="identifier"
		label="Identifier"
		hint="Your uniq identifier"
		lazy-rules
		:rules="[ val => val && val.length > 0 || 'Please type something']"
	/>
</template>

<script setup>
const identifier = defineModel()
</script>