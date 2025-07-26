<?php

namespace App\Service;

class UtilitiesService
{
    public function __construct(
        ){
    }

    public function returnNullIfStringNULLInDatabase($value)
    {
        if($value !== "NULL") {
            return $value;
        }else{
            return null;
        }
    }

}