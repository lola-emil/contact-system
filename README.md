Absolutely! Below is the **Markdown** version of the text you've provided:

````markdown
# Contact System - Proweaver Practice Project

## Repo Link
[GitHub Repository](https://github.com/lola-emil/contact-system)

---

## Project Overview

### Description:
A contact management system designed to allow users to store, manage, and share their contacts with other users. The system allows individuals to save contacts with essential information (name, phone number, email, company name) and easily share them with other registered users.

### Technologies Used:

#### Backend:
- **PHP:** v8.2
- **Laravel Framework:** v12.0
- **MySQL:** v8.0.44
- **Laravel Reverb** (for real-time notifications)

#### Frontend:
- **TypeScript:** v5.2.2
- **VueJS:** v3.5.13
- **Axios:** v1.12.2

---

### Features:
- **User Authentication:** Users can sign up, log in, and manage their accounts.
- **Contact Management:** Users can add, update, and delete their own contacts. Each contact stores a variety of information, such as contact name, phone number, email, and company.
- **Contact Sharing:** Users can share specific contacts with other users within the system. They can share multiple or single contacts with multiple or single users.
- **Permission-Based Contact Access:** Users can set shared contact permissions for other users. For example:
  - **Editor**: The receiver can edit/update the contact but cannot delete it.
  - **Viewer**: The contact is read-only and cannot be modified by the receiver.
- **Search and Filter:** Users can search their contacts by name, email, or phone number.
- **Real-Time Notification on Share:** Users will be notified instantly if someone shares a contact with them.

---

## Getting Started

This guide will walk you through setting up the application on your local development environment.

### Prerequisites

The following software should be installed on your machine:
- **PHP:** v8.0 or higher
- **Composer**
- **MySQL**
- **NodeJS** (comes with NPM if installed)
- **Git** for version control

---

### Installation Steps

1. **Clone the Repo:**
   ```bash
   git clone https://github.com/lola-emil/contact-system
````

2. **Navigate to the Project Directory:**

   ```bash
   cd contact-system
   ```

3. **Install PHP Dependencies:**

   ```bash
   composer install
   ```

4. **Migrate the Database:**

   ```bash
   php artisan migrate
   ```

5. **Install Frontend Packages:**

   ```bash
   npm install
   ```

6. **Start the Frontend Dev Server:**

   ```bash
   npm run dev
   ```

7. **Start the Backend Dev Server:**

   ```bash
   php artisan serve
   ```

---

After completing these steps, the **Contact System** should be up and running on your local development environment. You can now open the application in your browser at `http://localhost:8000` and begin using the contact management features.

``
