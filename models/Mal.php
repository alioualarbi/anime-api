<?php

class Mal extends \Phalcon\Mvc\Model
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
    public $id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $link;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $data;
    protected $_skipValidate;
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("animedb");
        $this->setSource("mal");
        $this->belongsTo('anime_id', '\Anime', 'id', ['alias' => 'Anime']);
        $this->enableValidator();
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'mal';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Mal[]|Mal|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Mal|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function disableValidator(){
        $this->_skipValidate = true;
    }

    public function enableValidator(){
        $this->_skipValidate = false;
    }

    public function validation(){
        $validation = new MalValidator();
        $validation->filter();
        return $this->validate($validation);
    }

}
