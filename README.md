# React PHP Todo App

A simple, full-stack TODO application built with React and PHP.

## Features

- Create, Read, Update, and Delete tasks
- Mark tasks as complete/incomplete
- Persistent storage using MySQL database
- RESTful API built with PHP
- Responsive front-end design using React

## Technologies Used

- Frontend:
  - React
  - Vite for build tool and development server
- Backend:
  - PHP
  - MySQL
- Development Tools:
  - Git for version control
  - npm for package management

## Project Structure

```
react-php-todo/
├── client/             # React frontend
│   ├── src/
│   │   ├── components/
│   │   └── App.jsx
│   ├── index.html
│   └── package.json
└── server/             # PHP backend
    ├── api.php
    └── db_connection.php
```

## Setup and Installation

### Prerequisites

- Node.js and npm
- PHP 7.4 or higher
- MySQL
- Web server (e.g., Apache, Nginx)

### Steps

1. Clone the repository:

   ```
   git clone https://github.com/bufordeeds/react-php-todo.git
   cd react-php-todo
   ```

2. Set up the database:

   - Create a new MySQL database named `task_manager`
   - Use the following SQL to create the `tasks` table:
     ```sql
     CREATE TABLE tasks (
       id INT AUTO_INCREMENT PRIMARY KEY,
       title VARCHAR(255) NOT NULL,
       completed TINYINT(1) NOT NULL DEFAULT 0
     );
     ```

3. Configure the backend:

   - Navigate to the `server` directory
   - Open `db_connection.php` and update the database connection details:
     ```php
     $host = 'localhost';
     $db = 'task_manager';
     $user = 'your_database_username';
     $pass = 'your_database_password';
     ```

4. Install frontend dependencies and start the development server:

   ```
   cd client
   npm install
   npm run dev
   ```

5. Configure your web server:

   - Set up your web server (Apache or Nginx) to serve the `server` directory
   - Ensure PHP is properly configured with your web server

6. Access the application:
   - The React app will be running at the URL provided by Vite (usually `http://localhost:5173`)
   - The API should be accessible at `http://localhost/path-to-your-server-directory/api.php`

### Note

Make sure your web server and MySQL server are running before starting the application.

## API Endpoints

- GET `/api.php`: Fetch all tasks
- POST `/api.php`: Create a new task
- PUT `/api.php`: Update an existing task
- DELETE `/api.php?id={taskId}`: Delete a task

## Future Improvements

- Add user authentication
- Implement task categories or tags
- Add due dates for tasks
- Enhance error handling and validation

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open source and available under the MIT License.
