<?php

use Phalcon\Mvc\Model\Transaction\Failed as TxFailed;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;

class EpisodeController extends BaseController
{

    public function index()
    {
        $qs = $this->request->getQuery();

        /**
         * If a request is made by the id or slug, redirect to the single methods to pull all the information.
         */
        if(isset($qs['id']) && preg_match("/^[1-9][0-9]*$/", $qs['id'])){
            return $this->findById($qs['id']);
        }
        if(isset($qs['slug']) && preg_match("/^[a-zA-Z0-9\-]+$/", $qs['slug'])){
            return $this->findBySlug($qs['slug']);
        }

        return $this->setResponse(false);
    }


    public function create(){
        $data = $this->request->getJsonRawBody();

        if ($data != null && !empty($data) && isset($data) && count((array)$data) == 6) {
            $episode = new Episode();
            try{

                $manager = new TxManager();
                $transaction = $manager->get();

                $episode->setTransaction($transaction);
                $episode->setAttributes($data);

                if($data->videos != null && isset($data->videos)){
                    $episode->setVideoData($data->videos);
                }

                if ($episode->save() === true) {
                    $this->response->setJsonContent(array('status' => 'OK', 'data' => $episode));
                } else {
                    $transaction->rollback();
                }

                $transaction->commit();

            }catch (TxFailed $e){

                $messages = $episode->getMessages();
                $error = [];
                foreach ($messages as $message) {
                    if( !empty($message->getMessage()) )
                        $error[] = $message->getMessage();
                }
                $this->response
                    ->setStatusCode(409, "Conflict")
                    ->setJsonContent(array('status' => 'ERROR', 'data' => $error));
            }
        }else{
            $this->response
                ->setStatusCode(400, "Bad Request")
                ->setJsonContent(array('status' => 'ERROR', 'data' => 'wrong data input'));
        }

        return $this->response;
    }

    public function update($id){
        $data = $this->request->getJsonRawBody();

        if ($data != null && !empty($data) && isset($data) && count((array)$data) == 6) {
            if ($episode = Episode::findFirst($id)) {

                try{

                    $manager = new TxManager();
                    $transaction = $manager->get();

                    $episode->setTransaction($transaction);
                    $episode->setAttributes($data);

                    if($data->videos != null && isset($data->videos)){
                        $episode->Videos->delete();
                        $episode->setVideoData($data->videos);
                    }

                    if ($episode->save() === true && $episode->getErrorsMsg() == false) {
                        $this->response->setJsonContent(array('status' => 'OK', 'data' => $episode));
                    } else {
                        $transaction->rollback();
                    }

                    $transaction->commit();

                }catch (TxFailed $e){

                    $messages = $episode->getMessages();
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
                    ->setJsonContent(array('status' => 'ERROR', 'data' => 'Episode not found'));
            }
        } else {
            $this->response
                ->setStatusCode(400, "Bad Request")
                ->setJsonContent(array('status' => 'ERROR', 'data' => 'wrong data input'));
        }

        return $this->response;
    }

    public function delete($id)
    {
        $data = $this->request->getJsonRawBody();

        if ($data != null && $data->id == $id) {

            if ($episode = Episode::findFirst($id)) {

                if ($episode->delete() == false) {
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
     * Find an episode by its id
     * @param $id
     * @return \Phalcon\Http\Response
     */
    public function findById($id)
    {
        $episode = Episode::get(['id' => $id]);
        return $this->setResponse($episode);
    }

    /**
     * Find an episode by its slug
     * @param $slug
     * @return \Phalcon\Http\Response
     */
    public function findBySlug($slug)
    {
        $episode = Episode::get(['slug' => $slug]);
        return $this->setResponse($episode);
    }

    /**
     * Get a list of the latest episodes
     * @param int $limit
     * @return \Phalcon\Http\Response
     */
    public function latestEpisodes($limit = 30){
        if($limit > 50) {
            $limit = 50;
        }

        if(isset($this->qs['get']) && $this->qs['get'] === 'dubbed')
            $type = 'dubbed';
        elseif(isset($this->qs['get']) && $this->qs['get'] === 'subbed')
            $type = 'subded';
        else
            $type = '';

        if(isset($this->qs['status']) && $this->qs['status'] === 'ongoing')
            $status = 'ongoing';
        else
            $status = '';

        $episode = Episode::get(['latest' => 'episodes', 'limit' => $limit, 'type' => $type, 'status' => $status]);

        return $this->setResponse($episode);
    }

}

