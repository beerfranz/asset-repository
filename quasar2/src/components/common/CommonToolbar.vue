<template>
  <q-toolbar class="q-my-md">
    <slot name="left" />

    <q-space />

    <div>
      <q-btn
        v-if="actions?.includes('submit')"
        label="submit"
        color="primary"
        icon="save"
        @click="emitSubmit"
      />

      <q-btn
        v-if="actions?.includes('reset')"
        label="reset"
        color="primary"
        flat
        class="q-ml-sm"
        icon="settings_backup_restore"
        @click="emitReset"
      />

      <q-btn
        v-if="actions?.includes('delete')"
        label="delete"
        color="primary"
        flat
        class="q-ml-sm"
        icon="delete"
        @click="toggleConfirmDelete"
      />

      <q-btn
        v-if="actions?.includes('add')"
        flat
        round
        dense
        icon="add"
        @click="emitAdd"
      />
    </div>

    <ConfirmDelete
      v-if="actions?.includes('delete')"
      :show="confirmDelete"
      @delete="emitDelete"
      @cancel="toggleConfirmDelete"
    />
  </q-toolbar>
</template>

<script setup>
import { ref, toRefs } from 'vue'
import ConfirmDelete from 'components/common/CommonConfirmDelete.vue'

const props = defineProps({
  actions: Array, // 'submit' | 'reset' | 'delete' | 'add'
});

const { actions } = toRefs(props)

const emit = defineEmits([
  'submit',
  'reset',
  'add',
  'delete',
])

function emitSubmit() {
  emit('submit')
}

function emitReset() {
  emit('reset')
}

function emitAdd() {
  emit('add')
}

function emitDelete() {
  emit('delete')
}

const confirmDelete = ref(false)

function toggleConfirmDelete() {
  confirmDelete.value = !confirmDelete.value
}
</script>
