## Requirements

The package has been tested in the following configuration:

* PHP version &gt;=7.1.3, &lt;=7.3
* Laravel Framework version &gt;=5.8, &lt;=6
* Elasticsearch version &gt;=7


## Installation

Use composer to install the package:

```
composer require unrelaxs/eslog-for-laravel
```

如果你的laravel框架版本 &lt;= 5.4 or [the package discovery](https://laravel.com/docs/5.5/packages#package-discovery)
is disabled, add the following providers in `config/app.php`:

```php
'providers' => [
    Laravel\Scout\ScoutServiceProvider::class,
    ScoutElastic\ScoutElasticServiceProvider::class,
    Unrelaxs\eslog\EslogServiceProvider::class,
]
```


## Configuration

执行发布命令

```
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
php artisan vendor:publish --provider="ScoutElastic\ScoutElasticServiceProvider"
php artisan vendor:publish --provider="Unrelaxs\eslog\EslogServiceProvider"
```


在这个文件添加配置，指定elastic服务器的链接:端口 `.evn`:

```
    SCOUT_DRIVER=elastic #指定使用elastic
    SCOUT_ELASTIC_HOST=http://域名:9200 #es服务器的链接
```

在这个文件修改 `config/logging.php`:

```
    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['custom'],  //这里至少指定 custom
            'ignore_exceptions' => false,
        ],
        'custom'  => [
            'driver' => 'custom',
            'via' => Unrelaxs\Eslog\Log\Handler\CreateEsLogger::class, //指定创建es的logger扩展包
            'url' =>  'http://ip', es的链接
            'port'=> '9200',
            'index'=> 'laralog', //es的索引名
            'toDB' => false, //是否写入数据库
        ]
```

创建一个索引配置文件:

```
php artisan make:index-configurator \\App\\Elasticsearch\\LogIndexConfigurator
```

此步骤可忽略，例如索引配置文件配置LogIndexConfigurator如下

```
    protected $settings = [
            'analysis' => [
                'analyzer' => [
                    'default' => [
                        'type' => 'ik_max_word',  //默认的分词器, 因为我独自安装了中文分析器，如何安装，请参考下面文献
                    ]
                ]
            ]
        ];
```

执行命令 生成一个logModel指向一个索引:

```
php artisan unrelaxs:create-mapping "\App\Elasticsearch\LogIndexConfigurator"
```

执行命令 把上面执行命令返回model路径 映射到es服务器:

```
php artisan elastic:update-mapping "\Unrelaxs\Eslog\Model\LogModel"
```

执行命令向es服务器创建索引 :

```
php artisan elastic:create-index "\App\Elasticsearch\LogIndexConfigurator"
```


在客户端调用Log::info('你好啊');，如在routes/web.php中这样

```
Route::get('/', function () {
    \Illuminate\Support\Facades\Log::info('成功加载');  //记录日志，将自动把数据推送到es服务器
    return view('welcome');
});
```

去es服务器查看效果吧！

## 参考文献


- [laravel-elastic文档](https://github.com/babenkoivan/scout-elasticsearch-driver)
- [中文分析器安装教程](https://blog.csdn.net/wolfcode_cn/article/details/81907220)

