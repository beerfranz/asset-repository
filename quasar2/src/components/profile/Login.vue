<template>
	<div class="q-pa-md" style="max-width: 400px">
		<q-form
			@submit="onSubmit"
			@reset="onReset"
			class="q-gutter-md"
		>
				<q-input
					filled
					v-model="username"
					label="username"
					hint="username"
					lazy-rules
					:rules="[ val => val && val.length > 0 || 'Please type something']"
				/>

				<q-input
					filled
					v-model="password"
					label="password"
					hint="password"
					type="password"
					lazy-rules
					:rules="[ val => val && val.length > 0 || 'Please type something']"
				/>

			<div>
				<q-btn label="Submit" type="submit" color="primary"/>
				<q-btn label="Create an account" type="button" color="secondary" flat class="q-ml-sm" />
			</div>
		</q-form>
	</div>
</template>

<script setup>

import { ref } from 'vue'

const username = ref(null)
const password = ref(null)

function onSubmit() {

}

function onReset() {

}

</script>
