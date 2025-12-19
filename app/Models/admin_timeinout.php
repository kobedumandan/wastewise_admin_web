<?php

namespace App\Models;

class admin_timeinout extends FirebaseModel
{
    protected function getCollection(): string
    {
        return 'admin_timeinout';
    }

    /**
     * Search by last name
     */
    public static function searchByLastName($lastName)
    {
        $instance = new static();
        $items = $instance->firebase->getAll($instance->getCollection());
        
        $filtered = array_filter($items, function($item) use ($lastName) {
            return isset($item['last_name']) && 
                   stripos($item['last_name'], $lastName) !== false;
        });
        
        return collect($filtered)->map(function ($item) {
            return new static($item);
        });
    }

    /**
     * Get paginated results
     */
    public static function paginate($perPage = 10, $search = null)
    {
        $items = $search 
            ? static::searchByLastName($search) 
            : static::all();
        
        // Simple pagination (for demonstration)
        return $items->forPage(request()->get('page', 1), $perPage);
    }
}
