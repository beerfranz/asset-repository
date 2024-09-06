const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
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

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
