<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\User;

class UserController extends Controller
{
    public function actionRegister($username, $email, $password, $role)
    {
        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->setPassword($password);
        $user->role = $role;

        if ($user->save()) {
            echo "User registered successfully!\n";
        } else {
            echo "Failed to register user.\n";
            foreach ($user->errors as $error) {
                echo implode(", ", $error) . "\n";
            }
        }
    }
}