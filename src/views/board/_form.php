<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;

/** @var $this \yii\web\View */
/** @var $form \yii\widgets\ActiveForm|\yii\bootstrap4\ActiveForm|\yii\bootstrap5\ActiveForm */
/** @var $model \simialbi\yii2\bulletin\models\Board */
/** @var $icons array */
/** @var $users array */

?>

<div class="row form-row g-3">
    <?= $form->field($model, 'title', [
        'options' => [
            'class' => ['form-group', 'col-12', 'col-sm-6', 'col-lg-8']
        ]
    ])->textInput(); ?>
    <?= $form->field($model, 'icon', [
        'options' => [
            'class' => ['form-group', 'col-12', 'col-sm-6', 'col-lg-4']
        ]
    ])->widget(Select2::class, [
        'data' => $icons,
        'options' => [
            'placeholder' => ''
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'escapeMarkup' => new JsExpression('function (r) { return r; }')
        ]
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
    <div class="form-group col-12 field-authorized-users">
        <?= Html::label(Yii::t('simialbi/bulletin', 'Authorized users'), 'authorized-users', [
            'class' => ['form-label']
        ]); ?>
        <?php
        $options = [
            'id' => 'authorized-users',
            'multiple' => true,
            'placeholder' => Yii::t('simialbi/bulletin', 'Select users')
        ];
        if ($model->is_public){
            $options['disabled'] = true;
        }
        ?>
        <?= Select2::widget([
            'name' => 'authorized-users[]',
            'value' => ArrayHelper::getColumn($model->users, 'id'),
            'data' => $users,
            'options' => $options
        ]); ?>
    </div>
</div>

<?php
$id = Html::getInputId($model, 'is_public');
$js = <<<JS
jQuery('#$id').on('change.sa', function () {
    var \$this = jQuery(this),
        \$select2 = jQuery('#authorized-users');
    \$select2.attr('disabled', \$this.is(':checked'));
});
JS;

$this->registerJs($js);
