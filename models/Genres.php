<?php

class Genres extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=5, nullable=false)
     */
    public $anime_id;

    /**
     *
     * @var string
     * @Primary
     * @Column(type="string", nullable=false)
     */
    public $genre;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("animedb");
        $this->setSource("genres");
        $this->belongsTo('anime_id', '\Anime', 'id', ['alias' => 'Anime']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'genres';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Genres[]|Genres|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Genres|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function validation(){
        $validation = new GenreValidator();
        $validation->filter();
        return $this->validate($validation);
    }

}
