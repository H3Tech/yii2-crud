$(document).ready(function () {
    $(document).on('click', 'button.download', function () {
        var $button = $(this);
        window.open($button.data('download-url') + '/' + $button.data('key'), '_blank');
    });
});
