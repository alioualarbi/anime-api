<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017-07-31
 * Time: 5:18 PM
 */

use Phalcon\Mvc\Model\Transaction\Failed as TxFailed;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;

class AnimeController extends BaseController
{

    /**
     * Gets a list of all the anime in the database.
     * Can be filtered by query strings based on the
     * anime attributes
     * @return \Phalcon\Http\Response
     */
    public function index()
    {

        if (!count($this->qs)) {
            $animes = Anime::getAnime(['anime' => 'list']);
        } else {

            //If a request is made by the id or slug, redirect to the single methods to pull all the information.
            if (isset($this->qs['id']) && preg_match("/^[1-9][0-9]*$/", $this->qs['id'])) {
                return $this->findById($this->qs['id']);
            }
            if (isset($this->qs['slug']) && preg_match("/^[a-zA-Z0-9\-]+$/", $this->qs['slug'])) {
                return $this->findBySlug($this->qs['slug']);
            }

            $animes = Anime::getAnime($this->qs);

        }

        return $this->setResponse($animes);
    }

    /**
     * Add a new anime
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function create()
    {
        $data = $this->request->getJsonRawBody();

        if ($data != null && !empty($data) && isset($data) && count((array)$data) >= 14) {

            $anime = new Anime();

            try {

                $manager = new TxManager();
                $transaction = $manager->get();

                $anime->setTransaction($transaction);
                $anime->setAttributes($data);

                if (isset($data->genre)) {
                    $anime->setGenre($data->genre);
                }

                if (isset($data->mal)) {
                    $anime->setMalData($data->mal);
                    $anime->setRelatedAnimeData($data->mal->data->related);
                }

                if ($anime->save() === true) {
                    $this->response->setJsonContent(array('status' => 'OK', 'data' => $anime));

                } else {
                    $transaction->rollback();
                }
                $transaction->commit();
            } catch (TxFailed $e) {
                $messages = $anime->getMessages();
                $error = [];
                foreach ($messages as $message) {
                    if( !empty($message->getMessage()) )
                        $error[] = $message->getMessage();
                }
                $this->response
                    ->setStatusCode(409, "Conflict")
                    ->setJsonContent(array('status' => 'ERROR', 'data' => $error));
            }

        } else {
            $this->response
                ->setStatusCode(400, "Bad Request")
                ->setJsonContent(array('status' => 'ERROR', 'data' => 'wrong data input'));
        }

        return $this->response;
    }

    /**
     * Update an existing anime
     * @param $id
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function update($id)
    {
        $data = $this->request->getJsonRawBody();

        if ($data != null && !empty($data) && isset($data) && count((array)$data) >= 14) {

            if ($anime = Anime::findFirst($id)) {

                try {

                    $manager = new TxManager();
                    $transaction = $manager->get();

                    $anime->setTransaction($transaction);
                    $anime->setAttributes($data);

                    if (isset($data->genre)) {
                        $anime->genres->delete();
                        $anime->updateGenre($data->genre);
                    }

                    if ($data->mal) {
                        $anime->updateMalData($data->mal);
                        if ($data->mal->data->related) {
                            $anime->RelatedAnime->delete();
                            $anime->setRelatedAnimeData($data->mal->data->related);
                        }
                    }

                    if ($anime->save() === true && $anime->getErrorsMsg() == false) {

                        $this->response->setJsonContent(array('status' => 'OK', 'data' => $anime));

                    } else {
                        $transaction->rollback();
                    }

                    $transaction->commit();

                } catch (TxFailed $e) {

                    $messages = $anime->getMessages();
                    $error = [];
                    foreach ($messages as $message) {
                        if( !empty($message->getMessage()) )
                            $error[] = $message->getMessage();
                    }

                    $this->response
                        ->setStatusCode(409, "Conflict")
                        ->setJsonContent(array('status' => 'ERROR', 'data' => $error));
                }

            } else {
                $this->response
                    ->setStatusCode(404, "Not Found")
                    ->setJsonContent(array('status' => 'ERROR', 'data' => 'Anime not found'));
            }

        } else {
            $this->response
                ->setStatusCode(400, "Bad Request")
                ->setJsonContent(array('status' => 'ERROR', 'data' => 'wrong data input'));
        }

        return $this->response;
    }

    /**
     * Delete an existing anime
     * @param $id
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function delete($id)
    {
        $data = $this->request->getJsonRawBody();

        if ($data != null && $data->id == $id) {

            if ($anime = Anime::findFirst($id)) {

                if ($anime->delete() == false) {
                    $this->response
                        ->setStatusCode(400, "Bad Request")
                        ->setJsonContent(array('status' => 'ERROR', 'data' => 'Anime not deleted'));
                } else {
                    $this->response
                        ->setStatusCode(204, "OK")
                        ->setJsonContent(array('status' => 'OK', 'data' => 'Anime deleted'));
                }
            } else {
                $this->response
                    ->setStatusCode(404, "Not Found")
                    ->setJsonContent(array('status' => 'ERROR', 'data' => 'Attempting to delete a non existing anime.'));
            }

        } else {
            $this->response
                ->setStatusCode(400, "Bad Request")
                ->setJsonContent(array('status' => 'ERROR', 'data' => 'wrong data input'));
        }

        return $this->response;
    }

    /**
     * Find an Anime by its id
     * @param $id
     * @return \Phalcon\Http\Response
     */
    public function findById($id)
    {
        $anime = Anime::getAnime(['id' => $id]);

        return $this->setResponse($anime);
    }

    /**
     * Find an Anime by its slug
     * @param $slug
     * @return \Phalcon\Http\Response
     */
    public function findBySlug($slug)
    {
        $anime = Anime::getAnime(['slug' => $slug]);

        return $this->setResponse($anime);
    }

    /**
     * Get a list of recently added anime
     * @param int $limit
     * @return \Phalcon\Http\Response
     */
    public function latestAnime($limit = 30)
    {
        $anime = Anime::getAnime(['latest' => 'anime']);
        return $this->setResponse($anime);
    }

    /**
     * Get a list of ongoing anime
     * @return \Phalcon\Http\Response
     */
    public function ongoingAnime()
    {
        $anime = Anime::getAnime(['status' => 'ongoing']);
        return $this->setResponse($anime);
    }

    /**
     * Search for anime based on a keyword
     * @param $keyword
     * @return \Phalcon\Http\Response
     */
    public function searchAnime($keyword)
    {
        $anime = Anime::getAnime(['search' => $keyword]);
        return $this->setResponse($anime);
    }


    public function Episodes($id){
        $episodes = Episode::find(array(
            "anime_id = :anime_id:",
            "bind" => array("anime_id" => $id)
        ));

        foreach($episodes as $episode){
            $this->println($episode->Videos->jsonSerialize());
        }


    }

}