<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Item extends Model
{
    protected $table = 'items';

    protected $fillable = [
        'user_id','checklist_id','description','is_completed','completed_at','due','due_interval','due_unit','urgency','assignee_id','task_id','created_by','updated_by','created_at','updated_at'
    ];

    public function getIsCompletedAttribute($is_completed){
        return $is_completed == 1 ? true : false;
    }
    
    public function setDueAttribute($value)
    {
        $this->attributes['due']    =  Carbon::parse($value);
    }
    
    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at']    =  Carbon::parse($value);
    }
    
    public function setCompletedAtAttribute($value)
    {
        $this->attributes['completed_at']    =  Carbon::parse($value);
    }

    public function users(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function items(){
        return $this->hasMany(Item::class,'checklist_id');
    }
}
