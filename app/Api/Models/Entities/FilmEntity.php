<?php

namespace Api\Models\Entities;

class FilmEntity extends Entity
{
    private int|null $id;
    private string $title;
    private int $year;
    private int|string $genre;

    /**
     * @throws EntityException
     */
    function __construct(array $params)
    {
        if(isset($params['id'])) {
            $this->id = $params['id'];
        }
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
        $this->title = $title;
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
        $out = [
            'title' => $this->title,
            'year' => $this->year,
            'genre' => $this->genre
        ];

        if(isset($this->id)) {
            $out['id'] = $this->id;
        }


        return $out;
    }
}