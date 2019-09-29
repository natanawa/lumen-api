<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class History extends Model
{
    protected $table = 'history';

    protected $fillable = [
        'loggable_type','loggable_id','action','kwuid','value','created_by','updated_by','created_at','updated_at'
    ];
    
    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at']    =  Carbon::parse($value);
    }
}
