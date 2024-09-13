<template>
	<div class="q-pa-md" style="max-width: 400px">
		<q-form
			@submit="onSubmit"
			@reset="onReset"
			class="q-gutter-md"
		>
				<q-input
					filled
					label="username"
					hint="username"
					lazy-rules
					:rules="[ val => val && val.length > 0 || 'Please type something']"
				/>


			<div>
				<q-btn label="Submit" type="submit" color="primary"/>
			</div>
		</q-form>
	</div>
</template>

<script setup>

</script>
