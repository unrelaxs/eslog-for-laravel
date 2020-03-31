<?php

namespace Unrelaxs\Eslog\Console;

use Illuminate\Console\Command;

class ElasticLogMappingCommand extends Command
{
    /**
     * {@inheritdoc}
     * @param indexPath 为 索引文件的路径
     */
    protected $signature = 'unrelaxs:create-mapping {indexPath}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Create an Elasticsearch log mapping';


    /**
     * Handle the command.
     *
     * @return void
     */
    public function handle()
    {
        $indexPath = $this->argument('indexPath');
        if (empty($indexPath)) {
           return $this->info('argument indexPath not found');
        }
        $indexPath=trim($indexPath, '\\');
        $tplContent = file_get_contents(__DIR__.'/tpl/LogModel.php.tpl');
        $tplContent = str_replace('{LogIndexConfigurator}', "\\{$indexPath}::class", $tplContent);
        file_put_contents(__DIR__.'/../Model/LogModel.php', $tplContent);
        return $this->info('success create \Unrelaxs\Eslog\Model::class');
    }
}
