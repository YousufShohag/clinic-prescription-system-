# 🏥 Smart Prescription Management System

![Banner](banner.png)  
*A modern Laravel 12-based system for managing patients, doctors, and prescriptions with ease.*

---

## 🚀 Features

### 👩‍⚕️ Patients
- Profile with **sex, age, date of birth, image & documents (uploads)**
- **Auto-computed** `next_return_date` from prescriptions
- **Previous prescriptions panel** available when creating a new prescription

### 🧑‍⚕️ Doctors
- Name, specialization, degree, BMDC reg. no., contact

### 📋 Prescriptions
- Clinical findings:
  - **O/E, BP, SpO₂, RR, temp, weight, height, BMI**
- Medicines with dose details:
  - **times/day, duration**
- Lab tests (with **categories & price**)
- **Return Date** (updates patient’s `next_return_date`)
- Printable **RX with QR code** (doctor, patient, RX id / link)

### 🧪 Tests & Categories
- CRUD for tests & categories
- Includes **price & note fields**

### 💡 Smart UI
- **No-scroll selectors** for medicines/tests
- Patient’s **previous RX list on the fly**
- **Tooltips** for long notes/names

---

## ⚙️ Tech Stack
- **Framework**: Laravel 12
- **Database**: MySQL / MariaDB
- **Frontend**: Blade + Bootstrap / Tailwind (customizable)
- **Other**: QR Code generation, file uploads

