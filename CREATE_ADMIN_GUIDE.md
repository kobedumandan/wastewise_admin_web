# 👨‍💼 Create Admin Accounts - Complete Guide

## 🚀 Quick Methods to Create Admin

### **Method 1: Using Seeder (Easiest)** ✅

Run this command:
```bash
php artisan db:seed --class=AdminSeeder
```

This creates the default admin:
- **Username:** `admin`
- **Password:** `admin`

---

### **Method 2: Using Factory in Tinker**

Open tinker:
```bash
php artisan tinker
```

#### Create Default Admin:
```php
Admin::createAdmin([
    'admin_username' => 'admin',
    'admin_password' => 'admin',
    'admin_fname' => 'Admin',
    'admin_lname' => 'User',
    'admin_cell' => '1234567890'
]);
```

#### Using Factory - Basic:
```php
// Create one admin with random data
Admin::factory()->create();
```

#### Using Factory - Custom Username & Password:
```php
Admin::factory()
    ->withUsername('superadmin')
    ->withPassword('mypassword')
    ->create();
```

#### Using Factory - Full Custom Details:
```php
Admin::factory()
    ->withUsername('john.admin')
    ->withPassword('secure123')
    ->withDetails('John', 'Doe', '09171234567')
    ->create();
```

#### Create Multiple Admins:
```php
Admin::factory()->count(5)->create();
```

Then type `exit` to quit tinker.

---

### **Method 3: Quick Script**

Create `create_admin.php` in project root:

```php
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simple method
$admin = \App\Models\Admin::createAdmin([
    'admin_username' => 'admin',
    'admin_password' => 'admin',
    'admin_fname' => 'Admin',
    'admin_lname' => 'User',
    'admin_cell' => '1234567890'
]);

echo "✅ Admin created successfully!\n";
echo "Username: {$admin->admin_username}\n";
echo "Key: {$admin->key}\n";

// Using Factory
$admin2 = \App\Models\Admin::factory()
    ->withUsername('manager')
    ->withPassword('manager123')
    ->withDetails('Jane', 'Smith', '09181234567')
    ->create();

echo "✅ Another admin created!\n";
echo "Username: {$admin2->admin_username}\n";
```

Run:
```bash
php create_admin.php
```

---

### **Method 4: Artisan Command** (Custom)

Create a custom command:
```bash
php artisan make:command CreateAdminCommand
```

Edit `app/Console/Commands/CreateAdminCommand.php`:

```php
<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;

class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create 
                            {username : Admin username} 
                            {password : Admin password} 
                            {--fname= : First name}
                            {--lname= : Last name}
                            {--cell= : Cell phone number}';

    protected $description = 'Create a new admin user';

    public function handle()
    {
        $admin = Admin::createAdmin([
            'admin_username' => $this->argument('username'),
            'admin_password' => $this->argument('password'),
            'admin_fname' => $this->option('fname') ?? 'Admin',
            'admin_lname' => $this->option('lname') ?? 'User',
            'admin_cell' => $this->option('cell') ?? '0000000000',
        ]);

        $this->info('✅ Admin created successfully!');
        $this->info("Username: {$admin->admin_username}");
        $this->info("Firebase Key: {$admin->key}");

        return 0;
    }
}
```

Then use it:
```bash
# Basic
php artisan admin:create admin admin123

# With details
php artisan admin:create john.admin secret123 --fname=John --lname=Doe --cell=09171234567
```

---

## 🎯 Factory Examples

### Create Admin with Default Password ('password'):
```php
Admin::factory()->create([
    'admin_username' => 'testadmin'
]);
// Login with: testadmin / password
```

### Create Multiple Admins at Once:
```php
Admin::factory()->count(3)->create();
```

### Create with Specific Data:
```php
Admin::factory()->create([
    'admin_username' => 'manager',
    'admin_fname' => 'Jane',
    'admin_lname' => 'Manager',
    'admin_cell' => '09991234567'
]);
```

---

## 📊 Check Created Admins

### View in Firebase Console:
1. Go to Firebase Console
2. Realtime Database → Data
3. Look under `admins` collection

### View in Tinker:
```bash
php artisan tinker
```

```php
// Get all admins
$admins = Admin::all();
foreach($admins as $admin) {
    echo $admin->admin_username . "\n";
}

// Find specific admin
$admin = Admin::findByUsername('admin');
echo $admin->admin_fname . " " . $admin->admin_lname;
```

---

## 🔐 Password Management

### All passwords are automatically hashed using bcrypt:
```php
// These are equivalent:
Admin::createAdmin(['admin_password' => 'mypassword']);
Admin::create(['admin_password' => Hash::make('mypassword')]);
```

### Test Password:
```php
$admin = Admin::findByUsername('admin');
$isValid = $admin->verifyPassword('admin'); // returns true/false
```

---

## ⚡ Quick Reference

| Task | Command |
|------|---------|
| Seed default admin | `php artisan db:seed --class=AdminSeeder` |
| Create via tinker | `Admin::createAdmin([...])` |
| Create with factory | `Admin::factory()->create()` |
| Create multiple | `Admin::factory()->count(5)->create()` |
| Custom admin | `Admin::factory()->withUsername('user')->withPassword('pass')->create()` |

---

## 🎉 Recommended Setup Flow

1. **First Time:**
```bash
php artisan db:seed --class=AdminSeeder
```

2. **Login with:**
   - Username: `admin`
   - Password: `admin`

3. **Change password in the system after first login!**

---

## 💡 Tips

- Default factory password is `password`
- Use `createAdmin()` method to auto-hash passwords
- All admins are stored in Firebase's `admins` collection
- Each admin gets a unique Firebase key
- Phone numbers are optional

That's it! Choose the method that works best for you. 🚀

