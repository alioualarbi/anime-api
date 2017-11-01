<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017-08-27
 * Time: 4:24 PM
 */

use \Phalcon\Validation;
use \Phalcon\Validation\Validator\PresenceOf;
use \Phalcon\Validation\Validator\Regex;
use \Phalcon\Validation\Validator\Digit;

class MalValidator extends Validation
{

    public function initialize(){

        $this->add(
            "link",
            new Regex(
                [
                    "pattern" => "#^https://myanimelist.net/anime/([0-9]+)/([^/]*)$#",
                    "message" => "Must be a valid myanimelist.net anime link",
                ]
            )
        );

        $this->add(
            [
                "data"

            ],
            new PresenceOf([
                "message" => ":field is required",
            ])
        );

        $this->add(
            "id",
            new Digit(
                [
                    "message" => ":field must be numeric.",
                ]
            )
        );

        $this->add(
            "id",
            new \Phalcon\Validation\Validator\Callback(
                [
                    "message" => "An Anime already exists with this mal id.",
                    "callback" => function ($data) {
                        $check = Mal::findFirst($data->id);

                        if ($check && ($check->id === $data->id) && ($check->anime->id === $data->anime_id)) {
                            return true;
                        } elseif($check && $check->id === $data->id) {
                            return false;
                        } else {
                            return true;
                        }

                    }
                ]
            )
        );

    }

    public function filter(){

        $this->setFilters("id", ['int', 'trim']);
        $this->setFilters("link", ['striptags', 'trim']);
        $this->setFilters("data", ['striptags', 'trim']);

        return $this;
    }

}