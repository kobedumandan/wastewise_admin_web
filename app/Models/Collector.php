<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;

class Collector extends FirebaseModel
{
    protected function getCollection(): string
    {
        return 'collectors';
    }

    /**
     * Find collector by username
     */
    public static function findByUsername($username)
    {
        $instance = new static();
        $items = $instance->firebase->getAll($instance->getCollection());
        
        foreach ($items as $item) {
            if (isset($item['username']) && $item['username'] === $username) {
                return new static($item);
            }
        }
        
        return null;
    }

    /**
     * Create a new collector with Firebase Auth user
     * 
     * @param array $data Must include: email, password, coll_fname, coll_lname, collcell_num, username
     * @return static
     * @throws \Exception
     */
    public static function createCollector(array $data)
    {
        $firebaseService = app(\App\Services\FirebaseService::class);
        
        // Validate required fields
        if (!isset($data['email']) || !isset($data['password'])) {
            throw new \Exception('Email and password are required for Firebase Auth');
        }

        // Check if email already exists in Firebase Auth
        $existingAuthUser = $firebaseService->getAuthUserByEmail($data['email']);
        if ($existingAuthUser) {
            throw new \Exception('Email already registered in Firebase Auth');
        }

        // Create user in Firebase Authentication
        try {
            $uid = $firebaseService->createAuthUser(
                $data['email'],
                $data['password'],
                [
                    'displayName' => ($data['coll_fname'] ?? '') . ' ' . ($data['coll_lname'] ?? ''),
                ]
            );
        } catch (\Kreait\Firebase\Exception\Auth\EmailExists $e) {
            throw new \Exception('Email already exists in Firebase Auth');
        } catch (\Exception $e) {
            throw new \Exception('Failed to create Firebase Auth user: ' . $e->getMessage());
        }

        // Prepare data for Realtime Database (don't store password)
        $collectorData = [
            'firebase_uid' => $uid,
            'email' => $data['email'],
            'coll_fname' => $data['coll_fname'] ?? '',
            'coll_lname' => $data['coll_lname'] ?? '',
            'collcell_num' => $data['collcell_num'] ?? '',
            'username' => $data['username'] ?? '',
            'created_at' => now()->toDateTimeString(),
        ];

        // Create record in Realtime Database using UID as key
        $instance = new static();
        $key = $firebaseService->create($instance->getCollection(), $collectorData, $uid);
        
        // Return instance with data
        $collectorData['key'] = $key;
        return new static($collectorData);
    }

    /**
     * Search by last name
     */
    public static function searchByLastName($lastName)
    {
        $instance = new static();
        $items = $instance->firebase->getAll($instance->getCollection());
        
        $filtered = array_filter($items, function($item) use ($lastName) {
            return isset($item['coll_lname']) && 
                   stripos($item['coll_lname'], $lastName) !== false;
        });
        
        return collect($filtered)->map(function ($item) {
            return new static($item);
        });
    }

    /**
     * Get total count
     */
    public static function count()
    {
        return static::all()->count();
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

