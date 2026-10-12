$(function () {
    if ($.fn.bootstrapMaterialDatePicker) {
        $('.staff-premium-datepicker').bootstrapMaterialDatePicker({ time: false, format: 'DD-MM-YYYY', clearButton: true, cancelText: 'Cancel', okText: 'Select' });
        $('#date_of_birth').bootstrapMaterialDatePicker('setMaxDate', new Date());
    }

    const previewUrls = {};
    const escapeHtml = (value) => $('<div>').text(value || '').html();

    function emptyPreview($preview) {
        $preview.html('<div class="staff-document-preview-empty"><i class="bx bx-file"></i><span>Choose a file to preview it here.</span></div>');
    }

    $('.staff-document-upload').on('change', function () {
        const file = this.files && this.files[0];
        const $preview = $('#' + $(this).data('preview'));
        const previewId = $preview.attr('id');

        if (previewUrls[previewId]) {
            URL.revokeObjectURL(previewUrls[previewId]);
            delete previewUrls[previewId];
        }
        if (!file) {
            emptyPreview($preview);
            return;
        }

        const url = URL.createObjectURL(file);
        previewUrls[previewId] = url;
        const name = escapeHtml(file.name);
        const size = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        let preview = '<div class="staff-document-preview-meta"><i class="bx bx-file"></i><span><strong>' + name + '</strong><small>' + size + '</small></span></div>';

        if (file.type.indexOf('image/') === 0) {
            preview += '<img src="' + url + '" alt="Preview of ' + name + '">';
        } else if (file.type === 'application/pdf') {
            preview += '<iframe src="' + url + '" title="Preview of ' + name + '"></iframe>';
        }
        $preview.html(preview);
    });
});
