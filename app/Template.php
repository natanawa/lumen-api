<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = 'templates';
   
    protected $fillable = [
        'name'
    ];

    public function checklist(){
        return $this->hasOne(Checklist::class,'template_id');
    }
}
