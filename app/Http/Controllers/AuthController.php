<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helper\Helper;
use App\User;
use Validator;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth',['except' => ['register','login']]);
    }

    public function login(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'email'             => 'required|email|max:100',
                'password'          => 'required|max:255'
            ]);

            if ($validator->fails()) {
                return Helper::response(200,'all',false,$validator->messages()->toJson());
            }else{
                $email      = $request->input('email');
                $password   = $request->input('password');

                $user   = User::where('email',$email)->first();
                if(Hash::check($password, $user->password)){
                    $token = base64_encode(str_random(40));
                    $user->update(['token' => $token]);
                    $result = ['user' => $user];
                    return Helper::response(200,'all',true,'Login Sukses',$result);
                }else{
                    return Helper::response(200,'all',false,'Login Gagal');
                }        
            }                
        }catch(\Exception $e){
            return Helper::response(500,'all',false,$e->getMessage());
        }
    }

    public function logout(Request $request){
        try{
            $data                   = $request->header();
            $auth                   = $data['authorization'];
            $user                   = User::where('token',str_replace('Bearer ','',$auth[0]));
            if($user){
                $user->update(['token' => '']);
                return Helper::response(200,'all',false,'Logout Sukses');
            }else{
                return Helper::response(200,'all',false,'Anda Sudah Logout');
            }    
        }catch(\Exception $e){
            return Helper::response(500,'all',false,$e->getMessage());
        }
    }
    
    public function register(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'name'      => 'required|max:255',
                'email'     => 'required|email|max:100|unique:users',
                'password'  => 'required'
            ]);

            if ($validator->fails()) {
                return Helper::response(200,'all',false,$validator->messages()->toJson());
            }else{
                $name       = $request->input('name');
                $email      = $request->input('email');
                $password   = Hash::make($request->input('password'));

                $register = User::create([
                    'name'      => $name,
                    'email'     => $email,
                    'password'  => $password
                ]);

                if($register){
                    return Helper::response(200,'all',true,'Registration Sukses',$register);
                }else{
                    return Helper::response(200,'all',true,'Registration Gagal');
                }            
            }    
        }catch(\Exception $e){
            return Helper::response(500,'all',false,$e->getMessage());
        }
    }

}
