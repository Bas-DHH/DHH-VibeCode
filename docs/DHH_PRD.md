## 1. Overview & Purpose

**De Horeca Helper (DHH)** is a web-based task management platform designed specifically for the hospitality industry to simplify and digitalize hygiene and registration processes. The core focus is on helping hospitality businesses comply with HACCP regulations through structured task management, automated routines, and digital records — replacing outdated paper checklists.

DHH enables staff and managers to efficiently track recurring hygiene-related tasks such as temperature checks, goods receiving, cleaning records, and critical cooking procedures. Through intuitive task assignment, real-time tracking, overdue monitoring, exportable task history, and a built-in free trial to paid subscription model, the platform aims to improve accountability, reduce compliance risks, and create a cleaner, safer working environment.

This Product Requirements Document (PRD) outlines the scope of the **Minimum Viable Product (MVP)** version of DHH. The MVP will be mobile-friendly, support single-location businesses, and include a 14-day free trial followed by a paid subscription. Future expansions such as multi-location support, advanced analytics, and API integrations will be defined post-MVP.

## 2. Problem Statement

Many hospitality businesses still rely on outdated paper checklists or ad hoc systems to manage hygiene and HACCP-related processes. In practice, it's common for restaurants to skip daily registrations for weeks or even months, and then retroactively fill in paperwork with made-up values just before inspections — a risky and non-compliant habit.

New or younger staff members often lack clear guidance or digital visibility into what needs to be done. Cleaning lists are forgotten, responsibilities are unclear, and managers are left in the dark about whether crucial hygiene tasks have been performed at all.

This results in significant compliance risks, operational inefficiencies, and potential health code violations. There is a pressing need for a focused, digital solution that ensures task visibility, accountability, and verifiable records — making daily hygiene routines manageable and inspection-ready at all times.

## 3. Goals & Objectives

### 3.1 Product Goals
- Replace paper-based checklists with structured digital tasks across 5 core hygiene categories.
- Enable staff to clearly see and complete their daily, weekly, and monthly hygiene responsibilities on mobile.
- Automatically track task completion, flag overdue tasks, and log user actions with timestamps.
- Provide one-click export of completed tasks for external audits and inspections.
- Support dual-language interface (Dutch & English) for broader accessibility.

### 3.2 Business Goals
- Validate market demand by converting at least 30% of trial users into paying customers within the first 3 months.
- Keep initial build time under 12 weeks to maintain momentum and budget.
- Support 1 location per account, with a structure that allows for future multi-location scaling.

### 3.3 Technical Goals
- Ensure the platform is fully responsive and usable on all modern mobile devices.
- Securely store task data and protect user access through basic account roles (admin vs. staff).
- Build with extendability in mind: future features like multi-location, reporting, and integrations must be technically feasible without major refactor.

## 4. Target Users / Personas

The DHH MVP is designed for small to mid-sized hospitality businesses, particularly restaurants, cafés, and foodservice operations that are required to comply with HACCP standards. Within these businesses, the primary users fall into three core personas:

### 4.1 Hospitality Manager / Business Owner
- **Responsibilities**: Oversee hygiene processes, manage compliance, ensure team performance.
- **Pain Points**:
  - No visibility into whether hygiene tasks are done daily.
  - Relies on unreliable paper checklists or verbal instructions.
  - Risks non-compliance during inspections.
- **Goals**:
  - Ensure staff follow hygiene routines consistently.
  - Save time and avoid compliance stress.
  - Be able to show proof of hygiene controls at any time.

### 4.2 Frontline Staff / Kitchen Employees
- **Responsibilities**: Execute daily hygiene and registration tasks (e.g., cleaning, temperature checks).
- **Pain Points**:
  - Forget tasks or are unsure what needs to be done and when.
  - Paper-based systems feel like an afterthought or waste of time.
  - Get blamed for missed tasks they weren’t even clearly assigned.
- **Goals**:
  - Know exactly what is expected each shift.
  - Quickly complete tasks without friction.
  - Avoid confusion or blame for non-compliance.

### 4.3 External Inspector / Auditor
- **Responsibilities**: Evaluate hygiene compliance during scheduled or surprise inspections.
- **Pain Points**:
  - Paper records are often incomplete, inconsistent, or falsified.
  - Difficult to quickly verify whether hygiene routines are followed.
- **Goals**:
  - Access clear, timestamped task histories.
  - Verify that critical tasks (e.g., temperature checks) were done correctly and on time.
  - Trust that the hygiene system used by the business is structured and reliable.

## 5. Core Features

The MVP version of De Horeca Helper (DHH) will include only the features necessary to ensure hospitality businesses can reliably manage daily hygiene processes, demonstrate HACCP compliance, and keep their teams on track. Each feature is mapped directly to a critical problem faced by the hospitality industry.

### 5.1 Task Management Engine
- Predefined hygiene task categories:
  - Temperature Control
  - Goods Receiving
  - Critical Cooking Processes
  - Verification of Measurement Devices
  - Cleaning Records
- Each task can be created manually or generated automatically on a recurring schedule (daily, weekly, or monthly).
- Tasks have clear statuses: Pending, Completed, or Overdue.
- Overdue tasks are automatically flagged and displayed as high-priority.
- Each day, daily tasks generate new task instances to ensure fresh tracking.

### 5.2 Mobile-Friendly Task View & Completion
- Staff can log in on mobile devices to see all pending tasks.
- Tasks are grouped by category and due date for clarity.
- Input requirements vary by task type (e.g., temperature value, comment, dropdown).
- Completion is logged with user name, timestamp, and task inputs.
- Staff can view historical task data (read-only).

### 5.3 User Roles & Access Control
- Admin role (business owner/manager): full access to all settings, task history, user management, and exports.
- Staff role: can view and complete tasks and view task history, but not edit settings or completed tasks.
- Only Admins can edit completed tasks; all edits are logged with timestamp and user ID.

### 5.4 Task History, Export & Integrity
- Managers can export completed tasks by predefined date ranges (e.g., last 7 days, this month).
- Export includes: task type, completion time, user, input values, and status.
- Formats: CSV (minimum) and optionally PDF.
- Task edits are restricted to Admins and are tracked for audit transparency.

### 5.5 Overdue Notifications & Alerts
- Admin receives daily email with summary of pending and overdue tasks.
- Staff see visual alerts in the app when logging in if they have overdue tasks.
- No push or SMS notifications included in MVP.

### 5.6 Minimal Dashboard View
- Admin dashboard shows: total tasks today, overdue tasks, and completed tasks.
- Staff dashboard shows: open tasks, what’s overdue, and what’s left to do today.

### 5.7 User & Business Setup
- Business accounts represent single locations (multi-location not supported in MVP).
- Admin creates staff users directly via name and email (no invite flow required).
- Staff receive welcome email with temporary password and login link.

### 5.8 Subscription & Trial Model
- 14-day free trial on sign-up, with automatic conversion to a paid subscription.
- Payment system includes automated billing and subscription management.
- Only 1 subscription tier in MVP (simple flat fee per business).

### 5.9 Localization & Language Support
- The MVP will support Dutch and English interface languages.
- All labels, instructions, and UI elements will be prepared for translation.
- Language preference will default to browser language or be manually selectable where applicable.

## 6. User Stories / Flows

This section describes the typical interactions users will have with DHH, organized as user stories. These stories capture the key goals and flows for each persona in the MVP.

### 6.1 Admin / Manager

**Story A: Setting up the business**  
As a manager, I want to create my business account and add staff users quickly, so we can start using the platform immediately.

**Story B: Creating or reviewing tasks**  
As a manager, I want to create recurring tasks or one-off tasks using predefined categories and templates, so hygiene responsibilities are clearly defined and repeat consistently.

**Story C: Reviewing task history**  
As a manager, I want to export completed task data by date range, so I can share it with inspectors or use it for audits.

**Story D: Correcting mistakes**  
As a manager, I want to edit a completed task (with history tracking), so I can correct user errors without losing audit integrity.

**Story E: Monitoring remotely**  
As a manager, I want to check task progress and overdue tasks even when I’m not on location, so I can stay in control from anywhere.

**Story F: Receiving daily updates**  
As a manager, I want to receive a daily summary email with overdue tasks, so I stay informed without logging in constantly.

### 6.2 Staff / Employee

**Story G: Seeing what I need to do**  
As a staff member, I want to log in and see only today’s tasks, so I know exactly what to do.

**Story H: Completing a task**  
As a staff member, I want to quickly fill in a task with the required information, so I can move on with my shift.

**Story I: Catching up**  
As a staff member, I want to see which tasks are overdue, so I can prioritize them during busy hours.

### 6.3 Inspector / Auditor

**Story J: Reviewing hygiene records**  
As an inspector, I want to see exportable task logs with timestamps, categories, and user names, so I can verify hygiene compliance easily.

## 7. Information Architecture

This section outlines the structure of the MVP application — how key components are organized and accessed. It defines the primary navigation, user roles, and layout of core functionality, optimized for mobile-first usage.

### 7.1 Main Navigation Structure (Mobile Web App)
- **Home / Dashboard**
  - Daily task overview
  - Count of open, completed, and overdue tasks
- **Tasks**
  - List of all active tasks grouped by category and due date
  - Filter by today, overdue, or specific category
- **History**
  - View of completed tasks by date
  - Admin: full history with export option
  - Staff: read-only access
- **Admin (only visible to Admins)**
  - User management (add/remove staff)
  - Task configuration (create/edit recurring tasks)
  - Export center (download CSV/PDF of past tasks)
  - Subscription settings

### 7.2 User Role-Based Access

| Section      | Admin Access                    | Staff Access         |
|--------------|----------------------------------|-----------------------|
| Dashboard    | Full                             | Own tasks overview    |
| Tasks        | View, complete, and manage tasks | View & complete tasks |
| History      | Full + export                    | Read-only             |
| Admin Panel  | Full                             | Hidden                |

Admins, especially in small or owner-operated businesses, may also be responsible for performing hygiene tasks. Therefore, they must have full task completion access in addition to management capabilities.

### 7.3 Page Hierarchy / Flow
1. **Login**
   - Staff logs in via email and password
   - Redirects based on role (Admin or Staff)
2. **Home (Dashboard)**
   - Summary of task status
   - Entry point for deeper actions
3. **Tasks**
   - Today’s tasks (default view)
   - Overdue tasks shown at top
   - Task detail → input fields → mark as complete
4. **History**
   - Task logs shown with filters by date
   - Admin can export from this view
5. **Admin Panel** (Admin only)
   - Create/edit recurring tasks (from predefined templates)
   - Add/remove users
   - Manage subscription and export task records

### 7.4 Future Scaling: Multi-Location Support
While the MVP supports single-location businesses only, the system will be built to accommodate future expansion into multi-location support. Each business account will eventually manage multiple distinct locations, each with their own:
- Task configurations
- Assigned users
- Completion data and dashboards

Later versions may include location-specific permissions, aggregated reporting, and centralized management views across all business units.

## 8. Wireframes / UI Mockups

**To be defined.**

Basic wireframes and screen layouts will be developed once the technical requirements and task logic are finalized. These will focus on mobile-first usability, clarity for task completion, and manager oversight. A visual flow diagram is planned to accompany the interface designs in the next iteration.

## 9. Technical Requirements

This section outlines the high-level technical considerations and constraints for building the DHH MVP.

### 9.1 Platform Architecture
- The DHH MVP will be built as a **web-based, mobile-first application** using responsive design (not a native app).
- The platform will support **multi-language functionality**, starting with **Dutch and English**. All interface texts, labels, and instructions will be structured for localization.
- The tech stack will be based on **Laravel (PHP)** for the backend and **React** (with **Inertia.js**) for the frontend layer.
- The frontend will use **Tailwind CSS** with **shadcn/ui** components for a modern and accessible UI.
- The application will be hosted on the user's **existing PHP/MySQL-capable hosting** environment, with optional deployment via Git or SSH.
- Laravel will manage:
  - User authentication (via Laravel Breeze or Jetstream)
  - MySQL database (tasks, users, history, etc.)
  - Backend logic, task validation, audit trails
  - Subscription billing via **Laravel Cashier + Mollie**
- The architecture is selected for its:
  - Strong backend capabilities (roles, billing, logs)
  - Compatibility with existing hosting (no need for Node.js or Vercel)
  - Scalability for multi-location, audit logging, and advanced admin features post-MVP
- This setup supports the DHH **“vibecoding”** workflow while ensuring a future-proof backend foundation.
- Estimated MVP-stage costs:
  - Hosting: ~€10–€30/month (existing server)
  - Domain & SSL: Likely included with hosting
  - Mollie (billing): Pay-per-transaction (no monthly fee)
  - Optional email service (e.g., Mailgun, Postmark): ~€10–€20/month depending on usage

This platform architecture combines long-term scalability with the flexibility and simplicity needed for rapid solo development.

## 10. Non-Functional Requirements

These requirements define how the DHH MVP should behave in terms of performance, reliability, usability, and compliance, beyond its core functionality.

### 10.1 Performance
- The application should load on mobile devices in under 2 seconds on a standard 4G connection.
- All task interactions (viewing, completing, switching pages) should respond within 300ms.
- Exporting task data (CSV) should complete within 5 seconds for date ranges up to 30 days.

### 10.2 Availability & Reliability
- The platform should target 99.9% uptime, leveraging managed services (e.g., Vercel + Supabase).
- Task data must persist reliably — no risk of data loss during reloads or navigation.
- Daily task generation must execute consistently, even if no user logs in.
- Daily automated database backups must be enabled (covered by Supabase on paid plans).

### 10.3 Security
- All data must be transmitted over HTTPS.
- User data is stored securely with access control based on role (Admin vs Staff).
- Completed task edits are only allowed for Admins and must be audit-logged.
- Admin login endpoints must be protected against brute force and injection attacks (Supabase default settings apply).
- Basic error logging must be available via Supabase logs (backend) and Vercel (frontend functions). Frontend must include error boundaries for graceful failures.

### 10.4 Usability & Accessibility
- The platform must be mobile-first and intuitive, with minimal training needed for staff users.
- Interface language must match the user’s browser setting or selected language (Dutch and English supported).
- Text contrast and tap targets must follow WCAG AA accessibility guidelines.

### 10.5 Maintainability
- The MVP must be built with modular logic (e.g., separate logic per task category) to allow future edits and additions without large-scale rewrites.
- Task input types and validation logic should be externally configurable (from predefined templates), not hardcoded.

### 10.6 Legal & Compliance
- The platform must comply with GDPR:
  - All user data is stored in the EU (Supabase EU region).
  - Users can request data deletion.
  - Email addresses and task records are encrypted at rest.

## 11. Future Roadmap / Expansion Plans

While the MVP focuses on delivering essential hygiene task management, De Horeca Helper is built with long-term scalability and industry adoption in mind. The following initiatives represent the next logical stages of product development, prioritized by customer value and technical feasibility.

### 11.1 Multi-Location Support
- Allow a single business account to manage multiple physical locations.
- Each location will have its own:
  - Users and roles
  - Task schedules and history
  - Export and reporting tools
- Central dashboard for head office oversight.

### 11.2 Advanced Reporting & Analytics
- Trends and compliance rates over time (e.g. overdue ratio, completed on time).
- Location and user-level performance breakdown.
- Visual summaries and downloadable reports.

### 11.3 Inspector Access / Read-Only Links
- Generate one-time or recurring shareable links for external inspectors.
- Inspector views task logs without needing an account.
- Optional digital signature capture for site visits.

### 11.4 Mobile App (Native or PWA)
- Installable version of DHH with offline support.
- Push notifications for overdue or critical tasks.
- Improved UX for high-frequency users (kitchen staff, managers on the floor).

### 11.5 Custom Task Builder (Advanced Users)
- Allow trusted businesses to create custom task templates within category constraints.
- Logic builder with supported field types, ranges, and validations.
- Still governed by HACCP structure to retain compliance integrity.

### 11.6 Integrations
- POS systems or employee time tracking for shift-based task triggering.
- External compliance or food safety APIs.
- Optional Slack/WhatsApp notifications for team awareness.

## 12. Metrics for Success

To evaluate whether the MVP of De Horeca Helper is successful, the following metrics will be tracked and reviewed. These indicators help validate product-market fit, usability, and revenue potential.

### 12.1 Product Usage Metrics
- % of daily tasks completed (target: 80%+ within 30 days of use)
- Overdue task reduction rate over the first month
- Median time-to-complete task from when it's assigned

### 12.2 User Engagement
- Active businesses using the platform weekly (target: 70% retention in first 30 days)
- Staff users logging in more than 3x per week
- Average number of tasks completed per staff per day

### 12.3 Conversion Metrics
- Free trial to paid conversion rate (target: 25–30%)
- Churn rate in the first 60 days (target: <10%)
- Trial activation rate (user finishes setup and completes first task)

### 12.4 Support & Reliability
- App uptime (target: 99.9%)
- Number of support requests per business (target: <2 in first month)
- Bug reports / critical failures per month

