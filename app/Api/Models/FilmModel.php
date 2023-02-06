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
    public function list(): array
    {
        try {
            $builder = new SqlBuilder();
            $sql = $builder->select("films.*, genres.name")
                ->as("genre")
                ->from($this->table)
                ->join("genres")
                ->on("films.genre = genres.id")
                ->build();
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException|SqlBuilderException $e) {
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

            $builder = new SqlBuilder();
            $sql = $builder->select("f.*, g.name")
                ->as("genre")
                ->from("films f")
                ->left_join("genres g")
                ->on("f.genre = g.id")
                ->where("f.id = :id")
                ->build();
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException|\TypeError|SqlBuilderException $e) {
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

        } catch (ModelException|RecordNotFoundException|SqlBuilderException $e) {
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

        } catch (ModelException|SqlBuilderException $e) {
            throw new ModelException($e->getMessage());
        } catch (\PDOException|\TypeError $e) {
            throw new ModelException("Wrong parameters given check your request." . $e->getMessage());
        }
    }

    /**
     * @throws ModelException
     * @throws SqlBuilderException
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