<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CafeAdmin extends Model
{
    protected $table = 'cafe_admin';
    protected $primaryKey = 'cafe_admin_id';
    public $timestamps = false;

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function cafe_info(){
        return $this->belongsTo('App\CafeInfo');
    }
}
