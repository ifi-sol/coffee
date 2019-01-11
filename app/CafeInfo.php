<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CafeInfo extends Model
{
    protected $table = 'cafe_info';
    protected $primaryKey = 'cafe_id';
    public $timestamps = false;

    public function cafeAdmin(){
        return $this->hasOne('App\CafeAdmin');
    }
}
