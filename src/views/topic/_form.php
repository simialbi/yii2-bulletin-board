<?php

use kartik\select2\Select2;
use marqu3s\summernote\Summernote;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\ReplaceArrayValue;

/** @var $this \yii\web\View */
/** @var $boardId int|null */
/** @var $boards array */
/** @var $form \yii\widgets\ActiveForm|\yii\bootstrap4\ActiveForm|\yii\bootstrap5\ActiveForm */
/** @var $topic \simialbi\yii2\bulletin\models\Topic */
/** @var $post \simialbi\yii2\bulletin\models\Post */
/** @var $categories array */

?>

<div class="row form-row g-3">
    <?= $form->field($topic, 'title', [
        'options' => [
            'class' => ['form-group', 'col-12', 'col-sm-6', 'col-lg-4']
        ]
    ])->textInput(); ?>
    <div class="form-group col-12 col-sm-6 col-lg-4">
        <?= Html::label(Yii::t('simialbi/bulletin', 'Categories'), 'categories', [
            'class' => ['form-label']
        ]); ?>
        <?= Select2::widget([
            'name' => 'categories[]',
            'value' => ArrayHelper::getColumn($topic->categories, 'id'),
            'data' => $categories,
            'options' => [
                'id' => 'categories',
                'multiple' => true,
                'placeholder' => Yii::t('simialbi/bulletin', 'Select categories')
            ],
            'pluginOptions' => [
                'allowBlank' => true
            ]
        ]); ?>
    </div>
    <div class="form-group col-12 col-sm-6 col-lg-4">
        <?= Html::label(Yii::t('simialbi/bulletin', 'Boards'), 'boards', [
            'class' => ['form-label']
        ]); ?>
        <?= Select2::widget([
            'name' => 'boards[]',
            'value' => $boardId ? [$boardId] : ArrayHelper::getColumn($topic->boards, 'id'),
            'data' => $boards,
            'options' => [
                'id' => 'boards',
                'multiple' => true,
                'placeholder' => Yii::t('simialbi/bulletin', 'Select boards')
            ],
            'pluginOptions' => [
                'allowBlank' => true
            ]
        ]); ?>
    </div>
    <?= $form->field($topic, 'status', [
        'options' => [
            'class' => ['form-group', 'col-12']
        ]
    ])->checkbox(); ?>
    <?= $form->field($post, 'text', [
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
