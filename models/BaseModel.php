<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017-08-29
 * Time: 8:33 PM
 */
class BaseModel extends \Phalcon\Mvc\Model
{

    protected $_skipValidate;
    protected $_errors;

    public function initialize(){
        $this->_errors = null;
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
     * Trigger errors before saving and cancel
     * @return bool
     */
    public function beforeSave(){
        $errs = $this->getErrorsMsg();
        if($errs){
            foreach($errs as $errmsg)
                $this->appendMessage(new \Phalcon\Mvc\Model\Message($errmsg));
            return false;
        }
        return true;
    }

    /**
     * Disable the validator
     */
    public function disableValidator(){
        $this->_skipValidate = true;
    }

    /**
     * Enable the validator
     */
    public function enableValidator(){
        $this->_skipValidate = false;
    }

    /**
     * Get error messages
     * @return mixed|array
     */
    public function getErrorsMsg(){
        return $this->_errors;
    }

    /**
     * Set error message;
     * @param $msg
     */
    public function setErrorMsg($msg){
        $this->_errors[] = $msg;
    }

}