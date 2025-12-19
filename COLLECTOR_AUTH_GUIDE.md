# 🔐 Collector Authentication Guide

## 📍 Where Passwords Are Stored

### ✅ Firebase Authentication (Password Storage)
- **Location**: Firebase Console → **Authentication** → **Users**
- **What's stored**:
  - Email address
  - Password (hashed & encrypted by Firebase)
  - Firebase UID (unique identifier)
  - Display Name (Firstname Lastname)
  
### 📊 Realtime Database (Profile Data)
- **Location**: Firebase Console → **Realtime Database** → `collectors/{firebase_uid}`
- **What's stored**:
  - `firebase_uid` - Links to Firebase Auth user
  - `email` - Collector's email
  - `coll_fname` - First name
  - `coll_lname` - Last name
  - `collcell_num` - Phone number
  - `username` - Username
  - `created_at` - Registration timestamp
  - ❌ **NO PASSWORD** (not stored here for security)

---

## 🔍 How to Verify a Collector Account

### Step 1: Check Firebase Authentication
1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Select your project
3. Navigate to **Authentication** → **Users**
4. You should see the collector's email listed
5. Click on the user to see:
   - Email
   - UID (this is the `firebase_uid`)
   - Creation date
   - ⚠️ Password is NOT visible (this is normal - Firebase encrypts it)

### Step 2: Check Realtime Database
1. Go to **Realtime Database**
2. Navigate to `collectors/{firebase_uid}`
3. You should see all the collector's profile data

---

## 🧪 Testing Collector Login

### In Flutter App:
```dart
import 'package:firebase_auth/firebase_auth.dart';

// Login
await FirebaseAuth.instance.signInWithEmailAndPassword(
  email: 'collector@example.com',
  password: 'password123',
);

// Get current user
User? user = FirebaseAuth.instance.currentUser;
String uid = user!.uid; // This is the firebase_uid

// Fetch collector data from Realtime Database
DatabaseReference ref = FirebaseDatabase.instance.ref('collectors/$uid');
DataSnapshot snapshot = await ref.get();
Map<dynamic, dynamic> collectorData = snapshot.value as Map;
```

---

## ✅ Why This Design?

1. **Security**: Passwords are encrypted and managed by Firebase
2. **Best Practice**: Never store passwords in Realtime Database
3. **Flutter Compatible**: Works perfectly with Firebase Auth in Flutter
4. **Scalable**: Firebase handles authentication scaling

---

## 🔧 Troubleshooting

### Can't see password in Firebase Console?
✅ **This is normal!** Passwords are encrypted and never shown. To verify:
- Check Authentication → Users tab (email should be there)
- Try logging in with the email/password in your Flutter app

### Need to reset a password?
You can reset it from Firebase Console → Authentication → Users → Select User → Reset Password

### Want to verify collector was created?
1. Check Firebase Authentication → Users (should see email)
2. Check Realtime Database → `collectors/` (should see profile data with matching UID)

---

## 📝 Summary

- ✅ Password is stored in **Firebase Authentication**
- ✅ Profile data is stored in **Realtime Database** (`collectors/`)
- ✅ They are linked by `firebase_uid`
- ✅ This is the secure, correct way to do it!




