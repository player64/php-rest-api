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
    function __construct(
        string     $title,
        int        $year,
        int|string $genre,
        int        $id = null,
    )
    {
        $this->id = $id;
        $this->set_title($title);
        $this->set_year($year);
        $this->set_genre($genre);
    }

    /**
     * @throws EntityException
     */
    public function set_title(string $title)
    {
        Validator::required_string($title);
        $this->title = $title;
    }

    /**
     * @throws EntityException
     */
    public function set_year(int $year)
    {
        Validator::year($year);

        $this->year = $year;
    }

    /**
     * @throws EntityException
     */
    public function set_genre(int | string $genre)
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
            'id' =>  $this->id,
            'title' => $this->title,
            'year' => $this->year,
            'genre' => $this->genre
        ];
    }
}