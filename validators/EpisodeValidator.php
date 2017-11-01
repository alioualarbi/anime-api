<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017-08-29
 * Time: 4:29 PM
 */

use \Phalcon\Validation;
use \Phalcon\Validation\Validator\Digit;
use \Phalcon\Validation\Validator\PresenceOf;

class EpisodeValidator extends Validation
{

    public function initialize(){

        $this->add(
            [
                "anime_id",
                "slug",
            ],
            new PresenceOf([
                "message" => ":field is required",
            ])
        );

        $this->add(
            [
                "title",
                "description"

            ],
            new PresenceOf(
                [
                    "allowEmpty" => true,
                ]
            )
        );

        $this->add(
            "anime_id",
            new Digit(
                [
                    "message" => ":field must be numeric.",
                ]
            )
        );

        $this->add(
            "number",
            new \Phalcon\Validation\Validator\Numericality(
                [
                    "message" => ":field must be numeric.",
                    "allowEmpty" => true,
                ]
            )
        );

        $this->add(
            "slug",
            new \Phalcon\Validation\Validator\Callback(
                [
                    "callback" => function ($data) {
                        return CustomValidator::validateSlug($this,'Episode',$data);
                    }
                ]
            )
        );

        $this->add(
            "anime_id",
            new \Phalcon\Validation\Validator\Callback(
                [
                    "message" => "Anime does not exist.",
                    "callback" => function ($data) {
                    $anime = Anime::findFirst($data->anime_id);
                    if($anime)
                        return true;
                    return false;
                    }
                ]
            )
        );
    }

    public function filter()
    {

        $this->setFilters("anime_id", ['int', 'trim']);
        $this->setFilters("slug", ['string', 'trim']);
        $this->setFilters("number", ['trim']);
        $this->setFilters("title", ['striptags', 'trim']);
        $this->setFilters("description", ['striptags', 'trim']);

        return $this;
    }

}