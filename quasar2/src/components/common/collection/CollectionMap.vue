<template>
	<div>
		<h3>{{ title }}</h3>
		<div id="map" style="heught:90vh;"></div>
	</div>

</template>

<script setup>

import { ref, onMounted } from 'vue'
import "leaflet/dist/leaflet.css"
import * as L from 'leaflet'
import 'leaflet.markercluster/dist/MarkerCuster.css'
import 'leaflet.markercluster/dist/MarkerCluster.Default.css'
import 'leaflet.markercluster'

const initialMap = ref(null)

onMounted(() => {
	initialMap.value = L.map('map', { zoomControl: true, zoom:1, zoomAnimation: false, fadeAnimation: true, markerZoomAnimation: true }).setView([23.8041, 90.4152], 6);
	L.tilelayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
		maxZoom: 19,
		attribution: '&copy; <a href="http://www.openstreetmap.org/cpyright">OpenStreetMap</a>'
	}).addTo(initialMap.value)

	// Add marker to map directly
	L.marker([24.3746, 88.6004]).addTo(initialMap.value)
	L.marker([22.3752, 91.8349]).addTo(initialMap.value)

	// Add marker to cluster
	const markers = L.markerClusterGroup()

	const marker = new L.marker([23.3044, 89.9344]).bindPopup('hello')
	markers.addLayer(marker)

	initialMap.value.addLayer(markers)

})

</script>