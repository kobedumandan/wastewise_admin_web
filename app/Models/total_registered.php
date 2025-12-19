<?php

namespace App\Models;

class total_registered extends FirebaseModel
{
    protected function getCollection(): string
    {
        return 'users'; // Count from users collection
    }

    /**
     * Get total users count
     */
    public static function getTotalCount()
    {
        $instance = new static();
        $items = $instance->firebase->getAll($instance->getCollection());
        
        return (object)['total_users' => count($items)];
    }
}

