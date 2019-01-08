<?php

use yii\helpers\Url;
use yii\web\View;
use h3tech\crud\models\Image;

/**
 * @var View $this
 * @var Image $image
 * @var array[] $sizes
 */

$this->registerJsVar('successMessage', Yii::t('h3tech/crud/crud', 'Image crop successful'));
?>
<?php $this->beginPage(); ?>
<?php $this->head(); ?>
<?php $this->beginBody(); ?>
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-gift"></i><?= Yii::t('h3tech/crud/crud', 'Crop Image') ?>
            </div>
            <div class="actions">
                <button class="btn btn-xs green" onclick="$MODAL.modal('hide'); return false;">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
        <div class="portlet-body">
            <div class="imagePreview">
                <div class="row" style="margin-left: 0px; margin-right: 0px;">
                    <div class="cropControls" style="padding: 5px 0;" data-image-id="<?= $image->id ?>">
                        <div class="alert alert-success" style="display: none;"></div>
                        <input type="hidden" class="image-id-holder" disabled="disabled" value="<?= $image->id ?>"/>

                        <?php foreach ($sizes as $size) : ?>
                            <button class="btn btn-sm setAscpectRatio" data-aspect-width="<?= $size['aspectWidth'] ?>"
                                    data-aspect-height="<?= $size['aspectHeight'] ?>" <?= count($sizes) === 1 ? 'style="display: none"' : '' ?>>
                                <?= "{$size['aspectWidth']}:{$size['aspectHeight']}" ?>
                            </button>
                        <?php endforeach; ?>

                        <button class="btn btn-sm green sendCrop" data-loading-text="<?= Yii::t('h3tech/crud/crud', 'Saving...') ?>"><i
                                    class="fa fa-check"></i> <?= Yii::t('h3tech/crud/crud', 'Save') ?>
                        </button>
                        <button class="btn btn-sm red" onclick="$MODAL.modal('hide');"><?= Yii::t('h3tech/crud/crud', 'Close') ?></button>
                    </div>
                </div>
                <div class="row" style="margin-left: 0px; margin-right: 0px;">
                    <div class="col-md-6 image-selector-preview-container">
                        <div style="position: static;">
                            <div id="crop">
                                <img class="image-selector-preview" src="<?= $image->originalUrl ?>?size=3"
                                     style="width: 100%"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="preview-pane" style="position: static;">
                            <div class="preview-container" style="width: 100%">
                                <img class="jcrop-preview" src="<?= $image->originalUrl ?>?size=3"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row"></div>
        </div>
    </div>
<?php $this->endBody(); ?>
<?php $this->endPage(); ?>
