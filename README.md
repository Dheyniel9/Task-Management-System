Task Management System (Simple Explanation)
===========================================

This is a simple app that helps you keep track of your tasks. You can add new tasks, update them, delete them, and see your list of tasks. It has two main parts:

   Backend: This is like the brain of the app. It uses a tool called Laravel (written in PHP) to handle all the data and rules.
   Frontend: This is what you see and click on. It uses Vue.js to make the app look nice and easy to use.
   Database: All your tasks are saved in a MySQL database, so you don’t lose them.

Features
--------

   Sign up and log in (with secure authentication)
   Add, edit, and delete tasks
   Mark tasks as completed or pending
   Set task priority (low, medium, high) with color coding
   Drag and drop to reorder tasks (order saved in backend)
   Filter and search tasks (by status, priority, or keyword)
   See task statistics (how many done, pending, etc.)
   Admin dashboard: Admins can see all users and their tasks, with stats and pagination
   Scheduled cleanup: Old tasks (over 30 days) are deleted automatically every night
   Responsive design: Works on mobile, tablet, and desktop
   Real-time updates: Tasks update instantly for everyone (WebSocket support)
   Secure: All user input is checked and cleaned to protect your data (follows OWASP security best practices)
   Unit tests: The backend is tested to make sure adding, editing, and reordering tasks works

What Do You Need To Run This?
-----------------------------

   PHP (version 8.1 or higher)
   Composer (helps install PHP stuff)
   Node.js and npm (helps run the frontend)
   MySQL (where your tasks are stored)

How To Set It Up (Step by Step)
-------------------------------

 1\. Set Up The Backend (The Brain)

1.  Open your terminal and go to the backend folder:

        cd backend


2.  Copy the example settings file:

        cp .env.example .env


3.  Open the new .env file and fill in your MySQL details:

        DBCONNECTION=mysql
        DBHOST=127.0.0.1
        DBPORT=3306
        DBDATABASE=yourdatabase
        DBUSERNAME=yourusername
        DBPASSWORD=yourpassword


4.  Install the backend tools:

        composer install


5.  Make a special app key:

        php artisan key:generate


6.  Set up the database tables:

        php artisan migrate --seed


7.  Start the backend server:

        php artisan serve



 2\. Set Up The Frontend (What You See)

1.  Open your terminal and go to the frontend folder:

        cd frontend


2.  Install the frontend tools:

        npm install


3.  Start the frontend server:

        npm run dev



 3\. How To Test If It Works

To test the backend:

    cd backend
    php artisan test


(If you have frontend tests, add them here)

What’s In Each Folder?
----------------------

   backend/ — The brain (Laravel API)
   frontend/ — What you see (Vue.js app)

Security Notes
--------------

   All user input is checked and cleaned to prevent hacking (XSS, CSRF, etc.).
   Passwords are always encrypted.
   Only admins can access admin features.
   The app follows modern security standards (OWASP).



API Documentation (Explained Simply)
------------------------------------

This section explains how your app talks to the backend (the server) using API endpoints. Each endpoint is like a door you knock on to ask for something or to send information.

 Authentication (Logging In & Out)

Register (Create a new user account)

   POST /api/auth/register
   Lets a new user sign up.
   Send:

        {
          "name": "Your Name",
          "email": "your@email.com",
          "password": "yourpassword",
          "passwordconfirmation": "yourpassword"
        }


   Returns info about the new user, a login token, and a message.

Login (Sign in)

   POST /api/auth/login
   Lets a user log in.
   Send:

        {
          "email": "your@email.com",
          "password": "yourpassword"
        }


   Returns info about the user, a login token, and a message.

Logout (Sign out)

   POST /api/auth/logout
   Logs you out (ends your session).
   Add your login token in the request header.

Get Current User (Who am I?)

   GET /api/auth/user
   Shows info about the user who is logged in.
   Add your login token in the request header.



 Tasks (Your To-Do List)

List Tasks (Show my tasks)

   GET /api/tasks
   Shows all your tasks.
   Add your login token in the request header.
   Optional filters:
       status: only show pending or completed tasks
       priority: only show low, medium, or high priority
       search: search by keyword

Create Task (Add a new task)

   POST /api/tasks
   Adds a new task to your list.
   Send:

        {
          "title": "What to do",
          "description": "Details (optional)",
          "priority": "low|medium|high (optional)"
        }



Get Task (See one task)

   GET /api/tasks/{id}
   Shows details for one specific task.

Update Task (Edit a task)

   PUT /api/tasks/{id}
   Changes details of a task.
   Send:

        {
          "title": "New title (optional)",
          "description": "New details (optional)",
          "status": "pending|completed (optional)",
          "priority": "low|medium|high (optional)"
        }



Delete Task (Remove a task)

   DELETE /api/tasks/{id}
   Deletes a task from your list.

Reorder Tasks (Change the order)

   POST /api/tasks/reorder
   Lets you change the order of your tasks.
   Send:

        {
          "order": [3, 1, 2]
        }


    (List of task IDs in the new order.)

Task Statistics (See stats)

   GET /api/tasks/statistics
   Shows stats like how many tasks you have, how many are done, etc.



 Admin Endpoints (For Admin Users Only)

List Users with Task Counts

   GET /api/admin/users
   Shows all users and how many tasks each has.

List All Tasks (All Users)

   GET /api/admin/tasks
   Shows all tasks from all users.
    (Admins can filter by user, status, priority, or search.)



 Notes

   For most requests, you need to be logged in. This means you need to send your token in the header like this:
    Authorization: Bearer YOURTOKEN
   Only register and login do not need a token.
   If you want to see more details or examples, check the backend code or the Postman collection.



In short:

   Use these endpoints to sign up, log in, add/edit/delete tasks, and see your stats.
   Admins can see info for all users.
   Always include your token after logging in, except for register/login.



This project is free to use under the MIT License.
