<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Controllers\AuthController;
use Illuminate\Support\Facades\Hash;
use App\Helper\Helper;
use App\User;

class UserTest extends TestCase
{
    protected $name;
    protected $email;
    protected $password;

    public function createFacker(){
        $userDetail = helper::createFacker();
        $this->name               = $userDetail->name;
        $this->email              = $userDetail->email;
        $this->password           = $userDetail->password;

    }

    public function testRegisterUserSection()
    {
        try{
            $this->createFacker();
            $paramTest           = [
                'name'      => $this->name,
                'email'     => $this->email,
                'password'  => $this->password
            ];
            $paramCase           = [
                'success'   => true,
                'code'      => 200,
                'message'   => 'Registration Sukses',
                'data'      => [
                    'name'      => $this->name,
                    'email'     => $this->email
                ],
            ];
            $status             = $this->call(
                'POST',
                '/register',
                $paramTest,
                [],
                [],
                [],
                []);
            $data               = (array)json_decode($this->response->getContent(),true);
            if(sizeof($data)>0 AND $data['success'] == true){
                $result             = [
                    'success'   => $data['success'],
                    'code'      => $data['code'],
                    'message'   => $data['message'],
                    'data'      => [
                        'name'      => $data['data']['name'],
                        'email'     => $data['data']['email']
                    ]
                ];
                App\User::destroy($data['data']['id']);
                $this->assertArraySubset($paramCase, $result);
            }else{
                throw new Exception('Respons: '.$status);
            }                
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }

    public function testLoginUserWithCredentialSection()
    {
        try{
            $this->createFacker();
            $user               = App\User::create(
                [
                    'name'      => $this->name,
                    'email'     => $this->email,
                    'password'  => Hash::make($this->password)
                ]
            );                    
            $paramTest           = [
                'email'     => $this->email,
                'password'  => $this->password
            ];
            $paramCase           = [
                'success'   => true,
                'code'      => 200,
                'message'   => 'Login Sukses'
            ];
            $status             = $this->call(
                'POST',
                '/login',
                $paramTest,
                [],
                [],
                [],
                []);
            $data               = (array)json_decode($this->response->getContent(),true);
            if(sizeof($data)>0 AND $data['success'] == true){
                $result             = [
                    'success'   => $data['success'],
                    'code'      => $data['code'],
                    'message'   => $data['message']
                ];
                App\User::destroy($user->id);                    
                $this->assertArraySubset($paramCase, $result);
            }else{
                throw new Exception('Responses: '.$status);
            }                
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }

    public function testLoginUserWithOutCredentialSection()
    {
        try{
            $this->createFacker();
            $user               = App\User::create(
                [
                    'name'      => $this->name,
                    'email'     => $this->email,
                    'password'  => Hash::make($this->password)
                ]
            );
            $paramTest           = [
                'email'     => $this->email,
                'password'  => str_random(10)
            ];
            $paramCase           = [
                'success'   => false,
                'code'      => 200,
                'message'   => 'Login Gagal'
            ];
            $status         = $this->call(
                'POST',
                '/login',
                $paramTest,
                [],
                [],
                [],
                []);
            $data               = (array)json_decode($this->response->getContent(),true);
            if(sizeof($data)>0 AND $data['success'] == false){
                $result             = [
                    'success'   => $data['success'],
                    'code'      => $data['code'],
                    'message'   => $data['message']
                ];
                App\User::destroy($user->id);                    
                $this->assertArraySubset($paramCase, $result);
            }else{
                throw new Exception('Responses: '.$status);
            }                
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }

    public function testLogOutUserWithCredentialSection(){
        try{
            $this->createFacker();
            $token              = base64_encode(str_random(40));
            $user               = App\User::create(
                [
                    'name'      => $this->name,
                    'email'     => $this->email,
                    'password'  => Hash::make($this->password),
                    'token'     => $token
                ]
            );
            $paramCase           = array (
                'code'      => 200,
                'message'   => 'Logout Sukses',
                'data'      => [],
            );
            $status             = $this->call(
                'GET',
                '/logout',
                [],
                [],
                [],
                $headers = [
                    'HTTP_Authorization' => 'Bearer '.$token,
                    'CONTENT_TYPE' => 'application/json',
                    'HTTP_ACCEPT' => 'application/json'
                ]
            );
            $data               = (array)json_decode($this->response->getContent(),true);
            if(sizeof($data)>0 AND $data['success'] == false){
                $result             = [
                    'code'      => $data['code'],
                    'message'   => $data['message'],
                    'data'      => []
                ];
                App\User::destroy($user->id);     
                $this->assertArraySubset($paramCase, $result);
            }else{
                throw new Exception('Responses: '.$status);
            }
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }

}
