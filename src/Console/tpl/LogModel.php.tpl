<?php
namespace Unrelaxs\Eslog\Model;

use Illuminate\Database\Eloquent\Model;
use App\Elasticsearch\LogIndexConfigurator;
use ScoutElastic\Searchable;

class LogModel extends Model {
    use Searchable;
    protected $table = 'logs';
    protected $fillable = ['content', 'level', 'created_at', 'updated_at', 'deleted_at'];

    protected $indexConfigurator = {LogIndexConfigurator};

    protected $mapping = [
        'properties' => [
            'id' => [
                'type' => 'integer',
            ],
            'content' => [
                'type' => 'text'
            ],
            'level' => [
                'type' => 'keyword'
            ],
        ]
    ];
}