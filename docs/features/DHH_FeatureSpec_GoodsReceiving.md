
# Feature Spec – Goods Receiving

## 1. Purpose
Used to verify temperature, packaging, and condition of delivered products at the time of receipt. Ensures food safety at the entry point and logs supplier compliance.

## 2. Task Configuration (Admin)

Admins define a task for each supplier or delivery routine.

### Configuration Fields

| Field                          | Type & Behavior |
|-------------------------------|-----------------|
| **Task Name (NL + EN)**        | Free text – typically the supplier name |
| **Frequency**                  | Daily / Weekly + weekday selection |
| **Instructions (NL + EN)**     | Two fields for multilingual display |
| **Allow Manual Task Creation** | Checkbox (default: enabled) – allows staff to add tasks for extra deliveries or product groups |

## 3. Task Completion (Staff)

When receiving goods, staff fill in the following:

### Displayed
- Task Name = Supplier name
- Instructions
- Product group-specific norms displayed as reference

### Required Inputs

1. **Supplier Did Not Visit**
   - Toggle
   - If selected → disables other fields and requires comment

2. **Product Group**
   - Dropdown with applied norms:
     - Fresh: ≤ 7°C
     - Canned/Dry: 15–25°C
     - Frozen: ≤ -18°C (max -15°C during transport)

3. **Product Name**
   - Free text

4. **Measured Temperature**
   - Numeric input
   - If exceeds norm → Corrective Action required

5. **Packaging**
   - Yes / No toggle
   - “No” requires Corrective Action

6. **Correct**
   - Yes / No toggle
   - “No” requires Corrective Action

7. **BBE**
   - Yes / No toggle
   - “No” requires Corrective Action

8. **Notes**
   - Optional

## 4. Validation Rules

- All toggles must be answered unless “Supplier Did Not Visit” is enabled
- Temperature must be numeric if provided
- Corrective Action required if:
  - Temp > norm
  - Any condition = “No”
- Notes required if no delivery

## 5. Logic

- Task assigned per supplier + frequency
- Manual tasks allowed for additional product groups
- System applies norm based on Product Group
- Task flagged as “Warning” if:
  - Temp exceeds norm
  - Any answer is “No”

## 6. Edge Cases

- **No delivery** → toggle “Supplier Did Not Visit” → notes required
- **Multiple product groups** → allow manual task creation per group
- **Temp not measurable** → allow “N/A” with note
- **Packaging condition unclear** → “No” + explanation

## 7. Export Format

Each entry includes:
- Task Name / Supplier
- Product Group
- Product Name
- Measured Temperature + norm
- Answers: Packaging / Correct / BBE
- Warning status
- Notes
- Corrective Actions
- Timestamp
- User
