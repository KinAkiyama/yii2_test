<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\components\AuthFillter;
use app\models\Token;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class UserController extends Controller
{
    public $modelClass = 'app\models\User';

    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => AuthFillter::class,
                'except' => ['login'],
            ],
        ];
    }

    public function actionGetUserInfo () { //необходимо указать токен в headers
        $request = Yii::$app->request;
        $authHeader = $request->headers->get('Authorization');

        if(!$authHeader) {
            throw new UnauthorizedHttpException('Authorization header missing');
        }

        $token = str_replace('Bearer ', '', $authHeader);

        $tokenModel = Token::find()->where(['token' => $token])->one();

        if (!$tokenModel || strtotime($tokenModel->expires_at) < time()) {
            throw new UnauthorizedHttpException('Invalid or expired token');
        }

        $user = User::findOne($tokenModel->user_id);

        if (!$user) {
            throw new UnauthorizedHttpException('User not found');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $user;
    }

}
