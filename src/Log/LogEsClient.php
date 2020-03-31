<?php
namespace Unrelaxs\Eslog\Log;

use Carbon\Carbon;
use ScoutElastic\Console\ElasticMigrateCommand;

class LogEsClient {
    /**es的链接
     * @var
     */
    protected $url;
    /**es的端口
     * @var
     */
    protected $port;
    /**es的索引
     * @var
     */
    protected $index;

    /**是否写入数据库
     * @var
     */
    protected $toDB = false;

    public function __construct($url, $port, $index, bool $toDB = false)
    {
        $this->url = $url;
        $this->port = $port;
        $this->index = $index;
        $this->toDB = $toDB;
    }

    public function write($record) {
        if (! class_exists('\Unrelaxs\Eslog\Model\LogModel')) {
            return;
        }
        $log = new \Unrelaxs\Eslog\Model\LogModel();
        $log->content = $record['message'];
        $log->level = $record['level_name'];
        $res = $log->save();
        var_dump($res);die;
    }
    public function close() {

    }
}
