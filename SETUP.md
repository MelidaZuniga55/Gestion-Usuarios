# Setup Instructions

Follow these steps to install and run the project.

## Prerequisites

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL (optional, can use SQLite)

## Installation Steps

1.  **Install PHP Dependencies**
    ```bash
    composer install
    ```

2.  **Install Node Dependencies**
    ```bash
    npm install
    ```

3.  **Environment Setup**
    Copy the example environment file and configure it.
    ```bash
    copy .env.example .env
    ```

4.  **Generate Application Key**
    ```bash
    php artisan key:generate
    ```

5.  **Database Setup**
    - **Option A (MySQL):** Create a database named `gestion_usuarios_api` (or whatever you set in `.env`).
    - **Option B (SQLite):**
        - Create an empty file `database/database.sqlite`.
        - Edit `.env` and change `DB_CONNECTION=mysql` to `DB_CONNECTION=sqlite`.
        - Remove `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` lines or comment them out.

6.  **Run Migrations**
    ```bash
    php artisan migrate
    ```

## Running the Application

1.  **Start the Backend Server**
    ```bash
    php artisan serve
    ```
    The API will be available at `http://127.0.0.1:8000`.

2.  **Start the Frontend Development Server (if needed)**
    ```bash
    npm run dev
    ```

## Testing the API

You can test the API endpoints using tools like Postman or cURL.
Common endpoints (check `routes/api.php` for details):
- `GET /api/users`
- `POST /api/users`
