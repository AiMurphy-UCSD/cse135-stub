# CSE 135 Analytics Backend System

## Overview

This project implements a simple analytics reporting platform built using **PHP, MySQL, and JavaScript collectors**.
The system collects analytics data from a test website and presents the results through an authenticated reporting dashboard.

The application supports **role-based access control**, multiple report types, data visualization, and PDF export of reports.

The goal of this system is to simulate a lightweight analytics pipeline including:

* data collection
* storage
* reporting
* visualization
* role-based access management

---

# System Architecture

The system consists of three major components:

### 1. Test Website

```
test.aidanmurphy.site
```

This site loads the analytics collector script and generates user activity events.

Events collected include:

* page views
* user interactions (clicks, scrolls, keyboard input)
* idle time
* page enter / leave
* performance metrics
* JavaScript errors

---

### 2. Collector Service

```
collector.aidanmurphy.site
```

The collector receives event data via HTTP POST requests and stores it in the database.

Endpoint:

```
POST /log/
```

The collector performs:

* CORS validation
* JSON parsing
* event normalization
* database insertion

All events are stored in the `events` table.

---

### 3. Reporting Dashboard

```
reporting.aidanmurphy.site
```

This site provides authenticated access to analytics reports and administration tools.

Main features:

* authentication system
* role-based authorization
* report dashboards
* tables and charts
* PDF export functionality
* user management

---

# Authentication and Authorization

The system supports three user roles.

### Super Admin

Full system access.

Capabilities:

* manage users
* view all reports
* access all sections
* export reports
* access raw tables and charts

---

### Analyst

Limited administrative role.

Capabilities:

* view reports
* view analytics sections assigned to them
* access charts and tables

Access can be restricted to specific report categories such as:

* performance
* behavior
* errors

---

### Viewer

Read-only role.

Capabilities:

* view saved reports only

Viewers cannot access dashboards, tables, charts, or analytics sections.

---

# Report Types

The system includes three report categories.

### Performance Report

Displays page performance metrics including page load timing data collected from the browser performance API.

Includes:

* chart visualization
* data table
* analyst commentary

---

### Behavior Report

Displays user interaction events including clicks, scroll events, idle detection, and navigation behavior.

Includes:

* activity chart
* event table
* analyst commentary

---

### Error Report

Displays captured JavaScript errors and unhandled promise rejections.

Includes:

* error frequency visualization
* error event table
* analyst commentary

---

# Data Storage

Analytics data is stored in the MySQL database:

```
cse135_analytics
```

Primary tables:

### users

Stores authentication and role information.

Fields include:

* username
* password hash
* role
* permitted sections

---

### events

Stores analytics events collected by the collector.

Fields include:

* session_id
* event_type
* page_url
* payload
* created_at

The `payload` column stores raw JSON event data.

---

# Report Export

Reports can be exported as PDF using the **Dompdf** library.

Export endpoint:

```
/export-report.php?type=<report_type>
```

Supported report types:

* performance
* behavior
* error

Generated reports are saved to:

```
/exports/
```

---

# Technologies Used

* PHP
* MySQL
* Bootstrap
* JavaScript
* Chart.js
* Dompdf

---

# Security Features

The application includes several security protections:

* password hashing
* session management
* role-based authorization
* protected routes
* CORS restrictions on collector endpoints

---

# Notes

This system was designed as a simplified analytics platform for instructional purposes and is not intended for production deployment.

It demonstrates the full lifecycle of analytics collection and reporting in a small web application.

---

# AI

Artificial intelligence tools were used during development to help clarify concepts and fill gaps in understanding where course materials were less explicit. In particular, AI was used as a reference tool for:

debugging implementation issues

understanding unfamiliar PHP or JavaScript patterns

resolving configuration problems with Apache, MySQL, and CORS

generating example structures for documentation and formatting

These tools were used in a similar manner to technical documentation or online forums — to assist with problem solving and accelerate development where necessary.

Due to the timing of the assignment coming closer to finals exam week, AI assistance was also used to help speed up portions of development and documentation while maintaining understanding of the implemented functionality.

