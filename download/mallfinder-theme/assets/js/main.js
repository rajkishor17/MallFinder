/**
 * MallFinder Theme Main JavaScript
 *
 * @package MallFinder
 */

(function() {
    'use strict';

    // Document Ready
    document.addEventListener('DOMContentLoaded', function() {
        initMobileMenu();
        initSearchBox();
        initMap();
    });

    /**
     * Mobile Menu Toggle
     */
    function initMobileMenu() {
        const menuToggle = document.querySelector('.menu-toggle');
        const mainNav = document.querySelector('.main-nav');
        
        if (menuToggle && mainNav) {
            menuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                mainNav.classList.toggle('active');
                
                // Update icon
                const isActive = mainNav.classList.contains('active');
                menuToggle.innerHTML = isActive 
                    ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>'
                    : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>';
            });

            // Close menu on link click
            mainNav.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', function() {
                    mainNav.classList.remove('active');
                });
            });
        }
    }

    /**
     * Search Box Enhancements
     */
    function initSearchBox() {
        // Location cascading dropdowns
        const stateSelect = document.getElementById('filter-state');
        const citySelect = document.getElementById('filter-city');
        const areaSelect = document.getElementById('filter-area');

        if (stateSelect && citySelect) {
            stateSelect.addEventListener('change', function() {
                const stateId = this.value;
                
                // Reset city and area
                citySelect.innerHTML = '<option value=""><?php _e('All Cities', 'mallfinder'); ?></option>';
                if (areaSelect) {
                    areaSelect.innerHTML = '<option value=""><?php _e('All Areas', 'mallfinder'); ?></option>';
                    areaSelect.disabled = true;
                }
                
                if (!stateId) {
                    citySelect.disabled = true;
                    return;
                }

                // Fetch cities via AJAX
                if (typeof mallfinderData !== 'undefined') {
                    fetch(mallfinderData.ajaxUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=mallfinder_get_cities_by_state&nonce=' + mallfinderData.nonce + '&state_id=' + stateId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.cities) {
                            citySelect.disabled = false;
                            data.data.cities.forEach(function(city) {
                                const option = document.createElement('option');
                                option.value = city.id;
                                option.textContent = city.title;
                                citySelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        }

        if (citySelect && areaSelect) {
            citySelect.addEventListener('change', function() {
                const cityId = this.value;
                
                // Reset area
                areaSelect.innerHTML = '<option value=""><?php _e('All Areas', 'mallfinder'); ?></option>';
                
                if (!cityId) {
                    areaSelect.disabled = true;
                    return;
                }

                // Fetch areas via AJAX
                if (typeof mallfinderData !== 'undefined') {
                    fetch(mallfinderData.ajaxUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=mallfinder_get_areas_by_city&nonce=' + mallfinderData.nonce + '&city_id=' + cityId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.areas) {
                            areaSelect.disabled = false;
                            data.data.areas.forEach(function(area) {
                                const option = document.createElement('option');
                                option.value = area.id;
                                option.textContent = area.title;
                                areaSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        }
    }

    /**
     * Initialize Leaflet Map
     */
    function initMap() {
        // Check if Leaflet is loaded
        if (typeof L === 'undefined') {
            return;
        }

        // Check for map container
        const mapContainer = document.getElementById('mall-map');
        const homeMap = document.getElementById('home-map');
        const singleMap = document.getElementById('single-mall-map');
        
        if (homeMap) {
            initHomeMap(homeMap);
        } else if (singleMap) {
            initSingleMap(singleMap);
        } else if (mapContainer) {
            initDefaultMap(mapContainer);
        }
    }

    /**
     * Home Page Map with all markers
     */
    function initHomeMap(container) {
        if (typeof mallfinderData === 'undefined') return;

        const map = L.map(container).setView([
            parseFloat(mallfinderData.defaultLat),
            parseFloat(mallfinderData.defaultLng)
        ], parseInt(mallfinderData.defaultZoom));

        L.tileLayer(mallfinderData.mapTileUrl, {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Load markers via AJAX
        fetch(mallfinderData.ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=mallfinder_get_map_data&nonce=' + mallfinderData.nonce
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.markers) {
                const bounds = [];
                
                data.data.markers.forEach(function(marker) {
                    const color = marker.category === 'upcoming' ? '#f59e0b' : '#10b981';
                    const m = L.circleMarker([marker.lat, marker.lng], {
                        radius: 10,
                        fillColor: color,
                        color: '#fff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(map);
                    
                    let popupContent = '<div style="min-width: 200px;">';
                    popupContent += '<h4 style="margin: 0 0 8px; font-size: 14px;"><a href="' + marker.permalink + '">' + marker.title + '</a></h4>';
                    if (marker.image) {
                        popupContent += '<img src="' + marker.image + '" alt="" style="width: 100%; height: 100px; object-fit: cover; border-radius: 8px; margin-bottom: 8px;">';
                    }
                    popupContent += '<p style="margin: 0 0 8px; font-size: 12px; color: #64748b;">' + marker.address + '</p>';
                    popupContent += '<a href="' + marker.permalink + '" style="display: inline-block; padding: 6px 12px; background: linear-gradient(to right, #10b981, #14b8a6); color: white; border-radius: 6px; font-size: 12px; text-decoration: none;">View Details</a>';
                    popupContent += '</div>';
                    
                    m.bindPopup(popupContent);
                    bounds.push([marker.lat, marker.lng]);
                });

                if (bounds.length > 0) {
                    map.fitBounds(bounds, { padding: [50, 50] });
                }
            }
        })
        .catch(error => console.error('Error loading map data:', error));
    }

    /**
     * Single Mall Map
     */
    function initSingleMap(container) {
        if (typeof mallfinderData === 'undefined') return;

        const lat = parseFloat(mallfinderData.defaultLat);
        const lng = parseFloat(mallfinderData.defaultLng);

        const map = L.map(container).setView([lat, lng], 15);

        L.tileLayer(mallfinderData.mapTileUrl, {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add marker
        L.circleMarker([lat, lng], {
            radius: 12,
            fillColor: '#10b981',
            color: '#fff',
            weight: 3,
            opacity: 1,
            fillOpacity: 0.9
        }).addTo(map);
    }

    /**
     * Default Map
     */
    function initDefaultMap(container) {
        if (typeof mallfinderData === 'undefined') return;

        const map = L.map(container).setView([
            parseFloat(mallfinderData.defaultLat),
            parseFloat(mallfinderData.defaultLng)
        ], parseInt(mallfinderData.defaultZoom));

        L.tileLayer(mallfinderData.mapTileUrl, {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        return map;
    }

    /**
     * Smooth Scroll for anchor links
     */
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    /**
     * Add fade-in animation to cards on scroll
     */
    function animateOnScroll() {
        const cards = document.querySelectorAll('.mall-card');
        
        cards.forEach(function(card) {
            const rect = card.getBoundingClientRect();
            const scrollPos = window.innerHeight;
            
            if (rect.top < scrollPos) {
                card.classList.add('fade-in');
            }
        });
    }

    window.addEventListener('scroll', animateOnScroll);
    animateOnScroll(); // Run on load

})();
