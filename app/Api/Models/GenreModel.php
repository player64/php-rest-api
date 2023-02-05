<?php

namespace Api\Models;

use Api\Models\Entities\EntityException;
use Api\Models\Entities\GenreEntity;

class GenreModel extends Model
{
    protected string $table = 'genres';

    protected array $columns = [
        'name',
    ];

    function __construct(\PDO $db)
    {
        parent::__construct($db);
        $this->entity = GenreEntity::class;
    }
}