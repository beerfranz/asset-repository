<template>
  <q-td auto-width>
    <q-btn
      v-if="actions.includes('show')"
      flat
      round
      dense
      color="secondary"
      icon="format_align_justify"
      @click="emitShow"
    />

    <q-btn
      v-if="actions.includes('update')"
      flat
      round
      dense
      color="secondary"
      icon="edit"
      @click="emitUpdate"
    />

    <q-btn
      v-if="actions.includes('delete')"
      icon="delete"
      flat
      round
      dense
      color="secondary"
      @click="toggleConfirmDelete"
    />

    <ConfirmDelete
      v-if="actions.includes('delete')"
      :show="confirmDelete"
      @delete="emitDelete"
      @cancel="toggleConfirmDelete"
    />
  </q-td>
</template>

<script setup>
import { ref, toRefs } from 'vue'
import ConfirmDelete from 'components/common/CommonConfirmDelete.vue'

const props = defineProps({
  actions: Object
});

const { actions } = toRefs(props)

const emit = defineEmits([
  'show', 'update', 'delete'
])

function emitShow() {
  emit('show')
}

function emitUpdate() {
  emit('update')
}

function emitDelete() {
  emit('delete')
}

const confirmDelete = ref(false)

function toggleConfirmDelete() {
  confirmDelete.value = !confirmDelete.value
}
</script>
