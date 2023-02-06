<?php

namespace Api\Models;

class SqlBuilderException extends \Exception {}

class SqlBuilder
{
    private string $select;
    private string $insert;

    private string $values;

    private string $update;

    private string $delete;

    private string $set;
    private string $from;
    private string $where;
    private string $like;
    private string $as;
    private string $join;
    private string $left_join;

    private string $on;

    public function select(string $select): SqlBuilder
    {
        $this->select = "SELECT $select" ;
        return $this;
    }

    public function insert(string $into, string|array $keys): SqlBuilder
    {
        if(gettype($keys) === 'array') {
            $keys = implode(', ', $keys);
        }
        $this->insert = "INSERT INTO $into ($keys)";
        return $this;
    }

    public function values(string|array $values): SqlBuilder
    {
        if(gettype($values) === 'array') {
            $values = $this->_generate_update_or_insert_keys($values);
        }
        $this->values = " VALUES ($values)";
        return $this;
    }

    public function update(string $update): SqlBuilder
    {
        $this->update = "UPDATE $update";
        return $this;
    }

    public function set(string|array $set): SqlBuilder
    {
        if(gettype($set) === 'array') {
            $set = $this->_generate_update_or_insert_keys($set, 'update');
        }
        $this->set = " SET $set";
        return $this;
    }

    public function delete(): SqlBuilder
    {
        $this->delete = "DELETE";
        return $this;
    }

    public function from(string $from): SqlBuilder
    {
        $this->from = " FROM $from";
        return $this;
    }

    public function where(string $where): SqlBuilder
    {
        $this->where = " WHERE $where";
        return $this;
    }

    public function like(string $like): SqlBuilder
    {
        $this->like = " LIKE $like";
        return $this;
    }

    public function as(string $as): SqlBuilder
    {
        $this->as = " AS $as";
        return $this;
    }

    public function join(string $join): SqlBuilder
    {
        $this->join = " JOIN $join";
        return $this;
    }

    public function left_join(string $left_join): SqlBuilder
    {
        $this->left_join = " LEFT JOIN $left_join";
        return $this;
    }

    public function on(string $on): SqlBuilder
    {
        $this->on = " ON $on";
        return $this;
    }

    private function _generate_update_or_insert_keys( array $columns, string $action = 'insert'): string
    {
        $out = '';
        $length = count($columns) - 1;
        foreach ($columns as $key => $value) {
            $out .= ($action === 'insert') ? ':' . $value : $value . ' = :' . $value;;
            $out .= ($key < $length) ? ', ' : '';
        }
        return $out;
    }

    /**
     * @throws SqlBuilderException
     */
    public function build(): string
    {
        $sql = '';
        if(isset($this->select)) {
            $sql .= $this->select;
        } elseif(isset($this->update) && isset($this->set) && isset($this->where)) {
            $sql .= $this->update;
            $sql .= $this->set;
            $sql .= $this->where;
        } elseif(isset($this->insert) && isset($this->values)) {
            $sql .= $this->insert;
            $sql .= $this->values;
        } else if(isset($this->delete)) {
            $sql .= $this->delete;
        } else {
            throw new SqlBuilderException('You need to use select, insert, delete or update keyword.');
        }

        // other attributes
        if(isset($this->as)) {
            $sql .= $this->as;
        }

        if(isset($this->from)) {
            $sql .= $this->from;
        }

        if(isset($this->join)) {
            $sql .= $this->join;
        }

        if(isset($this->left_join)) {
            $sql .= $this->left_join;
        }

        if(isset($this->on)) {
            $sql .= $this->on;
        }

        if(!isset($this->update) && isset($this->where)) {
            $sql .= $this->where;
        }

        if(isset($this->like)) {
            $sql .= $this->like;
        }

        return $sql;
    }

}