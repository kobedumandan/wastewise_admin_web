<?php

namespace App\Models;

use App\Services\FirebaseService;
use Illuminate\Support\Facades\App;

abstract class FirebaseModel
{
    protected $collection;
    protected $firebase;
    protected $attributes = [];
    public $key;

    public function __construct(array $attributes = [])
    {
        $this->firebase = App::make(FirebaseService::class);
        $this->fill($attributes);
    }

    /**
     * Get the collection name for this model
     */
    abstract protected function getCollection(): string;

    /**
     * Fill the model with attributes
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
            if ($key === 'key') {
                $this->key = $value;
            }
        }
        return $this;
    }

    /**
     * Get an attribute
     */
    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Set an attribute
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Check if attribute exists
     */
    public function __isset($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Save the model to Firebase
     */
    public function save()
    {
        $collection = $this->getCollection();
        
        if ($this->key) {
            // Update existing record
            $this->firebase->update($collection, $this->key, $this->attributes);
        } else {
            // Create new record
            $this->key = $this->firebase->create($collection, $this->attributes);
            $this->attributes['key'] = $this->key;
        }
        
        return $this;
    }

    /**
     * Delete the model from Firebase
     */
    public function delete()
    {
        if ($this->key) {
            $this->firebase->delete($this->getCollection(), $this->key);
            return true;
        }
        return false;
    }

    /**
     * Find a record by key
     */
    public static function find($key)
    {
        $instance = new static();
        $data = $instance->firebase->get($instance->getCollection(), $key);
        
        if (!$data) {
            return null;
        }
        
        return new static($data);
    }

    /**
     * Get all records
     */
    public static function all()
    {
        $instance = new static();
        $items = $instance->firebase->getAll($instance->getCollection());
        
        return collect($items)->map(function ($item) {
            return new static($item);
        });
    }

    /**
     * Find a record by a specific field
     */
    public static function where($field, $value)
    {
        $instance = new static();
        $items = $instance->firebase->query($instance->getCollection(), $field, $value);
        
        return collect($items)->map(function ($item) {
            return new static($item);
        });
    }

    /**
     * Get the first record matching the condition
     */
    public static function first($field, $value)
    {
        $results = static::where($field, $value);
        return $results->first();
    }

    /**
     * Create a new record
     */
    public static function create(array $attributes)
    {
        $instance = new static($attributes);
        $instance->save();
        return $instance;
    }

    /**
     * Take only a limited number of records
     */
    public static function take($limit)
    {
        $instance = new static();
        // Get all items and limit in memory (simple approach)
        $allItems = $instance->firebase->getAll($instance->getCollection());
        $limitedItems = array_slice($allItems, 0, $limit);
        
        return collect($limitedItems)->map(function ($item) {
            return new static($item);
        });
    }
    
    /**
     * Get records ordered by a field
     */
    public static function orderBy($field, $limit = null)
    {
        $instance = new static();
        $items = $instance->firebase->query($instance->getCollection(), $field, null, $limit);
        
        return collect($items)->map(function ($item) {
            return new static($item);
        });
    }
    
    /**
     * Get latest records
     */
    public static function latest($limit = null)
    {
        return static::orderBy('created_at', $limit);
    }

    /**
     * Convert model to array
     */
    public function toArray()
    {
        return $this->attributes;
    }
}

