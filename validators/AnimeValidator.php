<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017-08-26
 * Time: 7:23 PM
 */

use \Phalcon\Validation;
use \Phalcon\Validation\Validator\Date;
use \Phalcon\Validation\Validator\Digit;
use \Phalcon\Validation\Validator\InclusionIn;
use \Phalcon\Validation\Validator\PresenceOf;

class AnimeValidator extends Validation
{

    public function initialize()
    {

        $this->add(
            [
                "title",
                "synopsis",
                "slug"

            ],
            new PresenceOf([
                "message" => ":field is required",
            ])
        );

        $this->add(
            [
                "english",
                "japanese",
                "synonyms",
                "aired",
                "premiered",
                "duration",
                "rating"

            ],
            new PresenceOf(
                [
                    "allowEmpty" => true,
                ]
            )
        );

        $this->add(
            [
                "status",
                "type",
                "rating"
            ],
            new InclusionIn(
                [
                    "message" => [
                        "status" => "The status must be completed or ongoing",
                        "type" => "The type must be tv, movie, ova, ona, or special",
                        "rating" => "Incorrect rating value"
                    ],
                    "domain" => [
                        "status" => ["completed", "ongoing", "unknown"],
                        "type" => ["tv", "movie", "ova", "ona", "special"],
                        "rating" => ["None", "G - All Ages", "PG - Children", "PG-13 - Teens 13 or older", "R - 17+ (violence & profanity)", "R+ - Mild Nudity"]
                    ]
                ]
            )
        );

        $this->add(
            "episodes",
            new Digit(
                [
                    "message" => ":field must be numeric.",
                ]
            )
        );

        $this->add(
            "date",
            new Date(
                [
                    "format" => "Y-m-d H:i:s",
                    "message" => "The date format is incorrect. Must be in Y-m-d H:i:s format.",
                ]
            )
        );

        $this->add(
            "slug",
            new \Phalcon\Validation\Validator\Callback(
                [
                    "callback" => function ($data) {
                        return CustomValidator::validateSlug($this,'Anime',$data);
                    }
                ]
            )
        );

    }

    public function filter()
    {

        $this->setFilters("slug", ['string', 'trim']);
        $this->setFilters("title", ['striptags', 'trim']);
        $this->setFilters("synopsis", ['striptags', 'trim']);
        $this->setFilters("english", ['striptags', 'trim']);
        $this->setFilters("japanese", ['striptags', 'trim']);
        $this->setFilters("synonyms", ['striptags', 'trim']);
        $this->setFilters("type", ['string', 'trim']);
        $this->setFilters("episodes", ['int', 'trim']);
        $this->setFilters("status", ['string', 'trim']);
        $this->setFilters("date", ['string', 'trim']);
        $this->setFilters("aired", ['string', 'trim']);
        $this->setFilters("premiered", ['string', 'trim']);
        $this->setFilters("duration", ['string', 'trim']);
        $this->setFilters("rating", ['string', 'trim']);

        return $this;
    }

}