<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin;

use simialbi\yii2\models\UserInterface;
use Yii;
use yii\base\InvalidConfigException;

class Module extends \simialbi\yii2\base\Module
{
    const EDITOR_NONE = 0;
    const EDITOR_SUMMERNOTE = 1;
    const EDITOR_FROALA = 2;

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'simialbi\yii2\bulletin\controllers';

    /**
     * @inheritdoc
     */
    public $defaultRoute = 'bulletin';

    /**
     * @var int Rich Text Editor to use for bulletin content
     */
    public int $rtfEditor = self::EDITOR_SUMMERNOTE;
    
    /**
     * @var bool Set `true` to send an email to creator if someone votes in a voting. Defaults to `false`.
     */
    public bool $sendMailOnVote = false;

    /**
     * {@inheritDoc}
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        $identity = new Yii::$app->user->identityClass;
        if (!($identity instanceof UserInterface)) {
            throw new InvalidConfigException('The "identityClass" must extend "simialbi\yii2\models\UserInterface"');
        }
        if (!Yii::$app->hasModule('gridview')) {
            $this->setModule('gridview', [
                'class' => '\kartik\grid\Module',
                'exportEncryptSalt' => 'ror_HTbRh0Ad7K7DqhAtZOp50GLyia4c',
                'i18n' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@kvgrid/messages',
                    'forceTranslation' => true
                ]
            ]);
        }

        parent::init();

        $this->registerTranslations();
    }
}
