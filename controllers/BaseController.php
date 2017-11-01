<?php

use Phalcon\Http\Response;

class BaseController extends \Phalcon\Mvc\Controller
{
    public $qs; //query string

    public function onConstruct()
    {
        $this->qs = $this->request->getQuery();
        unset($this->qs['_url']);
        unset($this->qs['token']);
    }

    public function index()
    {

    }

    /**
     * Sets sends the response with json
     * @param $data
     * @return Response
     */
    protected function setResponse($data)
    {

        if ($data === false) {
            $this->response->setJsonContent(
                [
                    'status' => 'NOT-FOUND'
                ]
            );
        } elseif (empty($data)){
            $this->response->setJsonContent(
                [
                    'status' => 'NOT-FOUND'
                ]
            );
        } else {
            $this->response->setJsonContent(
                [
                    'status' => 'FOUND',
                    'data'   => $data
                ]
            );
        }

        return $this->response;
    }

    /**
     * @param $data
     */
    function println($data)
    {
        if (is_array($data) || is_object($data)) {
            echo "<pre>";
            print_r($data);
            echo "</pre>";
        } else {
            echo $data . '</br>';
        }
    }
}

