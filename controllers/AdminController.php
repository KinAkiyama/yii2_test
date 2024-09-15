<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;
use yii\web\Response;
use app\models\Token;
use app\models\User;
use yii\web\BadRequestHttpException;

class AdminController extends Controller //необходимо указать токен в headers
{

    protected function checkAdminRole()
    {
        $request = Yii::$app->request;
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader) {
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

        if ($user->role !== 1) {
            throw new UnauthorizedHttpException('Access denied');
        }

        return $user;
    }

    public function actionGetUsers()
    {
        $this->checkAdminRole();

        $users = User::find()->all();

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $users;
    }

    public function actionCreateUser()
    {
        $this->checkAdminRole();

        $request = Yii::$app->request;
        $data = $request->post();

        $user = new User();
        $user->username = isset($data['username']) ? $data['username'] : null;
        $user->email = isset($data['email']) ? $data['email'] : null;
        $user->setPassword(isset($data['password']) ? $data['password'] : null);
        $user->role = isset($data['role']) ? (int)$data['role'] : 0;

        if ($user->save()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $user;
        } else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            throw new BadRequestHttpException('Failed to create user: ' . implode(", ", $user->errors));
        }
    }

    public function actionUpdateUser($id)
    {
        $this->checkAdminRole();

        $request = Yii::$app->request;
        $data = $request->post();

        $user = User::findOne($id);

        if (!$user) {
            throw new BadRequestHttpException('User not found');
        }

        $user->attributes = $data;

        if (!$user->validate() || !$user->save()) {
            throw new BadRequestHttpException('Failed to update user');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $user;
    }

    public function actionDeleteUser($id)
    {
        $this->checkAdminRole();

        $user = User::findOne($id);

        if (!$user) {
            throw new BadRequestHttpException('User not found');
        }

        if (!$user->delete()) {
            throw new BadRequestHttpException('Failed to delete user');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return ['status' => 'success'];
    }
}
