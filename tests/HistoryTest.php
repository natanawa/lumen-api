<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Http\Schema\History\GetListofHistoryCollection;
use App\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helper\Helper;
use App\User;
use App\History;

class HistoryTest extends TestCase
{
    protected $name;
    protected $email;
    protected $password;
    protected $token;
    
    public function createFacker(){
        $userDetail = helper::createFacker();
        $this->name               = $userDetail->name;
        $this->email              = $userDetail->email;
        $this->password           = $userDetail->password;
        $this->is_completed       = $userDetail->is_completed;
        $this->token              = base64_encode(str_random(40));            
    }

    public function testGetListAllHistorySection()
    {
        try{
            $this->createFacker();
            $faker              = Faker\Factory::create();
            $user               = App\User::create([
                'name'      => $this->name,
                'email'     => $this->email,
                'password'  => Hash::make($this->password),
                'token'     => $this->token
            ]);

            $history            = App\History::create([
                'loggable_type'     => $faker->randomElement(['items','checklist','templates']),
                'loggable_id'       => $faker->numberBetween(1,5000),
                'action'            => $faker->sentence($nbWords = 1, $variableNbWords = true),
                'kwuid'             => App\User::all()->random()->id,
                'value'             => $faker->sentence($nbWords = 6, $variableNbWords = true),
                'created_by'        => App\User::all()->random()->id,
                'updated_by'        => App\User::all()->random()->id,
            ]);

            $data               = new GetListofHistoryCollection(History::paginate());
            $paramCase          = json_encode($data);
            $status             = (array) $this->call(
                'GET',
                '/checklists/histories',
                [],
                [],
                [],
                $headers = [
                    'HTTP_Authorization' => 'Bearer '.$this->token,
                    'CONTENT_TYPE' => 'application/json',
                    'HTTP_ACCEPT' => 'application/json'
                ]
            );
            $result             = json_decode($this->response->getContent(),true);
            unset($result['links'],$result['meta']);
            $result             = json_encode($result, true);
            $helper             = new Helper;
            if($helper->isJson($result)){
                $this->assertJsonStringEqualsJsonString($paramCase, $result);
            }else{
                throw new Exception('Responses: '.$status);
            }

            App\User::destroy($user->id);   
            App\History::destroy($history->id);   
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }   

    public function testGetHistoryByIdSection()
    {
        try{
            $this->createFacker();
            $faker              = Faker\Factory::create();
            $user               = App\User::create([
                'name'      => $this->name,
                'email'     => $this->email,
                'password'  => Hash::make($this->password),
                'token'     => $this->token
            ]);

            $history            = App\History::create([
                'loggable_type'     => $faker->randomElement(['items','checklist','templates']),
                'loggable_id'       => $faker->numberBetween(1,5000),
                'action'            => $faker->sentence($nbWords = 1, $variableNbWords = true),
                'kwuid'             => App\User::all()->random()->id,
                'value'             => $faker->sentence($nbWords = 6, $variableNbWords = true),
                'created_by'        => App\User::all()->random()->id,
                'updated_by'        => App\User::all()->random()->id,
            ]);

            $data               = new GetListofHistoryCollection(History::where('id',$history->id)->paginate());
            $paramCase          = json_encode($data);
            $status             = (array) $this->call(
                'GET',
                '/checklists/histories/'.$history->id,
                [],
                [],
                [],
                $headers = [
                    'HTTP_Authorization' => 'Bearer '.$this->token,
                    'CONTENT_TYPE' => 'application/json',
                    'HTTP_ACCEPT' => 'application/json'
                ]
            );
            $result             = json_decode($this->response->getContent(),true);
            unset($result['links'],$result['meta']);
            $result             = json_encode($result, true);
            $helper             = new Helper;
            if($helper->isJson($result)){
                $this->assertJsonStringEqualsJsonString($paramCase, $result);
            }else{
                throw new Exception('Responses: '.$status);
            }

            App\User::destroy($user->id);   
            App\History::destroy($history->id);   
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }   

}
