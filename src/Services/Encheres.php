<?php

namespace App\Services;

use App\Entity\Oeuvre;

class Encheres
{
    private $prix;
    private $oeuvre;

    public function __construct(Oeuvre $Oeuvre)
    {
        $this->prix = $Oeuvre->getPrix();
        $this->oeuvre = $Oeuvre;
    }

    public function bid($newPrice)
    {
        if ($newPrice > $this->prix){
            $this->oeuvre->setPrix($newPrice);
            return 'vous êtes actuellement l\enchère la plus haute';
        }else{
            return 'votre enchères n\'est pas assez élevée';
        };
        
    }
}