<?php

namespace app\components;

use Yii;
use yii\base\ActionFilter;
use yii\web\UnauthorizedHttpException;
use app\models\Token;

class AuthFillter extends ActionFilter
{
    public function beforeAction($action)
    {
        $token = Yii::$app->request->heasders->get('Authorization');

        if (!$token) {
            throw new UnauthorizedHttpException('Authorization header required');
        }

        $token = str_replace('Bearer ', '', $token);

        if (!Token::find()->where(['token' => $token])->exists()) {
            throw new UnauthorizedHttpException('Invalid token');
        }

        return parent::beforeAction($action);
    }
}