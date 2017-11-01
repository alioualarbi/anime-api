<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017-08-29
 * Time: 5:19 PM
 */


class CustomValidator{

    public static function validateSlug(\Phalcon\Validation $validation,$entity,$data){

        if(!preg_match("#^[a-z0-9\-\_]+$#", $data->slug)){
            $validation->appendMessage(new \Phalcon\Validation\Message('Slug must be lower case and can only contain - or _'));
            return false;
        }

        $check = $entity::findFirst([
            "slug = :slug:",
            "bind" => ["slug" => $data->slug]
        ]);

        // Updating
        if ($data->id) {
            if ($data->slug === $entity::findFirst($data->id)->slug) {
                return true;
            } elseif ($check && $check->slug === $data->slug) {
                $validation->appendMessage(new \Phalcon\Validation\Message("Cannot use the same slug of an existing $entity."));
                return false;
            } else {
                return true;
            }
        } //Creating
        else {
            if ($check && $data->slug === $check->slug){
                $validation->appendMessage(new \Phalcon\Validation\Message("An $entity already exists with this slug. Please choose a different slug."));
                return false;
            }
        }

        return true;
    }

}