
# Feature Spec – Dashboard & Notifications

## 1. Purpose
Gives users a quick overview of their responsibilities (for Staff) or task performance (for Admin). Provides alerts for overdue or incomplete tasks and ensures no critical hygiene steps are missed.

## 2. Dashboard Types

### A. Staff Dashboard

- Purpose: Show today's open tasks, overdue items, and quick access to complete them

#### Displayed Sections:

| Section                | Details                                                             |
|------------------------|---------------------------------------------------------------------|
| **Today’s Tasks**      | List or count of pending tasks grouped by category                  |
| **Overdue Tasks**      | Highlighted (red/orange), sorted to top                             |
| **Completed Tasks (Today)** | Optional short summary (read-only)                        |

#### Key Interactions:
- Tap → Go to task
- Overdue tasks always appear on top

### B. Admin Dashboard

- Purpose: Overview of hygiene performance and team activity

#### Displayed Sections:

| Section                | Details                                                             |
|------------------------|---------------------------------------------------------------------|
| **Open Tasks Today**   | Total count of uncompleted tasks                                     |
| **Overdue Tasks**      | Highlighted summary + quick filter                                   |
| **Completed Tasks**    | Count per category (optional: drill down)                            |
| **Task Performance**   | Optional in future (e.g., % completed per staff per week)            |

## 3. Notification Logic

### A. Staff Notifications

| Type                | Trigger                                       | Display                          |
|---------------------|-----------------------------------------------|----------------------------------|
| **Overdue Task Banner** | When user logs in and has overdue tasks      | In-app alert at top of screen    |
| **Warning Task Notice** | When user submits task with warning status   | Inline notification              |

### B. Admin Notifications

| Type                     | Trigger                                          | Display/Method         |
|--------------------------|--------------------------------------------------|------------------------|
| **Daily Summary Email**  | Sent every morning (8:00)                        | Email                  |
| **Overdue Alert (Email)**| Optional: if critical task overdue > X hours     | Future enhancement     |

#### Daily Summary Email includes:
- Total tasks scheduled today
- Tasks completed
- Overdue tasks
- List of flagged (warning) tasks

## 4. Logic

- Task status changes update both dashboards in real time
- Daily summary email is sent to all Admins
- No push/SMS notifications in MVP
- Overdue logic based on:
  - Task creation date + due time (e.g., “before midnight”)
  - If not completed by that time → flagged as overdue

## 5. Edge Cases

- **No tasks assigned** → show empty state message
- **User logs in after shift** → overdue tasks from previous day still shown
- **Admin with no users yet** → onboarding message shown in dashboard