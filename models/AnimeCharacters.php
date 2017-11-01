<?php

class AnimeCharacters extends \Phalcon\Mvc\Model
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
     * @var integer
     * @Primary
     * @Column(type="integer", length=7, nullable=false)
     */
    public $character_id;

    /**
     *
     * @var string
     * @Column(type="string", length=15, nullable=false)
     */
    public $role;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $name;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("animedb");
        $this->setSource("anime_characters");
        $this->belongsTo('anime_id', '\Anime', 'id', ['alias' => 'Anime']);
        $this->belongsTo('character_id', '\Characters', 'character_id', ['alias' => 'Characters']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'anime_characters';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return AnimeCharacters[]|AnimeCharacters|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return AnimeCharacters|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
