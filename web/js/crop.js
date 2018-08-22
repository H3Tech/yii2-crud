function calculateGcd(a, b) {
    if (a < 0) {
        a = -a;
    }
    if (b < 0) {
        b = -b;
    }

    while (b != 0) {
        a %= b;
        if (a == 0) {
            return b;
        }
        b %= a;
    }

    return a;
}

function getAspectRatio(width, height) {
    var divisor = gcd(width, height);

    return {
        width: width / divisor,
        height: height / divisor
    };
}

function updatePreview(c, $previewContainer, $pimg) {
    cropWidth = c.w;
    x = c.x;
    y = c.y;
    ar = (c.w / c.h);
    xsize = $previewContainer.width(),
        ysize = $previewContainer.height();

    if (parseInt(c.w) > 0) {
        var rx = xsize / c.w;
        var ry = ysize / c.h;

        $pimg.css({
            width: Math.round(boundx * rx) + 'px',
            height: Math.round(boundy * ry) + 'px',
            marginLeft: '-' + Math.round(c.x * rx) + 'px',
            marginTop: '-' + Math.round(c.y * ry) + 'px'
        });
    }
}

var jcrop_api = null, boundx, boundy, x, y, aspectRatio = 16 / 9, cropWidth;
var reinit = false;
var handleResponsive = function () {

    if ($(window).width() <= 1024 && $(window).width() >= 678) {
        $('.responsive-1024').each(function () {
            $(this).attr("data-class", $(this).attr("class"));
            $(this).attr("class", 'responsive-1024 col-md-12');
        });
    } else {
        $('.responsive-1024').each(function () {
            if ($(this).attr("data-class")) {
                $(this).attr("class", $(this).attr("data-class"));
                $(this).removeAttr("data-class");
            }
        });
    }
}

function destroyJCrop() {
    jcrop_api.destroy();
    jcrop_api = null;
    reinit = false;
}

var Crop = function () {
    var initializeCropper = function ($button) {
        var $preview = $('#preview-pane'),
            $previewContainer = $('#preview-pane .preview-container'),
            $previewImage = $('#preview-pane .preview-container img');

        if (jcrop_api == null) {
            var aspectWidth = $button.data('aspect-width');
            var aspectHeight = $button.data('aspect-height');

            var aspectRatio = aspectWidth / aspectHeight;

            $('.image-selector-preview').Jcrop({
                boxWidth: $(window).width() * 0.4,
                onSelect: function (c) {
                    $('.sendCrop').removeAttr('disabled');
                    updatePreview(c, $previewContainer, $previewImage);
                },
                onChange: function (c) {
                    $('.sendCrop').removeAttr('disabled');
                    updatePreview(c, $previewContainer, $previewImage);
                },
                onRelease: function () {
                    $('.sendCrop').attr('disabled', 'disabled');
                },
                aspectRatio: aspectRatio,
                keySupport: false
            }, function () {
                var bounds = this.getBounds();
                var img = new Image();
                img.src = $('.image-selector-preview').attr('src');
                boundx = img.width;
                boundy = img.height;

                jcrop_api = this;
                jcrop_api.setImage($('.image-selector-preview').attr('src'), function () {
                    setTimeout(function () {
                        $($('.setAscpectRatio').get(0)).click();
                    }, 100);
                });
            });

            $previewContainer.height($previewContainer.width() / aspectRatio);

            $('.setAscpectRatio').click(function () {
                var $aspectRatioButton = $(this);
                $('.setAscpectRatio').removeClass('activeRatio blue');
                $aspectRatioButton.addClass('activeRatio blue');

                $('.sendCrop').attr('disabled', 'disabled');

                var aspectWidth = $aspectRatioButton.data('aspect-width');
                var aspectHeight = $aspectRatioButton.data('aspect-width');

                var aspectRatio = aspectWidth / aspectHeight;

                sendData({url: $button.data('crop-check-url')}, {id: $button.data('key')}, function (data) {
                    var matchingCrop = null;
                    for (var index = 0; index < data.length; index++) {
                        var crop = data[index];

                        if (crop['aspect_width'] == aspectWidth && crop['aspect_height'] == aspectHeight) {
                            matchingCrop = crop;
                            break;
                        }
                    }

                    if (matchingCrop === null) {
                        $previewContainer.height($previewContainer.width() / aspectRatio);

                        jcrop_api.setOptions({
                            'aspectRatio': aspectRatio
                        });
                        $('.sendCrop').attr('disabled', 'disabled');
                        jcrop_api.release();
                    } else {
                        //Convert [0,1] to image space
                        matchingCrop.width *= boundx;
                        matchingCrop.x *= boundx;
                        matchingCrop.y *= boundy;

                        var img = new Image();
                        img.src = $('.image-selector-preview').attr("src");

                        $previewContainer.height($previewContainer.width() / aspectRatio);

                        var width = parseInt(matchingCrop.width);
                        var height = width / aspectRatio;
                        var rate = ($('.image-selector-preview').width() / img.width);

                        jcrop_api.setOptions({
                            'aspectRatio': aspectRatio,
                            'setSelect': [matchingCrop.x, matchingCrop.y, (parseInt(matchingCrop.x) + width), (parseInt(matchingCrop.y) + height)]
                        });
                        $('.sendCrop').removeAttr('disabled');
                    }
                    jcrop_api.focus();
                });
                jcrop_api.focus();
            });

            $('.sendCrop').click(function () {
                var $this = $(this);
                $this.button('loading');

                var $activeRatio = $('.activeRatio');

                var data = {
                    id: $(".image-id-holder").val(),
                    x: parseInt(x) / boundx,
                    y: parseInt(y) / boundy,
                    width: parseInt(cropWidth) / boundx,
                    aspectWidth: $activeRatio.data('aspect-width'),
                    aspectHeight: $activeRatio.data('aspect-height')
                };

                sendData({url: $button.data('crop-save-url')}, data, function (result) {
                    $('.image-id-holder').val(result.image_id);
                    $this.parents('.cropControls:first').data('image-id', result.image_id);
                    g_selectImageCropCallback.call(this, result);
                    showMessage('success', successMessage, $this.parents('.cropControls:first'));
                    $this.button('reset');
                });
            });
        }
    }

    return {
        init: function ($button) {
            if (!jQuery().Jcrop) {
                return;
            }

            // Metronic.addResizeHandler(handleResponsive);

            initializeCropper($button);

            // handleResponsive();
        }
    };
}();

function showMessage(className, message, containerDom) {
    var $containerDom = $(containerDom);
    var $target = $containerDom.find('.alert').first();

    $target.addClass('alert-' + className).text(message).slideDown();

    var messageTimeout = setTimeout(function () {
        clearTimeout(messageTimeout);
        messageTimeout = null;

        $target.slideUp(function () {
            $target.removeClass('alert-' + className).empty();
        });
    }, 5000);

    if (!$target.data('click-handled')) {
        $target.data('click-handled', true);
        $target.click(function () {
            clearTimeout(messageTimeout);
            messageTimeout = null;

            $target.slideUp(function () {
                $target.removeClass('alert-' + className).empty();
            });
        });
    }
}

var g_selectImageCropCallback = 0;

function selectImageCrop($button, callback) {
    var imageId = $button.data('key');
    var modalUrl = $button.data('modal-url');
    var width = $button.data('aspect-width');
    var height = $button.data('aspect-height');
    var gcd = calculateGcd(width, height);
    var aspectWidth = width / gcd;
    var aspectHeight = height / gcd;

    if (typeof callback === 'function') {
        g_selectImageCropCallback = callback;
    } else {
        g_selectImageCropCallback = function () {
        }
    }
    $MODAL = showModal(modalUrl + '?id=' + imageId + '&aspectWidth=' + aspectWidth + '&aspectHeight=' + aspectHeight);
    $MODAL.bind('loaded.bs.modal', function () {
        Crop.init($button);
    });

    $MODAL.bind('hidden.bs.modal', function () {
        destroyJCrop();
    });
}

function showModal(modalUrl) {
    var $obj = null;
    $('.modal').each(function (i, v) {
        if ($(v).data('modalUrl') == modalUrl) {
            $obj = $(v);
            return false;
        }
    });

    if ($obj == null) {
        $obj = $('#ajax').clone(false).removeAttr('id').appendTo('body').data('modalUrl', modalUrl);
    }

    $obj.modal({
        remote: modalUrl
    });

    $obj.unbind('show.bs.modal');
    $obj.unbind('shown.bs.modal');
    $obj.unbind('hide.bs.modal');
    $obj.unbind('hidden.bs.modal');
    $obj.unbind('loaded.bs.modal');

    $obj.bind('hidden.bs.modal', function () {
        $obj.remove();
    });
    return $obj;
}

function sendData(options, data, callback, errorCallback) {
    var opt = {
        url: '',
        sendType: 'post'
    }

    opt = $.extend(true, opt, options);
    $.ajax({
        type: opt.sendType,
        url: opt.url,
        data: data,
        dataType: 'json',
        success: function (data) {
            if (data && data.status && data.status === 'error') {
                console.error(data);

                if (errorCallback !== undefined) {
                    errorCallback.call(this, data);
                }

                return;
            }

            if (typeof(callback) === 'function') {
                callback.call(this, data);
            }
        },
        error: function (a, b, c) {
            if (errorCallback !== undefined) {
                errorCallback.call(this, data);
            }
        }
    });
}

$(document).ready(function () {
    $(document).on('click', 'button.crop', function () {
        selectImageCrop($(this));
    });
});
