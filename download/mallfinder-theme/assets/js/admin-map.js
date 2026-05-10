/**
 * MallFinder Admin Map Picker
 *
 * @package MallFinder
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        if (typeof L === 'undefined') {
            console.log('Leaflet not loaded');
            return;
        }

        var latInput = $('#mall_latitude');
        var lngInput = $('#mall_longitude');
        var mapContainer = document.getElementById('mall-map-picker');

        if (!mapContainer) return;

        // Get initial coordinates
        var lat = parseFloat(latInput.val()) || parseFloat(mallfinderAdmin.defaultLat);
        var lng = parseFloat(lngInput.val()) || parseFloat(mallfinderAdmin.defaultLng);

        // Initialize map
        var map = L.map(mapContainer).setView([lat, lng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add draggable marker
        var marker = L.marker([lat, lng], {
            draggable: true
        }).addTo(map);

        // Update inputs when marker is dragged
        marker.on('dragend', function(e) {
            var position = marker.getLatLng();
            latInput.val(position.lat.toFixed(6));
            lngInput.val(position.lng.toFixed(6));
        });

        // Update marker position when inputs change
        latInput.add(lngInput).on('change', function() {
            var newLat = parseFloat(latInput.val());
            var newLng = parseFloat(lngInput.val());
            
            if (!isNaN(newLat) && !isNaN(newLng)) {
                marker.setLatLng([newLat, newLng]);
                map.setView([newLat, newLng], 13);
            }
        });

        // Click on map to place marker
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            latInput.val(e.latlng.lat.toFixed(6));
            lngInput.val(e.latlng.lng.toFixed(6));
        });
    });

})(jQuery);
