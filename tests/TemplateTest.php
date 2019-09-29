<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Controllers\AuthController;
use App\Http\Schema\Checklist\GetChecklistResource;
use App\Http\Schema\Checklist\GetListofChecklistCollection;
use App\Http\Schema\Template\ListAllChecklistTemplateCollection;
use App\Http\Schema\Template\CreateChecklistTemplateResource;
use App\Http\Schema\Template\GetAssignTemplateCollection;
use App\Helper\Helper;
use App\User;
use App\Template;
use App\Checklist;
use App\Item;

class TemplateTest extends testCase
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

    public function testGetListOfChecklistTemplateSection()
    {
        try{
            $this->createFacker();
            $user               = App\User::create([
                'name'      => $this->name,
                'email'     => $this->email,
                'password'  => Hash::make($this->password),
                'token'     => $this->token
            ]);
            $data               = new ListAllChecklistTemplateCollection(Template::with('checklist')->paginate());
            $paramCase           = json_encode($data);
            $status             = (array) $this->call(
                'GET',
                '/checklists/templates',
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
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }

    public function testCreateChecklistTemplateSection()
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
            $paramTest          = [
                "attributes"    => [
                    "name"          => $faker->name,
                    "checklist"     => [
                        "description"       => $faker->sentence($nbWords = 6, $variableNbWords = true),
                        "due_interval"      => $faker->randomDigit,
                        "due_unit"          => $faker->randomElement(['minute','hour','day','week','month'])
                    ],
                    "items"         => [
                        [
                            "description"       => $faker->sentence($nbWords = 6, $variableNbWords = true),
                            "urgency"           => $faker->randomDigit,
                            "due_interval"      => $faker->randomDigit,
                            "due_unit"          => $faker->randomElement(['minute','hour','day','week','month'])    
                        ]
                    ]
                ]
            ];

            $status             = (array) $this->call(
                'POST',
                '/checklists/templates',
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
            $data               = new CreateChecklistTemplateResource(Template::all()->last());
            $paramCase           = json_encode($data);
            $result             = $this->response->getContent();
            $helper             = new Helper;
            if($helper->isJson($result)){
                $this->assertJsonStringEqualsJsonString($paramCase, $result);
            }else{
                throw new Exception('Responses: '.$status);
            }
            App\User::destroy($user->id);  
            Template::destroy(Template::all()->last()->id);
            Checklist::where('template_id',Template::all()->last()->id)->delete();
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }     

    public function testGetChecklistTemplateSection()
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
            $data               = new ListAllChecklistTemplateCollection(Template::where('id',$this->template_id)->with('checklist')->paginate());
            $paramCase          = json_encode($data);
            $status             = (array) $this->call(
                'GET',
                '/checklists/templates/'.$this->template_id,
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
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }       

    public function testUpdateChecklistTemplateSection()
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

            $paramTest          = [ 
                "name"                  => $faker->sentence($nbWords = 3, $variableNbWords = true),
                "checklist" => [
                    "description"       => $faker->sentence($nbWords = 6, $variableNbWords = true),
                    "due_interval"      => $faker->randomDigit,
                    "due_unit"          => $faker->randomElement(['minute','hour','day','week','month'])
                ],
                "items" =>[
                    [
                        "description"       => $faker->sentence($nbWords = 6, $variableNbWords = true),
                        "urgency"           => $faker->randomDigit,
                        "due_interval"      => $faker->randomDigit,
                        "due_unit"          => $faker->randomElement(['minute','hour','day','week','month'])
                    ],
                    [
                        "description"       => $faker->sentence($nbWords = 6, $variableNbWords = true),
                        "urgency"           => $faker->randomDigit,
                        "due_interval"      => $faker->randomDigit,
                        "due_unit"          => $faker->randomElement(['minute','hour','day','week','month'])
                    ]
                ]
            ];

            $status             = (array) $this->call(
                'PATCH',
                '/checklists/templates/'.$this->template_id,
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
            $result             = $this->response->getContent();
            $data               = new CreateChecklistTemplateResource(Template::all()->last());
            $paramCase           = json_encode($data);

            $helper             = new Helper;
            if($helper->isJson($result)){
                $this->assertJsonStringEqualsJsonString($paramCase, $result);
            }else{
                throw new Exception('Responses: '.$status);
            }                
            App\User::destroy($user->id);   
            Template::destroy($this->template_id);
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }

    public function testDeleteChecklistTemplateSection()
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
                "status"    => 201,
                "error"     => "success"
            ];
            $paramCase          = json_encode($data);
            $status             = (array) $this->call(
                'DELETE',
                '/checklists/templates/'.$this->template_id,
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
                $this->assertJsonStringEqualsJsonString($paramCase, $result);
            }else{
                throw new Exception('Responses: '.$status);
            }
            App\User::destroy($user->id);   
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }

    public function testAsignChecklistTemplateSection()
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
                [
                    "attributes" => [
                        "object_id"         => $faker->randomDigit,
                        "object_domain"     => $faker->sentence($nbWords = 2, $variableNbWords = true)
                    ]
                ],
                [
                    "attributes" => [
                        "object_id"         => $faker->randomDigit,
                        "object_domain"     => $faker->sentence($nbWords = 2, $variableNbWords = true)
                    ]
                ],
                [
                    "attributes" => [
                        "object_id"         => $faker->randomDigit,
                        "object_domain"     => $faker->sentence($nbWords = 2, $variableNbWords = true)
                    ]
                ]
            ];

            $status             = (array) $this->call(
                'POST',
                '/checklists/templates/'.$this->template_id.'/assigns',
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
            $result             = json_decode($this->response->getContent(), true);
            unset($result['meta']);
            $result             = json_encode($result);
            $datatemplate       = Checklist::where('template_id',$this->template_id)->with('items')->get();
            $data               = new GetAssignTemplateCollection($datatemplate);
            $paramCase          = json_encode($data);
            $helper             = new Helper;
            if($helper->isJson($result)){
                $this->assertJsonStringEqualsJsonString($paramCase, $result);
            }else{
                throw new Exception('Responses: '.$status);
            }                
            App\User::destroy($user->id);   
            Template::destroy($this->template_id);
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }
}