<?php
namespace Unrelaxs\Eslog\Log\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Unrelaxs\Eslog\Log\LogEsClient;

/**推送到es的处理器
 * Class PushEsHandler
 * @package App\Log\Handler\PushEsHandler
 */
class PushEsHandler extends AbstractProcessingHandler{
    protected $esclient;
    protected $url;
    protected $port;
    protected $index;
    protected $toDB;
    public function __construct($url, $port = 9200, $index = 'laralog', bool $toDB =false, $level = Logger::DEBUG, bool $bubble = true)
    {
        $this->url = $url;
        $this->port = $port;
        $this->index = $index;
        $this->toDB = $toDB;
        parent::__construct($level, $bubble);
    }

    protected function write(array $record): void
    {
        $client = $this->getClient();
        $client->write($record);
        // TODO: Implement write() method.
    }

    public function getClient() {
        if (!$this->esclient) {
            $this->esclient = new LogEsClient($this->url, $this->port, $this->index, $this->toDB);
        }
        return $this->esclient;
    }


    public function close(): void
    {
        $this->esclient->close();
    }

    public function setClient(LogEsClient $esClient) {
        $this->esclient = $esClient;
    }
}