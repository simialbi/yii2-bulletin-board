<?php

use rmrevin\yii\fontawesome\FAS;
use yii\helpers\Html;

$class = '\yii\widgets\ActiveForm';
if (class_exists('\yii\bootstrap4\ActiveForm')) {
    $class = '\yii\bootstrap4\ActiveForm';
} elseif (class_exists('\yii\bootstrap5\ActiveForm')) {
    $class = '\yii\bootstrap5\ActiveForm';
}

/** @var $this \yii\web\View */
/** @var $model \simialbi\yii2\bulletin\models\Board */
/** @var $icons array */
/** @var $users array */

$this->title = Yii::t('simialbi/bulletin', 'Update board {board}', [
    'board' => $model->title
]);
$this->params['breadcrumbs'] = [
    [
        'label' => Yii::t('simialbi/bulletin', 'Boards'),
        'url' => ['index']
    ],
    $this->title
];

?>

<div class="sa-bulletin-board-update">
    <?php $form = $class::begin([
        'id' => 'updateBoardForm'
    ]); ?>

    <div class="card">
        <div class="card-body">
            <?= $this->render('_form', [
                'form' => $form,
                'model' => $model,
                'icons' => $icons,
                'users' => $users
            ]); ?>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <?= Html::submitButton(FAS::i('save') . ' ' . Yii::t('simialbi/bulletin', 'Save'), [
                'class' => ['btn', 'btn-primary']
            ]); ?>
        </div>
    </div>

    <?php $class::end(); ?>
</div>

<?php
