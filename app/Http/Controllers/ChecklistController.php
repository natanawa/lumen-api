<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Schema\Checklist\ListofItemInGivenChecklistResource;
use App\Http\Schema\Checklist\GetChecklistResource;
use App\Http\Schema\Checklist\GetListofChecklistCollection;
use App\Helper\Helper;
use App\User;
use App\Template;
use App\Checklist;
use App\Item;

class ChecklistController extends Controller{
    protected $checklists;
    protected $items;
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->checklists   = new Checklist;
        $this->items        = new Item;
    }

    public function listofitemingivenchecklist($checklistId)
    {
        try{
            $datachecklist      = Checklist::with('items')->where('id',$checklistId)->first();
            if(count($datachecklist) > 0){
                return new ListofItemInGivenChecklistResource($datachecklist);
            }else{
                return Helper::response(404,'not',true,'');
            }
        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);
        }
    }

    public function getchecklist($checklistId)
    {
        try{
            $datachecklist      = Checklist::where('id',$checklistId)->first();
            if(count($datachecklist) > 0){
                return new GetChecklistResource($datachecklist);
            }else{
                return Helper::response(404,'error',true,'Not Found');
            }
        }catch(\Exception $e){
            return Helper::response(500,'error',true,'Server Error');
        }
    }

    public function getlistofchecklist()
    {
        try{
            $datachecklist      = Checklist::paginate();
            if(count($datachecklist) > 0){
                return new GetListofChecklistCollection($datachecklist);
            }else{
                return Helper::response(404,'not',true,'');
            }
        }catch(\Exception $e){
            return Helper::response(500,'not',false,'',[$e->getMessage()]);
        }
    }

    public function update(Request $request, $checklistId)
    {
        $req                    = json_decode($request->getContent(),true);
        $data                   = $req['data']['attributes'];
        $proses                 = Checklist::where('id',$checklistId);
        if($proses){
            $result  = $proses->update([
                'object_domain' => $data['object_domain'],
                'object_id'     => $data['object_id'],
                'description'   => $data['description'],
                'is_completed'  => $data['is_completed'],
                'completed_at'  => $data['completed_at'],
                'created_at'    => date("Y-m-d H:i:s",strtotime($data['created_at']))
            ]);    
            if($result){
                $datachecklist      = Checklist::where('id',$checklistId)->first();
                if(count($datachecklist) > 0){
                    return new GetChecklistResource($datachecklist);
                }else{
                    return Helper::response(200,'not',true,'');
                }
            }else{
                return Helper::response(200,'not',true,'');
            }
        }else{
            return Helper::response(404,'error',true,'Not Found');
        }
        if($result){
            $datachecklist      = $this->checklists->show($checklistId);
            return Helper::response(200,'not',true,'',$datachecklist);
        }
    }

    public function drop($checklistId)
    {
        try{
            $check             = Checklist::where('id',$checklistId);
            if($check){
                $result        = Checklist::where('id',$checklistId)->delete();
                if($result){
                    return Helper::response(201,'not',true,'success');
                }
            }else{
                return Helper::response(404,'error',true,'Not Found');
            }
        }catch(\Exception $e){
            return Helper::response(500,'error',true,$e->getMessage());
        }
    }

    public function saved(Request $request)
    {
        try{
            $req                    = json_decode($request->getContent(),true);
            $data                   = $req['data']['attributes'];
            $auth                   = $request->header();
            $token                  = $auth['authorization'];
            $user                   = User::where('token',str_replace('Bearer ','',$token[0]))->first();
            $dataitems              = $data['items'];
            $proses                 = Checklist::create([
                'template_id'   => Template::all()->random()->id,
                'object_domain' => $data['object_domain'],
                'object_id'     => $data['object_id'],
                'due'           => $data['due'],
                'urgency'       => $data['urgency'],
                'description'   => $data['description'],
                'created_by'    => $user->id,
                'task_id'       => $data['task_id']
            ]);
            foreach($dataitems as $val){
                $proses->items()->create([
                    'description'   => $val,
                    'user_id'       => $user->id
                ]);
            }
            $datachecklist      = Checklist::where('id',$proses->id)->first();
            return new GetChecklistResource($datachecklist);
        }catch(\Exception $e){
            return Helper::response(500,'error',true,$e->getMessage());
        }
    }
}
