<?php

namespace Api\Models;

/**
 * The class is responsible to establish connection to a database.
 * It utilises singleton design pattern to avoid multiple instances.
 */
class DbConnection
{
    private static array $instances = [];

    private static \PDO $connection;

    protected static string $host;
    protected static string $name;
    protected static string $user;
    protected static string $password;
    protected static int $port = 3306;

    /**
     * The construct method it sets the class attributes. If they are empty it tries to get from environment.
     * if its empty will trow the ModelException
     * You can sets values directly in attributes e.g.
     * private string $host = 'localhost';
     * @throws ModelException
     */
    protected function __construct()
    {
        if (!isset(self::$host)) {
            self::$host = $this->get_env('DB_HOST');
        }

        if (!isset(self::$name)) {
            self::$name = $this->get_env('DB_NAME');
        }

        if (!isset(self::$user)) {
            self::$user = $this->get_env('DB_USER');
        }

        if (!isset(self::$password)) {
            self::$password = $this->get_env('DB_PASSWORD');
        }
    }

    protected function __clone()
    {
    }

    /**
     * @throws ModelException
     */
    public function __wakeup()
    {
        throw new ModelException('Cannot un serialize a singleton.');
    }

    protected static function getInstance(): DbConnection
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }
        return self::$instances[$cls];
    }

    /**
     * @throws ModelException
     */
    protected static function connect(): void
    {
        try {
            self::$connection = new \PDO(
                'mysql:host=' . self::$host . ';port=' . self::$port . ';charset=utf8mb4;dbname=' . self::$name,
                self::$user,
                self::$password
            );
        } catch (\PDOException $e) {
            throw new ModelException('Cannot connect to database: ' . $e->getMessage());
        }
    }

    /**
     * @throws ModelException
     */
    public static function db(): \PDO
    {
        if(!isset(self::$connection)) {
            self::getInstance()->connect();
        }
        return self::$connection;
    }


    /**
     * @throws ModelException
     */
    protected function get_env(string $key): string
    {
        if ($val = getenv($key)) {
            return $val;
        }

        throw new ModelException('The environment variable ' . $key . ' not found');
    }
}