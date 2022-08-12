<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\models;

use simialbi\yii2\models\UserInterface;
use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $post_id
 * @property string $name
 * @property string $path
 * @property string $mime_type
 * @property int $size
 * @property int|string $created_by
 * @property int|string $updated_by
 * @property int|string|\DateTimeInterface $created_at
 * @property int|string|\DateTimeInterface $updated_at
 *
 * @property-read string $icon
 * @property-read Category $category
 * @property-read Topic $topic
 * @property-read Post $post
 * @property-read UserInterface $author
 * @property-read UserInterface $updater
 */
class Attachment extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName(): string
    {
        return '{{%bulletin__post_attachment}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'post_id', 'size'], 'integer'],
            [['name', 'path', 'mime_type'], 'string'],

            [['name', 'post_id', 'size', 'path', 'mime_type'], 'required']
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        return [
            'blameable' => [
                'class' => '\yii\behaviors\BlameableBehavior',
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    self::EVENT_BEFORE_UPDATE => 'updated_by'
                ]
            ],
            'timestamp' => [
                'class' => '\yii\behaviors\TimestampBehavior',
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => 'updated_at'
                ]
            ]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('simialbi/bulletin/model/attachment', 'Id'),
            'post_id' => Yii::t('simialbi/bulletin/model/attachment', 'Post'),
            'name' => Yii::t('simialbi/bulletin/model/attachment', 'Name'),
            'path' => Yii::t('simialbi/bulletin/model/attachment', 'Path'),
            'mime_type' => Yii::t('simialbi/bulletin/model/attachment', 'Mime type'),
            'size' => Yii::t('simialbi/bulletin/model/attachment', 'Size'),
            'created_by' => Yii::t('simialbi/bulletin/model/attachment', 'Created by'),
            'updated_by' => Yii::t('simialbi/bulletin/model/attachment', 'Updated by'),
            'created_at' => Yii::t('simialbi/bulletin/model/attachment', 'Created at'),
            'updated_at' => Yii::t('simialbi/bulletin/model/attachment', 'Updated at')
        ];
    }

    /**
     * Get attachment icon
     * @return string
     */
    public function getIcon(): string
    {
        switch ($this->mime_type) {
            case 'image/png':
            case 'image/jpeg':
            case 'image/gif':
            case 'image/wbmp':
            case 'image/bmp':
                return 'image';
            case 'application/msword':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.template':
            case 'application/vnd.ms-word.document.macroEnabled.12':
            case 'application/vnd.ms-word.template.macroEnabled.12':
                return 'file-word';
            case 'application/msexcel':
            case 'application/vnd.ms-excel':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.template';
            case 'application/vnd.ms-excel.sheet.macroEnabled.12';
            case 'application/vnd.ms-excel.template.macroEnabled.12';
            case 'application/vnd.ms-excel.addin.macroEnabled.12';
            case 'application/vnd.ms-excel.sheet.binary.macroEnabled.12';
                return 'file-excel';
            case 'application/mspowerpoint':
            case 'application/vnd.ms-powerpoint':
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
            case 'application/vnd.openxmlformats-officedocument.presentationml.template':
            case 'application/vnd.openxmlformats-officedocument.presentationml.slideshow':
            case 'application/vnd.ms-powerpoint.addin.macroEnabled.12':
            case 'application/vnd.ms-powerpoint.presentation.macroEnabled.12':
            case 'application/vnd.ms-powerpoint.template.macroEnabled.12':
            case 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12':
                return 'file-powerpoint';
            case 'application/pdf':
                return 'file-pdf';
            case 'application/json':
            case 'application/javascript':
            case 'application/xhtml+xml':
            case 'application/xml':
            case 'application/x-httpd-php':
            case 'text/css':
            case 'text/html':
            case 'text/javascript':
            case 'text/xml':
                return 'file-code';
            case 'video/mpeg':
            case 'video/mp4':
            case 'video/ogg':
            case 'video/quicktime':
            case 'video/vnd.vivo':
            case 'video/webm':
            case 'video/x-msvideo':
            case 'video/x-sgi-movie':
                return 'video';
            default:
                return 'file';
        }
    }

    /**
     * Get author
     * @return UserInterface
     */
    public function getAuthor(): UserInterface
    {
        return call_user_func([Yii::$app->user->identityClass, 'findIdentity'], $this->created_by);
    }

    /**
     * Get updater
     * @return UserInterface
     */
    public function getUpdater(): UserInterface
    {
        return call_user_func([Yii::$app->user->identityClass, 'findIdentity'], $this->updated_by);
    }

    /**
     * Get associated post
     * @return \yii\db\ActiveQuery
     */
    public function getPost(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Post::class, ['id' => 'post_id']);
    }

    /**
     * Get associated topic via post
     * @return \yii\db\ActiveQuery
     */
    public function getTopic(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Topic::class, ['id' => 'topic_id'])
            ->via('post');
    }

    /**
     * Get associated category via topic
     * @return \yii\db\ActiveQuery
     */
    public function getCategory(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id'])
            ->via('topic');
    }
}
