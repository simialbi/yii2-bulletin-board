<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\controllers;

use simialbi\yii2\bulletin\models\Attachment;
use simialbi\yii2\bulletin\models\Post;
use simialbi\yii2\dropzone\AfterUploadEvent;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\helpers\FileHelper;
use yii\web\Controller;

class AttachmentController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => '\yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['upload'],
                        'roles' => ['bulletinAuthor']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['bulletinUpdatePost'],
                        'roleParams' => function () {
                            return ['post' => Post::findOne(Yii::$app->request->get('postId'))];
                        }
                    ]
                ]
            ],
            'verbs' => [
                'class' => '\yii\filters\VerbFilter',
                'actions' => [
                    'delete' => ['POST', 'DELETE'],
                ]
            ]
        ];
    }


    /**
     * {@inheritDoc}
     */
    public function actions(): array
    {
        return [
            'upload' => [
                'class' => '\simialbi\yii2\dropzone\UploadAction',
                'fileName' => 'attachment',
                'upload' => 'web/uploads/bulletin-board',
                'on afterUpload' => function (AfterUploadEvent $event) {
                    echo basename($event->path);
                    Yii::$app->response->send();
                }
            ]
        ];
    }

    /**
     * Delete an attachment
     *
     * @param int|null $postId The posts id
     *
     * @return void
     * @throws StaleObjectException|Throwable
     */
    public function actionDelete(?int $postId = null): void
    {
        $fileName = Yii::$app->request->getBodyParam('file');
        $path = FileHelper::normalizePath(Yii::getAlias("@webroot/web/uploads/bulletin-board/$postId/$fileName"));

        FileHelper::unlink($path);
        if ($postId && ($file = Attachment::findOne(['name' => $fileName, 'post_id' => $postId]))) {
            $file->delete();
        }

        Yii::$app->response->setStatusCode(204)->send();
    }
}
