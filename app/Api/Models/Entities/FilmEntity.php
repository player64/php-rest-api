<?php

namespace Api\Models\Entities;

class FilmEntity extends Entity
{
    private string $title;
    private int $year;
    private int|string $genre;

    /**
     * @throws EntityException
     */
    function __construct(array $params)
    {
        $this->set_title($params['title']);
        $this->set_year($params['year']);
        $this->set_genre($params['genre']);
    }

    /**
     * @throws EntityException
     */
    public function set_title(string $title): void
    {
        Validator::required_string($title);
        $this->title = trim($title);
    }

    /**
     * @throws EntityException
     */
    public function set_year(int $year): void
    {
        Validator::year($year);

        $this->year = $year;
    }

    /**
     * @throws EntityException
     */
    public function set_genre(int | string $genre): void
    {
        if(gettype($genre) === 'integer') {
            $this->genre = $genre;
            return;
        }
        Validator::required_string($genre);
        $this->genre = $genre;
    }

    public function get(): array
    {
        return [
            'title' => $this->title,
            'year' => $this->year,
            'genre' => $this->genre
        ];
    }
}