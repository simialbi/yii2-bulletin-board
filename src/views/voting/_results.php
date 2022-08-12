<?php

use simialbi\yii2\chart\models\axis\CategoryAxis;
use simialbi\yii2\chart\models\axis\ValueAxis;
use simialbi\yii2\chart\models\data\JSONParser;
use simialbi\yii2\chart\models\series\ColumnSeries;
use simialbi\yii2\chart\widgets\LineChart;
use yii\helpers\Url;


/** @var $this \yii\web\View */
/** @var $model \simialbi\yii2\bulletin\models\Voting */

$series = new ColumnSeries([
    'dataFields' => [
        'categoryX' => 'answer',
        'valueY' => 'count'
    ],
    'name' => Yii::t('simialbi/bulletin', 'Answers')
]);
$categoryAxis = new CategoryAxis([
    'dataFields' => ['category' => 'answer']
]);
$data = $model;
?>

<div class="row">
    <div class="col-12">
        <?= LineChart::widget([
            'series' => [$series],
            'axes' => [
                $categoryAxis,
                new ValueAxis()
            ],
            'dataSource' => [
                'url' => Url::to(['voting/chart-data', 'id' => $model->id]),
                'parser' => new JSONParser([
                    'options' => [
                        'emptyAs' => 0,
                        'numberFields' => ['count']
                    ]
                ])
            ],
            'options' => [
                'id' => 'result-chart',
                'style' => [
                    'width' => '100%',
                    'height' => '300px'
                ]
            ]
        ]); ?>
    </div>
</div>
