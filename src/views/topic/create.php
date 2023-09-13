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
/** @var $boardId int */
/** @var $boards array */
/** @var $topic \simialbi\yii2\bulletin\models\Topic */
/** @var $post \simialbi\yii2\bulletin\models\Post */
/** @var $categories array */
/** @var $voting \simialbi\yii2\bulletin\models\Voting */
/** @var $votingAnswer \simialbi\yii2\bulletin\models\VotingAnswer */
/** @var $rtfEditor integer */

$this->title = Yii::t('simialbi/bulletin', 'Create topic');
$this->params['breadcrumbs'] = [
    [
        'label' => Yii::t('simialbi/bulletin', 'Boards'),
        'url' => ['bulletin/index']
    ],
    $this->title
];
?>

<div class="sa-bulletin-topic-create">
    <?php $form = $class::begin([
        'id' => 'createTopicForm'
    ]); ?>

    <div class="card">
        <div class="card-body">
            <?= $this->render('_form', [
                'form' => $form,
                'boardId' => $boardId,
                'boards' => $boards,
                'topic' => $topic,
                'post' => $post,
                'categories' => $categories,
                'voting' => $voting,
                'votingAnswer' => $votingAnswer,
                'rtfEditor' => $rtfEditor
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
