```
elastic文档： https://github.com/babenkoivan/scout-elasticsearch-driver
中文分析器安装教程：https://blog.csdn.net/wolfcode_cn/article/details/81907220
```

```
composer require unrelaxs/eslog
config/app.php添加上
"providers": [
    "\Unrelaxs\Eslog\EslogServiceProvider::class",
    Laravel\Scout\ScoutServiceProvider::class,
    ScoutElastic\ScoutElasticServiceProvider::class,
]
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
php artisan vendor:publish --provider="ScoutElastic\ScoutElasticServiceProvider"
php artisan vendor:publish
.env文件配置
SCOUT_ELASTIC_HOST=http://es.wlcat.com:9200

#创建一个索引配置文件
php artisan make:index-configurator \\App\\Elasticsearch\\LogIndexConfigurator
LogIndexConfigurator.php内容如下
<?php

namespace App\Elasticsearch;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

/**关于日志的索引配置
 * Class LogIndexConfigurator
 * @package App\Elasticsearch
 */
class LogIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    protected $name = 'laralog';
    /**
     * @var array
     */
    protected $settings = [
        'analysis' => [
            'analyzer' => [
                'default' => [
                    'type' => 'ik_max_word',  //默认的分词器, 因为我独自安装了中文分析器
                ]
            ]
        ]
    ];
}

下面的命令需指定刚刚创建的index索引配置文件
php artisan unrelaxs:create-mapping "\App\Elasticsearch\LogIndexConfigurator"
#log日志model迁移
php artisan migrate
接下来配置些东西
config/logging.php修改如下
    'default' => env('LOG_CHANNEL', 'stack'),
    'channels' => [
            'stack' => [
                'driver' => 'stack',
                'channels' => ['daily', 'custom'],  //注意，这里不能少了custom
                'ignore_exceptions' => false,
            ],
             //elasticsearch
             'custom'  => [  //上面指定了custom, 这里的配置才生效
                        'driver' => 'custom',
                        'via' => Unrelaxs\Eslog\Log\Handler\CreateEsLogger::class, //指定创建es的logger扩展包
                        'url' =>  'http://es.wlcat.com',  //es的链接
                        'port'=> '9200',        //es的端口
                        'index'=> 'laralog', //es的索引名
                        'toDB' => false, //是否写入数据库
             ]
        ]

//向es服务器创建索引
php artisan elastic:create-index "\App\Elasticsearch\LogIndexConfigurator"
//把logmodel映射到es服务器
php artisan elastic:update-mapping "\Unrelaxs\Eslog\Model\LogModel"
在客户端调用Log::info('你好啊');
如在routes/web.php中这样
Route::get('/', function () {
    \Illuminate\Support\Facades\Log::info('成功加载');  //记录日志，将自动把数据推送到es服务器
    return view('welcome');
});

这时es的服务器就有该条记录了
```