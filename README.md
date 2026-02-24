# NotesTips – A Private Notes Management Web App

NotesTips is a web application developed to manage your own notes in a private and secure way.
It includes a login system with simple user registration system, a complete CRUD system for managing notes,
and different filters for organizing them.

## Live Demo:

https://notestips.jmsweb.site

## Proyect Goal

The objective of this project is to improve my skills in the Laravel framework and gain experience working with more complex systems.
To achieve this, I built a functional MVP that includes multiple features, aiming to simulate a real-world notes management application.

## Features (MVP)

- User authentication (register, login, logout)
- Notes management (CRUD)
- Search, filters, and sorting
- User-based data isolation (ownership)

## Out of Scope (Post-MVP)

- Tags
- Note sharing
- Reminders
- User roles

## Technologies Used

- Backend: PHP (Laravel)
- Frontend: Blade, Tailwind CSS
- Database: MySQL with Eloquent ORM (Dockerized for development)
- Authentication: Laravel Breeze

## Running the Project

- Clone the repository and move to the project folder:

```bash
git clone https://github.com/joel12-Sant/notestips.git
cd notestips
```

- Install dependencies (Node.js and PHP):

```bash
composer install
npm install
```

- Configure environment variables:

```bash
cp .env.example .env
```

- (Optional) Start the database using Docker (development only):

```bash
docker compose up -d
```

If you prefer to use your own database, update the database connection variables in the .env file.

- Generate the application key

```bash
php artisan key:generate
```

- Run the migrations:

```bash
php artisan migrate
```

- Run the Vite development server (Tailwind/Assets):

```bash
npm run dev
```

- Start the Laravel development server

```bash
php artisan serve
```

- Open the app at: http://127.0.0.1:8000
