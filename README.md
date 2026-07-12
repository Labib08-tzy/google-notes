# 📝 Google Notes

A modern note-taking web application built with **Laravel 13**, **Google OAuth**, **Tailwind CSS**, and **MySQL**.

> Sign in with Google, create notes, archive them, and manage everything through a clean and responsive dashboard.

---

## ✨ Features

- 🔐 Google OAuth Authentication
- 👤 Google Profile Integration
- 📊 Responsive Dashboard
- 📝 Create Notes
- ✏️ Edit Notes
- 👀 View Notes
- 🗑 Delete Notes
- 📦 Archive & Restore Notes
- 🔍 Search Notes
- 📱 Responsive Design
- 🎨 Modern Google-inspired UI

---

## 🛠 Tech Stack

| Technology | Version |
|------------|----------|
| Laravel | 13 |
| PHP | 8.3+ |
| MySQL | 8+ |
| Tailwind CSS | Latest |
| Alpine.js | Latest |
| Laravel Socialite | Latest |
| Google OAuth 2.0 | ✔ |

---

## 📂 Project Structure

```
app/
bootstrap/
config/
database/
docs/
public/
resources/
routes/
storage/
tests/
```

---

## 🚀 Installation

Clone the repository

```bash
git clone https://github.com/Labib08-tzy/google-notes.git
```

Go to project

```bash
cd google-notes
```

Install dependencies

```bash
composer install
```

Install frontend

```bash
npm install
```

Copy environment

```bash
cp .env.example .env
```

Generate application key

```bash
php artisan key:generate
```

Run migrations

```bash
php artisan migrate
```

Run development server

```bash
php artisan serve
```

Start Vite

```bash
npm run dev
```

---

## 🔑 Google OAuth Setup

Create OAuth Credentials from Google Cloud Console.

Add these values to your `.env`:

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

---

## 📸 Screenshots

Coming Soon...

- Landing Page
- Dashboard
- Notes
- Archive
- Profile

---

## 📖 Documentation

Project documentation is available inside:

```
docs/
```

Including:

- PRD
- API
- Database Design
- Roadmap
- Tasks
- UI Guidelines

---

## 🎯 Future Improvements

- Dark Mode
- Rich Text Editor
- Tags
- Categories
- Export PDF
- PWA Support
- Notifications

---

## 👨‍💻 Author

**Labib08-tzy**

GitHub:

https://github.com/Labib08-tzy

---

## 📄 License

This project is licensed under the MIT License.
