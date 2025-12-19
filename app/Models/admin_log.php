<?php

namespace App\Models;

class admin_log extends FirebaseModel
{
    protected function getCollection(): string
    {
        return 'admin_logs';
    }

    /**
     * Get logs for a specific admin
     */
    public static function getByAdminId($adminId)
    {
        return static::where('admin_id', $adminId);
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
            $key = $item['key'] ?? null;
            unset($item['key']);
            $model = new static($item);
            if ($key) {
                $model->key = $key;
            }
            return $model;
        });
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
