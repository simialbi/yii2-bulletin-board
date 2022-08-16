<?php

use kartik\grid\GridView;
use kartik\select2\Select2;
use rmrevin\yii\fontawesome\FAS;
use yii\helpers\Html;

/** @var $this \yii\web\View */
/** @var $searchModel \simialbi\yii2\bulletin\models\SearchBoard */
/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $users array */

$this->title = Yii::t('simialbi/bulletin', 'Boards');
$this->params['breadcrumbs'] = [
    $this->title
];

$toolbar = [];
if (Yii::$app->user->can('bulletinCreateBoard')) {
    $toolbar = [
        [
            'content' => Html::a(FAS::i('plus') . ' <span class="d-none d-lg-inline-block">' . Yii::t('simialbi/bulletin', 'Create board') . '</span>', ['create'], [
                'class' => ['btn', 'btn-primary'],
                'data' => [
                    'pjax' => '0'
                ]
            ])
        ]
    ];
}
?>
<div class="sa-bulletin-board-index">
    <?= GridView::widget([
        'bsVersion' => 5,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax' => true,
        'export' => false,
        'bordered' => false,
        'panel' => [
            'heading' => $this->title,
            'headingOptions' => [
                'class' => [
                    'card-header',
                    'd-flex',
                    'align-items-center',
                    'justify-content-between',
                    'bg-white'
                ]
            ],
            'titleOptions' => [
                'class' => ['card-title', 'm-0']
            ],
            'summaryOptions' => [
                'class' => []
            ],
            'beforeOptions' => [
                'class' => [
                    'card-body',
                    'py-2',
                    'border-bottom',
                    'd-flex',
                    'justify-content-between',
                    'align-items-center'
                ]
            ],
            'footerOptions' => [
                'class' => ['card-footer', 'bg-white']
            ],
            'options' => [
                'class' => ['card']
            ]
        ],
        'panelTemplate' => '
            {panelHeading}
            {panelBefore}
            {items}
            {panelFooter}
        ',
        'panelHeadingTemplate' => '
            {title}
            {toolbar}
        ',
        'panelFooterTemplate' => '{pager}{footer}',
        'panelBeforeTemplate' => '{pager}{summary}',
        'panelAfterTemplate' => '',
        'containerOptions' => [],
        'toolbar' => $toolbar,
        'columns' => [
            [
                'class' => '\kartik\grid\SerialColumn',
                'vAlign' => GridView::ALIGN_MIDDLE
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'icon',
                'value' => function ($model) {
                    /** @var $model \simialbi\yii2\bulletin\models\Board */
                    return FAS::i($model->icon);
                },
                'format' => 'raw',
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'width' => '60px'
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'title',
                'vAlign' => GridView::ALIGN_MIDDLE
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'created_at',
                'format' => 'datetime',
                'vAlign' => GridView::ALIGN_MIDDLE
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'created_by',
                'filter' => $users,
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'options' => [
                        'placeholder' => ''
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ],
                'value' => 'author.fullname',
                'vAlign' => GridView::ALIGN_MIDDLE
            ],
            [
                'class' => '\kartik\grid\ActionColumn',
                'template' => '{update} {delete}',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'visibleButtons' => [
                    'update' =>  Yii::$app->user->can('bulletinUpdateBoard'),
                    'delete' =>  Yii::$app->user->can('bulletinDeleteBoard')
                ]
            ]
        ]
    ]); ?>
</div>
