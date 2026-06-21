# Appointment Booking System

## Overview

A scalable Appointment Booking System built using Laravel 13. The system allows doctors to define their availability, automatically generates appointment slots, enables patients to book appointments, and supports cancellation and rescheduling while preventing double bookings.

The solution follows industry-standard architecture patterns including:

* Service Repository Pattern
* Event-Driven Architecture
* Queue-Based Processing
* Transaction Management
* Row-Level Locking
* RESTful APIs
* Laravel Sanctum Authentication

---

# Features

## Sanctum Authentication

Laravel Sanctum was selected because:

- Native Laravel package
- Lightweight token authentication
- Easy token revocation
- Suitable for mobile and SPA clients
- Minimal configuration overhead

This project uses Personal Access Tokens with Bearer authentication for all protected endpoints.

## Doctor Availability Management

Doctors can create availability schedules by specifying:

* Date
* Start Time
* End Time
* Slot Duration

Example:

```text
Date: 2026-06-20
Start Time: 09:00
End Time: 12:00
Slot Duration: 30 Minutes
```

---

## Automatic Slot Generation

When availability is created, appointment slots are automatically generated using a background queue job.

Example:

```text
09:00 - 09:30
09:30 - 10:00
10:00 - 10:30
10:30 - 11:00
11:00 - 11:30
11:30 - 12:00
```

---

## Appointment Management

Patients can:

* View available slots
* Book appointments
* Cancel appointments
* Reschedule appointments
* View appointment history

---

## Double Booking Prevention

The system prevents concurrent bookings using:

* Database Transactions
* Row-Level Locking (`lockForUpdate`)
* Slot Status Validation

---

## Audit Trail

Every appointment action is tracked.

Supported actions:

```text
booked
cancelled
rescheduled
completed
no_show
```

---

# Technology Stack

* PHP 8.3+
* Laravel 13
* MySQL 8+
* Laravel Sanctum
* Laravel Queues
* Events & Listeners
* Service Repository Pattern

---

# System Architecture

```text
Controller
      ↓
Form Request Validation
      ↓
Service Layer
      ↓
Repository Layer
      ↓
Database
      ↓
API Resource
      ↓
JSON Response
```

---

# Database Design

## Tables

```text
users
doctors
doctor_availabilities
appointment_slots
appointments
appointment_histories
```

---

## users

Stores patient and admin information.

| Column   | Type   |
| -------- | ------ |
| id       | bigint |
| name     | string |
| email    | string |
| password | string |
| role     | enum   |

---

## doctors

Stores doctor information.

| Column         | Type   |
| -------------- | ------ |
| id             | bigint |
| name           | string |
| email          | string |
| specialization | string |

---

## doctor_availabilities

Stores doctor schedules.

| Column        | Type    |
| ------------- | ------- |
| doctor_id     | FK      |
| date          | date    |
| start_time    | time    |
| end_time      | time    |
| slot_duration | integer |

### Constraints

* No duplicate availability
* No overlapping time ranges

---

## appointment_slots

Stores generated appointment slots.

| Column          | Type     |
| --------------- | -------- |
| doctor_id       | FK       |
| availability_id | FK       |
| start_at        | datetime |
| end_at          | datetime |
| status          | enum     |

Status values:

```text
available
booked
blocked
```

---

## appointments

Stores booking information.

| Column              | Type   |
| ------------------- | ------ |
| reference_no        | string |
| patient_id          | FK     |
| doctor_id           | FK     |
| appointment_slot_id | FK     |
| status              | enum   |

Status values:

```text
booked
completed
cancelled
rescheduled
no_show
```

---

## appointment_histories

Stores appointment activity logs.

| Column         | Type   |
| -------------- | ------ |
| appointment_id | FK     |
| action         | string |
| old_data       | json   |
| new_data       | json   |
| created_by     | FK     |

---

# Design Decisions

## 1. Separate Availability and Appointments

Availability and appointments are stored separately.

Benefits:

* Better normalization
* Reduced duplication
* Easier maintenance
* Better scalability

---

## 2. Pre-Generated Slots

Slots are generated once and stored in the database.

Benefits:

* Faster slot retrieval
* Reduced processing during booking
* Simpler availability checks

---

## 3. Service Repository Pattern

### Service Layer

Handles business logic:

* Book Appointment
* Cancel Appointment
* Reschedule Appointment
* Availability Validation

### Repository Layer

Handles:

* Database Queries
* CRUD Operations
* Data Retrieval

Benefits:

* Separation of concerns
* Easier testing
* Cleaner codebase

---

## 4. Transaction-Based Booking

All critical booking operations use database transactions.

Benefits:

* Data consistency
* Atomic operations
* Automatic rollback on failure

---

## 5. Row-Level Locking

Booking and rescheduling use:

```php
lockForUpdate()
```

Benefits:

* Prevents race conditions
* Prevents double bookings
* Ensures data integrity

---

## 6. Event-Driven Architecture

Events:

```text
AppointmentBooked
AppointmentCancelled
AppointmentRescheduled
```

Listeners:

```text
SendNotification
CreateAppointmentHistory
```

Benefits:

* Loose coupling
* Better maintainability
* Easier feature expansion

---

## 7. Queue-Based Processing

Background jobs handle:

* Slot Generation
* Email Notifications
* SMS Notifications

Benefits:

* Faster API responses
* Better user experience
* Improved throughput

---

# Appointment Workflow

## Booking Flow

```text
Doctor Creates Availability
            ↓
Generate Slots Job
            ↓
Patient Views Slots
            ↓
Patient Selects Slot
            ↓
Transaction Starts
            ↓
Lock Slot
            ↓
Create Appointment
            ↓
Update Slot Status
            ↓
Dispatch Event
            ↓
Commit Transaction
```

---

## Cancellation Flow

```text
Fetch Appointment
        ↓
Validate Status
        ↓
Transaction Starts
        ↓
Update Appointment
        ↓
Release Slot
        ↓
Dispatch Event
        ↓
Commit Transaction
```

---

## Reschedule Flow

```text
Fetch Appointment
        ↓
Lock New Slot
        ↓
Validate Availability
        ↓
Release Old Slot
        ↓
Book New Slot
        ↓
Update Appointment
        ↓
Create History
        ↓
Dispatch Event
        ↓
Commit Transaction
```

---

# API Documentation

## Authentication

### Register

```http
POST /api/register
```

### Login

```http
POST /api/login
```

### Logout

```http
POST /api/logout
```

Authorization Header:

```text
Authorization: Bearer {token}
```

---

## Doctors

### Get Doctors

```http
GET /api/doctors
```

---

## Doctor Availability

### Create Availability

```http
POST /api/doctor-availabilities
```

Request:

```json
{
    "doctor_id": 1,
    "date": "2026-06-20",
    "start_time": "09:00",
    "end_time": "12:00",
    "slot_duration": 30
}
```

Business Rules:

* Doctor must exist
* Date must be valid
* End time must be greater than start time
* No overlapping availability

---

## Slots

### Get Available Slots

```http
GET /api/doctors/{doctorId}/slots?date=2026-06-20
```

Response:

```json
{
    "success": true,
    "data": []
}
```

---

## Appointments

### Book Appointment

```http
POST /api/appointments
```

Request:

```json
{
    "doctor_id": 1,
    "slot_id": 10
}
```

Rules:

* Patient must be authenticated
* Slot must exist
* Slot must be available

---

### Cancel Appointment

```http
POST /api/appointments/{id}/cancel
```

Rules:

* Appointment must exist
* Appointment must not already be cancelled

---

### Reschedule Appointment

```http
POST /api/appointments/{id}/reschedule
```

Request:

```json
{
    "appointment_slot_id": 20
}
```

Rules:

* Appointment must exist
* Appointment must not be cancelled
* New slot must be available

---

### Get Appointments

```http
GET /api/appointments
```

Filters:

```text
doctor_id
patient_id
status
date
```

Pagination supported.

---

# Standard API Response Format

## Success Response

```json
{
    "success": true,
    "message": "Request completed successfully",
    "data": {}
}
```

## Error Response

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "field": [
            "Validation message"
        ]
    }
}
```

---

# Data Volume Considerations

## Assumptions

* 200 Doctors
* 10,000 Bookings Per Day

Estimated Volume:

```text
10,000 Bookings / Day
300,000 Bookings / Month
3.65 Million Bookings / Year
```

---

# Performance Considerations

## Database Indexing

Indexes are added on:

```text
doctor_id
patient_id
status
slot_date
created_at
```

---

## Pagination

Large datasets use pagination.

```php
Appointment::paginate(20);
```

---

## Eager Loading

```php
Appointment::with([
    'doctor',
    'patient',
    'slot'
]);
```

Prevents N+1 query issues.

---

## Queue Processing

Heavy operations run asynchronously.

Examples:

* Slot Generation
* Emails
* Notifications

---

## Transactions and Locking

Booking and rescheduling operations use:

```php
DB::transaction()
lockForUpdate()
```

to guarantee consistency.

---

# Scalability Strategy

## Phase 1

Supports:

```text
200 Doctors
10,000 Bookings / Day
```

Infrastructure:

```text
1 Application Server
1 MySQL Database
1 Queue Worker
```

---

## Phase 2

Add Redis Cache.

Cache:

* Doctor List
* Available Slots
* Availability Data

---

## Phase 3

Add Multiple Queue Workers.

Benefits:

* Faster processing
* Improved throughput

---

## Phase 4

Add Read Replicas.

```text
Primary Database
       ↓
Read Replica 1
Read Replica 2
```

Reads:

```text
Doctor Listing
Appointment Listing
Reports
```

Writes:

```text
Booking
Cancellation
Rescheduling
```

---

## Phase 5

Partition appointment tables.

Example:

```text
appointments_2026
appointments_2027
appointments_2028
```

Benefits:

* Faster queries
* Smaller indexes
* Better long-term performance

---

# Installation

## Clone Repository

```bash
git clone https://github.com/your-org/doctor-appointment-system.git
```

## Install Dependencies

```bash
composer install
```

## Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

## Configure Database

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=doctor_appointment
DB_USERNAME=root
DB_PASSWORD=
```

## Run Migrations

```bash
php artisan migrate
```

## Run Seeders

```bash
php artisan db:seed
```

## Start Queue Worker

```bash
php artisan queue:work
```

## Start Application

```bash
php artisan serve
```

---

# Future Improvements

* Redis Caching
* SMS Integration
* Push Notifications
* WebSocket Real-Time Updates
* Multi-Clinic Support
* Multi-Tenant Architecture
* Advanced Reporting Dashboard

---