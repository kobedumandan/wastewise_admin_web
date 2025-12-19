<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials
    |--------------------------------------------------------------------------
    |
    | Path to the Firebase service account credentials JSON file.
    | You can generate this from Firebase Console > Project Settings > Service Accounts
    |
    */

    'credentials' => env('FIREBASE_CREDENTIALS') 
        ? (str_starts_with(env('FIREBASE_CREDENTIALS'), '/') || str_contains(env('FIREBASE_CREDENTIALS'), ':\\') 
            ? env('FIREBASE_CREDENTIALS') 
            : storage_path(env('FIREBASE_CREDENTIALS')))
        : storage_path('app/firebase/credentials.json'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Database URL
    |--------------------------------------------------------------------------
    |
    | The URL of your Firebase Realtime Database
    |
    */

    'database_url' => env('FIREBASE_DATABASE_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Firebase Storage Bucket
    |--------------------------------------------------------------------------
    |
    | The default Cloud Storage bucket name
    |
    */

    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET', ''),

    /*
    |--------------------------------------------------------------------------
    | Firebase Project ID
    |--------------------------------------------------------------------------
    |
    | Your Firebase project ID
    |
    */

    'project_id' => env('FIREBASE_PROJECT_ID', ''),
];

