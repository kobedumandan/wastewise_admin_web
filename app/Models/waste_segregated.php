<?php

namespace App\Models;

class waste_segregated extends FirebaseModel
{
    protected function getCollection(): string
    {
        return 'waste_segregated';
    }
}
