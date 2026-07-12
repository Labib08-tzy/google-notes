# PRD (Product Requirements Document)

# Google Notes
Version: 1.0
Status: Final
Framework: Laravel 12
Database: SQLite (Development) / MySQL (Production)

---

# 1. Project Overview

## Project Name
Google Notes

## Description

Google Notes adalah aplikasi web sederhana untuk mencatat aktivitas harian yang mewajibkan pengguna masuk menggunakan akun Google.

Aplikasi tidak menyediakan registrasi manual maupun login menggunakan email dan password.

Seluruh autentikasi dilakukan menggunakan Google OAuth sehingga setiap akun yang digunakan merupakan akun Google yang valid.

Setelah berhasil login, pengguna dapat membuat, mengedit, menghapus, mencari, dan mengarsipkan catatan miliknya sendiri.

---

# 2. Objectives

Tujuan proyek:

- Mempelajari implementasi Google OAuth di Laravel.
- Mempelajari Authentication.
- Mempelajari Authorization.
- Mempelajari CRUD.
- Mempelajari Relasi User dan Notes.
- Mempelajari Middleware.
- Mempelajari Dashboard.

---

# 3. Target Users

### User

Pengguna biasa yang ingin menyimpan catatan pribadi.

### Admin

Tidak ada.

Project ini hanya memiliki satu role yaitu User.

---

# 4. Authentication

## Login

Metode login:

✔ Login menggunakan Google

Tidak ada:

- Register Manual
- Login Email Password
- Forgot Password

Flow:

User membuka website

↓

Klik tombol

"Continue with Google"

↓

Dialihkan ke Google Login

↓

Memilih akun Google

↓

Google melakukan autentikasi

↓

Jika berhasil

↓

Laravel membuat akun baru apabila belum ada

atau

Laravel langsung login apabila akun sudah ada.

---

# 5. Authorization

Setelah login:

User hanya dapat melihat:

- Profil sendiri
- Catatan sendiri

User tidak boleh:

- Melihat catatan user lain
- Mengubah catatan user lain
- Menghapus catatan user lain

Semua data harus diproteksi menggunakan middleware auth.

---

# 6. Main Features

## Authentication

- Login Google
- Logout

---

## Dashboard

Dashboard menampilkan:

Greeting

Contoh:

Hello, Bian 👋

Foto Profil Google

Nama

Email

Total Notes

Total Archived Notes

Button Create Note

Daftar Note Terbaru

---

## Note Management

User dapat:

Create Note

Edit Note

Delete Note

Archive Note

Unarchive Note

View Detail Note

---

## Search

Search realtime berdasarkan:

Title

Content

---

## Profile

Menampilkan:

Google Avatar

Nama

Email

Tanggal akun dibuat

---

# 7. User Flow

Landing Page

↓

Login Google

↓

Dashboard

↓

Create Note

↓

Save

↓

Note muncul

↓

Edit

↓

Update

↓

Delete

atau

Archive

↓

Logout

---

# 8. Database Design

## users

id

google_id

name

email

avatar

email_verified_at

created_at

updated_at

---

## notes

id

user_id

title

content

is_archived

created_at

updated_at

---

Relationship

User

hasMany

Notes

Note

belongsTo

User

---

# 9. Pages

## Landing

Berisi:

Logo

Project Name

Deskripsi singkat

Button Continue with Google

---

## Dashboard

Sidebar

Navbar

Statistics

Latest Notes

Button Add Note

---

## Create Note

Input

Title

Textarea

Content

Button Save

Button Cancel

---

## Edit Note

Input

Title

Textarea

Content

Button Update

---

## Detail Note

Title

Content

Created Date

Updated Date

Archive Button

Delete Button

---

## Archived Notes

Menampilkan seluruh note yang sudah diarsipkan.

---

## Profile

Avatar

Nama

Email

Account Created

Logout Button

---

# 10. UI Style

Design:

Modern

Clean

Minimal

Google Inspired

Rounded Card

Soft Shadow

Responsive

Color Palette:

Primary

#4285F4

Secondary

#34A853

Accent

#FBBC05

Danger

#EA4335

Background

#F8F9FA

Text

#202124

Font

Poppins

---

# 11. Components

Navbar

Sidebar

Card

Button

Search Bar

Modal

Dropdown

Toast Notification

Loading Spinner

Confirmation Dialog

Pagination

Empty State

---

# 12. Validation

Title

Required

Maximum 100 Characters

Content

Required

Email

Tidak dapat diubah

Google Account Required

---

# 13. Notifications

Success

Note Created

Note Updated

Note Deleted

Note Archived

Login Success

Logout Success

Error

Validation Failed

Google Login Failed

Unauthorized

Server Error

---

# 14. Security

Google OAuth Authentication

CSRF Protection

XSS Protection

SQL Injection Protection

Authorization Policy

User hanya boleh mengakses note miliknya.

---

# 15. Middleware

auth

guest

verified

---

# 16. Folder Structure

app/

Http/

Controllers/

Auth/

NoteController.php

DashboardController.php

ProfileController.php

Models/

User.php

Note.php

Policies/

NotePolicy.php

resources/

views/

landing/

dashboard/

notes/

profile/

layouts/

routes/

web.php

database/

migrations/

seeders/

---

# 17. Success Criteria

Google Login berjalan.

User baru otomatis dibuat.

User lama dapat login kembali.

CRUD Note berjalan.

Search berjalan.

Archive berjalan.

Semua data hanya dapat diakses pemiliknya.

Responsive Desktop.

Responsive Mobile.

Tidak ada error saat CRUD.

---

# 18. Nice To Have (Optional)

Dark Mode

Pinned Notes

Favorite Notes

Rich Text Editor

Image Upload

Markdown Support

Reminder

Export PDF

Export TXT

Infinite Scroll

Autosave Draft

Recent Activity

Keyboard Shortcut
