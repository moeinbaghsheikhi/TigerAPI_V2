<?php

namespace App\Controllers;

use App\Database\QueryBuilder;
use App\Traits\ResponseTrait;
use App\Auth\JWTAuth as JWTAuth;
use App\Validations\ValidateData;

class AuthController
{
    use ResponseTrait;
    use JWTAuth;
    use ValidateData;

    protected $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
    }

    public function login($request)
    {
        // validate request
        $this->validate([
            'username||required|min:3|max:25',
            'password||required|min:8'
        ], $request);

        // get user
        $findUser = $this->queryBuilder->table('users')
            ->where('username', '=', $request->username)
            ->where('password', '=', $request->password)
            ->get()->execute();

        // Example validation: check if username is 'admin' and password is 'admin123'
        if ($findUser) {
            // Generate JWT token
            $token = $this->generateToken($request->username, $request->password);

            // Return token as JSON response
            return $this->sendResponse(data: ['token' => $token], message: "با موفقیت وارد شدید");
        } else {
            // If credentials are not valid, return error response
            return $this->sendResponse(message: "نام کاربری یا رمز عبور شما صحیح نیست!", error: true, status:  HTTP_Unauthorized);
        }
    }

    public function register($request){
        // validate request
        $this->validate([
            'username||required|min:3|max:25',
            'password||required|min:8'
        ], $request);
        $this->checkUnique('users', 'username', $request->username);

        $newUser = $this->queryBuilder->table('users')
            ->insert([
                'username' => $request->username,
                'password' => $request->password
            ])->execute();

        return $this->sendResponse(data: $newUser, message: "حساب کاربری شما با موفقیت ایجاد شد!");
    }

    public function verify($request){
        $verification = $this->verifyToken($request->token);

        return $this->sendResponse(data:$verification, message: "Unauthorized token body!" ,error: true, status: HTTP_BadREQUEST);
    }
}