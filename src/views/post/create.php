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
/** @var $topic \simialbi\yii2\bulletin\models\Topic */
/** @var $model \simialbi\yii2\bulletin\models\Post */
/** @var $boardId int */
/** @var $module \simialbi\yii2\bulletin\Module */

$this->title = Yii::t('simialbi/bulletin', 'Create new post');
$this->params['breadcrumbs'] = [
    [
        'label' => Yii::t('simialbi/bulletin', 'Boards'),
        'url' => ['bulletin/index']
    ],
    [
        'label' => $topic->title,
        'url' => ['topic/view', 'id' => $topic->id, 'boardId' => $boardId]
    ],
    $this->title
];
?>

<div class="sa-bulletin-post-create">
    <?php $form = $class::begin([
        'id' => 'createPostForm'
    ]); ?>

    <div class="card">
        <div class="card-body">
            <?= $this->render('_form', [
                'form' => $form,
                'model' => $model,
                'boardId' => $boardId,
                'module' => $module
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
