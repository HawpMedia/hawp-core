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
}); 
