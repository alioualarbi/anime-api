<?php

class RelatedAnime extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    public $created_at;

    /**
     *
     * @var integer
     * @Column(type="integer", length=5, nullable=false)
     */
    public $anime_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $value;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("animedb");
        $this->setSource("related");
        $this->belongsTo('anime_id', '\Anime', 'id', ['alias' => 'Anime']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'related';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return RelatedAnime[]|RelatedAnime|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return RelatedAnime|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * @param $data
     * @return array
     */
    public static function filterRelated($data){

        $related = [];
        foreach($data as $rel => $r){
            if($rel == "Adaptation")
                continue;
            if(is_array($r[0])){}else{
                $mta = Mal::findFirst(Helper::parseID('/anime/',$r[1]));
                if($mta){
                    $r[0] = strip_tags($r[0]);
                    $r[1] = $mta->anime_id;
                    $r[2] = Anime::findFirst($mta->anime_id)->slug;

                    $related[] = [$rel=>[$r[0],$r[1],$r[2]]];
                }
            }
            foreach($r as $rr){
                if(count($rr)>1){
                    $mtb = Mal::findFirst(Helper::parseID('/anime/',$rr[1]));
                    if($mtb) {
                        $rr[0] = strip_tags($rr[0]);
                        $rr[1] = $mtb->anime_id;
                        $rr[2] = Anime::findFirst($mtb->anime_id)->slug;

                        $related[] = [$rel=>[$rr[0], $rr[1], $rr[2]]];
                    }
                }
            }
        }

        return $related;
    }
}
