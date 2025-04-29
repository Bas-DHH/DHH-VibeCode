
# Feature Spec – Language & Localization

## 1. Purpose
Ensure users can operate DHH in their preferred language (Dutch or English). Supports multilingual teams and inspectors, while minimizing friction for Dutch-only businesses.

## 2. Supported Languages

| Language       | Code  | Active in MVP? |
|----------------|-------|----------------|
| **Dutch**      | `nl`  | ✅ Yes          |
| **English**    | `en`  | ✅ Yes          |

## 3. Language Selection (User Experience)

| Type             | Behavior                                                       |
|------------------|----------------------------------------------------------------|
| **Manual Selection** | ✅ In-app language toggle available (e.g., header or settings) |
| **Automatic Default**| First-time login defaults to browser language if supported |

✅ Manual toggle is always available  
✅ Setting is saved per user and used on future logins

## 4. Localized Fields (Admin Configuration)

Admins must input at least one language (usually Dutch). The other is optional and falls back automatically.

| Field                     | Required?   | Fallback Behavior         |
|---------------------------|-------------|---------------------------|
| Task Name (NL)            | ✅ Yes       |                           |
| Task Name (EN)            | ❌ Optional  | If empty, fallback to Dutch |
| Instructions (NL)         | ✅ Yes       |                           |
| Instructions (EN)         | ❌ Optional  | If empty, fallback to Dutch |

✅ Admin UI displays both fields clearly  
✅ System uses the correct field based on each user’s language setting

## 5. Staff Experience

| Behavior                               | Description                                                  |
|----------------------------------------|--------------------------------------------------------------|
| Language is user-controlled            | Each user sees interface + tasks in their selected language  |
| No mixing of languages per screen      | Entire interface follows one language                        |
| Task content localized                 | Task names and instructions shown based on selected language |
| Export follows Admin language          | Task export uses Admin’s current language at time of export  |

## 6. Developer Logic

| Element                         | Implementation Notes                                         |
|----------------------------------|--------------------------------------------------------------|
| Localized input storage         | e.g., `task_name_nl`, `task_name_en`, etc.                  |
| Dynamic content rendering       | System loads correct version per user’s setting             |
| Missing translation fallback    | If missing, default to Dutch                                |
| Interface labels/buttons        | Stored in i18n files (`nl.json`, `en.json`)                 |
| Notes/corrective actions        | Never translated — shown exactly as user typed              |

## 7. Edge Cases

| Scenario                          | Behavior                                                     |
|-----------------------------------|--------------------------------------------------------------|
| Admin skips EN input              | System shows Dutch to English users as fallback              |
| Staff switches language in session| UI updates instantly; task content switches if both versions exist |
| Browser sends unsupported lang    | Fall back to Dutch                                           |
| Mixed team (NL + EN users)        | Each user sees the app in their own language                 |
