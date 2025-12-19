<?php

namespace App\Models;

class admin_audit_history extends FirebaseModel
{
    protected function getCollection(): string
    {
        return 'admin_audit_history';
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
     * Search by name (firstname or lastname)
     */
    public static function searchByName($name)
    {
        $instance = new static();
        $items = $instance->firebase->getAll($instance->getCollection());
        
        $filtered = array_filter($items, function($item) use ($name) {
            return (isset($item['firstname']) && stripos($item['firstname'], $name) !== false) ||
                   (isset($item['lastname']) && stripos($item['lastname'], $name) !== false);
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
