<template>
  <q-layout view="hHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title>
          <div class="absolute-center">
            Asset repository
          </div>
        </q-toolbar-title>

        <div>Quasar v{{ $q.version }}</div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
    >
      <q-list>
        <q-item-label
          header
        >
          Navigation
        </q-item-label>

        <NavLink
          v-for="link in navList"
          :key="link.title"
          v-bind="link"
        />
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref } from 'vue'
import NavLink from 'components/Nav/NavLink.vue'
import { Api } from 'src/services/apiPlatformBackend/api'

defineOptions({
  name: 'MainLayout'
})

const api = new Api({ path: '/.well-known'})

const navList = ref([
  {
    title: 'Index',
    icon: 'code',
    link: '/'
  },
  {
    title: 'Assets',
    icon: 'school',
    link: '/assets'
  },
  {
    title: 'Settings',
    icon: 'settings',
    link: '/settings'
  },
  {
    title: 'Indicators',
    icon: 'troubleshoot',
    link: '/indicators'
  },
])

api.getOne('navigation.json')
  .then(response => {
    if (response.data.hasOwnProperty('assessment')) {
      navList.value.push({ title: 'Assessments', icon: 'checklist', link: '/assessments' })
    }
  })

const leftDrawerOpen = ref(false)

function toggleLeftDrawer () {
  leftDrawerOpen.value = !leftDrawerOpen.value
}
</script>
