# Database Documentation

## users

| Column | Type |
|---------|------|
| id | bigint |
| google_id | string |
| name | string |
| email | string |
| avatar | string |
| email_verified_at | timestamp |
| created_at | timestamp |
| updated_at | timestamp |

---

## notes

| Column | Type |
|---------|------|
| id | bigint |
| user_id | bigint |
| title | string |
| content | text |
| is_archived | boolean |
| created_at | timestamp |
| updated_at | timestamp |

---

## Relationship

User

1 User

↓

Many Notes

Note

Many Notes

↓

Belongs To User

---

## Rules

Every Note must belong to exactly one User.

Deleting User deletes all Notes.