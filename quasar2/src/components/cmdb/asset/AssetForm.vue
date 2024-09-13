<template>
  <Form
    :api="asset"
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
import { asset } from 'src/services/asset'
import Form from 'components/common/form/Form.vue'
import FormIdentifier from 'components/common/form/FormIdentifier.vue'

const props = defineProps({
  itemId: String,
})

const form = ref({})

</script>