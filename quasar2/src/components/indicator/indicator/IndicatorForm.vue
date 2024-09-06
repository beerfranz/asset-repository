<template>
  <Form
    :api="indicator"
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
import { indicator } from 'src/services/indicator'
import Form from 'components/common/form/Form.vue'
import FormIdentifier from 'components/common/form/FormIdentifier.vue'

const props = defineProps({
  itemId: String,
})

const form = ref({})

</script>
