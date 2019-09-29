<?php
namespace App\Helper;

class Helper{

	public function isJson($string){
		return is_string($string) && is_array(json_decode($string, true)) ? true : false;
	}    

	public static function response($code = 200,$type = 'all',$status = true,$message = "",$data = array())
	{
		if($type == 'all'){
			return response()->json(
				[
					'success'   => $status,
					'code'      => $code,
					'message'   => $message,
					'data'      => $data
				], $code
			);
		}else if($type == "error"){
			return response()->json(
				[
					'status'      => $code,
					'error'       => $message,
				], $code
			);
		}else{
			return response()->json(
				[
					'data'      => $data
				], $code
			);
		}
	}    

	public static function getEmail($email){
		$faker              = \Faker\Factory::create();
		if(count(\App\User::where('email',$email)->first())>0){
			self::getEmail($email);            
		}{
			$email          = $faker->email;
			return $email;
		}
	}


    public static function createFacker(){
        $faker              = \Faker\Factory::create();
        $name               = $faker->name;
        $email              = self::getEmail($faker->email);
        $password           = $faker->password;
        $is_completed		= $faker->boolean;
        
        return (object)['name' => $name,'email' => $email,'password' => $password,'is_completed' => $is_completed];
    }
}