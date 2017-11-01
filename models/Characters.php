<?php

class Characters extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=7, nullable=false)
     */
    public $character_id;

    /**
     *
     * @var string
     * @Column(type="string", length=55, nullable=false)
     */
    public $first_name;

    /**
     *
     * @var string
     * @Column(type="string", length=55, nullable=false)
     */
    public $last_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $alt_name;

    /**
     *
     * @var string
     * @Column(type="string", length=55, nullable=false)
     */
    public $jap_name;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $description;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("animedb");
        $this->setSource("characters");
        $this->hasMany('character_id', 'AnimeCharacters', 'character_id', ['alias' => 'AnimeCharacters']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'characters';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Characters[]|Characters|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Characters|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
