<?php

namespace app\controllers;

use Yii;
use App\models\User;
use app\components\AuthFillter;
use yii\rest\Controller;
use yii\filters\AccessControl;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;

class UserController extends Controller
{
    public $modelClass = 'app\models\User';

    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => AuthFillter::class,
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    'allow' => true,
                    'roles' => ['0'],
                ],
                [
                    'allow' => true,
                    'actions' => ['update', 'delete', 'change-role'],
                    'roles' => ['1'],
                    'matchCallback' => function ($rule, $action) {
                        return Yii::$app->user->identity->role === 1;
                    },
                ],
            ],
        ];
    }

    public function actionUpdate($id)
    {
        $user = $this->findModel($id);

        $data = Yii::$app->request->getBodyParams();
        $user->load($data, '');

        if ($user->save()) {
            return $user;
        } else {
            Yii::$app->response->statusCode = 422;
            return $user->errors;
        }
    }

    public function actionDelete($id)
    {
        $user = $this->findModel($id);

        if ($user->delete()) {
            return ['status' => 'User deleted successfully'];
        } else {
            Yii::$app->response->statusCode = 422;
            return ['status' => 'Failed to delete user'];
        }
    }

    public function actionChangeRole($id)
    {
        $user = $this->findModel($id);

        $data = Yii::$app->request->getBodyParams();
        $role = $data['role'] ?? null;

        if (!in_array($role, [1,0])) {
            Yii::$app->response->statusCode = 400;
            return ['status' => 'Invalid role'];
        }

        $user->role = $role;

        if ($user->save()) {
            return $user;
        } else {
            Yii::$app->response->statusCode = 422;
            return $user->errors;
        }
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
