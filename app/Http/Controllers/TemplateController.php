<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Schema\Template\ListAllChecklistTemplateCollection;
use App\Http\Schema\Template\GetAssignTemplateCollection;
use App\Http\Schema\Template\DetailTemplateCollection;
use App\Http\Schema\Template\CreateChecklistTemplateResource;
use App\Helper\Helper;
use App\Template;
use App\Checklist;
use App\Item;
use App\User;
use Faker\Factory as Faker;

class TemplateController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function listallchecklisttemplate()
    {
        try{
            $data      = Template::with('checklist')->paginate();
            if(count($data) > 0){
                return new ListAllChecklistTemplateCollection($data);
            }else{
                return Helper::response(200,'not',true,'');
            }
        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);

        }
    }

    public function getchecklisttemplate($templateId)
    {
        try{
            $data      = Template::where('id',$templateId)->with('checklist')->paginate();
            if(count($data) > 0){
                return new DetailTemplateCollection($data);
            }else{
                return Helper::response(200,'not',true,'');
            }
        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);
        }
    }

    public function assigns(Request $request,$templateId)
    {
        $faker        = Faker::create();
        $req          = json_decode($request->getContent(),true);
        $data         = $req['data'];
        $auth         = $request->header();
        $token        = $auth['authorization'];
        $user         = User::where('token',str_replace('Bearer ','',$token[0]))->first();
        $template     = Template::where('id',$templateId)->first();
        foreach($data as $items){
            $resultData  = array();
            $detailItem  = $items['attributes'];

            $getChecklist   = Checklist::create([
                'template_id'   => $templateId,
                'object_id'     => $detailItem['object_id'],
                'object_domain' => $detailItem['object_domain'],
                "description"   => $faker->sentence($nbWords = 6, $variableNbWords = true),
                "due_interval"  => $faker->randomDigit,
                "due_unit"      => $faker->randomElement(['minute','hour','day','week','month']),
                'created_by'    => $user->id,
            ]);

            $getItem        = Item::create([
                'user_id'       => $user->id,
                'checklist_id'  => $getChecklist->id,
                "description"   => $faker->sentence($nbWords = 6, $variableNbWords = true),
                "urgency"       => $faker->randomDigit,
                "due_interval"  => $faker->randomDigit,
                "due_unit"      => $faker->randomElement(['minute','hour','day','week','month']),
                'assignee_id'   => $user->id,
                'created_by'    => $user->id,
            ]);
            if($getItem){
                $resultData['action']  = 'update';
            }
            $check[] = $resultData;
        }
        if(sizeof($check)>0){
            $datatemplate      = Checklist::where('template_id',$templateId)->with('items')->get();
            if(count($datatemplate) > 0){
                return new GetAssignTemplateCollection($datatemplate);
            }else{
                return Helper::response(200,'not',true,'');
            }
        }else{
            return Helper::response(404,'not',true,'Not Found');
        }
    }

    public function saved(Request $request)
    {
        try{
            $faker                  = Faker::create();
            $req                    = json_decode($request->getContent(),true);
            $data                   = $req['data']['attributes'];
            $auth                   = $request->header();
            $token                  = $auth['authorization'];
            $user                   = User::where('token',str_replace('Bearer ','',$token[0]))->first();
            $dataitems              = $data['items'];
            $getTemplate            = Template::create([
                'name'   => $data['name']
            ]);
            $getChecklist           = $getTemplate->checklist()->create([
                'description'   => $data['checklist']['description'],
                'due_interval'  => $data['checklist']['due_interval'],
                'due_unit'      => $data['checklist']['due_unit']
            ]);
            foreach($dataitems as $val){
                $getChecklist->items()->create([
                    'created_by'    => $user->id,
                    'user_id'       => $user->id,
                    'description'   => $val['description'],
                    'urgency'       => $val['urgency'],
                    'due_interval'  => $val['due_interval'],
                    'due_unit'      => $val['due_unit']
                ]);
            }
            $datatemplate           = Template::find($getTemplate->id);
            return new CreateChecklistTemplateResource($datatemplate);
        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);
        }
    }    

    public function update(Request $request,$templateId)
    {
        try{
            $faker                  = Faker::create();
            $req                    = json_decode($request->getContent(),true);
            $data                   = $req['data'];
            $auth                   = $request->header();
            $token                  = $auth['authorization'];
            $user                   = User::where('token',str_replace('Bearer ','',$token[0]))->first();
            $dataitems              = $data['items'];
            $findtemplate           = Template::where('id',$templateId)->first();
            $getTemplate            = $findtemplate->update(
                [
                    'name'   => $data['name']
                ]
            );
            $findCheckList          = Checklist::where('template_id',$templateId)->first();
            Checklist::where('id',$findCheckList->id)->update(
                [
                    'description'   => $data['checklist']['description'],
                    'due_interval'  => $data['checklist']['due_interval'],
                    'due_unit'      => $data['checklist']['due_unit']
                ]
            );
            $findItem           = Item::where('checklist_id',$findCheckList->id)->get()->toArray();
            foreach($findItem as $key => $val){
                if(isset($dataitems[$key])){
                    Item::where('id',$val['id'])->update(
                        [
                            'updated_by'    => $user->id,
                            'description'   => $dataitems[$key]['description'],
                            'urgency'       => $dataitems[$key]['urgency'],
                            'due_interval'  => $dataitems[$key]['due_interval'],
                            'due_unit'      => $dataitems[$key]['due_unit']
                        ]
                    );
                };
            }
            $datatemplate           = Template::find($findtemplate->id);
            return new CreateChecklistTemplateResource($datatemplate);
        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);
        }
    }

    public function drop($templateId)
    {
        try{
            $check             = Template::where('id',$templateId)->first();
            if($check){
                $result        = Template::where('id',$templateId)->delete();
                if($result){
                    return Helper::response(201,'error',false,'success');
                }
            }else{
                return Helper::response(404,'not',false,'Not Found');
            }

        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);
        }
    }
}
