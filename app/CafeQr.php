<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CafeQr extends Model
{
    protected $table = 'cafe_qr';
    protected $primaryKey = 'cafe_qr_id';
    public $timestamps = false;
}
