<?php

namespace App\Models;

class collector_audit extends FirebaseModel
{
    protected function getCollection(): string
    {
        return 'collector_audits';
    }

    /**
     * Search by name
     */
    public static function searchByName($name)
    {
        $instance = new static();
        $items = $instance->firebase->getAll($instance->getCollection());
        
        $filtered = array_filter($items, function($item) use ($name) {
            return (isset($item['firstname']) && stripos($item['firstname'], $name) !== false) ||
                   (isset($item['lastname']) && stripos($item['lastname'], $name) !== false);
        });
        
        return collect($filtered)->map(function ($item, $key) {
            $model = new static($item);
            $model->key = $key;
            return $model;
        });
    }
}

