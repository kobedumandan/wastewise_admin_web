<?php

namespace App\Models;

class collection_schedule extends FirebaseModel
{
    protected function getCollection(): string
    {
        return 'collection_schedules';
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
     * Override find() to ensure key is set
     */
    public static function find($key)
    {
        $instance = new static();
        $data = $instance->firebase->get($instance->getCollection(), $key);
        
        if (!$data) {
            return null;
        }
        
        $model = new static($data);
        $model->key = $key;
        return $model;
    }
}

