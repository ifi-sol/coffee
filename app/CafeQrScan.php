<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CafeQrScan extends Model
{
    protected $table = 'cafe_qr_scanned';
    protected $primaryKey = 'qr_scan_id';
    public $timestamps = false;
}
