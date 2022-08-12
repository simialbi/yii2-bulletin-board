<?php


/** @var $this \yii\web\View */
/** @var $form \yii\widgets\ActiveForm|\yii\bootstrap4\ActiveForm|\yii\bootstrap5\ActiveForm */
/** @var $model \simialbi\yii2\bulletin\models\Voting */
/** @var $userAnswer \simialbi\yii2\bulletin\models\VotingUserAnswer */

?>

<div class="row form-row g-3">
    <?php if ($model->multiple_answers_allowed): ?>
        <?= $form->field($userAnswer, 'answer_id', [
            'options' => [
                'class' => ['form-group', 'col-12']
            ]
        ])->checkboxList($model->getAnswers()->select(['answer', 'id'])->indexBy('id')->column()); ?>
    <?php else: ?>
        <?= $form->field($userAnswer, 'answer_id', [
            'options' => [
                'class' => ['form-group', 'col-12']
            ]
        ])->radioList($model->getAnswers()->select(['answer', 'id'])->indexBy('id')->column()); ?>
    <?php endif; ?>
</div>
