
# Laravel Task Management System

A role-based task management system built with Laravel, designed to streamline project organization, task assignment, and performance evaluation. This system supports three primary user roles — **Admin**, **Manager**, and **User** — each with specific access levels and responsibilities.


## 🛠 Installation & Setup

Follow the steps below to set up and run the system locally on your machine.

### ✅ Step 1: crate folder in htdocs and Clone the Repository

Open your terminal (e.g., PowerShell or CMD) and run:

**```powershell**
git clone https://github.com/MGFrancois123/group-3-22RP03920_-_22RP02034-TaskManagementSystem.git .


 ✅ Step 2: composer update
 before you make php artisan migrate remove file **"2024_01_10_000000_normalize_task_status_values"**
 ✅ Step 3: php artisan migrate
 ✅ Step 4: php artisan serve



## 🚀 Features

👥 User Registration & Role Assignment
✨ Default Behavior
New users can register via the Register page.

By default, every registered user is assigned the role: user.

🔐 How to Set Admin or Manager Role (Local Setup)
To set a user as Admin or Manager:

Open phpMyAdmin: http://localhost/phpmyadmin

Go to your Laravel database.

Open the users table.

Find the user you want to promote.

Update the role field:

admin for Admin

manager for Manager

user (default role)

⚠️ Make sure the role value is exactly one of these: admin, manager, user

🚀 Features
👮‍♂️ Roles & Permissions
Role-based access: Admin, Manager, User

Laravel authentication

Access to features based on role

📁 Project Management (Manager Role)
Create, edit, delete projects

Assign users to projects

Manage associated tasks

📝 Task Management
Tasks tied to projects

Assign to users

Track status: Pending, In-Progress, Completed

Include description, due date, category, priority

📤 Task Submissions
Users upload task deliverables

File upload required

Managers download and evaluate submissions

🧮 Task Evaluation
Evaluated by managers:

Quality Score (0–100)

Timeliness Score (0–100)

Feedback comments

Scores are averaged

🧑‍💼 Admin Dashboard
View total users, tasks, projects

Role statistics

System performance metrics

Manage user roles

Edit system settings

🔒 Security
Auth required for all actions

Role-based restrictions

Input validation & file protection

Resource ownership enforced



---

## 📦 Database Schema Overview

| Table Name           | Description                              |
|----------------------|------------------------------------------|
| `users`              | Stores user credentials and roles        |
| `projects`           | Project metadata                         |
| `tasks`              | Task details and assignments             |
| `task_submissions`   | User-submitted task files                |
| `manager_evaluations`| Evaluations with scores and feedback     |
| `task_scores`        | Performance metrics for each user/task   |
| `settings`           | System-wide configuration and preferences|

---

## ⚙️ Business Rules

- Only **Managers** can create/edit/delete projects.
- **Tasks** must be linked to a **Project**.
- **Users** can only access tasks assigned to them.
- **Managers** can only manage projects they created.
- **Admins** have unrestricted access.
- Task evaluation must include both quality & timeliness scores.
- Submissions **require file attachments**.




