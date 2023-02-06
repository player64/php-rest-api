<?php

namespace Api\Models;

use Api\Models\Entities\EntityException;

abstract class Model
{
    protected string $table;

    protected array $columns;

    protected \PDO $db;
    protected string $entity;

    function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @throws ModelException
     */
    public function list(): array
    {

        try {
            $builder = new SqlBuilder();
            $sql = $builder->select("*")
                ->from($this->table)
                ->build();
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException|SqlBuilderException $e) {
            throw new ModelException($e->getMessage());
        }
    }

    /**
     * @throws RecordNotFoundException
     * @throws SqlBuilderException
     */
    public function get(int $id)
    {
        $record = $this->findById($id, true);
        if (!isset($record['id'])) {
            throw new RecordNotFoundException("The record has not been found.");
        }

        return $record;
    }

    /**
     * @throws ModelException
     * @throws RecordNotFoundException
     */
    public function delete(int $id): void
    {
        try {
            $record = $this->findById($id);

            if (!$record) {
                throw new RecordNotFoundException("The record has not been found.");
            }

            $builder = new SqlBuilder();
            $sql = $builder->delete()
                ->from($this->table)
                ->where("id = :id")
                ->build();
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
        } catch (\PDOException|SqlBuilderException $e) {
            throw new ModelException($e->getMessage());
        }
    }

    /**
     * @throws ModelException
     */
    public function create(array $request): array
    {
        try {
            $this->validate_params($request);

            // validate params as create entity
            $entity = new $this->entity($request);
            $entity_array = $entity->get();

            $builder = new SqlBuilder();
            $sql = $builder->insert($this->table, $this->columns)
                ->values($this->columns)
                ->build();

            $stmt = $this->db->prepare($sql);
            $stmt = $this->_bind_params($stmt, $entity_array);

            $stmt->execute();

            return array_merge(
                ['id' => (int)$this->db->lastInsertId()],
                $entity_array
            );

        } catch (EntityException|ModelException|SqlBuilderException $e) {
            throw new ModelException($e->getMessage());
        } catch (\PDOException|\TypeError $e) {
            throw new ModelException("Wrong parameters given check your request." . $e->getMessage());
        }
    }

    /**
     * @throws ModelException
     * @throws RecordNotFoundException
     */
    public function update(int $id, array $request): array
    {
        try {
            $record = $this->findById($id);

            if (!$record) {
                throw new RecordNotFoundException("The record has not been found.");
            }

            $this->validate_params($request);


            $entity = new $this->entity($request);
            $entity_array = $entity->get();

            $builder = new SqlBuilder();
            $sql = $builder->update($this->table)
                ->set($this->columns)
                ->where("id = :id")
                ->build();

            $stmt = $this->db->prepare($sql);
            $stmt = $this->_bind_params($stmt, $entity_array, $id);
            $stmt->execute();

            return array_merge(
                ['id' => (int)$record['id']],
                $entity_array
            );
        } catch (EntityException|ModelException|SqlBuilderException $e) {
            throw new ModelException($e->getMessage());
        } catch (\PDOException|\TypeError $e) {
            throw new ModelException("Wrong parameters given check your request." . $e->getMessage());
        }
    }


    /**
     * @throws SqlBuilderException
     */
    protected function findById(int $id, $as_object = false)
    {
        $builder = new SqlBuilder();
        $sql = $builder->select("*")
            ->from($this->table)
            ->where("id = ?")
            ->build();

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $id, \PDO::PARAM_INT);
        $stmt->execute();
        if ($as_object) {
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $stmt->fetch();
    }

    private function _bind_params($stmt, array $entity, int $id = null)
    {
        foreach ($entity as $key => &$value) {
            $type = (gettype($value) === 'string') ? \PDO::PARAM_STR : \PDO::PARAM_INT;
            $stmt->bindParam(":$key", $value, $type);
        }

        if ($id !== null) {
            $stmt->bindValue(':id', $id);
        }

        return $stmt;
    }

    /**
     * @throws ModelException
     */
    protected function validate_params(array $request): void
    {
        foreach ($this->columns as $key) {
            if (!isset($request[$key])) {
                throw new ModelException('Wrong parameters given. ' . strtoupper($key) . ' is required');
            }
        }
    }

    /**
     * @throws ModelException
     * @throws SqlBuilderException
     */
    public function get_or_create(array $request, array $params): int
    {
        $select = $this->select_row_by_key_and_value($params['key'], $params['value']);

        if (isset($select['id'])) {
            return $select['id'];
        }
        return $this->create($request)['id'];
    }

    /**
     * @throws SqlBuilderException
     */
    protected function select_row_by_key_and_value(string $key, string $value): array|false
    {
        $builder = new SqlBuilder();
        $sql = $builder->select("id")
            ->from($this->table)
            ->where("LOWER($key)")
            ->like("LOWER(:value)")
            ->build();


        $value = '%'.$value.'%';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':value', $value, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

}