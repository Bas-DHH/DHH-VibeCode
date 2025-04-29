
# Feature Spec – User Management

## 1. Purpose
Allows Admins to manage users and subscriptions for their business, while the platform Super Admin oversees billing, support, and global account control.

## 2. Roles

| Role             | Permissions                                                                 |
|------------------|------------------------------------------------------------------------------|
| **Super Admin**  | Global access for billing, support, impersonation, business deletion, admin handover |
| **Admin**        | Manages a single business:<br>• Tasks<br>• Users<br>• Exports<br>• Subscription |
| **Staff**        | Can view and complete tasks, and view task history (read-only)              |

## 3. Super Admin Capabilities (Internal Only)

| Action                        | Description                                                    |
|-------------------------------|----------------------------------------------------------------|
| **View all businesses**       | Internal dashboard listing all accounts                        |
| **Manage subscriptions**      | View, cancel, reactivate accounts (via Mollie)                 |
| **Impersonate users**         | Debug or support sessions as Admin/Staff                      |
| **Reset passwords**           | Manual override if needed                                     |
| **Delete business**           | Only Super Admin can fully delete an account                  |
| **Reassign Admin ownership**  | Only Super Admin can transfer admin rights if owner leaves    |

## 4. Admin Capabilities

| Action                        | Description                                                                 |
|-------------------------------|-----------------------------------------------------------------------------|
| **Add User**                  | Enter name + email → system sends login link + temp password                |
| **Edit User Info**            | Change name or email                                                       |
| **Delete / Deactivate User** | Revoke access (with confirmation)                                          |
| **Reset Password**            | Send password reset email                                                  |
| **Assign Roles**              | Can choose between Staff or Admin during creation                          |
| **Manage Subscription**       | View billing, update payment, cancel/reactivate (via Mollie)               |

## 5. User Creation Flow

1. Admin inputs:
   - Name
   - Email
   - Role (default: Staff)

2. System:
   - Sends email with login link + temp password
   - Creates account under business
   - Requires new password on first login

✅ Admin cannot demote or remove themselves  
✅ All role changes logged internally (audit optional later)

## 6. Staff Experience

| Action           | Behavior                                               |
|------------------|--------------------------------------------------------|
| Login            | Email + password                                       |
| Forgot Password  | Sends reset email                                      |
| Role limitations | Cannot access task setup, exports, users, or billing   |

## 7. Validation & Security

- Emails must be unique per business
- Passwords: min 8 chars, 1 number
- HTTPS enforced for all login + user actions
- **Brute-force protection**: after 5 failed attempts, short delay or captcha (MVP level)
- No one can change their own role (Admin/Super Admin restriction)

## 8. Edge Cases

- Email typo → Admin can edit or delete
- User never logs in → no status shown (MVP skips active/inactive)
- Too many Admins? → allowed, but all share equal rights
- Locked out Admin → Super Admin can restore access or hand over control

## 9. Export / Audit

- All tasks are logged with user ID + timestamp
- No separate user export in MVP
- Audit trail for user creation/role changes may be logged silently (optional)
