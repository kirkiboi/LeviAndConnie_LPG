# L&C Smart Retail Management System

## Business Context
L&C Smart Retail is a local retail business specializing in LPG tanks, refill services, tank exchanges, delivery services, and LPG accessories. This system was developed to automate and centralize the business's core operations.

## Purpose & Objectives
The L&C Smart Retail Management System is a comprehensive web application aimed at improving transaction processing, inventory management, employee monitoring, and report generation, while significantly reducing manual errors.

**Core Features Include:**
- **Point of Sale (POS):** Streamlined checkout process with multiple payment methods (Cash, GCash).
- **Inventory & Stock Monitoring:** Real-time stock tracking with low-stock alerts and automatic movement logging.
- **Employee Attendance:** Automated session tracking and daily wage calculation.
- **Reporting Dashboard:** Instant visual metrics for daily sales, order count, active employees, and low-stock items.
- **Secure Authentication:** Custom session management and role-based access control (Owner vs. Employee).

## Technology Stack
- **Backend:** PHP 8.3, Laravel 13.x
- **Database:** MySQL
- **Frontend:** Blade Templates, Vanilla CSS, Vanilla JavaScript

## Setup Instructions

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/landc-smart-retail.git
   cd landc-smart-retail
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install NPM dependencies:**
   ```bash
   npm install
   npm run build
   ```

4. **Environment Setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Configure your database credentials in the `.env` file.*

5. **Run Migrations & Seeders:**
   ```bash
   php artisan migrate --seed
   ```

6. **Serve the Application:**
   ```bash
   php artisan serve
   ```
   Visit `http://localhost:8000` in your browser.

## Technical Highlights
- **Pessimistic Locking (`lockForUpdate`):** Implemented in the POS controller to prevent stock overselling during concurrent transactions.
- **Database Transactions:** Used extensively to guarantee data consistency across dependent records (e.g., Inventory Restock creating Stock Movements and Expenses atomically).
- **Optimized Queries:** Removed N+1 query loops in the reporting dashboard by utilizing Eloquent aggregate functions (`groupBy`, `selectRaw`).
- **Responsive UI:** Custom CSS providing a lightweight, fast, and mobile-friendly interface without the overhead of heavy frontend frameworks.

## License
The L&C Smart Retail Management System is open-sourced software.
