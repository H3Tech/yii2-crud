$(document).ready(function () {
    new ClipboardJS('button.copy-url', {
        text: function(trigger) {
            return $(trigger).siblings('a.kv-file-download').eq(0).attr('href');
        }
    });
});
