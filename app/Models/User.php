<?php

namespace App\Models;

class User extends FirebaseModel
{
    protected function getCollection(): string
    {
        return 'users';
    }

    /**
     * Search by last name
     */
    public static function searchByLastName($lastName)
    {
        $instance = new static();
        $items = $instance->firebase->getAll($instance->getCollection());
        
        $filtered = array_filter($items, function($item) use ($lastName) {
            return isset($item['user_lname']) && 
                   stripos($item['user_lname'], $lastName) !== false;
        });
        
        return collect($filtered)->map(function ($item, $key) {
            $model = new static($item);
            $model->key = $key;
            return $model;
        });
    }

    /**
     * Get total count of users
     */
    public static function getTotalCount()
    {
        $instance = new static();
        $items = $instance->firebase->getAll($instance->getCollection());
        return count($items);
    }

    /**
     * Override all() to ensure keys are set
     */
    public static function all()
    {
        $instance = new static();
        $items = $instance->firebase->getAll($instance->getCollection());
        
        return collect($items)->map(function ($item) {
            // Extract key from item
            $key = $item['key'] ?? null;
            // Remove key from attributes to avoid duplicate
            unset($item['key']);
            $model = new static($item);
            // Set key on model
            if ($key) {
                $model->key = $key;
            }
            return $model;
        });
    }
}


