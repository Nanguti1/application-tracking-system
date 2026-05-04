# Laravel Applicant Tracking System (ATS)

A production-ready, enterprise-scale ATS built with Laravel 13, Inertia.js, and React 19.

## Overview

This is a monolithic Laravel application designed to manage large-scale recruitment operations with thousands of applicants per job opening. The system automates candidate screening, interview scheduling, and hiring pipeline management.

## Key Features

### 1. **Job Management**
- Create, edit, publish, and archive job openings
- Support for multiple employment types (full-time, part-time, contract, etc.)
- Job duplication for creating similar positions
- Salary range configuration
- Interview scheduling preferences (fixed or rolling)
- Application deadline management
- Automatic candidate shortlisting on deadline

### 2. **Public Career Portal**
- Candidate-facing job search interface
- Advanced filtering (department, location, employment type, salary)
- Full-text search across job descriptions
- Job detail pages with application forms
- Responsive design for mobile compatibility
- Social profile integration (LinkedIn, portfolio)

### 3. **Candidate Application System**
- Application form with CV upload capability
- Profile-based applications (reusable information)
- Duplicate application prevention
- Application status tracking
- CV parsing and analysis
- Candidate dashboard for managing applications

### 4. **CV Parsing & Matching Engine**
- Automatic text extraction from PDF and DOCX files
- Skill extraction using pattern matching
- Experience level detection
- Education and certification extraction
- Keyword-based matching against job requirements
- Weighted scoring system:
  - Keyword matching (30%)
  - Skill matching (40%)
  - Education matching (15%)
  - Experience matching (15%)
- Match percentage and ranking scores
- Recommendations (Highly Recommended, Recommended, Consider, Not Recommended)

### 5. **Application Deadline Processing**
- Scheduled job deadline processing
- Automatic candidate ranking on deadline
- Top N automatic shortlisting
- Bulk rejection of non-selected candidates
- Queue-based processing for scalability
- Email notifications for status changes

### 6. **Email Template System**
- WYSIWYG editor for email templates
- Dynamic placeholder support:
  - `{candidate_name}`
  - `{job_title}`
  - `{company_name}`
  - `{interview_date}` & `{interview_time}`
  - `{interview_duration}`
  - `{start_date}`
- Pre-built templates for:
  - Rejection emails
  - Shortlist invitations
  - Interview invitations
  - Offer letters
  - Congratulations emails
  - Regret emails

### 7. **Interview Scheduling**
- Configurable interview availability windows
- Break time management
- Slot duration configuration
- Automated scheduling from available slots
- Manual rescheduling with reason tracking
- Interview status tracking (scheduled, completed, no-show, cancelled)
- Interviewer feedback and scoring

### 8. **Rolling Interview Workflow**
- Multi-stage interview support:
  1. CV Review
  2. Phone Screening
  3. Technical Interview
  4. HR Interview
  5. Final Interview
  6. Offer Stage
- Configurable interview progression
- Status transitions (Proceed, Rejection, Hold, Offer)
- Automatic email notifications for each stage

### 9. **Admin Dashboard**
- Real-time statistics:
  - Total jobs and active jobs
  - Total applicants
  - Shortlisted candidates
  - Rejected candidates
  - Pending interviews
- Hiring funnel visualization
- Application trends (last 30 days)
- Top jobs by application count
- Department-level analytics

### 10. **Candidate Pipeline (Kanban)**
- Drag-and-drop status management
- Columns for each application stage
- Real-time status updates
- Candidate information cards
- Quick filtering and searching

### 11. **Search & Filtering**
- HR/Recruiter can filter applications by:
  - Application status
  - Match score
  - Experience level
  - Education
  - Location
  - Application date
- Optimized queries with eager loading
- Database indexing for performance
- Pagination for large datasets

### 12. **Notifications**
- Email notifications for:
  - Application status changes
  - Interview scheduling
  - Interview reminders
  - Offer decisions
- In-app notification system
- SMS architecture (abstraction layer for future integration)
- Notification logging and delivery tracking

### 13. **Audit Logging**
- Track who changed candidate status
- Log interview scheduling changes
- Monitor template modifications
- Record rejections with reasons
- Timeline of all actions per application
- User-based activity tracking

### 14. **User Roles & Permissions**
- **Super Admin**: Full system access
- **HR Admin**: Full ATS management (jobs, applications, interviews)
- **Recruiter**: Application and pipeline management
- **Interviewer**: Interview feedback and scheduling
- **Candidate**: Application and profile management
- Permission-based access control using Spatie Permissions

### 15. **Document Management**
- Secure CV storage with MediaLibrary
- Cover letter attachment storage
- Offer letter generation and storage
- Interview attachments
- Access control based on roles

### 16. **Reporting Module**
- CSV/Excel export of candidates
- Hiring conversion rates
- Time-to-hire metrics
- Department hiring statistics
- Candidate source analytics
- Export-ready data formats

### 17. **AI Enhancement Layer** (Extensible)
- AI-generated candidate summaries
- Interview question suggestions
- Duplicate application detection
- Ranking explanation generation
- Service layer abstraction for OpenAI/other providers

## Database Schema

### Core Tables

```
jobs
├── id, title, department, location
├── employment_type, salary_min, salary_max
├── description, requirements
├── deadline_at, interview_date_start, interview_date_end
├── candidates_to_shortlist, interview_type
├── status (draft|published|closed|interviewing|offer_stage|filled|archived)
└── timestamps

job_applications
├── id, job_id, user_id
├── full_name, email, phone
├── cover_letter, linkedin_profile, portfolio_url
├── years_of_experience, education_level
├── expected_salary, location, availability_date
├── status (applied|screening|shortlisted|interview|...)
├── match_score, ranking_score, match_details (JSON)
├── shortlisted_at, rejected_at, rejection_reason
└── timestamps

resumes
├── id, job_application_id
├── original_filename, file_path, mime_type, file_size
├── raw_text, parsed_data (JSON), extracted_email, extracted_phone
└── timestamps

interviews
├── id, job_id, job_application_id, interview_stage_id
├── scheduled_at, duration_minutes
├── interview_type, interviewer_name, interviewer_email
├── location, meeting_link
├── status (scheduled|rescheduled|completed|no_show|cancelled)
├── feedback, interviewer_score
├── rescheduled_at, reschedule_reason
└── timestamps

interview_stages
├── id, job_id
├── name, order, description, duration_minutes
└── timestamps

email_templates
├── id, job_id (nullable)
├── name, type (rejection|shortlist|interview_invitation|...)
├── subject, body, placeholders_available (JSON)
├── is_default
└── timestamps

offers
├── id, job_application_id
├── offered_salary, position_title, start_date
├── offer_letter, status (draft|sent|accepted|rejected|expired)
├── sent_at, accepted_at, rejected_at, expires_at
└── timestamps

interview_schedules
├── id, job_id
├── interview_date, start_time, end_time
├── breaks (JSON), slot_duration_minutes
└── timestamps

notifications_log
├── id, notifiable_type, notifiable_id
├── type, notification_type (email|in_app|sms)
├── recipient_email, recipient_phone
├── subject, body
├── status (pending|sent|failed|bounced)
├── error_message, retry_count, sent_at
└── timestamps

+ Spatie Permission tables (roles, permissions, role_has_permissions, model_has_roles, model_has_permissions)
```

## Installation & Setup

### Prerequisites
- PHP 8.3+
- Laravel 13
- MySQL 8.0+
- Redis (for queues)
- Node.js 18+ (for frontend)

### Installation Steps

```bash
# Install Composer dependencies
composer install

# Install npm dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed roles and permissions
php artisan db:seed RoleAndPermissionSeeder

# Build frontend assets
npm run build

# Create storage symlink
php artisan storage:link
```

### Development

```bash
# Start development server with queue and Vite
composer run dev

# Run tests
php artisan test

# Run specific test file
php artisan test tests/Feature/JobManagementTest.php

# Process scheduled jobs and queues
php artisan schedule:work
php artisan queue:work
```

## API & Routes

### Public Routes
- `GET /careers` - Job listing page
- `GET /careers/{job}` - Job details
- `POST /careers/{job}/apply` - Submit application

### Authenticated Routes
- `GET /jobs` - Jobs list (admin)
- `GET /jobs/create` - Create job form
- `POST /jobs` - Store job
- `GET /jobs/{job}/edit` - Edit job
- `POST /jobs/{job}/publish` - Publish job
- `GET /jobs/{job}/applications` - Applications for job
- `GET /applications/{application}` - Application details
- `POST /applications/{application}/shortlist` - Shortlist
- `POST /applications/{application}/reject` - Reject
- `GET /jobs/{job}/pipeline` - Kanban pipeline
- `GET /interviews` - Interview list
- `POST /interviews` - Schedule interview
- `GET /admin/dashboard` - Admin dashboard

## Architecture

### Folder Structure

```
app/
├── Actions/              # One-off actions
├── Services/             # Business logic (JobService, ApplicationService, CVParsingService)
├── Models/               # Eloquent models
├── Controllers/          # HTTP controllers
├── Policies/             # Authorization policies
├── Jobs/                 # Queue jobs
├── Notifications/        # Email/SMS notifications
├── Console/Commands/     # Scheduled commands
├── Http/Requests/        # Form validation
├── Exceptions/           # Custom exceptions
└── Traits/               # Reusable traits

database/
├── migrations/           # Schema migrations
├── factories/            # Model factories for testing
└── seeders/              # Database seeders

resources/
├── js/
│   ├── pages/           # Inertia pages
│   │   ├── Careers/     # Public portal pages
│   │   ├── Admin/       # Admin pages
│   │   └── Candidate/   # Candidate pages
│   ├── components/      # Reusable components
│   ├── layouts/         # Page layouts
│   └── hooks/           # React hooks
└── css/                 # Tailwind CSS

tests/
├── Feature/             # Feature tests
├── Unit/                # Unit tests
└── TestCase.php         # Base test class
```

### Key Services

**JobService**: Manages job lifecycle (create, publish, archive, duplicate)
**ApplicationService**: Handles applications (submit, shortlist, reject, filter)
**CVParsingService**: Extracts data from CVs and scores candidates
**InterviewService**: Manages interview scheduling and feedback

## Performance Optimizations

1. **Eager Loading**: All relationships preloaded in queries
2. **Database Indexes**: Indexed on frequently queried columns (status, created_at, user_id, job_id)
3. **Queue Processing**: Long-running tasks (CV parsing, email sending) use Laravel queues
4. **Pagination**: Large result sets paginated with cursor pagination
5. **Caching**: Application counts and statistics cached
6. **Chunking**: Bulk operations processed in chunks
7. **Scheduled Tasks**: Deadline processing runs at scheduled times (not on-demand)

## Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test suite
php artisan test tests/Feature/JobManagementTest.php

# Run specific test method
php artisan test tests/Feature/JobManagementTest.php --filter=test_hr_admin_can_create_job
```

## Configuration

### Environment Variables

```
QUEUE_CONNECTION=redis
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://localhost:7700
```

### Scheduled Tasks

The following commands run on schedule (configured in `app/Console/Kernel.php`):

- `ats:process-deadlines` - Process job deadlines every hour
- Queue worker processes background jobs (CV parsing, emails)

## Security

- CSRF protection on all forms
- Authorization policies on all resources
- Rate limiting on public endpoints
- File upload validation (PDF/DOCX only)
- Secure file storage outside public directory
- SQL injection prevention via Eloquent ORM
- Audit logging of sensitive actions

## Future Enhancements

1. **AI Integration**: OpenAI integration for CV summarization and interview questions
2. **SMS Notifications**: Twilio integration for SMS notifications
3. **Bulk Operations**: Bulk candidate rejection, shortlisting
4. **Advanced Analytics**: ML-based hiring predictions
5. **Video Interviews**: Integrated video interview functionality
6. **Background Checks**: Third-party background check integration
7. **Salary Intelligence**: Market rate suggestions
8. **Slack Integration**: Slack notifications for hiring activities

## Support & Documentation

For detailed API documentation, refer to the inline code comments and PHPDoc blocks.

## License

MIT
