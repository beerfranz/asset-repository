<template>
	<div class="q-pa-md" style="max-width: 400px">
		<q-form
			@submit="onSubmit"
			@reset="onReset"
			class="q-gutter-md"
		>
			<slot></slot>

			<div>
				<q-btn label="Submit" type="submit" color="primary"/>
				<q-btn label="Reset" type="reset" color="primary" flat class="q-ml-sm" />
			</div>
		</q-form>
	</div>
</template>

<script setup>
import { onMounted } from 'vue'

const props = defineProps({
  api: Object,
  itemId: {
		type: String,
		default: '',
  }
})

const form = defineModel()

const api = props.api

import { useRouter } from 'vue-router'
const router = useRouter()

import { useQuasar } from 'quasar'
const $q = useQuasar()

function getFormData() {
	return form.value
}

function onSubmit () {

	let promise = null

	if (props.itemId !== '') {
		promise = api.put(props.itemId, getFormData())
	} else {
		promise = api.post(getFormData())
	}
	
	promise
	.then(response => {

		$q.notify({
			color: 'green-4',
			textColor: 'white',
			icon: 'cloud_done',
			message: 'Submitted'
		})

	  router.push({ 'name': api.getRouteNameCollection() })
	})
	.catch(err => {
		$q.notify({
			color: 'red-4',
			textColor: 'white',
			icon: 'cloud_error',
			message: 'Error ' + err,
		})
	})
}

function onReset() {
	if (props.itemId !== '') {
		getServerData()
	} else {
		form.value = {}
	}
}

function getServerData() {
	api.getOne(props.itemId)
	.then(response => {
		form.value = response.data
	})
	.catch(err => {
		$q.notify({
			color: 'red-4',
			textColor: 'white',
			icon: 'cloud_error',
			message: 'Error ' + err,
		})
	})
}

onMounted(() => {
	onReset()
})

</script>
