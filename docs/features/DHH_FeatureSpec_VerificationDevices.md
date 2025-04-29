
# Feature Spec – Verification of Measurement Devices

## 1. Purpose
Used to verify that thermometers and probes used in food safety processes are accurate and compliant with HACCP regulations. Ensures reliable measurement of temperatures during preparation, cooking, and storage.

## 2. Task Configuration (Admin)

Admins set up recurring tasks for thermometer accuracy checks. Every check must include both the boiling water and melting ice tests.

### Configuration Fields

| Field                          | Type & Behavior |
|-------------------------------|-----------------|
| **Task Name (NL + EN)**        | Fixed: “Test thermometer” |
| **Allow Manual Task Creation** | Checkbox – enabled by default |
| **Frequency**                  | Dropdown:<br>• Every 3 months (recommended)<br>• Every 6 months<br>• Yearly |
| **Instructions (NL + EN)**     | E.g., “Use melting ice and boiling water to test thermometer accuracy. Record both readings.” |

## 3. Task Completion (Staff)

Staff perform the test using:
- Ice water (0°C reference)
- Boiling water (100°C reference)

### Displayed
- Task Title: “Test thermometer”
- Instructions (based on language)
- Reminder of the expected margin:
  - “Valid if between -1.0°C and +1.0°C (ice)”
  - “Valid if between 99.0°C and 101.0°C (boiling)”

### Required Inputs

1. **Device Name / ID**
   - Free text (e.g., “Blue digital probe”)

2. **Boiling Water 100°C**
   - Numeric input
   - Expected: 99.0°C – 101.0°C

3. **Melting Ice 0°C**
   - Numeric input
   - Expected: -1.0°C – +1.0°C

4. **Is Device Accurate?**
   - Yes / No toggle
   - Automatically set to “No” if either reading is outside margin

5. **Notes**
   - Optional

6. **Corrective Action**
   - Required if accuracy = “No”

## 4. Validation Rules

- Both temperature fields are required and must be numeric
- Device name is required
- Validation logic:
  - 99.0°C ≤ Boiling Water ≤ 101.0°C
  - -1.0°C ≤ Melting Ice ≤ +1.0°C
- If either is outside range:
  - Toggle auto-set to “No”
  - Corrective Action becomes required

## 5. Logic

- Task always titled “Test thermometer”
- System enforces both tests as required steps
- Task is flagged “Warning / Failed” if:
  - Either temp is invalid
  - Accuracy marked “No”
- Manual task creation allowed for unscheduled tests

## 6. Edge Cases

- Only one reading done → block submission (both are required)
- Device not available → allow comment-only fallback
- Thermometer newly received → allow manual entry outside schedule
- User unsure → advised to always select “No” and explain

## 7. Export Format

Each task export includes:
- Task Name
- Device ID / Name
- Measured Temp – Boiling Water
- Measured Temp – Melting Ice
- Validity (within ±1.0°C margin for both)
- Accuracy Toggle (Yes/No)
- Notes
- Corrective Action
- Timestamp
- User
- Status (OK / Failed)
