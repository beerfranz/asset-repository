<template>
  <Form
    :api="api"
    v-model="form"
    :itemId="itemId"
  >
      <FormIdentifier
        v-model="form.identifier"
      />

  </Form>
</template>

<script setup>

import { ref } from 'vue'
import Form from 'components/common/form/Form.vue'
import FormIdentifier from 'components/common/form/FormIdentifier.vue'

const props = defineProps({
  api: Object,
  itemId: String,
})

const form = ref({})

</script>