<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017-08-29
 * Time: 10:01 PM
 */
use \Phalcon\Validation\Validator\PresenceOf;
use \Phalcon\Validation\Validator\Digit;
use \Phalcon\Validation\Validator\InclusionIn;

class VideoValidator extends \Phalcon\Validation
{

    public function initialize()
    {
        $this->add(
            [
                "episode_id",
                "host",
                "value",
                "type"
            ],
            new PresenceOf([
                "message" => ":field is required",
            ])
        );

        $this->add(
            "episode_id",
            new Digit(
                [
                    "message" => ":field must be numeric.",
                ]
            )
        );

        $this->add(
            [
                "host",
                "type"
            ],
            new InclusionIn(
                [
                    "message" => [
                        "host" => "Only trollvid or mp4upload accepted.",
                        "type" => ":field can only be sub or dub.",
                    ],
                    "domain" => [
                        "host" => ["trollvid", "mp4upload"],
                        "type" => ['subbed', 'dubbed'],
                    ]
                ]
            )
        );

        $this->add(
            "value",
            new \Phalcon\Validation\Validator\Callback(
                [
                    "callback" => function ($data) {

                        $check = Videos::findFirst([
                            "value = :value:",
                            "bind" => ["value" => $data->value]
                        ]);

                        // Updating
                        if ($data->id) {
                            if ($data->value === Videos::findFirst($data->id)->value) {
                                return true;
                            } elseif ($check && $check->value === $data->value) {
                                $this->appendMessage(new \Phalcon\Validation\Message("Cannot use the same value of an existing video."));
                                return false;
                            } else {
                                return true;
                            }
                        } //Creating
                        else {
                            if ($check && $data->value === $check->value){
                                $this->appendMessage(new \Phalcon\Validation\Message("A video already exists with this value."));
                                return false;
                            }
                        }

                        return true;
                    }
                ]
            )
        );
    }

    public function filter()
    {

        $this->setFilters("episode_id", ['int', 'trim']);
        $this->setFilters("host", ['string', 'trim']);
        $this->setFilters("value", ['string', 'trim']);
        $this->setFilters("type", ['string', 'trim']);

    }

}