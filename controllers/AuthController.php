<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017-08-22
 * Time: 10:58 PM
 */

class AuthController extends BaseController
{

    /**
     * Sends a secure token with admin permissions
     */
    public function secureToken(){

        $user = $this->request->getPost("username","string");
        $pass = $this->request->getPost("password","string");

        if( (isset($user) && !empty($user) && $user === 'cloud') && (isset($pass) && !empty($pass) && $pass === 'nopass') ){
            $payload = [
                'sub'   => 1,
                'role'  => 'admin',
                'iat' => time(),
            ];
            $token = $this->auth->make($payload);

            $this->response->setJsonContent(['status' => 'OK','token' => $token]);

        }else{
            $this->response
                ->setStatusCode(401, "Unauthorized")
                ->setJsonContent(['status' => 'ERROR','data' => "Unauthorized access."]);
        }

        return $this->response;
    }

    /**
     * Sends a guest token
     */
    public function guestToken(){
        $payload = [
            'sub'   => 2,
            'role'  => 'guest',
            'iat' => time(),
        ];
        $token = $this->auth->make($payload);

        $this->response->setJsonContent(['status' => 'OK','token' => $token]);

        return $this->response;
    }

}