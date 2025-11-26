# Project Context

## Purpose
This is a comprehensive School Management System built with Laravel, designed to handle all aspects of educational institution administration including academic management, financial operations, student records, and communication systems. The system supports multiple user roles including Super Admin, Support Team, Teachers, Parents, and Students with role-based access control and specialized workflows for each user type.

## Tech Stack
- **Backend Framework**: Laravel 8.40 (PHP ^7.2||^8.0)
- **Database**: MySQL with UTF-8 charset, fallback to SQLite
- **Frontend**: Blade templating engine with jQuery and AJAX
- **PDF Generation**: Barryvdh DomPDF for reports and receipts
- **Spreadsheet**: PhpSpreadsheet and Maatwebsite Excel for data export
- **Barcodes**: Milon Barcode for generating QR codes and barcodes
- **Caching**: Redis integration for performance optimization
- **Testing**: PHPUnit for unit and feature testing
- **Authentication**: Custom Laravel authentication with role-based middleware
- **File Storage**: Local filesystem with organized directory structure

## Project Conventions

### Code Style
- **PSR-4 Autoloading**: Standard Laravel namespace structure
- **Naming Conventions**:
  - Models: Singular (e.g., `StudentRecord`, `Payment`)
  - Controllers: Plural with descriptive names (e.g., `StudentRecordController`)
  - Database tables: Plural snake_case (e.g., `student_records`, `payment_records`)
  - Views: Organized by user role and feature (e.g., `pages.support_team.marks.index`)
- **Code Organization**: Repository pattern for data access, Service classes for business logic
- **Middleware**: Custom middleware for role-based access control (teamSA, teamSAT, super_admin, my_parent)
- **Form Requests**: Validation classes for complex form handling
- **Helper Classes**: Custom helpers for common operations (Qs, Mk, Pay, DateHelper)

### Architecture Patterns
- **MVC Pattern**: Standard Laravel MVC with additional service layers
- **Repository Pattern**: Data access abstraction through repository classes
- **Middleware Pattern**: Role-based access control and request filtering
- **Observer Pattern**: Model observers for automatic data handling
- **Service Layer**: Business logic encapsulation in service classes
- **Dependency Injection**: Constructor-based DI for controllers and services
- **Event-Driven**: Laravel events for system notifications and logging

### Testing Strategy
- **Unit Tests**: PHPUnit for testing individual components
- **Feature Tests**: Laravel's built-in testing framework for integration testing
- **Test Coverage**: Focus on critical business logic and user workflows
- **Database Testing**: SQLite in-memory database for fast test execution
- **Custom Test Commands**: Artisan commands for data setup and cleanup

### Git Workflow
- **Branch Naming**: `feature/`, `bugfix/`, `hotfix/` prefixes
- **Commit Messages**: Conventional Commits format (e.g., `feat: add payment journal functionality`)
- **Code Review**: Pull requests required for all changes
- **Version Control**: Semantic versioning with tags for releases

## Domain Context
This is a school management system with the following key domains:

### Academic Management
- **Student Records**: Enrollment, promotion, graduation tracking
- **Class Management**: Classes, sections, subjects, and academic sessions
- **Examination System**: Term-based exams with mark recording and tabulation
- **Grading System**: Configurable grading scales with remarks and comments
- **Time Tables**: Class scheduling with time slot management
- **Skills Assessment**: Competency-based evaluation system

### Financial Management
- **Payment Processing**: Tuition fees, payment tracking, and receipts
- **Financial Records**: Encaissements (income), Recettes (revenue), Décaissements (expenses)
- **Payment Journal**: Comprehensive transaction logging and reporting
- **Duplicate Prevention**: Systematic prevention of duplicate payments
- **Batch Processing**: Group payment operations and receipt generation

### User Management
- **Role-Based Access**: Super Admin, Support Team, Teachers, Parents, Students
- **Authentication**: Custom login system with pin verification for sensitive operations
- **Profile Management**: User profile updates and password management
- **Session Management**: Academic year/session handling with active session tracking

### Communication & Reporting
- **Result Slips**: Individual student reports with director comments
- **Statistical Reports**: Class-wise and student-wise performance analytics
- **Notification System**: Payment reminders and academic alerts
- **Export Capabilities**: Excel and PDF exports for various reports

## Important Constraints
- **Data Integrity**: Strict validation and unique constraints to prevent duplicate records
- **Security**: Role-based access control with middleware protection
- **Performance**: Caching strategies for frequently accessed data
- **Compliance**: Educational institution data privacy requirements
- **Localization**: Multi-language support (French/English) based on institutional needs
- **Decimal Precision**: Support for decimal marks in academic calculations
- **Session Management**: Academic year-based data organization

## External Dependencies
- **DomPDF**: PDF generation for reports and official documents
- **PhpSpreadsheet**: Excel file manipulation for data exports
- **Maatwebsite Excel**: Laravel Excel integration for spreadsheet operations
- **Milon Barcode**: Barcode and QR code generation for student identification
- **Redis**: Caching layer for performance optimization
- **Hashids**: ID obfuscation for secure URL parameters
- **Guzzle HTTP**: HTTP client for external API integrations
