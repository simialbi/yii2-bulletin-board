<?php

use simialbi\yii2\chart\models\axis\CategoryAxis;
use simialbi\yii2\chart\models\axis\ValueAxis;
use simialbi\yii2\chart\models\data\JSONParser;
use simialbi\yii2\chart\models\series\ColumnSeries;
use simialbi\yii2\chart\widgets\LineChart;
use yii\helpers\Url;
use yii\web\JsExpression;


/** @var $this \yii\web\View */
/** @var $model \simialbi\yii2\bulletin\models\Voting */
/** @var $boardId int */

$series = new ColumnSeries([
    'dataFields' => [
        'categoryX' => 'answer',
        'valueY' => 'count'
    ],
    'name' => Yii::t('simialbi/bulletin', 'Answers')
]);
$series->appendix = new JsExpression("
{$series->varName}.columns.template.tooltipText = '{users}';
{$series->varName}.columns.template.adapter.add('fill', function(fill, target) {
    return chartResultChart.colors.getIndex(target.dataItem.index);
});
var bullet = {$series->varName}.bullets.push(new am4charts.LabelBullet());
bullet.interactionsEnabled = false;
bullet.dy = 50;
bullet.label.text = '{valueY}';
bullet.label.fontSize = '40px';
bullet.label.fill = am4core.color('#ffffff');");
$categoryAxis = new CategoryAxis([
    'dataFields' => ['category' => 'answer']
]);
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
                'url' => Url::to(['voting/chart-data', 'id' => $model->id, 'boardId' => $boardId]),
                'parser' => new JSONParser([
                    'options' => [
                        'emptyAs' => 0,
                        'numberFields' => ['count']
                    ]
                ])
            ],
            'options' => [
                'id' => 'resultChart',
                'style' => [
                    'width' => '100%',
                    'height' => '400px'
                ]
            ]
        ]); ?>
    </div>
</div>
