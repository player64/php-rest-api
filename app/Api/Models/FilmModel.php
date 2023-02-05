<?php

namespace Api\Models;
use Api\Models\Entities\FilmEntity;

class FilmModel extends Model
{
    protected string $table = 'films';

    protected array $columns = [
        'title',
        'year',
        'genre'
    ];

    function __construct(\PDO $db)
    {
        parent::__construct($db);
        $this->entity = FilmEntity::class;
    }

    /**
     * @throws ModelException
     * */
    public function list(): array {
        try {
            $sql = "SELECT films.*, genres.name AS genre FROM films JOIN genres ON films.genre = genres.id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new ModelException($e->getMessage());
        }
    }

    /**
     * @throws ModelException
     * @throws RecordNotFoundException
     */
    public function get(int $id): array
    {
        try {
            $record = $this->findById($id);

            if (!$record) {
                throw new RecordNotFoundException("The record has not been found.");
            }
            $sql = "SELECT f.*, g.name as genre FROM films f LEFT JOIN genres g ON f.genre = g.id WHERE f.id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException|\TypeError $e) {
            throw new ModelException($e->getMessage());
        }
    }

    /**
     * @throws ModelException
     */
    public function update(int $id, array $request): array
    {
        try {
            $this->validate_params($request);

            if (gettype($request['genre']) === 'string') {
                $request['genre'] = $this->create_or_get_genre($request['genre']);
            }

            return parent::update($id, $request);

        } catch (ModelException|RecordNotFoundException $e) {
            throw new ModelException($e->getMessage());
        } catch (\PDOException|\TypeError $e) {
            throw new ModelException("Wrong parameters given on update. Check your request.");
        }
    }

    /**
     * @throws ModelException
     */
    public function create(array $request): array
    {
        try {
            $this->validate_params($request);

            if (gettype($request['genre']) === 'string') {
                $request['genre'] = $this->create_or_get_genre($request['genre']);
            }

            return parent::create($request);

        } catch (ModelException $e) {
            throw new ModelException($e->getMessage());
        } catch (\PDOException|\TypeError $e) {
            throw new ModelException("Wrong parameters given check your request.".$e->getMessage());
        }
    }

    /**
     * @throws ModelException
     */
    private function create_or_get_genre($name): int
    {
        $genreModel = new GenreModel($this->db);
        return $genreModel->get_or_create(
            ['name' => $name],
            ['key' => 'name', 'value' => $name]
        );
    }
}