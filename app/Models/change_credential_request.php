<?php

namespace App\Models;

class change_credential_request extends FirebaseModel
{
    protected function getCollection(): string
    {
        return 'change_credential_requests';
    }

    /**
     * Get all change credential requests
     */
    public static function all()
    {
        $instance = new static();
        $items = parent::all();
        
        // Ensure key is set for each item
        foreach ($items as $item) {
            if (!isset($item->key) && isset($item->firebase_key)) {
                $item->key = $item->firebase_key;
            }
        }
        
        return $items;
    }

    /**
     * Find a change credential request by key
     */
    public static function find($key)
    {
        $instance = new static();
        $item = parent::find($key);
        
        if ($item && !isset($item->key)) {
            $item->key = $key;
        }
        
        return $item;
    }
}
