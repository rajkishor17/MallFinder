/**
 * MallFinder Theme JavaScript
 */

(function($) {
    'use strict';

    // Document ready
    $(document).ready(function() {
        initSearchAutocomplete();
        initSmoothScroll();
    });

    /**
     * Search Autocomplete
     */
    function initSearchAutocomplete() {
        var searchInput = $('#mall-search-input, #mall-search-input-single');
        var suggestionsBox = $('#search-suggestions, #search-suggestions-single');
        var searchTimeout = null;

        searchInput.on('input', function() {
            var searchTerm = $(this).val().trim();
            clearTimeout(searchTimeout);

            if (searchTerm.length < 2) {
                suggestionsBox.empty().hide();
                return;
            }

            var input = $(this);
            searchTimeout = setTimeout(function() {
                $.ajax({
                    url: mallfinder_ajax.ajax_url,
                    type: 'GET',
                    data: {
                        action: 'mallfinder_search',
                        term: searchTerm,
                        nonce: mallfinder_ajax.nonce
                    },
                    success: function(response) {
                        if (response.length > 0) {
                            var html = '';
                            $.each(response, function(index, mall) {
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
                            suggestionsBox.html(html).show();
                        } else {
                            suggestionsBox.html('<div class="suggestion-empty">No malls found</div>').show();
                        }
                    }
                });
            }, 300);
        });

        // Hide suggestions when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-input-wrapper').length) {
                suggestionsBox.hide();
            }
        });

        // Show suggestions on focus
        searchInput.on('focus', function() {
            if ($(this).val().length >= 2) {
                $(this).trigger('input');
            }
        });
    }

    /**
     * Smooth Scroll
     */
    function initSmoothScroll() {
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
    }

})(jQuery);
