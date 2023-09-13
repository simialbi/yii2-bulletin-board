<?php

use kartik\select2\Select2;
use rmrevin\yii\fontawesome\FAS;
use simialbi\yii2\bulletin\Module;
use simialbi\yii2\dropzone\DropZone;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\ReplaceArrayValue;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var $this \yii\web\View */
/** @var $boardId int|null */
/** @var $boards array */
/** @var $form \yii\widgets\ActiveForm|\yii\bootstrap4\ActiveForm|\yii\bootstrap5\ActiveForm */
/** @var $topic \simialbi\yii2\bulletin\models\Topic */
/** @var $post \simialbi\yii2\bulletin\models\Post */
/** @var $categories array */
/** @var $voting \simialbi\yii2\bulletin\models\Voting */
/** @var $votingAnswer \simialbi\yii2\bulletin\models\VotingAnswer */
/** @var $rtfEditor integer */

$i = 0;
?>
<div class="row">
    <div class="col-12 col-sm-7 col-md-8">
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
                    'class' => ['form-group', 'col-2']
                ]
            ])->checkbox(); ?>
            <?= $form->field($topic, 'has_voting', [
                'options' => [
                    'class' => ['form-group', 'col-2']
                ]
            ])->checkbox(); ?>
            <?= $form->field($voting, 'multiple_answers_allowed', [
                'options' => [
                    'class' => ['form-group', 'col-3']
                ]
            ])->checkbox(['disabled' => !$topic->has_voting]); ?>
            <?php
            $field = $form->field($post, 'text', [
                'options' => [
                    'class' => ['form-group', 'col-12']
                ]
            ]);
            switch ($rtfEditor) {
                case Module::EDITOR_NONE:
                    $field->textarea([
                        'rows' => 10,
                    ]);
                    break;
                case Module::EDITOR_SUMMERNOTE:
                    $field->widget(\marqu3s\summernote\Summernote::class, [
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
                    ]);
                    break;
                case Module::EDITOR_FROALA:
                    $field->widget(\sandritsch91\yii2\froala\FroalaEditor::class, [
                        'clientOptions' => [
                            'height' => 300,
                            'imagePaste' => false
                        ]
                    ]);
                    break;
                default:
                    throw new \yii\base\InvalidConfigException('Invalid rich text editor');
            }
            echo $field;
            ?>
            <div class="form-group col-12 field-attachment">
                <?php
                $files = [];
                if ($post->getAttachments()->count('id') > 0) {
                    foreach ($post->attachments as $attachment) {
                        $file = [
                            'name' => $attachment->name,
                            'size' => $attachment->size
                        ];
                        if ($attachment->icon === 'image') {
                            $file['url'] = Url::to(
                                $attachment->path,
                                Yii::$app->request->isSecureConnection ? 'https' : 'http'
                            );
                        }
                        $files[] = $file;
                    }
                }
                ?>
                <?= DropZone::widget([
                    'url' => Url::to(['attachment/upload', 'boardId' => $boardId]),
                    'name' => 'attachment',
                    'storedFiles' => $files,
                    'options' => [
                        'class' => ['border-1', 'border-light', 'rounded']
                    ],
                    'clientOptions' => [
                        'addRemoveLinks' => true,
                        'dictDefaultMessage' => Yii::t('simialbi/bulletin', 'Drop files here to upload'),
                        'dictRemoveFile' => Yii::t('simialbi/bulletin', 'Remove file'),
                        'dictRemoveFileConfirmation' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    ],
                    'clientEvents' => [
                        'removedfile' => new JsExpression('function (file) {
                            jQuery.ajax({
                                url: \'' . Url::to(['attachment/delete', 'postId' => $post->id]) . '\',
                                method: \'post\',
                                data: {
                                    file: file.name
                                }
                            });
                        }'),
                        'success' => new JsExpression('function (file, response) {
                            jQuery(file.previewElement)
                                .closest(\'form\')
                                .prepend(\'<input type="hidden" name="attachments[]" value="\' + response + \'">\');
                        }')
                    ]
                ]); ?>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-5 col-md-4 border-left">
        <div class="row form-row g-3 answer-container">
            <div class="col-10">
                <?= $form->field($voting, 'question', [
                    'options' => [
                        'class' => ['form-group']
                    ]
                ])->textInput(['disabled' => !$topic->has_voting]); ?>
            </div>
            <div class="col-2 d-flex align-items-end">
                <button type="button" class="btn btn-primary"<?= (!$topic->has_voting) ? ' disabled' : ''; ?> id="add-answer-btn">
                    <?= FAS::i('plus'); ?>
                </button>
            </div>
            <?= $form->field($votingAnswer, '[]answer', [
                'options' => [
                    'class' => ['form-group', 'col-12'],
                    'style' => ['display' => 'none']
                ]
            ])->textInput(['disabled' => true]); ?>
            <?php if ($topic->has_voting): ?>
                <?php foreach ($voting->answers as $i => $answer): ?>
                    <?= $form->field($answer, '[]answer', [
                        'options' => [
                            'class' => ['form-group', 'col-12']
                        ],
                        'inputOptions' => [
                            'id' => 'votinganswer-answer-' . $i
                        ]
                    ]); ?>
                <?php endforeach; ?>
                <?php if (count($voting->answers)): ?>
                    <?php $i++; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php

$idCheckbox = Html::getInputId($topic, 'has_voting');
$idQuestion = Html::getInputId($voting, 'question');
$idAnswer = Html::getInputId($votingAnswer, 'answer');
$idMultipleAnswers = Html::getInputId($voting, 'multiple_answers_allowed');
$errorString = Yii::t('yii', '{attribute} must be a string.', [
    'attribute' => $votingAnswer->getAttributeLabel('answer')
]);
$errorRequired = Yii::t('yii', '{attribute} cannot be blank.', [
    'attribute' => $votingAnswer->getAttributeLabel('answer')
]);
$js = <<<JS
var \$template = jQuery('#$idAnswer').closest('.field-votinganswer-answer').clone();
\$template.attr('style', '');
\$template.find('input').prop('disabled', false);
var i = $i;
jQuery('#createTopicForm').yiiActiveForm('remove', 'votinganswer-answer');
jQuery('#$idAnswer').closest('.field-votinganswer-answer').remove();

jQuery('#$idCheckbox').on('change.sa', function () {
    var hasVoting = jQuery(this).is(':checked');
    jQuery('#$idQuestion').prop('disabled', !hasVoting);
    jQuery('#$idMultipleAnswers').prop('disabled', !hasVoting);
    jQuery('#add-answer-btn').prop('disabled', !hasVoting);
    
    if (hasVoting) {
        addAnswer();
    } else {
        var \$answers = jQuery('.answer-container').find('.field-votinganswer-answer');
        \$answers.each(function () {
            var \$this = jQuery(this),
                id = \$this.data('key');
            jQuery('#createTopicForm').yiiActiveForm('remove', 'votinganswer-answer-' + id);
            \$this.remove();
        });
        i = 0;
    }
});
jQuery('#add-answer-btn').on('click', addAnswer);

function addAnswer() {
    var \$answer = \$template.clone(),
        id = 'votinganswer-answer-' + i;
    \$answer.addClass('field-votinganswer-answer-' + i).data('key', i);
    \$answer.find('label').attr('for', 'voting-answer-' + i);
    \$answer.find('input').prop('id', 'voting-answer-' + i);
    jQuery('.answer-container').append(\$answer);
    jQuery('#createTopicForm').yiiActiveForm('add', {
        id: id,
        name: 'answer',
        container: '.field-votinganswer-answer-' + i,
        input: '#voting-answer-' + i,
        error: '.invalid-feedback',
        validate: function (attribute, value, messages, deferred, \$form) {
            yii.validation.string(value, messages, {message: '$errorString', skipOnEmpty: true});
            yii.validation.required(value, messages, {message: '$errorRequired'});
        }
    });
    i++;
}
JS;

$this->registerJs($js, $this::POS_LOAD);
