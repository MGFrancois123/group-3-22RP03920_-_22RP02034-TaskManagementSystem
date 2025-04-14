# Laravel Task Management System

A role-based task management system built with Laravel, designed to streamline project organization, task assignment, and performance evaluation. This system supports three primary user roles — **Admin**, **Manager**, and **User** — each with specific access levels and responsibilities.

---

## 🚀 Features

### ✅ **User Roles & Authentication**
- **Roles**: Admin, Manager, User
- Laravel built-in authentication
- Role-based access control
- Users must log in to access features

### 📁 **Project Management**
- Managers can create, edit, and delete projects
- Each project includes:
  - Name & description
  - Start & end dates
  - Status (pending, in_progress, completed)
  - Associated users
- Project tasks managed within their scope

### 📝 **Task Management**
- Tasks belong to projects
- Attributes include:
  - Name, description, due date
  - Status (Pending, In-Progress, Completed)
  - Assigned user
  - Category & priority
- Managers assign tasks and track progress

### 🔍 **Task Workflow**
1. Manager creates and assigns tasks
2. User works on the assigned task
3. User submits deliverables (file required)
4. Manager reviews and evaluates
5. Task marked as completed upon approval

### 📊 **Task Evaluation**
- Manager evaluates tasks with:
  - **Quality Score** (0–100)
  - **Timeliness Score** (0–100)
  - Feedback/comments
- System calculates average scores
- Submissions include file uploads

### 🔧 **Admin Features**
- Full access to all system data
- Dashboard displays:
  - User, task, project stats
  - Task priorities & categories
  - Performance metrics
- Manage users, roles, and system settings

### 📂 **File Management**
- Users upload files as part of task submission
- Managers can download and review submissions
- Secure and organized file storage

### 🔐 **Security Features**
- Role-based access control
- Input validation & sanitization
- Resource ownership verification
- Secure file upload & download handling

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

---

## 🛠 Installation

```bash
git clone https://github.com/yourusername/laravel-task-manager.git
cd laravel-task-manager
composer install
cp .env.example .env
php artisan key:generate
