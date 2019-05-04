<?php

namespace ChuJC\AMapDistrict\Console;

use ChuJC\AMapDistrict\District;
use Illuminate\Console\Command;
use Exception;

class DistrictCommand extends Command
{

    protected $name = 'district';

    protected $description = '调用高德地图行政区接口把数据插入到数据库';

    private $model;

    public function handle()
    {
        set_time_limit(0);
        $this->model = config('district.model');
        if (!$this->model) {
            throw new Exception('未找到配置文件, 请执行 "php artisan vendor:publish --provider="ChuJC\AMapDistrict\DistrictServiceProvider" --tag="config""');
        }

        $district = new District();

        $districtInfo = $district->getDistrict(['subdistrict' => 1]);

        $districtArray = json_decode($districtInfo, true);

        $this->info('开始插入数据: ' . date('Y-m-d H:i:s'));

        //插入国家
        $inertCountry = $this->getInsertItem($districtArray['districts']);
        $districtModel = new $this->model;
        $districtModel = $districtModel::create($inertCountry[0]);

        //循环插入省级行政区
        $inertProvince = $this->getInsertItem($districtArray['districts'][0]['districts'], $districtModel->id);
        $districtModel::insert($inertProvince);

        //获取插入的省级行政单位
        $provinceArray = $districtModel::where("level", 'province')->orderBy('adcode')->get()->toArray();

        foreach ($provinceArray as $province) {
            //获取省级行政单位下3级所有地区
            $districtInfo = $district->getDistrict([
                'subdistrict' => 3,
                'keywords' => $province['adcode']
            ]);
            $districtArray = json_decode($districtInfo, true);

            //循环插入市级行政区
            $inertCityArray = $this->getInsertItem($districtArray['districts'][0]['districts'], $province['id']);

            $cityCount = 0;
            foreach ($inertCityArray as $city) {
                $cityModel = $districtModel::create($city);
                //循环插入区县级行政区
                $inertDistrictArray = $this->getInsertItem($districtArray['districts'][0]['districts'][$cityCount]['districts'], $cityModel->id);

                $cityDistrict = 0;
                foreach ($inertDistrictArray as $districts) {
                    $districtModel = $districtModel::create($districts);
                    //插入街道镇级行政区
                    $inertStreetArray = $this->getInsertItem($districtArray['districts'][0]['districts'][$cityCount]['districts'][$cityDistrict]['districts'], $districtModel->id);
                    $districtModel->insert($inertStreetArray);
                    $cityDistrict++;
                }
                $cityCount++;
            }
        }

        $this->info('数据插入完成: ' . date('Y-m-d H:i:s'));
    }


    private function getInsertItem(array $districtArray, $parent_id = 0)
    {
        $inertArray = [];
        foreach ($districtArray as $value) {

            $inertArray[] = [
                'parent_id' => $parent_id,
                'citycode' => is_array($value['citycode']) ? '': $value['citycode'],
                'adcode' => $value['adcode'],
                'name' => $value['name'],
                'center' => $value['center'],
                'level' => $value['level'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        return $inertArray;
    }



}
