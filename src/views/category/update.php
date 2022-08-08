<?php

$class = '\yii\widgets\ActiveForm';
if (class_exists('\yii\bootstrap4\ActiveForm')) {
    $class = '\yii\bootstrap4\ActiveForm';
} elseif (class_exists('\yii\bootstrap5\ActiveForm')) {
    $class = '\yii\bootstrap5\ActiveForm';
}

/** @var $this \yii\web\View */
/** @var $model \simialbi\yii2\bulletin\models\Category */

$this->title = Yii::t('simialbi/bulletin', 'Update category {category}', [
    'category' => $model->title
]);
$this->params['breadcrumbs'] = [
    [
        'label' => Yii::t('simialbi/bulletin', 'Categories'),
        'url' => ['index']
    ],
    $this->title
];

?>

<div class="sa-bulletin-category-update">
    <?php $form = $class::begin([
        'id' => 'updateCategoryForm'
    ]); ?>

    <div class="card">
        <div class="card-body">
            <?= $this->render('_form', [
                'form' => $form,
                'model' => $model
            ]); ?>
        </div>
    </div>

    <?php $class::end(); ?>
</div>

<?php
