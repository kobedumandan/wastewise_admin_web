<?php

namespace App\Models;

class admin_activity extends FirebaseModel
{
    protected function getCollection(): string
    {
        return 'admin_activities';
    }

    /**
     * Get recent activities
     */
    public static function recent($limit = 5)
    {
        return static::take($limit);
    }
}
