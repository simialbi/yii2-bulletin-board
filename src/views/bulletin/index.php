<?php


use rmrevin\yii\fontawesome\FAS;
use yii\helpers\Url;
use yii\widgets\ListView;

$navClass = '\yii\widgets\Menu';
$pagerClass = '\yii\widgets\LinkPager';
if (class_exists('\yii\bootstrap4\Nav')) {
    $navClass = '\yii\bootstrap4\Nav';
    $pagerClass = '\yii\bootstrap4\LinkPager';
} elseif (class_exists('\yii\bootstrap5\Nav')) {
    $navClass = '\yii\bootstrap5\Nav';
    $pagerClass = '\yii\bootstrap5\LinkPager';
}

/** @var $this \yii\web\View */
/** @var $navigation array */
/** @var $board \simialbi\yii2\bulletin\models\Board|null */
/** @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = Yii::t('simialbi/bulletin', 'Overview');
$this->params['breadcrumbs'] = [
    $this->title
];
?>

<div class="sa-bulletin-bulletin-index">
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
            <?php if ($board): ?>
                <div class="card">
                    <div class="card-header d-flex justify-content-around align-items-center">
                        <h4 class="card-title m-0"><?= $board->title; ?></h4>
                        <a type="button" href="<?= Url::to(['topic/create', 'boardId' => $board->id]); ?>"
                           class="btn btn-primary ml-auto ms-auto">
                            <?= FAS::i('plus') ?>
                        </a>
                    </div>
                    <div class="card-body">
                        <?= ListView::widget([
                            'dataProvider' => $dataProvider
                        ]); ?>
                    </div>
                    <?php if ($dataProvider->getCount() > 0): ?>
                        <div class="card-footer">
                            <?= $pagerClass::widget([
                                'pagination' => $dataProvider->getPagination()
                            ]); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
