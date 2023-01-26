<?php

use rmrevin\yii\fontawesome\FAS;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

$navClass = '\yii\widgets\Menu';
$formClass = '\yii\widgets\ActiveForm';
if (class_exists('\yii\bootstrap4\Nav')) {
    $navClass = '\yii\bootstrap4\Nav';
    $formClass = '\yii\bootstrap4\ActiveForm';
} elseif (class_exists('\yii\bootstrap5\Nav')) {
    $navClass = '\yii\bootstrap5\Nav';
    $formClass = '\yii\bootstrap5\ActiveForm';
}

/** @var $this \yii\web\View */
/** @var $model \simialbi\yii2\bulletin\models\Voting */
/** @var $navigation array */
/** @var $board \simialbi\yii2\bulletin\models\Board */
/** @var $userHasVoted bool */
/** @var $userAnswer \simialbi\yii2\bulletin\models\VotingUserAnswer */
/** @var $results bool */

if (!Yii::$app->request->isAjax):
?>
<div class="row">
    <div class="col-2 col-sm-4">
        <div class="card">
            <div class="card-body">
                <?= $navClass::widget([
                    'items' => $navigation,
                    'options' => [
                        'class' => ['flex-column', 'nav-pills']
                    ],
                    'encodeLabels' => false
                ]); ?>
            </div>
        </div>
    </div>
    <div class="col-10 col-sm-8">
<?php
endif;

Pjax::begin([
    'id' => 'bulletin-content-pjax'
]);

$form = $formClass::begin([
    'id' => 'votingForm',
    'action' => ['voting/vote', 'id' => $model->id, 'boardId' => $board->id]
]);
?>
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <a type="button" href="<?= Url::to(['topic/view', 'id' => $model->topic_id, 'boardId' => $board->id]) ?>"
               class="btn btn-primary mr-1 me-1">
                <?= FAS::i('arrow-left'); ?>
            </a>
            <h4 class="card-title my-0 ml-2 ms-2"><?= $model->question; ?></h4>
        </div>
        <div class="card-body">
            <?php if ($userHasVoted || $results): ?>
                <?= $this->render('_results', [
                    'model' => $model,
                    'boardId' => $board->id
                ]); ?>
            <?php else: ?>
                <?= $this->render('_form', [
                    'form' => $form,
                    'model' => $model,
                    'userAnswer' => $userAnswer
                ]); ?>
            <?php endif; ?>
        </div>
        <?php if (!$userHasVoted): ?>
            <div class="card-footer d-flex justify-content-end">
                <?= Html::submitButton(FAS::i('save') . ' ' . Yii::t('simialbi/bulletin', 'Save'), [
                    'class' => ['btn', 'btn-primary']
                ]); ?>
            </div>
        <?php endif; ?>
    </div>
<?php
$formClass::end();
Pjax::end();

if (!Yii::$app->request->isAjax):
    ?>
        </div>
    </div>
<?php
endif;
