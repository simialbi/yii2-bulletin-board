<?php

use kartik\grid\GridView;

/** @var $this \yii\web\View */
/** @var $searchModel \simialbi\yii2\bulletin\models\SearchCategory */
/** @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = Yii::t('simialbi/bulletin', 'Categories');
$this->params['breadcrumbs'] = [
    $this->title
];

?>
<div class="sa-bulletin-category-index">
    <?= GridView::widget([
        'bsVersion' => 5,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax' => true,
        'export' => false,
        'bordered' => false,
        'panel' => [],
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
                'class' => '\kartik\grid\ActionColumn',
                'template' => '{update} {delete}',
                'vAlign' => GridView::ALIGN_MIDDLE
            ]
        ]
    ]); ?>
</div>
