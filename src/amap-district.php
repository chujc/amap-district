<?php

return [
    //district Eloquent Model
    'model' => ChuJC\AMapDistrict\DistrictModel::class,

    //高德 Web服务 key
    'key' => '',

    //是否缓存
    'cache' => true,

    //缓存时间 单位：分钟
    'cache_time' => 60,
];