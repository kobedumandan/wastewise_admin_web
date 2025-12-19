<?php

namespace App\Services;

use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Contract\Auth;

class FirebaseService
{
    protected $database;
    protected $auth;

    public function __construct(Database $database, Auth $auth)
    {
        $this->database = $database;
        $this->auth = $auth;
    }

    /**
     * Get a reference to a database path
     */
    public function getReference($path)
    {
        return $this->database->getReference($path);
    }

    /**
     * Create a new record
     */
    public function create($collection, $data, $key = null)
    {
        $reference = $this->getReference($collection);
        
        if ($key) {
            $reference->getChild($key)->set($data);
            return $key;
        }
        
        $newRef = $reference->push($data);
        return $newRef->getKey();
    }

    /**
     * Get a single record by key
     */
    public function get($collection, $key)
    {
        $snapshot = $this->getReference($collection)->getChild($key)->getSnapshot();
        
        if (!$snapshot->exists()) {
            return null;
        }
        
        $data = $snapshot->getValue();
        $data['key'] = $key;
        
        return $data;
    }

    /**
     * Get all records from a collection
     */
    public function getAll($collection)
    {
        $snapshot = $this->getReference($collection)->getSnapshot();
        
        if (!$snapshot->exists()) {
            return [];
        }
        
        $items = [];
        foreach ($snapshot->getValue() as $key => $value) {
            $value['key'] = $key;
            $items[] = $value;
        }
        
        return $items;
    }

    /**
     * Update a record
     */
    public function update($collection, $key, $data)
    {
        $this->getReference($collection)->getChild($key)->update($data);
        return true;
    }

    /**
     * Delete a record
     */
    public function delete($collection, $key)
    {
        $this->getReference($collection)->getChild($key)->remove();
        return true;
    }

    /**
     * Query records with a condition
     */
    public function query($collection, $orderBy = null, $equalTo = null, $limitToFirst = null)
    {
        $reference = $this->getReference($collection);
        
        // Firebase requires orderBy when using other query parameters
        if ($limitToFirst || $equalTo !== null) {
            if (!$orderBy) {
                // Default to ordering by key if no orderBy specified
                $reference = $reference->orderByKey();
            } else {
                $reference = $reference->orderByChild($orderBy);
            }
        } elseif ($orderBy) {
            $reference = $reference->orderByChild($orderBy);
        }
        
        if ($equalTo !== null) {
            $reference = $reference->equalTo($equalTo);
        }
        
        if ($limitToFirst) {
            $reference = $reference->limitToFirst($limitToFirst);
        }
        
        $snapshot = $reference->getSnapshot();
        
        if (!$snapshot->exists()) {
            return [];
        }
        
        $items = [];
        foreach ($snapshot->getValue() as $key => $value) {
            $value['key'] = $key;
            $items[] = $value;
        }
        
        return $items;
    }

    /**
     * Check if a record exists
     */
    public function exists($collection, $key)
    {
        return $this->getReference($collection)->getChild($key)->getSnapshot()->exists();
    }

    /**
     * Get Firebase Auth instance
     */
    public function auth()
    {
        return $this->auth;
    }

    /**
     * Create a user in Firebase Authentication
     * 
     * @param string $email
     * @param string $password
     * @param array $userData Additional user properties (displayName, etc.)
     * @return string Firebase UID
     */
    public function createAuthUser($email, $password, array $userData = [])
    {
        $properties = [
            'email' => $email,
            'password' => $password,
            'emailVerified' => false,
        ];

        // Add custom properties if provided
        if (!empty($userData)) {
            $properties = array_merge($properties, $userData);
        }

        $userRecord = $this->auth->createUser($properties);
        return $userRecord->uid;
    }

    /**
     * Get a user from Firebase Authentication by UID
     * 
     * @param string $uid
     * @return \Kreait\Firebase\Auth\UserRecord|null
     */
    public function getAuthUser($uid)
    {
        try {
            return $this->auth->getUser($uid);
        } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
            return null;
        }
    }

    /**
     * Get a user from Firebase Authentication by email
     * 
     * @param string $email
     * @return \Kreait\Firebase\Auth\UserRecord|null
     */
    public function getAuthUserByEmail($email)
    {
        try {
            return $this->auth->getUserByEmail($email);
        } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
            return null;
        }
    }

    /**
     * Delete a user from Firebase Authentication
     * 
     * @param string $uid
     * @return bool
     */
    public function deleteAuthUser($uid)
    {
        try {
            $this->auth->deleteUser($uid);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update a user's email in Firebase Authentication
     * 
     * @param string $uid
     * @param string $newEmail
     * @return bool
     */
    public function updateAuthUserEmail($uid, $newEmail)
    {
        try {
            $this->auth->updateUser($uid, ['email' => $newEmail]);
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Failed to update email: ' . $e->getMessage());
        }
    }

    /**
     * Update a user's password in Firebase Authentication
     * 
     * @param string $uid
     * @param string $newPassword
     * @return bool
     */
    public function updateAuthUserPassword($uid, $newPassword)
    {
        try {
            $this->auth->updateUser($uid, ['password' => $newPassword]);
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Failed to update password: ' . $e->getMessage());
        }
    }
}

