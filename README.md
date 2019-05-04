<h1 align="center"> amap-district </h1>

<p align="center"> 高德地区中国标准行政区划数据导入数据库工具 与 行政地区SDK</p>


## 安装
```bash
composer require chujc/amap-district
```
- 创建迁移文件
```bash
php artisan district:table
```
- 创建数据表
  > 注意已有数据表
```bash
php artisan migrate
```
- 创建配置文件
```bash
php artisan vendor:publish --provider="ChuJC\AMapDistrict\DistrictServiceProvider" --tag="config"
```
- 配置高德web接口key
  > config 文件夹下面的 district.php 中的 **key**

## 导入数据进数据库
```bash
php artisan district
```

## 用法
```php
use ChuJC\AMapDistrict\District;

$disrtict = new District();
//参数与返回接口 参考高德地图行政区域接口
$response = $disrtict->getDistrict(['keywords' => '重庆']);

```

## 配置
- 默认开始缓存了
- 模型可以替换

## 高德接口
- [高德地图行政区域接口查询](https://lbs.amap.com/api/webservice/guide/api/district)

## SQL
也可以直接使用**district.sql** 文件导入

## License

MIT