<?php

use marqu3s\summernote\Summernote;
use simialbi\yii2\dropzone\DropZone;
use yii\helpers\ReplaceArrayValue;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var $this \yii\web\View */
/** @var $form \yii\widgets\ActiveForm|\yii\bootstrap4\ActiveForm|\yii\bootstrap5\ActiveForm */
/** @var $model \simialbi\yii2\bulletin\models\Post */
/** @var $boardId int */

?>

<div class="row form-row g-3">
    <?= $form->field($model, 'title', [
        'options' => [
            'class' => ['form-group', 'col-12']
        ]
    ])->textInput(); ?>
    <?= $form->field($model, 'text', [
        'options' => [
            'class' => ['form-group', 'col-12']
        ]
    ])->widget(Summernote::class, [
        'clientOptions' => [
            'disableDragAndDrop' => true,
            'height' => 300,
            'toolbar' => new ReplaceArrayValue([
                ['actions', ['undo', 'redo']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                ['script', ['subscript', 'superscript']],
                ['list', ['ol', 'ul']],
                ['insert', ['link']],
                ['clear', ['clear']]
            ])
        ]
    ]); ?>
    <div class="form-group col-12 field-attachment">
        <?php
        $files = [];
        if ($model->getAttachments()->count('id') > 0) {
            foreach ($model->attachments as $attachment) {
                $files[] = [
                    'name' => $attachment->name,
                    'size' => $attachment->size,
                    'url' => $attachment->path
                ];
            }
        }
        ?>
        <?= DropZone::widget([
            'url' => Url::to(['attachment/upload', 'boardId' => $boardId]),
            'name' => 'attachment',
            'storedFiles' => $files,
            'options' => [
                'class' => ['border-1', 'border-light', 'rounded']
            ],
            'clientOptions' => [
                'addRemoveLinks' => true,
                'dictDefaultMessage' => Yii::t('simialbi/bulletin', 'Drop files here to upload'),
                'dictRemoveFile' => Yii::t('simialbi/bulletin', 'Remove file'),
                'dictRemoveFileConfirmation' => Yii::t('yii', 'Are you sure you want to delete this item?'),
            ],
            'clientEvents' => [
                'removedfile' => new JsExpression('function (file) {
                    jQuery.ajax({
                        url: \'' . Url::to(['attachment/delete', 'postId' => $model->id]) . '\',
                        method: \'post\',
                        data: {
                            file: file.name
                        }
                    });
                }'),
                'success' => new JsExpression('function (file, response) {
                    jQuery(file.previewElement)
                        .closest(\'form\')
                        .prepend(\'<input type="hidden" name="attachments[]" value="\' + response + \'">\');
                }')
            ]
        ]); ?>
    </div>
</div>
