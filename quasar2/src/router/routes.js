const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
  },
  {
    path: '/login',
    component: () => import('pages/profile/Login.vue'),
  },
  {
    path: '/assets',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { name: 'asset_collection', path: '', component: () => import('pages/asset/AssetCollection.vue') },
      { name: 'asset_create', path: 'create', component: () => import("pages/asset/AssetCreate.vue") },
      { name: 'asset_update', path: 'edit/:id/', component: () => import("pages/asset/AssetUpdate.vue") },
      { name: 'asset_show', path: ':id', component: () => import("pages/asset/AssetShow.vue") },
    ]
  },
  {
    path: '/indicators',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { name: 'indicator_collection', path: '', component: () => import('pages/indicator/IndicatorCollection.vue') },
      { name: 'indicator_create', path: 'create', component: () => import("pages/indicator/IndicatorCreate.vue") },
      { name: 'indicator_update', path: 'edit/:id/', component: () => import("pages/indicator/IndicatorUpdate.vue") },
      { name: 'indicator_show', path: ':id', component: () => import("pages/indicator/IndicatorShow.vue") },
    ]
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
