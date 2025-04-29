
# Feature Spec – Subscription & Billing

## 1. Purpose
Manages access to DHH through a free trial and paid monthly subscription using Mollie. Ensures that each business has an active payment status before accessing hygiene tools.

## 2. Access & Roles

| Role             | Access to Billing |
|------------------|-------------------|
| **Admin**        | ✅ Full access to manage their own subscription |
| **Staff**        | ❌ No billing access |
| **Super Admin**  | ✅ Global access to view/manage all accounts via internal tools |

## 3. Billing Flow

| Stage                         | Behavior                                                                 |
|-------------------------------|--------------------------------------------------------------------------|
| **Business Registration**     | Admin enters name, email, business name, and payment info                |
| **Trial Start (14 Days)**     | Trial starts after payment info is entered (no card = no trial)         |
| **Conversion to Paid Plan**   | Automatic billing after 14 days unless cancelled                         |
| **Recurring Billing**         | Monthly, via Mollie auto-renewal                                         |
| **Cancellation**              | Ends auto-renewal; access continues until end of current period          |

## 4. Admin Subscription Controls

Located under Admin Panel > Subscription

| Option                      | Description                                      |
|-----------------------------|--------------------------------------------------|
| **View Status**             | Trial / Active / Cancelled / Expired            |
| **Update Payment Method**   | Opens Mollie-hosted checkout                    |
| **Cancel Subscription**     | Cancels auto-renewal, retains access until period ends |
| **Reactivate Subscription** | Available after cancellation or expiration      |

## 5. Super Admin Capabilities

| Action                     | Description                                     |
|----------------------------|-------------------------------------------------|
| **View All Subscriptions** | List of businesses, status, trial start/end     |
| **Manually Cancel / Extend** | Can override billing status for support        |
| **Access Mollie Dashboard**| View logs, invoice PDFs, refunds, etc.          |

## 6. Validation & Access Logic

| Condition               | Access Behavior                                                   |
|-------------------------|--------------------------------------------------------------------|
| **No payment or expired trial** | Access locked → redirected to reactivation screen         |
| **Trial active**        | Full access                                                        |
| **Subscription active** | Full access                                                        |
| **Cancelled (post-period)** | Locked out with reactivation option                           |

## 7. Reactivation Flow (After Lockout)

When a business is locked out:
- Admin can still log in, but is shown a Reactivation Page instead of the dashboard
- This page shows:
  - Current status (e.g., “Your trial has expired”)
  - Last payment/expiry date
  - Button: “Reactivate Now” → redirects to Mollie
  - Link: Contact support (email Super Admin)

✅ No access to tasks or exports until billing is restored  
✅ Reactivation restores full functionality immediately after payment confirmation

## 8. Edge Cases

| Scenario                     | Behavior                                           |
|------------------------------|----------------------------------------------------|
| **Payment fails**            | Mollie handles retry; optional email alert to Admin |
| **Business inactive 30+ days** | Optional flag for Super Admin to follow up       |
| **Refund requested**         | Manual support via Super Admin                     |
| **Duplicate business signup**| Super Admin can merge or delete manually           |

## 9. Export / Reporting (Super Admin Only)

| Feature                     | Description                                    |
|-----------------------------|------------------------------------------------|
| Trial and conversion report | Businesses in trial / conversion rate          |
| Cancellation report         | With optional reason tagging                   |
| Payment overview            | View in Mollie (API optional later)            |
