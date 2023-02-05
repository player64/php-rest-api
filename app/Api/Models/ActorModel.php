<?php

namespace Api\Models;

use Api\Models\Entities\ActorEntity;

class ActorModel extends Model
{
    protected string $table = 'actors';

    protected array $columns = [
        'name',
        'surname',
        'gender'
    ];

    function __construct(\PDO $db)
    {
        parent::__construct($db);
        $this->entity = ActorEntity::class;
    }
}