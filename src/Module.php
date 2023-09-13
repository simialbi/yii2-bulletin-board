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
     * Rich Text Editor to use for bulletin content
     * @var integer
     */
    public int $rtfEditor = self::EDITOR_SUMMERNOTE;

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
