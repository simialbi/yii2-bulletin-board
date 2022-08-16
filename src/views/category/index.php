<?php

use kartik\grid\GridView;
use rmrevin\yii\fontawesome\FAS;
use yii\helpers\Html;

/** @var $this \yii\web\View */
/** @var $searchModel \simialbi\yii2\bulletin\models\SearchCategory */
/** @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = Yii::t('simialbi/bulletin', 'Categories');
$this->params['breadcrumbs'] = [
    $this->title
];

$toolbar = [];
if (Yii::$app->user->can('bulletinCreateCategory')) {
    $toolbar = [
        [
            'content' => Html::a(FAS::i('plus') . ' <span class="d-none d-lg-inline-block">' . Yii::t('simialbi/bulletin', 'Create category') . '</span>', ['create'], [
                'class' => ['btn', 'btn-primary'],
                'data' => [
                    'pjax' => '0'
                ]
            ])
        ]
    ];
}
?>
<div class="sa-bulletin-category-index">
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
                'value' => 'author.fullname',
                'vAlign' => GridView::ALIGN_MIDDLE
            ],
            [
                'class' => '\kartik\grid\ActionColumn',
                'template' => '{update} {delete}',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'visibleButtons' => [
                    'update' =>  Yii::$app->user->can('bulletinUpdateCategory'),
                    'delete' =>  Yii::$app->user->can('bulletinDeleteCategory')
                ]
            ]
        ]
    ]); ?>
</div>
