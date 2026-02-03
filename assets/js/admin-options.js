jQuery(document).ready(function($) {
    function getPreviewUrl(attachment) {
        if (!attachment) {
            return '';
        }
        if (attachment.sizes) {
            if (attachment.sizes.thumbnail && attachment.sizes.thumbnail.url) {
                return attachment.sizes.thumbnail.url;
            }
            if (attachment.sizes.medium && attachment.sizes.medium.url) {
                return attachment.sizes.medium.url;
            }
            if (attachment.sizes.large && attachment.sizes.large.url) {
                return attachment.sizes.large.url;
            }
            if (attachment.sizes.full && attachment.sizes.full.url) {
                return attachment.sizes.full.url;
            }
        }
        return attachment.url || '';
    }

    function getImageControls($input) {
        var $controls = $input.siblings('.hawp-image-controls');
        if (!$controls.length) {
            $controls = $input.closest('td, .form-field, .field').find('.hawp-image-controls').first();
        }
        return $controls;
    }

    function getRemoveButton($controls, fieldId) {
        if (!$controls || !$controls.length) {
            return $();
        }
        var $button = $controls.find('.hawp-remove-image');
        if (!$button.length) {
            $button = $controls.find('.button-link-delete');
            if ($button.length) {
                $button.addClass('hawp-remove-image');
            }
        }
        if (!$button.length) {
            $button = $('<input type="button" class="button button-link-delete hawp-remove-image" value="Remove Image" />');
            $button.on('click', function() {
                window.hawpRemoveImage(fieldId);
            });
            $controls.append($button);
        }
        return $button;
    }

    window.hawpRemoveImage = function(fieldId) {
        var $input = $('#' + fieldId);
        if (!$input.length) {
            return;
        }
        $input.val('');
        $('#' + fieldId + '_preview').empty();
        var $controls = getImageControls($input);
        var $removeButton = getRemoveButton($controls, fieldId);
        if ($removeButton.length) {
            $removeButton.hide();
        }
    };

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
            var $input = $('#' + fieldId);
            var $controls = getImageControls($input);
            var $removeButton = getRemoveButton($controls, fieldId);
            var previewUrl = getPreviewUrl(attachment);
            $input.val(attachment.id || '');
            if (previewUrl) {
                $('#' + fieldId + '_preview').html('<img src="' + previewUrl + '" alt="" />');
            } else {
                $('#' + fieldId + '_preview').empty();
            }
            if ($removeButton.length) {
                $removeButton.show();
            }
        });

        frame.open();
    };

    function getCodeEditorSettings(type) {
        if (!window.hawpCodeEditorSettings || !window.hawpCodeEditorSettings[type]) {
            return null;
        }
        return $.extend(true, {}, window.hawpCodeEditorSettings[type]);
    }

    function initCodeEditors($scope) {
        if (!window.wp || !window.wp.codeEditor) {
            return;
        }
        $scope.find('textarea[data-code-editor]').each(function() {
            var $textarea = $(this);
            if ($textarea.data('codeEditorInitialized')) {
                return;
            }
            var type = $textarea.data('code-editor') || 'text/html';
            var settings = getCodeEditorSettings(type);
            if (!settings) {
                return;
            }
            $textarea.data('codeEditorInitialized', true);
            var editor = wp.codeEditor.initialize(this, settings);
            if (editor && editor.codemirror) {
                $textarea.data('codeEditorInstance', editor.codemirror);
            }
        });
    }

    function refreshCodeEditors($scope) {
        $scope.find('.CodeMirror').each(function() {
            if (this.CodeMirror) {
                this.CodeMirror.refresh();
            }
        });
    }

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

        initCodeEditors($targetPanel);
        refreshCodeEditors($targetPanel);

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
    } else {
        initCodeEditors($(document));
    }
}); 
