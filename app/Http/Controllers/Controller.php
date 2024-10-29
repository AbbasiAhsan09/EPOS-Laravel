<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{


    static function getFirstLetters($string) {
        // Split the string into words
        $words = explode(" ", $string);
        $firstLetters = "";
    
        // Loop through each word
        foreach ($words as $word) {
            // Get the first letter of each word and concatenate it
            $firstLetters .= $word[0];
        }
    
        return $firstLetters;
    }

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;



}
