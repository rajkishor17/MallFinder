/**
 * MallFinder Theme JavaScript
 * 
 * @package MallFinder
 */

(function($) {
    'use strict';
    
    // Global variables
    var map = null;
    var markers = [];
    var currentFilters = {
        search: '',
        status: '',
        state: '',
        city: '',
        area: ''
    };
    
    // Document ready
    $(document).ready(function() {
        initMap();
        initFilters();
        initMobileMenu();
    });
    
    /**
     * Initialize Map
     */
    function initMap() {
        var mapContainer = document.getElementById('mall-map');
        if (!mapContainer) return;
        
        // Initialize Leaflet map
        map = L.map('mall-map').setView([
            parseFloat(mallfinderData.defaultLat) || 20.5937,
            parseFloat(mallfinderData.defaultLng) || 78.9629
        ], parseInt(mallfinderData.defaultZoom) || 5);
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 18
        }).addTo(map);
        
        // Load initial markers
        loadMapMarkers();
    }
    
    /**
     * Load Map Markers
     */
    function loadMapMarkers() {
        if (!map) return;
        
        // Clear existing markers
        markers.forEach(function(marker) {
            map.removeLayer(marker);
        });
        markers = [];
        
        // Fetch malls from API
        $.ajax({
            url: mallfinderData.ajaxUrl,
            type: 'GET',
            data: {
                action: 'get_malls',
                nonce: mallfinderData.nonce,
                status: currentFilters.status,
                state: currentFilters.state,
                city: currentFilters.city,
                area: currentFilters.area
            },
            success: function(response) {
                if (response.success && response.data) {
                    response.data.forEach(function(mall) {
                        var markerColor = mall.status === 'upcoming' ? '#f59e0b' : '#10b981';
                        
                        var marker = L.circleMarker([mall.lat, mall.lng], {
                            radius: 10,
                            fillColor: markerColor,
                            color: '#fff',
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 0.8
                        }).addTo(map);
                        
                        // Popup content
                        var popupContent = '<div style="min-width: 200px;">' +
                            '<h4 style="margin: 0 0 0.5rem; font-weight: 600;">' + mall.title + '</h4>' +
                            '<p style="margin: 0 0 0.5rem; font-size: 0.875rem; color: #64748b;">' + (mall.address || '') + '</p>' +
                            '<span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; ' +
                            'background: ' + (mall.status === 'upcoming' ? '#fef3c7' : '#d1fae5') + '; ' +
                            'color: ' + (mall.status === 'upcoming' ? '#b45309' : '#047857') + ';">' +
                            (mall.status === 'upcoming' ? 'Upcoming' : 'Existing') + '</span><br>' +
                            '<a href="' + mall.url + '" style="display: inline-block; margin-top: 0.5rem; font-size: 0.875rem; color: #059669;">View Details →</a>' +
                            '</div>';
                        
                        marker.bindPopup(popupContent);
                        markers.push(marker);
                    });
                    
                    // Fit bounds if we have markers
                    if (markers.length > 0) {
                        var group = L.featureGroup(markers);
                        map.fitBounds(group.getBounds().pad(0.1));
                    }
                }
            }
        });
    }
    
    /**
     * Initialize Filters
     */
    function initFilters() {
        // Search input
        $('#mall-search').on('input', debounce(function() {
            currentFilters.search = $(this).val();
            filterMalls();
        }, 300));
        
        // Status filter
        $('#status-filter').on('change', function() {
            currentFilters.status = $(this).val();
            loadMapMarkers();
            filterMalls();
        });
        
        // State filter
        $('#state-filter').on('change', function() {
            var stateId = $(this).val();
            currentFilters.state = stateId;
            currentFilters.city = '';
            currentFilters.area = '';
            
            // Load cities for selected state
            if (stateId) {
                loadCities(stateId);
            } else {
                $('#city-filter').html('<option value=""><?php _e("All Cities", "mallfinder"); ?></option>').prop('disabled', true);
                $('#area-filter').html('<option value=""><?php _e("All Areas", "mallfinder"); ?></option>').prop('disabled', true);
            }
            
            loadMapMarkers();
            filterMalls();
        });
        
        // City filter
        $('#city-filter').on('change', function() {
            var cityId = $(this).val();
            currentFilters.city = cityId;
            currentFilters.area = '';
            
            if (cityId) {
                loadAreas(cityId);
            } else {
                $('#area-filter').html('<option value=""><?php _e("All Areas", "mallfinder"); ?></option>').prop('disabled', true);
            }
            
            loadMapMarkers();
            filterMalls();
        });
        
        // Area filter
        $('#area-filter').on('change', function() {
            currentFilters.area = $(this).val();
            loadMapMarkers();
            filterMalls();
        });
        
        // Status toggle buttons
        $('.status-toggle').on('click', function() {
            $('.status-toggle').removeClass('active btn-primary btn-amber').addClass('btn-outline');
            $(this).removeClass('btn-outline').addClass('active');
            
            var status = $(this).data('status');
            currentFilters.status = status === 'all' ? '' : status;
            
            $('#status-filter').val(currentFilters.status);
            loadMapMarkers();
            filterMalls();
        });
    }
    
    /**
     * Load Cities for State
     */
    function loadCities(stateId) {
        $.ajax({
            url: mallfinderData.ajaxUrl,
            type: 'GET',
            data: {
                action: 'get_cities',
                nonce: mallfinderData.nonce,
                state_id: stateId
            },
            success: function(response) {
                if (response.success && response.data) {
                    var options = '<option value=""><?php _e("All Cities", "mallfinder"); ?></option>';
                    response.data.forEach(function(city) {
                        options += '<option value="' + city.slug + '">' + city.name + '</option>';
                    });
                    $('#city-filter').html(options).prop('disabled', false);
                    $('#area-filter').html('<option value=""><?php _e("All Areas", "mallfinder"); ?></option>').prop('disabled', true);
                }
            }
        });
    }
    
    /**
     * Load Areas for City
     */
    function loadAreas(cityId) {
        $.ajax({
            url: mallfinderData.ajaxUrl,
            type: 'GET',
            data: {
                action: 'get_areas',
                nonce: mallfinderData.nonce,
                city_id: cityId
            },
            success: function(response) {
                if (response.success && response.data) {
                    var options = '<option value=""><?php _e("All Areas", "mallfinder"); ?></option>';
                    response.data.forEach(function(area) {
                        options += '<option value="' + area.slug + '">' + area.name + '</option>';
                    });
                    $('#area-filter').html(options).prop('disabled', false);
                }
            }
        });
    }
    
    /**
     * Filter Malls
     */
    function filterMalls() {
        $.ajax({
            url: mallfinderData.ajaxUrl,
            type: 'GET',
            data: {
                action: 'search_malls',
                nonce: mallfinderData.nonce,
                search: currentFilters.search,
                status: currentFilters.status,
                state: currentFilters.state,
                city: currentFilters.city,
                area: currentFilters.area
            },
            success: function(response) {
                if (response.success && response.data) {
                    $('#malls-grid').html(response.data.html);
                    
                    // Update pagination if needed
                    if (response.data.pagination) {
                        $('.pagination').html(response.data.pagination);
                    }
                }
            }
        });
    }
    
    /**
     * Initialize Mobile Menu
     */
    function initMobileMenu() {
        $('#mobile-menu-toggle').on('click', function() {
            $('#mobile-nav').toggleClass('active');
        });
    }
    
    /**
     * Debounce function
     */
    function debounce(func, wait) {
        var timeout;
        return function() {
            var context = this;
            var args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    }
    
})(jQuery);

// Go to Top functionality
document.addEventListener('DOMContentLoaded', function() {
    var goTop = document.getElementById('go-to-top');
    
    if (goTop) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                goTop.classList.add('visible');
            } else {
                goTop.classList.remove('visible');
            }
        });
        
        goTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});
