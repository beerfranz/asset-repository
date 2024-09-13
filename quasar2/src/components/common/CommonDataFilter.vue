<template>
  <q-expansion-item
    v-model="filtersExpanded"
    label="Filters"
    icon="search"
  >
    <q-card>
      <q-card-section @keydown.enter.prevent="emitFilter">
        <slot name="filter"></slot>
      </q-card-section>

      <q-card-section>
        <q-btn label="Filter" color="primary" @click="emitFilter" />

        <q-btn
          label="Reset"
          color="primary"
          flat
          class="q-ml-sm"
          @click="emitReset"
        />
      </q-card-section>
    </q-card>
  </q-expansion-item>
</template>

<script setup>
import { ref } from 'vue';

const filtersExpanded = ref(false);

const emit = defineEmits([
  'filter',
  'reset',
]);

function emitFilter() {
  emit('filter');
}

function emitReset() {
  emit('reset');
}
</script>
