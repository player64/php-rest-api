<?php

namespace Api\Models;

use Api\Models\Entities\EntityException;
use Api\Models\Entities\FilmEntity;
use Api\Models\Entities\Entity;
class FilmModel extends Model
{
    protected string $table = 'films';

    public array $columns = [
        'title',
        'year',
        'genre'
    ];

    function __construct(\PDO $db) {
        parent::__construct($db);
    }

    /**
     * @throws ModelException
     */
    public function create(Entity $entity): array
    {
        try {
            $sql = "INSERT INTO films (title, year, genre) VALUES (:title, :year, :genre)";
            $stmt = $this->db->prepare($sql);

            $entity_array = $entity->get();

            $stmt->bindParam(":title", $entity_array['title'], \PDO::PARAM_STR);
            $stmt->bindParam(":year", $entity_array['year'], \PDO::PARAM_INT);
            $stmt->bindParam(":genre", $entity_array['genre'], \PDO::PARAM_INT);

            $stmt->execute();

            return array_merge(
                ['id' => (int) $this->db->lastInsertId()],
                $entity_array
            );

        } catch (EntityException | ModelException $e ) {
            throw new ModelException($e->getMessage());
        } catch(\PDOException | \TypeError $e) {
            throw new ModelException("Wrong parameters given check your request.");
        }
    }
}