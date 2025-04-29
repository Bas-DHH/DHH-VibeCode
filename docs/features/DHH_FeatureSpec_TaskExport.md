
# Feature Spec – Task Export

## 1. Purpose
Allows Admins to export completed hygiene and registration tasks for audits, internal review, or health inspections. Ensures task data is accessible and structured for compliance.

## 2. Access & Permissions

| Role             | Access                     |
|------------------|----------------------------|
| **Admin**        | ✅ Full access to export    |
| **Staff**        | ❌ No access                |
| **Super Admin**  | ✅ Internal export access (optional use only) |

## 3. Export Options (Admin View)

| Field                     | Behavior                                                                 |
|---------------------------|--------------------------------------------------------------------------|
| **Date Range**            | Start + End date picker (default = last 7 days)                          |
| **Task Category Filter**  | Optional dropdown (e.g., Cleaning, Cooking, etc.)                        |
| **Export Format**         | CSV download (UTF-8 encoded) only                                        |
| **Language of Export**    | Matches Admin interface language (NL or EN)                              |

## 4. Export Content (CSV Structure)

Each task export will contain the following columns (fields adjust by task type):

| Field                         | Notes                                                          |
|-------------------------------|----------------------------------------------------------------|
| Task Name                     | As configured by admin (in selected language)                  |
| Task Category                 | One of the 5 defined task types                                |
| Product / Asset Name          | E.g., “Fridge 1”, “Chicken breast”                             |
| Measured Value / Status       | E.g., 3.4°C or “Cleaned = No”                                  |
| Validation Norm (if present)  | E.g., “≤ 7°C”, “≥ 75°C”                                        |
| Extra Questions               | Results (Y/N) for each configured toggle                       |
| Corrective Actions            | Required if warning or “No” toggles used                       |
| Notes                         | Optional user-entered comments                                 |
| Status                        | OK or Warning (based on validation)                            |
| Timestamp                     | Date + time of completion                                      |
| Completed by (user)           | Staff or Admin name                                            |

## 5. Export Behavior

| Feature                  | Behavior                                                       |
|--------------------------|----------------------------------------------------------------|
| **One-click download**   | Generates and downloads CSV instantly                          |
| **Validation handling**  | Missing fields (if any) labeled as “incomplete” in CSV         |
| **Empty results**        | CSV shows message: “No tasks completed in this range”          |
| **File naming**          | Format: `dhh_export_<category>_<date-range>.csv`               |

## 6. UI Flow (Admin Panel)

- Admin clicks “Export”
- Selects:
  - Date Range (start + end)
  - Optional Task Category
- Clicks “Download CSV”
- File is downloaded directly

## 7. Edge Cases

| Scenario                            | System Behavior                                             |
|-------------------------------------|-------------------------------------------------------------|
| No data in range                    | Download still triggers, shows “No data” row                |
| Large range (e.g. 6+ months)        | Minor UI spinner delay; warn if > 10,000 rows               |
| Malformed records (e.g. deleted user) | Shows “Unknown User” in CSV                                |
| Same task done multiple times       | Each completion shown as separate row                      |
