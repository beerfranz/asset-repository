<template>
  <q-dialog :model-value="show" persistent>
    <q-card>
      <q-card-section class="row items-center">
        <q-avatar icon="warning" color="primary" text-color="white" />

        <span class="q-ml-sm">
          Delete ?
        </span>
      </q-card-section>

      <q-card-actions align="right">
        <q-btn
          flat
          label="Cancel"
          color="primary"
          v-close-popup
          @click="emitCancel"
        />

        <q-btn
          flat
          label="Delete"
          color="primary"
          v-close-popup
          @click="emitDelete"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { toRef } from 'vue';

const props = defineProps({
  show: Boolean,
})

let show = toRef(props, 'show');

const emit = defineEmits([
  'cancel', 'delete'
])

function emitCancel() {
  emit('cancel');
}

function emitDelete() {
  emit('delete');
}
</script>
