<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;

class Admin extends FirebaseModel
{
    protected function getCollection(): string
    {
        return 'admins';
    }

    /**
     * Find admin by username
     */
    public static function findByUsername($username)
    {
        $instance = new static();
        $items = $instance->firebase->getAll($instance->getCollection());
        
        foreach ($items as $item) {
            if (isset($item['admin_username']) && $item['admin_username'] === $username) {
                return new static($item);
            }
        }
        
        return null;
    }

    /**
     * Create a new admin with hashed password
     */
    public static function createAdmin(array $data)
    {
        if (isset($data['admin_password'])) {
            $data['admin_password'] = Hash::make($data['admin_password']);
        }
        
        return static::create($data);
    }

    /**
     * Verify password
     */
    public function verifyPassword($password)
    {
        return Hash::check($password, $this->admin_password);
    }

    /**
     * Get total count of admins
     */
    public static function count()
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
