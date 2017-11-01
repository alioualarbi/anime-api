<?php

class Anime extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=5, nullable=false)
     */
    public $id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $slug;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $title;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $synopsis;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $english;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $japanese;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $synonyms;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $type;

    /**
     *
     * @var integer
     * @Column(type="integer", length=4, nullable=false)
     */
    public $episodes;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $status;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $date;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $aired;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $premiered;

    /**
     *
     * @var string
     * @Column(type="string", length=25, nullable=false)
     */
    public $duration;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $rating;


    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("animedb");
        $this->setSource("anime");
        $this->hasMany('id', 'Mal', 'anime_id', ['alias' => 'mal']);
        $this->hasMany('id', 'AnimeCharacters', 'anime_id', ['alias' => 'AnimeCharacters']);
        $this->hasMany('id', 'Genres', 'anime_id', ['alias' => 'genres']);
        $this->hasMany('id', 'RelatedAnime', 'anime_id', ['alias' => 'RelatedAnime']);
        $this->hasMany('id', 'Episode', 'anime_id', ['alias' => 'Episode']);
        $this->allowEmptyStringValues(['english', 'japanese', 'synonyms', 'aired', 'premiered', 'duration', 'rating']);
        $this->enableValidator();
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'anime';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Anime[]|Anime|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Anime|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Sets the attributes for creating/updating an anime
     * @param $data
     * @return $this
     */
    public function setAttributes($data){
        $this->slug = $data->slug;
        $this->title = $data->title;
        $this->synopsis = $data->synopsis;
        $this->english = $data->english;
        $this->japanese = $data->japanese;
        $this->synonyms = $data->synonyms;
        $this->type = $data->type;
        $this->episodes = $data->episodes;
        $this->status = $data->status;
        $this->date = $data->date;
        $this->aired = $data->aired;
        $this->premiered = $data->premiered;
        $this->duration = $data->duration;
        $this->rating = $data->rating;

        return $this;
    }

    /**
     * Validate the anime information given
     * @return bool
     */
    public function validation(){
        $validation = new AnimeValidator();
        $validation->filter();
        return $this->validate($validation);
    }

    /**
     * Sets the genre of an anime
     * @param $data
     * @return $this
     */
    public function setGenre($data){
        $genres = [];
        foreach($data as $g){
            $genre = new Genres();
            $genre->genre = $g;
            $genres[] = $genre;
        }
        $this->genres = $genres;

        return $this;
    }

    /**
     * Updates the genre of an anime
     * @param $data
     * @return $this
     */
    public function updateGenre($data){
        foreach($data as $g){
            $genre = new Genres();
            $genre->anime_id = $this->id;
            $genre->genre = $g;
            if($genre->save()===false){
                foreach ($genre->getMessages() as $message)
                    $this->setErrorMsg($message->getMessage());
            }
        }

        return $this;
    }

    /**
     * Sets myanimelist data for an anime
     * @param $data
     * @return $this
     */
    public function setMalData($data){
        $mal = new Mal();
        $mal->id = $data->id;
        $mal->link = $data->link;
        $mal->data = json_encode($data->data);
        $this->mal = $mal;

        return $this;
    }

    /**
     * Updates myanimelist data for an anime
     * @param $data
     * @return $this
     */
    public function updateMalData($data){
        $mal = Mal::findFirst($data->id);
        if($mal){
            echo 'updating';
            $mal->link = $data->link;
            $mal->data = json_encode($data->data);
            $this->mal = $mal;
        }

        return $this;
    }

    /**
     * Sets the related anime for an anime
     * @param $data
     * @return $this
     */
    public function setRelatedAnimeData($data){
        if(isset($data)){
            $rel_arr = [];
            if(!empty($rel = RelatedAnime::filterRelated($data))){
                foreach($rel as $r){
                    $related = new RelatedAnime();
                    $related->value = json_encode($r);
                    $rel_arr[] = $related;
                }
                $this->RelatedAnime = $rel_arr;
            }
        }

        return $this;
    }

    /**
     * Gets the anime genre and it's related anime
     * @param $anime
     * @return array of anime
     */
    public static function getInfo($anime)
    {
        if ($anime)
        {
            $data = $anime->jsonSerialize();
            if ($anime->Genres) {
                foreach ($anime->Genres as $genre)
                    $data['genres'][] = $genre->genre;
                $data['genres'] = implode(', ', $data['genres']);
            }
            if (isset($anime->RelatedAnime)) {
                foreach ($anime->RelatedAnime as $related)
                    $data['RelatedAnime'][] = json_decode($related->value, true);
            }
            return $data;
        }

        return [];
    }

    /**
     *
     * @param array $params
     * @return array|Anime
     */
    public static function getAnime($params = null)
    {
        $anime = [];
        $helper = new Helper;

        $qs = $params['qs'] ?? null;
        $_qs = $helper->qs ?? null;
        $expire = $params['expire'] ?? 90;

        if(count($qs)){
            $key = Helper::createKey($qs);
        }else{
            $key = Helper::createKey($params);
        }

        if ($helper->cache->exists($key)) {
            $anime = $helper->cache->get($key);

        } else {
            if (isset($params['id'])) {
                $anime = Anime::getFullAnime($params['id']);

            } elseif (isset($params['slug'])) {
                $anime = Anime::getFullAnime(array(
                    "slug = :slug:",
                    "bind" => array("slug" => $params['slug'])
                ));
            } elseif (isset($params['status']) && $params['status'] === 'ongoing') {
                $anime = Anime::find([
                    'columns' => 'id, title, english, slug',
                    "status = 'ongoing'",
                    'order' => 'title ASC',
                ])->jsonSerialize();
            } elseif ( isset($params['latest']) && $params['latest'] === 'anime') {
                $anime = Anime::find([
                    'columns' => 'id, title, english, slug',
                    'order' => 'id DESC',
                    'limit' => 50
                ])->jsonSerialize();
            } elseif (isset($params['anime']) && $params['anime'] === 'list') {
                $anime = Anime::find([
                    'columns' => 'id, title, english, slug',
                    'order' => 'title ASC',
                ])->jsonSerialize();
            } elseif (isset($params['search'])) {
                $builder = $helper->mm->createQuery("SELECT id, title, english, slug 
                                                    FROM Anime
                                                    WHERE CONCAT_WS('', title, english, synonyms) LIKE :keyword:
                                                    ORDER BY title ASC");
                $anime = $builder->execute(array('keyword' => "%$params[search]%"))->jsonSerialize();
            } elseif (isset($qs)) {

                if (isset($qs['get']) && $qs['get'] === 'dubbed') {
                    $type = "AND Videos.type = 'dubbed'";
                    unset($qs['get']);
                } elseif (isset($qs['get']) && $qs['get'] === 'subbed') {
                    $type = "AND Videos.type = 'subbed'";
                    unset($qs['get']);
                } else {
                    $type = '';
                    unset($qs['get']);
                }

                if(isset($qs['order'])){
                    $order = 'Anime.'.$qs['order'][0].' '. $qs['order'][1];
                    unset($qs['order']);
                }else{
                    $order = 'Anime.title';
                }

                if(isset($qs['limit'])){
                    $limit = $qs['limit'];
                    unset($qs['limit']);
                }else{
                    $limit = 9999;
                }

                $conds = []; //array to hold conditions based on the query string
                $params = []; //bind parameters

                //Build the conditions and bind parameters
                foreach ($qs as $q => $s) {
                    if ($q === 'genre') {
                        $conds[] = "$q = :$q:";
                    } else {
                        $conds[] = "Anime.$q = :$q:";
                    }
                    $params[$q] = $s;
                }
                $conds = implode(" AND ", $conds);

                $anime = $helper->mm->createBuilder()
                    ->columns(['Anime.id', 'Anime.title', 'Anime.english', 'Anime.slug'])
                    ->from('Anime')
                    ->leftJoin('Genres', 'Genres.anime_id = Anime.id')
                    ->Join('Episode', 'Anime.id = Episode.anime_id')
                    ->Join('Videos', "Episode.id = Videos.episode_id $type")
                    ->where($conds)
                    ->groupBy('Anime.id')
                    ->orderBy($order)
                    ->limit($limit)
                    ->getQuery()
                    ->execute($params)
                    ->jsonSerialize();
            } else {}

            if ($anime) {
                $helper->cache->save($key, $anime, $expire);
            }
        }

        //Get an episode list if requested
        if (isset($anime['id']) && isset($_qs['episodes']) && $_qs['episodes'] === 'all') {

            if(isset($_qs['get']) && $_qs['get'] === 'dubbed')
                $type = 'dubbed';
            elseif(isset($_qs['get']) && $_qs['get'] === 'subbed')
                $type = 'subbed';
            else
                $type = '';

            if ($episodes = Episode::get(['anime_id' => $anime['id'], 'type' => $type]))
                $anime['episode_list'] = $episodes;
        }

        return $anime;
    }

    /**
     * Get an anime with all details
     * @param null $parameters
     * @return array|Anime
     */
    public static function getFullAnime($parameters = null){
        return self::getInfo(self::findFirst($parameters));
    }

}
