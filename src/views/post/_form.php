<?php

use marqu3s\summernote\Summernote;
use yii\helpers\ReplaceArrayValue;

/** @var $this \yii\web\View */
/** @var $form \yii\widgets\ActiveForm|\yii\bootstrap4\ActiveForm|\yii\bootstrap5\ActiveForm */
/** @var $model \simialbi\yii2\bulletin\models\Post */

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
</div>
