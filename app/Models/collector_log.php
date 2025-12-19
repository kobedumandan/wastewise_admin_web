<?php

namespace App\Models;

class collector_log extends FirebaseModel
{
    protected function getCollection(): string
    {
        return 'collector_logs';
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

    /**
     * Search by last name
     */
    public static function searchByLastName($lastName)
    {
        $instance = new static();
        $items = $instance->firebase->getAll($instance->getCollection());
        
        $filtered = array_filter($items, function($item) use ($lastName) {
            return isset($item['lastname']) &&
                   stripos($item['lastname'], $lastName) !== false;
        });
        
        return collect($filtered)->map(function ($item) {
            $key = $item['key'] ?? null;
            unset($item['key']);
            $model = new static($item);
            if ($key) {
                $model->key = $key;
            }
            return $model;
        });
    }
}

