<?php

namespace App\Models;

class pickup_request extends FirebaseModel
{
    protected function getCollection(): string
    {
        return 'pickup_requests';
    }
}

