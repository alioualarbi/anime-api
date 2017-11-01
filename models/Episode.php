<?php

class Episode extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=7, nullable=false)
     */
    public $id;

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
    public $slug;

    /**
     *
     * @var integer
     * @Column(type="integer", length=5, nullable=true)
     */
    public $number;

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
    public $description;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $date;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $videos;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("animedb");
        $this->setSource("episodes");
        $this->hasMany('id', 'Videos', 'episode_id', ['alias' => 'Videos']);
        $this->belongsTo('anime_id', '\Anime', 'id', ['alias' => 'Anime', 'reusable' => true]);
        $this->allowEmptyStringValues(['title', 'description']);
        $this->skipAttributes(['date']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'episodes';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Episode[]|Episode|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Episode|\Phalcon\Mvc\Model\ResultInterface|array
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Validate the episode information given
     * @return bool
     */
    public function validation(){
        $validation = new EpisodeValidator();
        $validation->filter();
        return $this->validate($validation);
    }

    public function setAttributes($data){

        $this->anime_id = $data->anime_id;
        if($data->number == '' || empty($data->number) || !isset($data->number) ){
            $this->number = null;
        }else{
            $this->number = $data->number;
        }
        $this->slug = $data->slug;
        $this->title = $data->title;
        $this->description = $data->description;
    }

    public function setVideoData($data){
        $videos = [];
        foreach($data as $v){
            $video = new Videos();
            $video->host = $v->host;
            $video->value = $v->value;
            $video->type = $v->type;
            $videos[] = $video;
        }
        $this->Videos = $videos;
        return $this;
    }
    
    /**
     * Gets an episode by its id or slug and gets a list of episodes based
     * on the arguments given.
     * @param array $params
     * @param int $expire
     * @return array
     */
    public static function get($params, $expire = 1)
    {
        $helper = new Helper;
        $episode = [];
        $key = rtrim(str_replace('::', ':', implode(':', $params)), ':');
        if ($helper->cache->exists($key)) {
            $episode = $helper->cache->get($key);
        } else {

            if(isset($params['id'])) {
                $episode = Episode::findFirst($params['id'])->jsonSerialize();
                $episode['videos'] = Episode::getVideos($episode['id']) ?? null;

            } elseif(isset($params['slug'])) {
                $episode = Episode::findFirst(array(
                    "slug = :slug:",
                    "bind" => array("slug" => $params['slug'])
                ))->jsonSerialize();
                $episode['videos'] = Episode::getVideos($episode['id']) ?? null;
            }  elseif(isset($params['anime_id'])) {

                $results = Episode::find(array(
                    "columns" => "id",
                    "anime_id = :anime_id:",
                    "bind" => array("anime_id" => $params['anime_id']),
                    "order" => "number desc"
                ))->jsonSerialize();

                foreach ($results as $result){
                    $episode[] = $result['id'];
                }

            } elseif(isset($params['latest'])) {

                if(isset($params['status']))
                    $params['status'] = "AND status = '$params[status]'";
                else
                    $params['status'] = '';

                if(isset($params['type']))
                    $params['type'] = " AND videos.type = '$params[type]'";
                else
                    $params['type'] = '';

                $episode = Episode::LatestSingleEpisodes($params, $helper->db);

            } else {}

            if($episode) {
                if (isset($episode['id'])){
                    $episode = Episode::addEpisodeNavigation($episode, $helper->db);

                    $anime = Anime::getAnime(['id' => $episode['anime_id']]);
                    $ep_type = ($anime['type'] == 'tv' ? 'Episode' : ucfirst($anime['type']));
                    $episode['alias'][] = trim("$anime[title] $ep_type $episode[number]");
                    if(!empty($anime['english']) && $anime['english'] != $anime['title'])
                        $episode['alias'][] = trim("$anime[english] $ep_type $episode[number]");
                }

                $helper->cache->save($key, $episode, $expire);
            }
        }

        return $episode;
    }

    public static function LatestSingleEpisodes($params,$db){
        $episodes = [];
        /*
        $query = $helper->mm->createQuery("SELECT Anime.id, Anime.slug, Anime.title, Anime.english, Anime.type, Anime.status, Episode.id, Episode.slug, Episode.number,
                                            CONCAT('[',GROUP_CONCAT(DISTINCT CONCAT('{\"host\":\"',Videos.host,'\",\"value\":\"',Videos.value,'\",\"type\":\"',Videos.type,'\"}')),']') as videos
                                            FROM Episode
                                            INNER JOIN Videos ON Videos.episode_id = Episode.id $params[3]
                                            INNER JOIN Anime ON Anime.id = Episode.anime_id $params[4]
                                            GROUP BY Episode.id
                                            ORDER BY Episode.id DESC
                                            LIMIT $params[2]");
        $episodes = $query->execute()->jsonSerialize();
        */
        $query = $db->query("SELECT episodes.id
                            FROM episodes
                            INNER JOIN anime ON episodes.anime_id = anime.id $params[status]
                            LEFT JOIN videos ON videos.episode_id = episodes.id and host = 'trollvid' $params[type]
                            WHERE episodes.id IN (SELECT MAX(episodes.id) AS id FROM episodes INNER JOIN videos on videos.episode_id = episodes.id $params[type] GROUP BY anime_id)
                            ORDER BY episodes.id DESC");
        $query->setFetchMode(Phalcon\Db::FETCH_ASSOC);
        $results = $query->fetchAll($query);
        foreach ($results as $result){
            $episodes[] = $result['id'];
        }
        return $episodes;
    }

    /**
     * Add next/previous episode navigation
     * @param $episode
     * @return array
     */
    public static function addEpisodeNavigation($episode, $db)
    {

        if($episode['number'] === null)
            $sql = "SELECT episodes.id, episodes.number
                          FROM episodes 
                          WHERE episodes.id = $episode[id]
                          UNION ALL  
                          (select episodes.id, episodes.number from episodes where anime_id = $episode[anime_id] and episodes.id > $episode[id] order by episodes.id asc limit 1)
                          UNION ALL  
                          (select episodes.id, episodes.number from episodes where anime_id = $episode[anime_id] and episodes.id < $episode[id] order by episodes.id desc limit 1)";
        else
            $sql = "SELECT episodes.id, episodes.number
                          FROM episodes 
                          WHERE episodes.id = $episode[id]
                          UNION ALL  
                          (select episodes.id, episodes.number from episodes where anime_id = $episode[anime_id] and episodes.number > $episode[number] order by episodes.number asc limit 1)
                          UNION ALL  
                          (select episodes.id, episodes.number from episodes where anime_id = $episode[anime_id] and episodes.number < $episode[number] order by episodes.number desc limit 1)";

        $n = $db->query($sql);
        $n->setFetchMode(Phalcon\Db::FETCH_ASSOC);
        $n = $n->fetchAll($n);

        if (isset($n[1]) && isset($n[2])) {
            $episode['next'] = $n[1]['id'];
            $episode['previous'] = $n[2]['id'];
            return $episode;
        } elseif (isset($n[1])) {
            if ($n[0]['number'] < $n[1]['number']) {
                $episode['next'] = $n[1]['id'];
                return $episode;
            } else {
                $episode['previous'] = $n[1]['id'];
                return $episode;
            }
        } else {
            return $episode;
        }
    }

    /**
     * Gets the videos for an episode
     * @param $episode_id
     * @return array
     */
    public static function getVideos($episode_id)
    {
        $videos = Videos::find(array(
            "columns" => "host, value, type",
            "episode_id = :episode_id:",
            "bind" => array("episode_id" => $episode_id)
        ))->jsonSerialize();

        return $videos;
    }
}
