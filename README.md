# Appointment Booking System

A scalable Appointment Booking System built with Laravel 13 using Service-Repository Pattern, Queue Jobs, Events, Listeners, Transactions, and REST APIs.

---

## Features

### Doctor Availability

Doctors can define their availability with:

- Date
- Start Time
- End Time
- Slot Duration

Example:

```text
Date: 2026-06-20
Start Time: 09:00 AM
End Time: 12:00 PM
Slot Duration: 30 Minutes
```

---

### Automatic Slot Generation

Availability automatically generates appointment slots.

Example:

```text
09:00 - 09:30
09:30 - 10:00
10:00 - 10:30
10:30 - 11:00
11:00 - 11:30
11:30 - 12:00
```

Slot generation runs through Laravel Queue Jobs.

---

### Appointment Booking

Patients can:

- View available slots
- Book appointments
- Receive booking reference number

Example Response:

```json
{
    "success": true,
    "message": "Appointment booked successfully",
    "data": {
        "reference_no": "APT-20260620-0001"
    }
}
```

---

### Prevent Double Booking

System prevents duplicate bookings using:

- Database Transactions
- Row Level Locking (`lockForUpdate`)
- Slot Status Management

---

### Appointment Cancellation

Patients can cancel appointments.

When cancelled:

- Appointment status becomes `cancelled`
- Slot becomes available again

---

### Appointment Rescheduling

Patients can reschedule appointments.

Flow:

```text
Old Slot -> Available
New Slot -> Booked
Appointment -> Updated
History -> Created
Notification -> Sent
```

---

### Appointment History

All appointment actions are tracked.

Supported actions:

```text
booked
cancelled
rescheduled
completed
no_show
```

---

## Tech Stack

- Laravel 13
- MySQL 8+
- Laravel Sanctum
- Queue Jobs
- Events & Listeners
- Service Layer
- Repository Pattern

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

Stores patients and administrators.

| Column | Type |
|----------|----------|
| id | bigint |
| name | string |
| email | string |
| password | string |
| role | enum |

---

## doctors

Stores doctor information.

| Column | Type |
|----------|----------|
| id | bigint |
| name | string |
| email | string |

---

## doctor_availabilities

Stores doctor schedules.

| Column | Type |
|----------|----------|
| doctor_id | FK |
| date | date |
| start_time | time |
| end_time | time |
| slot_duration | integer |

---

## appointment_slots

Stores generated slots.

| Column | Type |
|----------|----------|
| doctor_id | FK |
| availability_id | FK |
| start_at | datetime |
| end_at | datetime |
| status | enum |

---

## appointments

Stores booking information.

| Column | Type |
|----------|----------|
| reference_no | string |
| patient_id | FK |
| doctor_id | FK |
| appointment_slot_id | FK |
| status | enum |

---

## appointment_histories

Stores audit logs.

| Column | Type |
|----------|----------|
| appointment_id | FK |
| action | string |
| old_data | json |
| new_data | json |

---

# Architecture

```text
Controller
    ↓
Request Validation
    ↓
Service Layer
    ↓
Repository Layer
    ↓
Database
    ↓
Resource
    ↓
JSON Response
```

---

# Folder Structure

```text
app
├── Events
├── Listeners
├── Jobs
├── Models
├── Repositories
├── Services
├── Http
│   ├── Controllers
│   ├── Requests
│   └── Resources
```

---

# API Endpoints

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

---

## Slots

### Get Available Slots

```http
GET /api/doctors/{doctor_id}/slots
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
    "patient_id": 1,
    "slot_id": 5
}
```

---

### Cancel Appointment

```http
POST /api/appointments/{id}/cancel
```

---

### Reschedule Appointment

```http
POST /api/appointments/{id}/reschedule
```

Request:

```json
{
    "appointment_slot_id": 10
}
```

---

# Booking Flow

```text
Doctor Creates Availability
            ↓
Generate Slots Job
            ↓
Patient Views Slots
            ↓
Patient Books Slot
            ↓
Slot Locked
            ↓
Appointment Created
            ↓
Event Triggered
            ↓
Notification Sent
```

---

# Reschedule Flow

```text
Fetch Appointment
        ↓
Lock New Slot
        ↓
Validate Slot
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
Send Notification
```

---

# Queue Configuration

Run Queue Worker:

```bash
php artisan queue:work
```

---

# Installation

Clone Repository

```bash
git clone https://github.com/MayankSurati/uniAthena-Mayank
```

Run Migration

```bash
php artisan migrate
```

Run Seeder

```bash
php artisan db:seed
```

Start Queue

```bash
php artisan queue:work
```

Start Application

```bash
php artisan serve
```

---

# Scalability Considerations

- Service Repository Pattern
- Queue Based Slot Generation
- Transaction Based Booking
- Row Level Locking
- Database Indexing
- Audit History Tracking
- Event Driven Notifications
- Optimized for Millions of Records

---

# Author

Mayank Surati
Senior Laravel Developer