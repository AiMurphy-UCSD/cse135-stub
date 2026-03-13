# CSE135 -- Analytics Backend Checkpoint

## Live Backend

Reporting Dashboard
https://reporting.aidanmurphy.site

------------------------------------------------------------------------

## Grader Login

Username: `grader`
Password: `UCSD1234`

After logging in you will be redirected to the dashboard.

------------------------------------------------------------------------

## 1. Authentication System

The backend implements a session-based authentication system using PHP.

Features:

-   Login page (`/login.php`)
-   Logout functionality (`/logout.php`)
-   Session-based authentication
-   Navigation between backend pages
-   Protection against forceful browsing

Protected routes include:

-   `/table.php`
-   `/charts.php`

If these routes are accessed without authentication, the user is
redirected to the login page.

------------------------------------------------------------------------

## 2. Data Table / Grid

A database-backed table view is implemented at:

`/table.php`

This page queries the analytics database and displays collected events
from the `events` table, including:

-   `id`
-   `session_id`
-   `event_type`
-   `page_url`
-   `created_at`

The table is generated dynamically from the MySQL database.

------------------------------------------------------------------------

## 3. Data Visualization Charts

Charts are implemented using **Chart.js** and can be viewed at:

`/charts.php`

The chart visualizes analytics data stored in the database.
Currently the chart displays event counts grouped by `event_type`.

The chart updates dynamically as new analytics events are inserted.

------------------------------------------------------------------------

## Application Structure

The backend follows a lightweight MVC-style organization:

    public_html/
    │
    ├── index.php
    ├── login.php
    ├── logout.php
    │
    ├── table.php
    ├── charts.php
    │
    ├── app/
    │   ├── auth.php
    │   ├── config.php
    │   └── db.php
    │
    ├── views/
    │   ├── dashboard.php
    │   ├── table.php
    │   ├── charts.php
    │   └── partials/
    │
    └── api/

-   **Controllers:** entry pages such as `index.php`, `table.php`,
    `charts.php`
-   **Views:** rendering templates located in `/views`
-   **Services:** database and authentication logic in `/app`

------------------------------------------------------------------------

## Data Source

The reporting backend reads analytics events from the MySQL database
used in previous assignments:

Database: `cse135_analytics`
Table: `events`

Events are inserted by the analytics collector service.

