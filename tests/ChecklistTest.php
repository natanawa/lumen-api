<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Controllers\AuthController;
use Illuminate\Support\Facades\Hash;
use App\Http\Schema\Checklist\GetChecklistResource;
use App\Http\Schema\Checklist\GetListofChecklistCollection;
use App\Helper\Helper;
use Illuminate\Http\Request;
use App\User;
use App\Template;
use App\Checklist;
use App\Item;

class ChecklistTest extends TestCase
{
    protected $name;
    protected $email;
    protected $password;
    protected $template_id;
    protected $checklist_id;
    protected $item_id;
    protected $is_completed;
    protected $token;
    
    public function createFacker(){
        $userDetail = helper::createFacker();
        $this->name               = $userDetail->name;
        $this->email              = $userDetail->email;
        $this->password           = $userDetail->password;
        $this->is_completed       = $userDetail->is_completed;
        $this->token              = base64_encode(str_random(40));            

        $count_template               = Template::count();
        if($count_template > 0){
            $this->template_id        = Template::get()->last()->id+1;
        }else{
            $this->template_id        = 1;
        }

        $count_checklist        = Checklist::count();
        if($count_checklist > 0){
            $this->checklist_id       = Checklist::get()->last()->id+1;
        }else{
            $this->checklist_id       = 1;
        }

        $count_item             = Item::count();
        if($count_item > 0){
            $this->item_id            = Item::get()->last()->id+1;
        }else{
            $this->item_id            = 1;
        }
    }

    public function testGetChecklistSection()
    {
        try{
            $this->createFacker();
            $user               = App\User::create([
                'name'      => $this->name,
                'email'     => $this->email,
                'password'  => Hash::make($this->password),
                'token'     => $this->token
            ]);

            factory(Template::class)->create([
                'id'    => $this->template_id
            ]);

            factory(Checklist::class)->create([
                'id'            => $this->checklist_id,
                'template_id'   => $this->template_id
            ]);

            factory(Item::class)->create([
                'id'            => $this->item_id,
                'checklist_id'  => $this->checklist_id
            ]);

            $data               = new GetChecklistResource(Checklist::where('id',$this->checklist_id)->first());
            $paramCase           = json_encode($data);
            $status             = (array) $this->call(
                'GET',
                '/checklists/'.$this->checklist_id,
                [],
                [],
                [],
                $headers = [
                    'HTTP_Authorization' => 'Bearer '.$this->token,
                    'CONTENT_TYPE' => 'application/json',
                    'HTTP_ACCEPT' => 'application/json'
                ]
            );
            $result             = $this->response->getContent();
            $helper             = new Helper;
            if($helper->isJson($result)){
                $this->assertJsonStringEqualsJsonString(
                    $paramCase, $result
                );
            }else{
                throw new Exception('Responses: '.$status);
            }
            App\User::destroy($user->id);   
            Template::destroy($this->template_id);
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }

    public function testUpdateChecklistSection()
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
            
            factory(Template::class)->create([
                'id'    => $this->template_id
            ]);

            factory(Checklist::class)->create([
                'id'            => $this->checklist_id,
                'template_id'   => $this->template_id
            ]);

            factory(Item::class)->create([
                'id'            => $this->item_id,
                'checklist_id'  => $this->checklist_id
            ]);

            $data               = new GetChecklistResource(Checklist::where('id',$this->checklist_id)->first());
            $paramCase          = json_encode($data);
            $paramTest          = [
                "type"      => "checklists",
                "id"        => $this->checklist_id,
                "attributes"    => [
                    "object_domain"     => $faker->jobTitle,
                    "object_id"         => $faker->randomDigit,
                    "description"       => $faker->sentence($nbWords = 6, $variableNbWords = true),
                    "is_completed"      => $faker->boolean,
                    "completed_at"      => $faker->date('Y-m-d H:i:s'),
                    "created_at"        => $faker->date('Y-m-d H:i:s')
                ],
                "links"     => [
                    'self' => route('updatechecklist',['checklistId' => $this->checklist_id])
                ]
            ];
            $status             = (array) $this->call(
                'GET',
                '/checklists/'.$this->checklist_id,
                [],
                [],
                [],
                $headers = [
                    'HTTP_Authorization' => 'Bearer '.$this->token,
                    'CONTENT_TYPE' => 'application/json',
                    'HTTP_ACCEPT' => 'application/json'
                ]
            );
            $result             = $this->response->getContent();
            $helper             = new Helper;
            if($helper->isJson($result)){
                $this->assertJsonStringEqualsJsonString(
                    $paramCase, $result
                );
            }else{
                throw new Exception('Responses: '.$status);
            }
            App\User::destroy($user->id);   
            Template::destroy($this->template_id);
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }

    public function testDeleteChecklistSection()
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
            factory(Template::class)->create([
                'id'    => $this->template_id
            ]);

            factory(Checklist::class)->create([
                'id'            => $this->checklist_id,
                'template_id'   => $this->template_id
            ]);

            factory(Item::class)->create([
                'id'            => $this->item_id,
                'checklist_id'  => $this->checklist_id
            ]);

            $data               = [
                "data"    => []
            ];
            $paramCase          = json_encode($data);

            $status             = (array) $this->call(
                'DELETE',
                '/checklists/'.$this->checklist_id,
                [],
                [],
                [],
                $headers = [
                    'HTTP_Authorization' => 'Bearer '.$this->token,
                    'CONTENT_TYPE' => 'application/json',
                    'HTTP_ACCEPT' => 'application/json'
                ]
            );
            $result             = $this->response->getContent();
            $helper             = new Helper;
            if($helper->isJson($result)){
                $this->assertJsonStringEqualsJsonString(
                    $paramCase, $result
                );
            }else{
                throw new Exception('Responses: '.$status);
            }

            App\User::destroy($user->id);   
            Template::destroy($this->template_id);
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }    

    public function testCreateChecklistSection()
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
            factory(Template::class)->create([
                'id'    => $this->template_id
            ]);
            $paramTest          = [
                "attributes"    => [
                    "object_domain" => $faker->jobTitle,
                    "object_id"     => $faker->randomDigit,
                    "due"           => $faker->date('Y-m-d H:i:s'),
                    "urgency"       => $faker->randomDigit,
                    "description"   => $faker->sentence($nbWords = 6, $variableNbWords = true),
                    "items"         => [
                        $faker->sentence($nbWords = 6, $variableNbWords = true),
                        $faker->sentence($nbWords = 6, $variableNbWords = true),
                        $faker->sentence($nbWords = 6, $variableNbWords = true)
                    ],
                    "task_id"       => $faker->randomDigit
                ]
            ];

            $status             = (array) $this->call(
                'POST',
                '/checklists',
                [],
                [],
                [],
                $headers = [
                    'HTTP_Authorization' => 'Bearer '.$this->token,
                    'CONTENT_TYPE' => 'application/json',
                    'HTTP_ACCEPT' => 'application/json'
                ],
                $json = json_encode(['data' => $paramTest])
            );
            $data               = new GetChecklistResource(Checklist::all()->last());
            $paramCase          = json_encode($data);

            $result             = $this->response->getContent();
            $helper             = new Helper;
            if($helper->isJson($result)){
                $this->assertJsonStringEqualsJsonString(
                    $paramCase, $result
                );
            }else{
                throw new Exception('Responses: '.$status);
            }
            App\User::destroy($user->id);   
            Template::destroy($this->template_id);
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }

    public function testGetListOfChecklistSection()
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

            $data               = new GetListofChecklistCollection(Checklist::paginate());
            $paramCase          = json_encode($data);
            $status             = (array) $this->call(
                'GET',
                '/checklists',
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
                $this->assertJsonStringEqualsJsonString(
                    $paramCase, $result
                );
            }else{
                throw new Exception('Responses: '.$status);
            }
            App\User::destroy($user->id);   
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }    
}