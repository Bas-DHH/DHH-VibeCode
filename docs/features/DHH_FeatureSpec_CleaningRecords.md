
# Feature Spec – Cleaning Records

## 1. Purpose
Used to verify that cleaning tasks are completed correctly and consistently in line with HACCP hygiene standards. Ensures critical surfaces and equipment are properly cleaned and optionally disinfected.

## 2. Task Configuration (Admin)

Admins create recurring cleaning tasks using a standard template. Tasks can be created per location, object, or department.

### Configuration Fields

| Field                          | Type & Behavior |
|-------------------------------|-----------------|
| **Task Name (NL + EN)**        | E.g., “Clean salad prep station” |
| **Allow Manual Task Creation** | Checkbox – enabled by default |
| **Frequency**                  | None / Daily / Weekly / Monthly<br>+ weekday selector (if weekly) |
| **Instructions (NL + EN)**     | Short description shown during task (e.g., “Use disinfectant X and wipe all handles and counters.”) |
| **“Cleaned” Question**         | Always shown and required |
| **“Disinfected” Question**     | Optional – admin chooses whether to include this extra step |

## 3. Task Completion (Staff)

Staff confirm that the cleaning process has been completed and optionally disinfected.

### Displayed
- Task Name
- Instructions (localized)
- Questions

### Required Inputs

1. **Cleaned**
   - Yes / No toggle
   - “No” → Corrective Action required

2. **Disinfected**
   - Yes / No toggle (if enabled for this task)
   - “No” → Corrective Action required

3. **Notes**
   - Optional free text field

## 4. Validation Rules

- “Cleaned” must be answered
- “Disinfected” must be answered if shown
- If any answer = “No” → Corrective Action becomes mandatory
- Notes are always optional unless triggered

## 5. Logic

- Task generated based on configured frequency
- Manual task creation allowed if enabled
- “Cleaned” toggle is always shown and required
- “Disinfected” toggle shown only if configured
- Task marked “Warning” if:
  - Either toggle = “No”

## 6. Edge Cases

- Task not completed (e.g., area blocked) → allow submission with “No” + required note
- Disinfectant not available → “No” + corrective action (e.g., reorder supply)
- Skipped or missed → task marked as overdue in dashboard

## 7. Export Format

Each cleaning record includes:
- Task Name
- “Cleaned” (Y/N)
- “Disinfected” (Y/N or N/A)
- Notes
- Corrective Actions
- Timestamp
- User
- Status (OK / Warning)
