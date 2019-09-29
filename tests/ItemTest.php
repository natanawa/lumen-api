<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Schema\Checklist\ListofItemInGivenChecklistResource;
use App\Http\Schema\Checklist\GetChecklistItemResource;
use App\Http\Schema\Item\CreateChecklistItemResource;
use App\Http\Schema\Item\ItemAllCollection;
use Carbon\Carbon;
use App\Helper\Helper;
use App\User;
use App\Template;
use App\Checklist;
use App\Item;

class ItemTest extends TestCase
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
    
    public function testDisplayCompleteItemsSection()
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

            $items              = Item::where('id',$this->item_id)->get()->toArray();
            $paramCase          = [
                'data'      => [
                    'id'            => $items[0]['id'],
                    'item_id'       => $items[0]['id'],
                    'is_completed'  => true,
                    'checklist_id'  => $items[0]['checklist_id']
                ]
            ];
            $paramTest          = [
                'item_id'      => $items[0]['id']
            ];
            $status             = (array) $this->call(
                'POST',
                '/checklists/complete',
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
            $data               = (array)json_decode($this->response->getContent(),true);
            if(sizeof($data)>0){
                $result             = array(
                    'data'      => array(
                        'id'            => $data['data'][0]['id'],
                        'item_id'       => $data['data'][0]['item_id'],
                        'is_completed'  => $data['data'][0]['is_completed'] == 1 ? true : false,
                        'checklist_id'  => $data['data'][0]['checklist_id']
                    )
                );
                $this->assertArraySubset($paramCase, $result);
            }else{
                throw new Exception('Responses: '.$status);
            }                
            App\User::destroy($user->id);   
            Template::destroy($this->template_id);
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }

    public function testDisplayIncompleteItemsSection()
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

            $items              = Item::where('id',$this->item_id)->get()->toArray();
            $paramCase          = [
                'data'      => [
                    'id'            => $items[0]['id'],
                    'item_id'       => $items[0]['id'],
                    'is_completed'  => false,
                    'checklist_id'  => $items[0]['checklist_id']
                ]
            ];
            $paramTest          = [
                'item_id'      => $items[0]['id']
            ];
            $status             = (array) $this->call(
                'POST',
                '/checklists/incomplete',
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
            $data               = (array)json_decode($this->response->getContent(),true);
            if(sizeof($data)>0){
                $result             = [
                    'data'      => [
                        'id'            => $data['data'][0]['id'],
                        'item_id'       => $data['data'][0]['item_id'],
                        'is_completed'  => $data['data'][0]['is_completed'] == 1 ? true : false,
                        'checklist_id'  => $data['data'][0]['checklist_id']
                    ]
                ];
                $this->assertArraySubset($paramCase, $result);
            }else{
                throw new Exception('Responses: '.$status);
            }                
            App\User::destroy($user->id);   
            Template::destroy($this->template_id);
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }

    public function testDisplayAllListItemsInGivenChecklistSection()
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

            $data               = new ListofItemInGivenChecklistResource(Checklist::with('items')->where('id',$this->checklist_id)->first());
            $paramCase          = json_encode($data);
            $status             = (array) $this->call(
                'GET',
                '/checklists/'.$this->checklist_id.'/items',
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
            Template::destroy($this->template_id);
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }

    public function testCreateChecklistItemSection()
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

            $data               = new CreateChecklistItemResource(Checklist::where('id',$this->checklist_id)->first());
            $paramCase          = json_encode($data);
            $paramTest          = [
                'attribute'     => [
                    "description"   => $faker->sentence($nbWords = 6, $variableNbWords = true),
                    "due"           => $faker->date('Y-m-d H:i:s'),
                    "urgency"       => $faker->randomDigit,
                    "assignee_id"   => App\User::all()->random()->id
                ],
            ];
            $status             = (array) $this->call(
                'POST',
                '/checklists/'.$this->checklist_id.'/items',
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

    public function testGetChecklistItemSection()
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

            $data               = new GetChecklistItemResource(Item::where('checklist_id',$this->checklist_id)->where('id',$this->item_id)->first());
            $paramCase          = json_encode($data);
            $status             = (array) $this->call(
                'GET',
                '/checklists/'.$this->checklist_id.'/items/'.$this->item_id,
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
            Template::destroy($this->template_id);
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }   

    public function testUpdateChecklistItemSection()
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

            $data               = new CreateChecklistItemResource(Checklist::where('id',$this->checklist_id)->first());
            $paramCase          = json_encode($data);
            $paramTest          = [
                'attribute'     => [
                    "description"   => $faker->sentence($nbWords = 6, $variableNbWords = true),
                    "due"           => $faker->date('Y-m-d H:i:s'),
                    "urgency"       => $faker->randomDigit,
                    "assignee_id"   => App\User::all()->random()->id
                ],
            ];
            $status             = (array) $this->call(
                'PATCH',
                '/checklists/'.$this->checklist_id.'/items/'.$this->item_id,
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

    public function testDeleteChecklistItemSection()
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
            $paramTest          = [
                'attribute'     => [
                    "description"   => $faker->sentence($nbWords = 6, $variableNbWords = true),
                    "due"           => $faker->date('Y-m-d H:i:s'),
                    "urgency"       => $faker->randomDigit,
                    "assignee_id"   => App\User::all()->random()->id
                ],
            ];
            $status             = (array) $this->call(
                'DELETE',
                '/checklists/'.$this->checklist_id.'/items/'.$this->item_id,
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
            Template::destroy($this->template_id);
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    }    

    public function testUpdateBulkChecklistSection()
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
                "data" => [
                    [
                        "status"    =>  200,
                        "id"        =>  $this->item_id,
                        "action"    =>  "update"
                    ]
                ]
            ];
            $paramCase           = json_encode($data);
            $paramTest          = [ 
                [
                    'id'                => $this->item_id,
                    'action'            => 'update',
                    'attributes'        => [
                        "description"   => $faker->sentence($nbWords = 6, $variableNbWords = true),
                        "due"           => $faker->date('Y-m-d H:i:s'),
                        "urgency"       => $faker->randomDigit
                    ]    
                ]
            ];
            $status             = (array) $this->call(
                'POST',
                '/checklists/'.$this->checklist_id.'/items/_bulk',
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

    public function testSummaryItemSection()
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

            $date       = Carbon::now();
            $today      = count(Item::whereDate('due',Carbon::now()->format('Y-m-d H:i:s'))->get());
            $past_due   = count(Item::whereDate('due','<',Carbon::now()->format('Y-m-d H:i:s'))->get());
            $this_week  = count(Item::whereBetween('due',[Carbon::now()->startOfWeek()->format('Y-m-d H:i:s'),Carbon::now()->endOfWeek()->format('Y-m-d H:i:s')])->get());
            $past_week  = count(Item::whereBetween('due',[Carbon::now()->subWeek()->subDay(7)->format('Y-m-d H:i:s'),Carbon::now()->subWeek()->format('Y-m-d H:i:s')])->get());
            $this_month = count(Item::whereBetween('due',[Carbon::now()->startOfMonth()->format('Y-m-d H:i:s'),Carbon::now()->endOfMonth()->format('Y-m-d H:i:s')])->get());
            $past_month = count(Item::whereBetween('due',[Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'),Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d H:i:s')])->get());
            $total      = count(Item::all());
            $data           = [
                'data' => [
                    'today'         => $today,
                    'past_due'      => $past_due,
                    'this_week'     => $this_week,
                    'past_week'     => $past_week,
                    'this_month'    => $this_month,
                    'past_month'    => $past_month,
                    'total'         => $total
                ]
            ];
            $paramCase           = json_encode($data);
            $status             = (array) $this->call(
                'GET',
                '/checklists/items/summaries',
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

    public function testGetAllItemsSection()
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

            $data               = new ItemAllCollection(Item::paginate());
            $paramCase          = json_encode(['data' => $data]);
            $status             = (array) $this->call(
                'GET',
                '/checklists/items/',
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
            Template::destroy($this->template_id);
        }catch(\Exception $e){
            $this->expectException($e->getMessage());
        }
    } 
}
