
# Feature Spec – Temperature Control

## 1. Purpose
Used to register daily/weekly temperatures of fridges, freezers, and equipment to ensure HACCP compliance and operational safety. Tasks are assigned per asset and logged by staff, then reviewed by managers.

## 2. Task Configuration (Admin)

Admins create temperature control tasks using a standard template. These tasks are either recurring (daily/weekly) or manually added by staff if permitted.

### Configuration Fields

| Field                         | Type & Behavior |
|------------------------------|-----------------|
| **Task Name (NL + EN)**       | Two required fields for localization |
| **Allow Manual Task Creation** | Checkbox (default off) |
| **Norm / Validation**         | Dropdown list: Temperature Norms | Numeric °C input, shown to staff |
| **Frequency**                 | Options: None / Daily / Weekly<br>Weekly → select weekdays |
| **Extra Questions**           | Predefined fixed list (checkbox to include):<br>• FIFO followed<br>• BBE checked<br>• Covered<br>• Cleanliness<br>• Codering / Stickers |
| **Instructions (NL + EN)**    | Two fields shown based on user language |

### Temperature Norms

| Equipment              | Norm       |
|------------------------|------------------------|
| Koelcel (Fridge)       | Max 7°C                |
| Vriezer (Freezer)      | ≤ -18°C                |
| Koelwerkbank           | Max 7°C                |
| Saladiere              | Max 7°C                |
| Vaatwasser (Wash)      | > 60°C                 |
| Naspoel (Rinse)        | > 80°C                 |
| Softijsmachine         | “Pasteurisation done” (text/comment-based) |

## 3. Task Completion (Staff)

### Displayed
- Task Name (based on user language)
- Instruction (same)
- Temperature Norm (e.g., “Keep below 7°C”)

### Required Inputs
1. **Measured Temperature**
   - Numeric input
   - If temperature exceeds norm → require **Corrective Action (textarea)**
   - Cannot complete without it

2. **Extra Questions (if configured)**
   - Shown as horizontal Yes / No toggles
   - If “No” selected → **Corrective Action** required

3. **Notes (optional)**
   - Free text field

## 4. Validation Rules

- Temperature field must be filled and numeric
- If temperature > norm → Corrective Action required
- If any extra question is “No” → Corrective Action required
- Notes are always optional
- Submission blocked with missing/invalid fields

## 5. Logic

- One task per asset per recurrence (daily/weekly)
- Completed task = locked (editable only by Admin)
- Task flagged as “Warning” if:
  - Temp exceeds norm
  - Any extra question = “No”

## 6. Edge Cases

- **Broken thermometer** → allow comment-only mode, flag as incomplete
- **Duplicate asset names** → allow suffix (e.g., “Fridge 1 (Prep)”)
- **Incorrect entries** → Admins can correct via audit-logged edit
- **Future: Smart sensors** → allow integration with Bluetooth devices

## 7. Export Format

Each logged task includes:
- Task Name
- Temperature value
- Norm
- Warning status (OK / Warning)
- Answers to extra questions (Y/N)
- Notes
- Corrective actions (if any)
- User
- Timestamp
