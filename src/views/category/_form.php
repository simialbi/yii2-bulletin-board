<?php

use kartik\select2\Select2;

/** @var $this \yii\web\View */
/** @var $form \yii\widgets\ActiveForm|\yii\bootstrap4\ActiveForm|\yii\bootstrap5\ActiveForm */
/** @var $model \simialbi\yii2\bulletin\models\Category */

?>

<div class="row form-row g-3">
    <?= $form->field($model, 'title', [
        'options' => [
            'class' => ['form-group', 'col-12']
        ]
    ])->textInput(); ?>
    <?= $form->field($model, 'description', [
        'options' => [
            'class' => ['form-group', 'col-12']
        ]
    ])->textarea(['rows' => 5]); ?>
</div>
