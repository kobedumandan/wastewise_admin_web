<?php

namespace App\Models;

class Purok extends FirebaseModel
{
    protected function getCollection(): string
    {
        return 'puroks';
    }

    /**
     * Update or create a purok schedule
     */
    public static function updateOrCreate(array $search, array $data)
    {
        $instance = new static();
        $items = $instance->firebase->getAll($instance->getCollection());
        
        // Find existing record
        foreach ($items as $item) {
            $match = true;
            foreach ($search as $key => $value) {
                if (!isset($item[$key]) || $item[$key] !== $value) {
                    $match = false;
                    break;
                }
            }
            
            if ($match) {
                // Update existing
                $existing = new static($item);
                $existing->fill($data);
                $existing->save();
                return $existing;
            }
        }
        
        // Create new
        return static::create(array_merge($search, $data));
    }

    /**
     * Delete schedules before a certain date
     */
    public static function deleteBeforeDate($date)
    {
        $instance = new static();
        $items = $instance->firebase->getAll($instance->getCollection());
        
        foreach ($items as $item) {
            if (isset($item['date_schedule']) && $item['date_schedule'] < $date) {
                $instance->firebase->delete($instance->getCollection(), $item['key']);
            }
        }
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
