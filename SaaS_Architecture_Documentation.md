# GalaxyPOS: SaaS Architecture & Implementation Guide

## 1. Introduction

This document outlines the architecture and implementation of the GalaxyPOS system, transforming it into a multi-tenant Software-as-a-Service (SaaS) application. The key objectives of this architecture are data isolation, scalability, and automated tenant provisioning.

## 2. High-Level Architecture

The system is built on a robust, subdomain-based multi-tenancy model.

- **Central Database (`main`):** A single database stores global data, including user accounts for authentication, subscription plans, roles, and metadata for each tenant.
- **Tenant Databases (`tenant_x`):** Each tenant has their own dedicated database for all their business-specific data (e.g., products, sales, customers). This ensures complete data isolation.
- **Tenant Identification:** Tenants are identified via their unique subdomain (e.g., `client-a.galaxypos.com`). The application detects the subdomain from the incoming request and switches to the correct tenant database automatically.
- **Automated Provisioning:** A new tenant's infrastructure (subdomain, database) is created automatically by an **n8n** workflow, which is triggered by the Laravel application upon new user registration.

---

## 3. Implementation Details

### Part 1: Self-Service UI Enhancements

To create a professional and consistent user experience for account management, the following improvements were made to the self-service pages (`inactive`, `suspended`, `pending`, etc.).

1.  **Dedicated Layout:** A new layout file, `resources/views/layouts/account_status.blade.php`, was created to provide a consistent background and centered layout for all status pages.
2.  **Standardized Component:** A reusable Blade component, `resources/views/components/status-card.blade.php`, was created to ensure uniform styling for the status cards, including the icon, title, message, and action buttons.
3.  **Centralized Styling:** All inline styles were removed and consolidated into a new dedicated stylesheet, `public/css/account-status.css`, for easier maintenance and a consistent design language.

### Part 2: Multi-Tenant Database Architecture

This is the core of the SaaS implementation, ensuring each tenant's data is separate and secure.

1.  **Database Configuration (`config/database.php`):
    - The default `mysql` connection was renamed to `main` to clearly define its purpose.
    - A new connection template named `tenant` was added. This template is dynamically configured at runtime with the specific tenant's database credentials.

2.  **Tenant Metadata (`tenants` table):
    - A `tenants` table was created in the `main` database to store information about each tenant.
    - **Migration:** `database/migrations/*_create_tenants_table.php`
    - **Schema:**
        - `id`
        - `subdomain` (unique identifier for the tenant)
        - `db_database` (the name of the tenant's database)
        - `db_host`, `db_port`, `db_username`, `db_password`
    - A corresponding `App\Models\Tenant` model was created and configured to always use the `main` database connection.

3.  **Automatic Database Switching (Middleware):
    - The `App\Http\Middleware\IdentifyTenantBySubdomain` middleware was created to handle the core tenancy logic.
    - On every web request, it performs the following steps:
        1.  Extracts the subdomain from the request's host (e.g., `client-a` from `client-a.galaxypos.com`).
        2.  Queries the `tenants` table on the `main` connection to find a matching tenant.
        3.  If a tenant is found, it dynamically configures the `tenant` database connection with the credentials stored for that tenant.
        4.  It then sets the application's default database connection to this `tenant` connection for the remainder of the request.
    - This middleware is registered globally in the `web` group in `app/Http/Kernel.php` to ensure it runs on every request.

4.  **Model Configuration (Data Isolation):
    - To ensure that models handling global data always use the central database, the following property was added to them:
      ```php
      protected $connection = \'main\';
      ```
    - This was applied to: `User`, `Role`, `Plan`, `Subscription`, and `Tenant`.
    - All other models (e.g., `Product`, `Sale`) will use the default connection, which is dynamically switched by the middleware, thus ensuring they only interact with the correct tenant's database.

### Part 3: Automated Tenant Provisioning Trigger

To automate the creation of new tenants, the application now triggers your n8n workflow after a user registers.

1.  **Event-Driven Trigger:** The system leverages Laravel's built-in `Illuminate\Auth\Events\Registered` event, which fires automatically when a new user is successfully created.

2.  **Queued Listener (`ProvisionNewTenant`):
    - A listener, `App\Listeners\ProvisionNewTenant`, was created to handle this event.
    - To ensure the user registration process is fast and reliable, this listener is **queued**. It pushes a job to a queue instead of running immediately.
    - The job makes an HTTP POST request to a webhook URL (provided by you in the `.env` file).
    - The request sends the new user's data, which your n8n workflow can use:
      ```json
      {
        "user": {
          "id": 123,
          "email": "new.user@example.com",
          "name": "New User"
        }
      }
      ```

3.  **Queue Infrastructure:
    - The queue driver was configured to use the `database`.
    - A `jobs` table was created in the `main` database to store pending jobs.

---

## 4. Final Setup & Operation

To run the application, follow these steps:

1.  **Configure Environment (`.env` file):
    Ensure the following variables are set in your `.env` file:
    ```env
    # Set the main database credentials
    DB_CONNECTION=main
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_main_database
    DB_USERNAME=your_main_db_user
    DB_PASSWORD=your_main_db_password

    # Set the queue driver to database
    QUEUE_CONNECTION=database

    # Add the n8n webhook URL for provisioning new tenants
    N8N_PROVISIONING_WEBHOOK_URL=https://your-n8n-webhook-url.com/path
    ```

2.  **Run the Queue Worker:
    The queued jobs (like provisioning a new tenant) require a worker process to execute them. Start the worker by running the following command in your terminal and keep it running:
    ```shell
    php artisan queue:work
    ```
    In a production environment, you must use a process manager like `supervisor` to ensure the queue worker is always running.

3.  **Expected n8n Workflow:
    Your n8n workflow should be configured to:
    1.  Receive the webhook from the Laravel application.
    2.  Use the aapanel API to create a new database and subdomain.
    3.  Connect to the `main` Laravel database.
    4.  Add a new entry to the `tenants` table with the subdomain and the new database credentials.
    5.  Update the `users` table to set the `tenant_id` for the user who triggered the workflow.
