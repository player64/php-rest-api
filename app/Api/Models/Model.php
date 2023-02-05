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
            $sql = "SELECT * FROM $this->table";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new ModelException($e->getMessage());
        }
    }

    /**
     * @throws RecordNotFoundException
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
            $sql = "DELETE FROM $this->table WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
        } catch (\PDOException $e) {
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

            $stmt = $this->create_insert_or_update_sql_statement('insert', $entity_array);
            $stmt->execute();

            return array_merge(
                ['id' => (int)$this->db->lastInsertId()],
                $entity_array
            );

        } catch (EntityException|ModelException $e) {
            throw new ModelException($e->getMessage());
        } catch (\PDOException|\TypeError $e) {
            throw new ModelException("Wrong parameters given check your request.".$e->getMessage());
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

            // validate params as create entity
            $entity = new $this->entity($request);
            $entity_array = $entity->get();

            $stmt = $this->create_insert_or_update_sql_statement('update', $entity_array, $id);
            $stmt->execute();

            return array_merge(
                ['id' => (int)$record['id']],
                $entity_array
            );
        } catch (EntityException|ModelException $e) {
            throw new ModelException($e->getMessage());
        } catch (\PDOException|\TypeError $e) {
            throw new ModelException("Wrong parameters given check your request." . $e->getMessage());
        }
    }


    protected function findById(int $id, $as_object = false)
    {
        $sql = "SELECT * FROM $this->table WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $id, \PDO::PARAM_INT);
        $stmt->execute();
        if ($as_object) {
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        return $stmt->fetch();
    }

    private function create_insert_or_update_sql_statement(string $action, array $entity, int $id = null): mixed
    {
        $sql = match ($action) {
            'insert' => $this->create_insert_sql(),
            'update' => $this->create_update_sql()
        };
        $stmt = $this->db->prepare($sql);

        return $this->prepare_values($stmt, $entity, $id);

    }

    private function prepare_values($stmt, array $entity, int $id = null)
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

    private function create_insert_sql(): string
    {
        $columns_as_string = implode(', ', $this->columns);
        $key_values = $this->generate_update_or_insert_keys();

        return "INSERT INTO $this->table ($columns_as_string) VALUES ($key_values)";
    }

    private function create_update_sql(): string
    {
        $key_values = $this->generate_update_or_insert_keys('update');
        return "UPDATE $this->table SET $key_values WHERE id = :id";
    }


    protected function generate_update_or_insert_keys(string $action = 'insert'): string
    {
        $out = '';
        $length = count($this->columns) - 1;
        foreach ($this->columns as $key => $value) {
            $out .= ($action === 'insert') ? ':' . $value : $value . ' = :' . $value;;
            $out .= ($key < $length) ? ', ' : '';
        }
        return $out;
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
     */
    public function get_or_create(array $request, array $params): int
    {
        $select = $this->select_row_by_key_and_value($params['key'], $params['value']);
        if (isset($select['id'])) {
            return $select['id'];
        }
        return $this->create($request)['id'];
    }

    protected function select_row_by_key_and_value(string $key, string $value) : array | false
    {
        $value = '%' . $value . '%';
        $stmt = $this->db->prepare("SELECT id FROM $this->table WHERE LOWER($key) LIKE LOWER(:value)");
        $stmt->bindParam(':value', $value);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

}