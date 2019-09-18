<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class FrenchToDateTimeTransformer implements DataTransformerInterface {

    

    public function transform($date){

        if($date === null){

            return '';
        }

        return $date->format('d/m/Y');

    }
    

    public function reverseTransform($frenchDate){

        //frenchDate = 18/09/2019
        
        if($frenchDate === null){
            // Exception
            throw new TransformationFailedException("Vous devez fournir une date !");

        }

        $date = \DateTime::createFromFormat('d/m/Y', $frenchDate);

        if($date === false){
            // Exception
            throw new TransformationFailedException("Le format de la date n'est pas le bon !");

        }

        return $date;


    }




}


?>
