<?php

use kartik\select2\Select2;

/** @var $this \yii\web\View */
/** @var $form \yii\widgets\ActiveForm|\yii\bootstrap4\ActiveForm|\yii\bootstrap5\ActiveForm */
/** @var $model \simialbi\yii2\bulletin\models\Category */

?>

<div class="row form-row g-3">
    <?= $form->field($model, 'title', [
        'options' => [
            'class' => ['form-group', 'col-12', 'col-sm-6', 'col-lg-4']
        ]
    ])->textInput(); ?>
    <?= $form->field($model, 'icon', [
        'options' => [
            'class' => ['form-group', 'col-12', 'col-sm-6', 'col-lg-4']
        ]
    ])->widget(Select2::class, [
        'data' => []
    ]); ?>
    <?= $form->field($model, 'status', [
        'options' => [
            'class' => ['form-group', 'col-12', 'col-sm-6', 'col-lg-2']
        ]
    ])->checkbox(); ?>
    <?= $form->field($model, 'is_public', [
        'options' => [
            'class' => ['form-group', 'col-12', 'col-sm-6', 'col-lg-2']
        ]
    ])->checkbox(); ?>
    <?= $form->field($model, 'description', [
        'options' => [
            'class' => ['form-group', 'col-12']
        ]
    ])->textarea(['rows' => 5]); ?>
</div>
