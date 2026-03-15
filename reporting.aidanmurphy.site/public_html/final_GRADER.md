# Grader Instructions

## Reporting Dashboard

Main dashboard:

```
https://reporting.aidanmurphy.site
```

---

# Test Accounts

### Super Admin

Username:

```
superadmin
```

Password:

```
UCSD1234
```

Capabilities:

* manage users
* view all reports
* access analytics sections
* export reports

---

### Analyst

Username:

```
sam
```

Password:

```
analyst123
```

Capabilities:

* view reports
* view analytics sections assigned to the analyst
* access charts and tables

---

### Viewer

Username:

```
viewer1
```

Password:

```
viewer123
```

Capabilities:

* view reports only

---

# Reports

Available reports:

```
/performance-report.php
/behavior-report.php
/error-report.php
```

Each report contains:

* chart visualization
* data table
* analyst commentary
* export to PDF button

---

# Report Export

Example export:

```
https://reporting.aidanmurphy.site/export-report.php?type=performance
```

This generates a downloadable PDF.

---

# User Management

Accessible to the **super admin**.

```
/admin/users.php
```

Allows:

* creating users
* assigning roles
* assigning report sections

---

# Analytics Collector

The analytics collector endpoint:

```
https://collector.aidanmurphy.site/log/
```

The collector receives event data from the test website:

```
https://test.aidanmurphy.site
```

Collected events include:

* activity
* performance
* error

---

# Notes for Grading

The system demonstrates:

* full authentication and authorization
* multiple user roles
* report visualization
* data tables and charts
* PDF export functionality
* analytics data collection pipeline


## Suggested Testing Scenario

To quickly explore the system, the following short scenario demonstrates the different permission levels and features.

### 1. Login as Super Admin

Login using:

Username: `superadmin`
Password: `UCSD1234`

From this account you can:

* access the dashboard
* view all report sections
* export reports as PDF
* manage users in `/admin/users.php`

Try exporting a report from one of the report pages using the **Export PDF** button.

---

### 2. Login as Analyst

Logout and login as:

Username: `sam`
Password: `analyst123`

This account demonstrates **restricted access**. Analysts can view reports and analytics sections assigned to them but cannot manage users.

You should still be able to view charts, tables, and report data.

---

### 3. Login as Viewer

Logout and login as:

Username: `viewer1`
Password: `viewer123`

This account demonstrates **read-only access**. Viewers can only access the saved report pages and cannot access the dashboard, charts, or user management pages.

---

After exploring the roles above, feel free to navigate the system freely to inspect the analytics dashboards, reports, and export functionality.
