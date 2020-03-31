```
elastic文档： https://github.com/babenkoivan/scout-elasticsearch-driver
中文分析器安装教程：https://blog.csdn.net/wolfcode_cn/article/details/81907220
```

```
composer require unrelaxs/eslog
php artisan vendor:publish
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
php artisan migrate
接下来配置些东西
config/app.php添加上
"providers": ["\Unrelaxs\Eslog\EslogServiceProvider::class"]

config/logging.php修改如下
    'default' => env('LOG_CHANNEL', 'stack'),
    'channels' => [
            'stack' => [
                'driver' => 'stack',
                'channels' => ['daily', 'custom'],
                'ignore_exceptions' => false,
            ],
             //elasticsearch
             'custom'  => [
                        'driver' => 'custom',
                        'via' => Unrelaxs\Eslog\Log\Handler\CreateEsLogger::class,
                        'url' =>  'http://es.wlcat.com',
                        'port'=> '9200',
                        'index'=> 'laralog', //es的索引名
                        'toDB' => false, //是否写入数据库
             ]
        ]

在客户端调用Log::info('你好啊');
这时es的服务器就有该条记录了
```