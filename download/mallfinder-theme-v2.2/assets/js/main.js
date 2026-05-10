/**
 * MallFinder Theme JavaScript
 *
 * @package MallFinder
 */

(function($) {
    'use strict';

    // Mobile Menu Toggle
    $(document).ready(function() {
        $('.menu-toggle').on('click', function() {
            $('.main-nav').toggleClass('active');

            // Toggle icon
            var $icon = $(this).find('svg');
            if ($icon.length) {
                if ($('.main-nav').hasClass('active')) {
                    $icon.html('<line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>');
                } else {
                    $icon.html('<line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line>');
                }
            }
        });

        // Close mobile menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.site-header').length) {
                $('.main-nav').removeClass('active');
            }
        });

        // Smooth scroll for anchor links
        $('a[href*="#"]:not([href="#"])').on('click', function() {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 500);
                    return false;
                }
            }
        });

        // Add animation to cards on scroll
        var observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('slide-up');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.mall-card, .store-card').forEach(function(card) {
            observer.observe(card);
        });

        // Initialize map if container exists and Leaflet is loaded
        if (typeof L !== 'undefined') {
            initializeMaps();
        }
    });

    /**
     * Initialize Maps
     */
    function initializeMaps() {
        // Home page map
        var $homeMap = $('#home-map');
        if ($homeMap.length && typeof mallfinderData !== 'undefined') {
            // Map is initialized in index.php with inline script
        }

        // Single mall map
        var $mallMap = $('#mall-single-map');
        if ($mallMap.length && typeof mallfinderData !== 'undefined') {
            // Map is initialized in single-mall.php with inline script
        }
    }

    /**
     * Search functionality with filters
     */
    $(document).ready(function() {
        var $searchInput = $('#mall-search-input');
        var $suggestionsBox = $('#search-suggestions');
        var searchTimeout;

        // Search input handler
        if ($searchInput.length) {
            $searchInput.on('input', function() {
                clearTimeout(searchTimeout);
                var query = $(this).val().trim();

                if (query.length >= 2) {
                    searchTimeout = setTimeout(function() {
                        performSearch(query);
                    }, 300);
                } else {
                    $suggestionsBox.hide().empty();
                }
            });

            // Show suggestions on focus
            $searchInput.on('focus', function() {
                if ($(this).val().length >= 2) {
                    $suggestionsBox.show();
                }
            });

            // Hide suggestions on click outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-input-wrapper').length) {
                    $suggestionsBox.hide();
                }
            });
        }

        // Perform AJAX search
        function performSearch(query) {
            var category = $('#category-filter').val() || 'all';
            var state = $('#state-filter').val() || '';
            var city = $('#city-filter').val() || '';
            var area = $('#area-filter').val() || '';

            $.ajax({
                url: mallfinderData.ajaxUrl,
                type: 'GET',
                data: {
                    action: 'mallfinder_search',
                    term: query,
                    category: category,
                    state: state,
                    city: city,
                    area: area,
                    nonce: mallfinderData.nonce
                },
                success: function(response) {
                    if (response && response.length > 0) {
                        var html = '';
                        response.forEach(function(mall) {
                            var badgeClass = mall.category === 'upcoming' ? 'badge-upcoming' : 'badge-existing';
                            var badgeText = mall.category === 'upcoming' ? 'Upcoming' : 'Existing';
                            html += '<a href="' + mall.url + '" class="suggestion-item">';
                            html += '<img src="' + mall.image + '" alt="' + mall.title + '" class="suggestion-image">';
                            html += '<div class="suggestion-content">';
                            html += '<div class="suggestion-title">' + mall.title + '</div>';
                            html += '<div class="suggestion-address">' + mall.address + '</div>';
                            html += '</div>';
                            html += '<span class="suggestion-badge ' + badgeClass + '">' + badgeText + '</span>';
                            html += '</a>';
                        });
                        $suggestionsBox.html(html).show();
                    } else {
                        $suggestionsBox.html('<div class="suggestion-empty">No malls found</div>').show();
                    }
                },
                error: function() {
                    $suggestionsBox.html('<div class="suggestion-empty">Error searching malls</div>').show();
                }
            });
        }
    });

    /**
     * Clear Filters functionality
     */
    $(document).ready(function() {
        // Clear filters on home page
        $('#clear-filters').on('click', function(e) {
            e.preventDefault();
            $('#category-filter').val('all');
            $('#state-filter').val('');
            $('#city-filter').val('');
            $('#area-filter').val('');
            $('#mall-search-input').val('');
            $(this).closest('form').submit();
        });

        // Clear filters on single mall page
        $('#clear-filters-single').on('click', function(e) {
            e.preventDefault();
            $('#state-filter-single').val('');
            $('#city-filter-single').val('');
            $('#area-filter-single').val('');
            $('#mall-search-input-single').val('');
            $(this).closest('form').submit();
        });
    });

    /**
     * Filter auto-submit on change
     */
    $(document).ready(function() {
        $('select[name="category"], select[name="state"], select[name="city"], select[name="area"]').on('change', function() {
            $(this).closest('form').submit();
        });
    });

    /**
     * Back to top button
     */
    $(document).ready(function() {
        // Create back to top button
        var $backToTop = $('<button class="back-to-top" aria-label="Back to top" style="display: none; position: fixed; bottom: 20px; right: 20px; width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(to right, #10b981, #14b8a6); color: white; border: none; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 999;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 24px; height: 24px;"><polyline points="18 15 12 9 6 15"></polyline></svg></button>');

        $('body').append($backToTop);

        // Show/hide button based on scroll position
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 300) {
                $backToTop.fadeIn();
            } else {
                $backToTop.fadeOut();
            }
        });

        // Scroll to top on click
        $backToTop.on('click', function() {
            $('html, body').animate({ scrollTop: 0 }, 500);
        });
    });

})(jQuery);
