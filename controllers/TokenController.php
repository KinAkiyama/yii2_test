<?php

namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\rest\Controller;
use app\models\User;
use app\models\Token;
use yii\web\BadRequestHttpException;

class TokenController extends Controller
{
    public function actionLogin()
    {
        $request = Yii::$app->request;
        $username = $request->post('username');
        $password = $request->post('password');

        $user = User::findByUsername($username);

        if (!$user || !$user->validatePassword($password)){
            throw new BadRequestHttpException('Invalid username or password');
        }

        $token = Yii::$app->security->generateRandomString(32);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+5 hour'));

        $tokenModel = new Token();
        $tokenModel->user_id = $user->id;
        $tokenModel->token = $token;
        $tokenModel->expires_at = $expiresAt;
        $tokenModel->save();

        return ['token' => $token];
    }

}
