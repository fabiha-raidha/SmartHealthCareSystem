# Smart Healthcare Management System (SHMS)
 
A PHP + MySQL web application for managing the day-to-day operations of a healthcare facility — patients, doctors, appointments, prescriptions, billing, insurance, investigations, symptoms/diagnoses, and care recommendations — all from a single admin dashboard.
 
## Overview
 
SHMS is a classic admin-panel CRUD system. An admin registers/logs in, then manages every entity (patients, doctors, appointments, etc.) through paired **view / add / update / delete** pages, with a shared sidebar for navigation and a dashboard that summarizes key counts (patients, doctors, appointments, prescriptions).
 
## Features
 
- **Admin authentication** — registration and login with hashed passwords (`password_hash` / `password_verify`), session-based access control
- **Patient management** — add, view, search, update, delete patient records
- **Doctor management** — manage doctor profiles and specializations
- **Appointment scheduling** — link patients to doctors with date, time, and status
- **Prescriptions** — record medicines and notes per patient/doctor
- **Billing** — track bill amount, status, and payment date per patient
- **Insurance** — manage insurance policies and claims per patient
- **Investigations** — record diagnostic reports and results per patient
- **Symptoms & disease tracking** — log symptoms, diagnosed disease, and doctor's notes
- **Recommendations** — preventive care and follow-up advice per patient
- **Dashboard** — quick overview of total patients, doctors, appointments, and prescriptions
## Tech Stack
 
- **PHP** (procedural, `mysqli` with prepared statements)
- **MySQL / MariaDB**
- **Bootstrap 5** (via CDN) for the UI
- No framework or package manager — plain PHP files served directly
## Project Structure
 
Each module generally follows the same four-file pattern:
 
```
{module}_view.php     # List / search records
{module}_form.php      # Add a new record   (some modules use {module}_add.php)
{module}_update.php    # Edit an existing record
{module}_delete.php    # Delete a record
```
 
| Module | Files |
|---|---|
| Admin | `admin_login.php`, `admin_register.php`, `logout.php` |
| Patients | `form.php`, `view.php`, `update.php`, `delete.php` |
| Doctors | `doctor_form.php`, `doctor_view.php`, `doctor_update.php`, `doctor_delete.php` |
| Appointments | `appointment_form.php`, `appointment_view.php`, `appointment_update.php`, `appointment_delete.php` |
| Prescriptions | `prescription_form.php`, `prescription_view.php`, `prescription_update.php`, `prescription_delete.php` |
| Billing | `billing_form.php` / `billing_add.php`, `billing_view.php`, `billing_update.php`, `billing_delete.php` |
| Insurance | `insurance_form.php`, `insurance_view.php`, `insurance_update.php`, `insurance_delete.php` |
| Investigations | `investigation_form.php`, `investigation_view.php`, `investigation_update.php`, `investigation_delete.php` |
| Symptom & Disease | `symptom_form.php`, `symptom_view.php`, `symptom_update.php`, `symptom_delete.php` |
| Recommendations | `recommendation_form.php`, `recommendation_view.php`, `recommendation_update.php`, `recommendation_delete.php` |
 
Other key files:
- `db.php` — MySQL connection config
- `dashboard.php` — post-login landing page with summary stats
- `sidebar.php` — shared navigation, included on every dashboard page
- `index.php` — public landing/welcome page
## Database
 
The app expects a MySQL database named **`crud`** with the following tables (inferred from the queries in the code — there's no `.sql` file in this repo, so you'll need to create these yourself):
 
```sql
CREATE DATABASE IF NOT EXISTS crud;
USE crud;
 
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);
 
CREATE TABLE users (              -- patients
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    gender VARCHAR(10) NOT NULL
);
 
CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);
 
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status VARCHAR(20) NOT NULL,
    FOREIGN KEY (patient_id) REFERENCES users(id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id)
);
 
CREATE TABLE prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    medicine VARCHAR(255) NOT NULL,
    notes TEXT,
    prescription_date DATE NOT NULL,
    FOREIGN KEY (patient_id) REFERENCES users(id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id)
);
 
CREATE TABLE billing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    bill_date DATE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    bill_status VARCHAR(20) NOT NULL,
    payment_date DATE,
    remarks TEXT,
    FOREIGN KEY (patient_id) REFERENCES users(id)
);
 
CREATE TABLE insurance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    company_name VARCHAR(100) NOT NULL,
    policy_number VARCHAR(100) NOT NULL,
    claim_amount DECIMAL(10,2),
    claim_status VARCHAR(20),
    remarks TEXT,
    FOREIGN KEY (patient_id) REFERENCES users(id)
);
 
CREATE TABLE investigations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    subject VARCHAR(100) NOT NULL,
    report TEXT,
    result TEXT,
    report_date DATE NOT NULL,
    FOREIGN KEY (patient_id) REFERENCES users(id)
);
 
CREATE TABLE symptom_disease (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    symptom VARCHAR(255) NOT NULL,
    disease VARCHAR(255),
    doctor_note TEXT,
    record_date DATE NOT NULL,
    FOREIGN KEY (patient_id) REFERENCES users(id)
);
 
CREATE TABLE recommendations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    recommendation TEXT,
    preventive_care TEXT,
    follow_up_advice TEXT,
    record_date DATE NOT NULL,
    FOREIGN KEY (patient_id) REFERENCES users(id)
);
```
 
## Getting Started
 
### Prerequisites
- PHP 7.4+ with the `mysqli` extension
- MySQL or MariaDB
- A local server stack such as XAMPP, WAMP, MAMP, or `php -S`
### Setup
 
1. Clone the repo into your server's document root (e.g. `htdocs/` for XAMPP):
```bash
   git clone https://github.com/fabiha-raidha/SmartHealthCareSystem.git
```
2. Create the `crud` database and tables using the schema above (via phpMyAdmin or the MySQL CLI).
3. Check `db.php` and update the credentials if your MySQL setup differs from the defaults (`localhost`, user `root`, no password):
```php
   $servername = "localhost";
   $username   = "root";
   $password   = "";
   $dbname     = "crud";
```
4. Start your server (e.g. `php -S localhost:8000` from the project folder, or via XAMPP/WAMP) and open `index.php` in your browser.
5. Register an admin account from the welcome page, then log in to reach the dashboard.
## Security Notes
 
This is a course/learning project. Before using it beyond a local demo, consider:
- Moving DB credentials out of source control (e.g. environment variables)
- Adding CSRF protection to forms
- Escaping/validating all user input consistently across modules
- Adding role-based access control beyond the single "admin" role
 
