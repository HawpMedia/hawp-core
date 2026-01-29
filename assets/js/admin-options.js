jQuery(document).ready(function($) {
    // Define the function in the global scope
    window.hawpSelectImage = function(fieldId) {
        var frame = wp.media({
            title: 'Select Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#' + fieldId).val(attachment.id);
            $('#' + fieldId + '_preview').html('<img src="' + attachment.sizes.thumbnail.url + '" alt="" />');
        });

        frame.open();
    };

    // Hash-based tab navigation for theme options (mirrors WP Rocket style).
    var $tabLinks = $('.hm-Header .hm-nav-tab');
    var $tabPanels = $('.hm-tab-panel');

    function buildDisplayUrl(tabId) {
        var url = new URL(window.location.href);
        url.searchParams.set('page', 'hawptheme');
        url.searchParams.delete('tab');
        url.hash = tabId;
        return url.toString();
    }

    function buildSubmitUrl(tabId) {
        var url = new URL(window.location.href);
        url.searchParams.set('page', 'hawptheme');
        url.searchParams.set('tab', tabId);
        url.hash = tabId;
        return url.toString();
    }

    function getTabFromHash() {
        var hash = window.location.hash ? window.location.hash.replace('#', '') : '';
        if (!hash) {
            var url = new URL(window.location.href);
            hash = url.searchParams.get('tab') || '';
        }
        if (!hash && $tabPanels.length) {
            hash = $tabPanels.first().data('tab');
        }
        return hash;
    }

    function activateTab(tabId) {
        if (!$tabPanels.length) {
            return;
        }

        var $targetPanel = $tabPanels.filter('[data-tab="' + tabId + '"]');
        if (!$targetPanel.length) {
            $targetPanel = $tabPanels.first();
            tabId = $targetPanel.data('tab');
        }

        $tabLinks.removeClass('active isActive');
        $tabLinks.filter('[data-tab="' + tabId + '"]').addClass('isActive');

        $tabPanels.removeClass('is-active').hide();
        $targetPanel.addClass('is-active').show();

        // Clean the URL to hash-only for display.
        var displayUrl = buildDisplayUrl(tabId);
        window.history.replaceState(null, '', displayUrl);
    }

    if ($tabPanels.length) {
        activateTab(getTabFromHash());

        $(window).on('hashchange', function() {
            activateTab(getTabFromHash());
        });

        $tabLinks.on('click', function(e) {
            var targetTab = $(this).data('tab');
            if (!targetTab) {
                return;
            }
            e.preventDefault();
            activateTab(targetTab);
        });

        // Preserve active tab after form submission by updating referrer to include tab.
        $('.hm-tab-panel form').on('submit', function() {
            var activeTab = $tabPanels.filter('.is-active').data('tab');
            var refField = $(this).find('input[name="_wp_http_referer"]');
            if (activeTab && refField.length) {
                refField.val(buildSubmitUrl(activeTab));
            }
        });
    }
}); 
