# Firebase Migration Guide - WasteWise System

## Overview
Your WasteWise system has been successfully migrated from MySQL to Firebase Realtime Database.

## 🔧 Setup Instructions

### Step 1: Get Firebase Credentials

1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Select your WasteWise project (the same one used by your mobile app)
3. Click the gear icon ⚙️ next to "Project Overview"
4. Go to **"Project settings"**
5. Navigate to the **"Service accounts"** tab
6. Click **"Generate new private key"**
7. Save the downloaded JSON file as `credentials.json`

### Step 2: Place Credentials File

1. Copy the `credentials.json` file to:
   ```
   storage/app/firebase/credentials.json
   ```
2. Make sure the file is NOT committed to Git (it's already in .gitignore)

### Step 3: Update .env File

Add these lines to your `.env` file:

```env
# Firebase Configuration
FIREBASE_CREDENTIALS=app/firebase/credentials.json
FIREBASE_DATABASE_URL=https://YOUR-PROJECT-ID.firebaseio.com
FIREBASE_STORAGE_BUCKET=YOUR-PROJECT-ID.appspot.com
FIREBASE_PROJECT_ID=YOUR-PROJECT-ID
```

**Replace:**
- `YOUR-PROJECT-ID` with your actual Firebase project ID
- Find your database URL in Firebase Console > Realtime Database section

**⚠️ Important Notes:**
- For `FIREBASE_CREDENTIALS`, use the relative path from the `storage` folder: `app/firebase/credentials.json`
- Or use an absolute path like: `C:\xampp\htdocs\...\storage\app\firebase\credentials.json`
- Do NOT use PHP functions in .env file (like `storage_path()`)
- The path will automatically be resolved by the config file

### Step 4: Clear Caches

Run these commands:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Step 5: Test the Connection

Try logging in to the admin panel. If you see errors, check:
1. Credentials file exists and is valid
2. .env variables are correct
3. Firebase database URL is correct

## 📊 Firebase Database Structure

Your data will be organized in Firebase as follows:

```
wastewise-db/
├── admins/
│   ├── {admin-key}/
│   │   ├── admin_username
│   │   ├── admin_password (hashed)
│   │   ├── admin_fname
│   │   ├── admin_lname
│   │   └── admin_cell
│   └── ...
│
├── admin_logs/
│   ├── {log-key}/
│   │   ├── admin_id
│   │   ├── admin_timein
│   │   ├── admin_timeout
│   │   ├── last_name
│   │   ├── cell_number
│   │   └── admin_username
│   └── ...
│
├── admin_activities/
│   ├── {activity-key}/
│   │   ├── firstname
│   │   ├── lastname
│   │   ├── purok
│   │   ├── cell_num
│   │   └── action
│   └── ...
│
├── admin_timeinout/
│   └── ...
│
├── waste_segregated/
│   └── {record-key}/
│       └── total_waste_segregated
│
└── puroks/
    ├── {purok-key}/
    │   ├── purok_name
    │   └── date_schedule
    └── ...
```

## 🔐 Creating Your First Admin (Optional)

If you need to create an admin account manually:

1. Go to Firebase Console > Realtime Database
2. Add a new record under `admins/`:

```json
{
  "admins": {
    "admin1": {
      "admin_username": "admin",
      "admin_password": "$2y$12$hashed_password_here",
      "admin_fname": "Admin",
      "admin_lname": "User",
      "admin_cell": "1234567890"
    }
  }
}
```

Or use Laravel Tinker:

```bash
php artisan tinker
```

Then run:

```php
\App\Models\Admin::createAdmin([
    'admin_username' => 'admin',
    'admin_password' => 'your_password',
    'admin_fname' => 'Admin',
    'admin_lname' => 'User',
    'admin_cell' => '1234567890'
]);
```

## 🚀 What Changed

### Removed
- ✅ All MySQL migrations
- ✅ Eloquent ORM dependencies for main models
- ✅ Database-specific queries

### Added
- ✅ Firebase PHP SDK
- ✅ FirebaseService for database operations
- ✅ FirebaseModel base class
- ✅ Firebase-compatible models
- ✅ Firebase configuration

### Models Updated
All models now extend `FirebaseModel`:
- ✅ Admin
- ✅ admin_log
- ✅ admin_activity
- ✅ admin_timeinout
- ✅ admin_audit
- ✅ admin_audit_history
- ✅ waste_segregated
- ✅ Purok

### Controllers Updated
- ✅ AdminController - Firebase authentication
- ✅ ScheduleController - Firebase operations
- ✅ PurokController - Firebase queries

## 💡 Firebase Usage in Your Code

### Creating Records
```php
$admin = Admin::create([
    'admin_username' => 'newadmin',
    'admin_password' => 'password123'
]);
```

### Reading Records
```php
// Get all
$admins = Admin::all();

// Find by key
$admin = Admin::find('admin-key-123');

// Find by field
$admin = Admin::where('admin_username', 'admin')->first();
```

### Updating Records
```php
$admin = Admin::find('admin-key-123');
$admin->admin_fname = 'Updated Name';
$admin->save();
```

### Deleting Records
```php
$admin = Admin::find('admin-key-123');
$admin->delete();
```

## 🔍 Troubleshooting

### Error: "Firebase credentials file not found"
- Check that `storage/app/firebase/credentials.json` exists
- Verify the path in .env matches the actual location

### Error: "Permission denied"
- Check Firebase Database Rules in Firebase Console
- For development, you can temporarily use:
```json
{
  "rules": {
    ".read": true,
    ".write": true
  }
}
```
⚠️ **Warning:** Don't use these rules in production!

### Error: "Database URL not set"
- Check FIREBASE_DATABASE_URL in .env
- Make sure there are no extra spaces

### Session Issues
- SQLite is still used for sessions/cache
- Make sure `database/database.sqlite` exists
- Run: `touch database/database.sqlite` if needed

## 📝 Important Notes

1. **Backups**: Firebase automatically backs up your data, but you should still export your data regularly
2. **Rules**: Set proper Firebase security rules for production
3. **Indexing**: Configure indexes in Firebase Console for better query performance
4. **Sync**: Your mobile app and web system now share the same Firebase database!

## 🎯 Next Steps

1. ✅ Place Firebase credentials file
2. ✅ Update .env configuration
3. ✅ Clear caches
4. ✅ Test admin login
5. ✅ Create initial admin account
6. ✅ Set Firebase security rules
7. ✅ Test all features

## Need Help?

- [Firebase Documentation](https://firebase.google.com/docs/database)
- [Firebase PHP SDK](https://firebase-php.readthedocs.io/)
- Check Laravel logs: `storage/logs/laravel.log`

