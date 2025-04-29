
# Feature Spec – Critical Cooking Processes

## 1. Purpose
Used to ensure food is safely cooked, reheated, held, or cooled, in line with HACCP norms. This module enforces critical control points (CCPs) through validated temperature logs.

## 2. Task Configuration (Admin)

Admins create cooking tasks using a dropdown that defines both the task name and the temperature norm automatically. Manual tasks are also allowed for extra checks.

### Configuration Fields

| Field                          | Type & Behavior |
|-------------------------------|-----------------|
| **Task Type**                  | Dropdown – selects preset task (see list below) |
| **Frequency**                  | None / Daily / Weekly + weekday checkboxes |
| **Instructions (NL + EN)**     | Short description shown during task |
| **Allow Manual Task Creation** | Checkbox – enabled by default |

### Preset Task List (Dropdown Options)

| Task Type Selection Option | Temperature Norm |
|----------------------------|------------------|
| **Terugkoelen / Cooling down A** | ≥ 75°C |
| **Terugkoelen / Cooling down B** | ≤ 7°C |
| **Kerntemperatuur gegaard component / Core temperature of cooked component** | ≥ 75°C |
| **Kerntemperatuur product in warmhoudvoorziening / Core temperature in hot holding equipment** | ≥ 60°C |
| **Serveertemperatuur product warm en koud / Serving temperature hot and cold** | Warm ≥ 60°C / Cold ≤ 7°C |
| **Frituur / Deep-frying** | ≤ 175°C |
| **Kerntemperatuur geregenereerd component / Core temperature of regenerated component** | ≥ 60°C (within 60 min) |
| **Kerntemperatuur warm component op buffet / Core temperature of hot buffet items** | ≥ 60°C |
| **Koude producten op buffet (buiten 2-uursborging) / Cold buffet items (outside 2-hour rule)** | ≤ 7°C |

## 3. Task Completion (Staff)

### Displayed
- Task Name (Dutch/English)
- Norm
- Instructions (NL or EN based on user language)

### Required Inputs

1. **Product Name**
   - Free text input

2. **Measured Temperature**
   - Numeric
   - If outside norm → Corrective Action required

3. **Notes**
   - Optional

## 4. Validation Rules

- Product Name is required
- Temperature must be filled and numeric
- If temp outside norm → Corrective Action required
- Notes optional unless flagged

## 5. Logic

- Task created from fixed dropdown
- Norm and task name are pre-filled
- Manual tasks can be added for ad hoc checks
- System flags task as “Warning” if:
  - Temp is out of range

## 6. Edge Cases

- **Cooling B** tasks: system does not track time; staff must know it must be within 5 hours
- **Forgot to measure temp** → allow comment-only fallback (admin-configured)
- **Special cooking (e.g. sous-vide)** → future option to add custom template

## 7. Export Format

Each record includes:
- Task Type / Name
- Product Name
- Temperature
- Norm
- Warning status
- Notes
- Corrective Action (if any)
- User
- Timestamp
