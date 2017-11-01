<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017-08-26
 * Time: 11:08 PM
 */

use \Phalcon\Validation;
use \Phalcon\Validation\Validator\InclusionIn;

class GenreValidator extends Validation
{

    public function initialize(){

        $this->add(
            "genre",
            new InclusionIn(
                [
                    "message" => ":field is not recognized as valid, please check spelling.",
                    "domain"  => ['action','adventure','cars','comedy','dementia','demons','drama','ecchi','fantasy','game','harem','historical','horror','josei','kids','magic',
                        'martial arts','mecha','military','music','mystery','parody','police','psychological','romance','samurai','school','sci-fi','seinen','shoujo','shoujo ai',
                        'shounen','shounen ai','slice of life','space','sports','super power','supernatural','thriller','vampire','yuri']
                ]
            )
        );

    }

    public function filter(){
        $this->setFilters("genre", ['string', 'trim']);
    }

}

/*class GenreValidator extends Validator implements ValidatorInterface {

    public function validate(Validation $validator, $attribute) {

        //obtain date value
        $genre_data = $validator->getValue($attribute);

        if(empty($genre_data)){
            $validator->appendMessage(new Message('at least 1 genre must be included.','genre', 'Genre'));
            return false;
        }

        print_r($genre_data);

        $genre_arr = ['action','adventure','cars','comedy','dementia','demons','drama','ecchi','fantasy','game','harem','historical','horror','josei','kids','magic',
            'martial arts','mecha','military','music','mystery','parody','police','psychological','romance','samurai','school','sci-fi','seinen','shoujo','shoujo ai',
            'shounen','shounen ai','slice of life','space','sports','super power','supernatural','thriller','vampire','yuri'];

        $message = '';

        foreach($genre_data as $genre){
            if(!in_array(strtolower(trim($genre)), $genre_arr))
                $message.= "$genre is not recognized as a valid genre, please check spelling.";
        }

        if($message){
            $validator->appendMessage(new Message($message,'genre', 'Genre'));
            return false;
        }

        return true;
    }
}*/