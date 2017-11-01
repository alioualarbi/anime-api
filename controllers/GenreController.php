<?php

class GenreController extends BaseController
{
    /**
     * Gets a list of anime in multiple genre
     * @return \Phalcon\Http\Response
     */
    public function index()
    {
        $list = [];
        if (isset($this->qs['include']) && preg_match("/^[a-zA-Z\ \-\,]+$/", $this->qs['include'])) {
            $filter = new Phalcon\Filter();

            $include = explode(',', strtolower($this->qs['include']));
            $count = count($include);
            sort($include);

            if(isset($this->qs['get']) && $this->qs['get'] === 'dubbed')
                $type = " AND Videos.type = 'dub'";
            elseif(isset($this->qs['get']) && $this->qs['get'] === 'subbed')
                $type = " AND Videos.type = 'sub'";
            else
                $type = '';

            for ($i = 0; $i < $count; $i++)
                $include[$i] = $filter->sanitize($include[$i], ['string']);

            $key = Helper::createKey($this->qs);

            if ($this->cache->exists($key)) {
                $list = $this->cache->get($key);
            } else {
                if ($count > 0) {
                    $list = $this->modelsManager->createBuilder()
                        ->columns(['Anime.id', 'Anime.title', 'english', 'Anime.slug'])
                        ->from('Anime')
                        ->leftJoin('Genres', 'Anime.id = Genres.anime_id')
                        ->leftJoin('Episode', 'Anime.id = Episode.anime_id')
                        ->leftJoin('Videos', "Episode.id = Videos.episode_id $type")
                        ->inWhere('genre', $include)
                        ->groupBy(['Genres.anime_id'])
                        ->having("COUNT(*) = $count")
                        ->getQuery()
                        ->execute()
                        ->jsonSerialize();
                    if ($list) {
                        $this->cache->save($key, $list, 300);
                    }
                }
            }
        }
        return $this->setResponse($list);
    }

    /**
     * Get a list of anime in a single genre
     * @param $genre
     * @return \Phalcon\Http\Response
     */
    public function get($genre)
    {
        $key = 'genre:' . strtolower($genre);
        if ($this->cache->exists($key)) {
            $list = $this->cache->get($key);
        } else {
            $list = $this->modelsManager->createQuery("SELECT id, title, english, slug 
                                                    FROM Anime
                                                    LEFT JOIN Genres ON Genres.anime_id = Anime.id
                                                    WHERE Genres.genre = :genre:
                                                    ORDER BY title ASC");
            $list = $list->execute(array('genre' => "$genre"))->jsonSerialize();
            if ($list) {
                $this->cache->save($key, $list, 300);
            }
        }
        return $this->setResponse($list);
    }

}

