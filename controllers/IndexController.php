<?php

class IndexController extends BaseController
{

    public function index()
    {


        $ongoing_anime_subbed = Anime::getAnime([
            'qs' => [
                'status' => 'ongoing',
                'get' => 'subbed',
                'order' => ['title', 'asc']
            ]
        ]);
        $ongoing_anime_dubbed = Anime::getAnime([
            'qs' => [
                'status' => 'ongoing',
                'get' => 'dubbed',
                'order' => ['title', 'asc']
            ]
        ]);
        $latest_anime_subbed = Anime::getAnime([
            'qs' => [
                'get' => 'subbed',
                'order' => ['id', 'desc'],
                'limit' => 30
            ],
            'expire' => 1
        ]);
        $latest_anime_dubbed = Anime::getAnime([
            'qs' => [
                'get' => 'dubbed',
                'order' => ['id', 'desc'],
                'limit' => 30
            ]
        ]);

        $latest_episodes_subbed = Episode::get([
            'latest' => 'episodes',
            'limit' => 30,
            'type' => 'subbed',
            'status' => 'ongoing']);

        $latest_episodes_dubbed = Episode::get([
            'latest' => 'episodes',
            'limit' => 30,
            'type' => 'dubbed',
            'status' => 'ongoing']);

        $this->println($ongoing_anime_subbed);

        /*
        $this->response->setJsonContent([
            'status' => 'FOUND',
            'data'   => [
                'ongoing_anime' => ['subbed' => $ongoing_anime_subbed, 'dubbed' => $ongoing_anime_dubbed],
                'latest_anime' => ['subbed' => $latest_anime_subbed, 'dubbed' => $latest_anime_dubbed],
                'latest_episodes' => ['subbed' => $latest_episodes_subbed, 'dubbed' => $latest_episodes_dubbed],
            ]
        ]);

        return $this->response;
        */
    }

}

