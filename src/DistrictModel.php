<?php

namespace ChuJC\AMapDistrict;


use Illuminate\Database\Eloquent\Model;

class DistrictModel extends Model
{
    protected $table = 'districts';

    protected $fillable = [
        'parent_id', 'citycode', 'adcode', 'name', 'center', 'level'
    ];


}
