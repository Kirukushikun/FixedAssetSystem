# Fixed Asset Management System (FAMS) — System Overview

> A complete description of what the system does, module by module. Written to give any reader a clear mental picture of the system without needing to open any code or UI.

---

## What is FAMS?

FAMS is an internal web application that tracks every fixed asset owned by the company — from the moment it is acquired, through its entire working life (assignment, transfer, repair, audit), until it is finally disposed of. It serves six farm locations: **BFC, BDL, PFC, RH, BBGC, and HATCHERY**, with each asset, employee, and user scoped to a specific farm.

The system replaces paper-based tracking and manual spreadsheets with a single source of truth, enforces multi-level approval workflows for high-risk actions (disposal, transfer), generates official documents (accountability forms, transfer forms), and produces QR codes that can be physically attached to assets for instant identification.

---

## Who Uses It?

Access is role-based. Every user is assigned one or more roles, and each role grants a specific set of permissions. The key roles in the system are:

| Role | Primary Responsibility |
|---|---|
| **Admin / IT** | Full system access; manages users, roles, settings, and all modules |
| **Accounting** | Encodes new assets, approves disposals at the final stage, processes transfers |
| **Division Head** | First-level approver for disposal and transfer requests from their farm |
| **SME** (Subject Matter Expert) | Reviews asset conditions during employee clearance |
| **Auditor** | Submits physical audit entries for assets via QR scan or direct access |
| **Purchasing** | Schedules external service/repair; manages SME-related activities |
| **Farm Staff** | Views assets; initiates transfer or disposal requests for their farm |

A user marked **Admin** in the system bypasses all permission checks entirely and has full access to everything.

---

## Module Breakdown

---

### 1. Dashboard

**Who:** Any user with `dashboard.view` permission (typically Admin, Accounting, Division Heads)

**What it shows:**

The dashboard is the system's home screen — a live summary of the entire asset inventory at a glance. It is divided into several visual panels:

- **Totals strip** — Four key numbers across the top: total active assets, assets currently assigned to an employee, total employees, and pending clearance flags.
- **Asset Condition chart** — A horizontal bar breakdown of how many assets are in each condition: Good, Defective, Under Repair, and For Replacement.
- **Asset Status chart** — A percentage breakdown of all status values: Available, Issued, Transferred, For Transfer, For Disposal, Disposed, and Lost.
- **Farm Distribution** — A visual breakdown of how many assets are held by each farm location, including a percentage of the total inventory.
- **Alerts panel** — Automatically surfaced warnings, such as assets that have been marked Lost, assets stuck in Repair for more than 30 days, or assets still assigned to employees who have already been marked deleted (unreturned items).
- **Category browser** — A collapsible list of all asset categories with sub-categories, letting the user drill into counts per category.

**Export from Dashboard:**

Users with export permission can download a filtered Excel file of assets directly from the dashboard by selecting:
- Category Type (IT or NON-IT)
- Category and Sub-category
- Farm, Department
- Asset age range (minimum and maximum years)

All aggregate figures on the dashboard are cached for 2 minutes to reduce database load under concurrent users.

---

### 2. Asset Management

**Who:** Any user with `assets.view`; create/edit/delete/import/export/audit gated separately

**What it is:**

The core module of the system. This is where all assets live — every physical item the company owns that falls above the ₱20,000 threshold (or is otherwise recorded, even if expensed). Each asset is represented by a single record containing:

- **Identification:** Auto-generated Reference ID (format: `FA-YYYY-0001`), serial number, brand, model
- **Classification:** Category Type (IT or NON-IT), Category (e.g., IT Equipment, Vehicles), Sub-category (e.g., Laptop, Forklift)
- **Lifecycle data:** Acquisition date, item cost, depreciated value, usable life (in years)
- **Status:** Available / Issued / Transferred / For Transfer / For Disposal / Disposed / Lost
- **Condition:** Good / Defective / Repair / Replace
- **Assignment:** The employee currently holding the asset, their farm and department
- **Location:** Physical location notes
- **IT-specific technical data:** For IT assets — processor, RAM, storage, IP address, MAC address, VPN address (stored as structured JSON, extensible with dynamic fields)
- **Attachments:** File attachments (purchase receipts, photos, documents)
- **Remarks:** Free-text notes

**The Asset Table:**

The main asset list is a searchable, filterable, paginated table. Users can filter by:
- Keyword search (searches across ref ID, brand, model, serial)
- Category Type, Category, Sub-category
- Farm, Department
- Status, Condition
- Acquisition date range
- Cost range

**Asset Actions:**

- **Create** — Opens a form to encode a new asset. Category type selection drives which fields appear (IT assets show the technical data section; NON-IT hides it). An employee can be assigned immediately on creation.
- **Edit** — Modify any field on an existing asset. Saving records a history entry automatically.
- **View** — Read-only detail page for the asset, showing all data including the full assignment history and audit log.
- **Audit** — Submit a physical audit entry (see Audit section below).
- **Delete** — Soft-delete (moves to Trash); the asset is hidden from all views but recoverable.

**Repairs:**

Each asset has a repair log. Repair records track the date, type of repair, cost, notes, source (internal/external), and optional service report attachment. Repair history is visible inside the asset detail view.

**Transfer and Assignment:**

Assets can be reassigned to a different employee directly from the edit form. For cross-farm or formal transfers, the dedicated Transfer Workspace handles the approval chain.

**Import / Export:**

- **Export:** Filtered Excel download of assets, audit logs, or repair logs
- **Import:** Bulk asset upload via Excel template (standard import for normal operations; a separate Migration Import is available for initial data loading with pre-validation)

---

### 3. QR Code Management

**Who:** Users with `assets.qr` permission

**What it does:**

Every asset in the system can have a QR code generated and attached to it. The QR Code Management module gives users a dedicated workspace to manage the physical rollout of QR labels across the asset inventory.

The module shows all assets in a list with two toggle columns:
- **Printed** — Has the QR sticker been printed?
- **Affixed** — Has the sticker been physically attached to the asset?

Users can mark individual assets or select multiple assets in bulk to mark as printed, then send them to a print-ready layout. The print view renders all selected QR codes in a grid format suitable for label paper.

**QR Scan Flow:**

When a QR code is scanned (e.g., by a mobile phone camera), the user is taken to a public-facing asset page — no login required. This page shows:
- The asset's reference ID, category, brand, model, status, and condition
- Assignment information if the asset is currently issued
- A choice modal for unauthenticated visitors with two options:
  - **"View Asset Details"** — Dismisses the modal and shows the basic info page
  - **"I'm an Auditor"** — Redirects to the login page with a post-login redirect so the auditor lands directly on the pre-filled audit form for that asset after signing in

Authenticated auditors who scan a QR code skip the modal entirely and land directly on the audit form.

---

### 4. Employee Management

**Who:** Users with `employees.view`; edit/import/export gated separately

**What it does:**

Manages the list of people assets can be assigned to. An employee record contains:
- Employee ID (unique identifier)
- Full name
- Position / job title
- Farm assignment
- Department

Employees are not system users — they are the custodians of physical assets. A system user (someone who logs in) is a separate concept from an employee.

**Employee Table:**

Searchable and filterable by farm, department, position, and flag status. Users can create, edit, and delete employees. Deleting marks the employee as deleted but does not remove them from the database — their assigned assets are flagged as unreturned and surface as alerts on the dashboard.

**Employee Detail View:**

Clicking into an employee shows:
- All assets currently assigned to them
- Any active flags on their record (Pending Clearances, Damaged Asset, Lost Asset, etc.)
- SME review history for their assets
- Options to unassign individual assets or bulk-unassign all

**Flags:**

Employees can be manually flagged for conditions like Pending Clearances or Under Investigation. Flags are created manually by authorized users or automatically triggered by SME reviews. Each flag has a type, associated asset (if applicable), source, and remarks. Flags can be resolved individually or all at once from the employee detail view.

**Forms:**

From the employee module, authorized users can generate official documents:
- **Accountability Form** — Lists all assets assigned to a specific employee; used for HR handover and new employee onboarding
- **Transfer Form** — Documents asset reassignment between employees

A document library stores all generated forms with their snapshots (point-in-time data captured at generation time).

**Import / Export:**

Employees can be bulk-imported via Excel and exported to Excel.

---

### 5. Transfer Workspace

**Who:** All farm staff (to request); Division Heads (to approve); Accounting (to complete)

**What it does:**

Handles the formal process of moving an asset from one employee to another, particularly when the transfer crosses farm boundaries or requires managerial approval. The workspace is organized into tabs based on the user's role.

**Workflow:**

1. A farm user submits a transfer request, identifying the asset and the target employee. They can choose an internal transfer (same farm) or an external transfer (different farm and department).
2. The request goes to the **Division Head tab** where a division head reviews and approves it. If the requester is themselves a Division Head, this step is skipped automatically.
3. Once approved, it moves to the **Accounting tab** where Accounting completes the transfer — this physically reassigns the asset to the new employee in the database, updating the assigned employee, farm, department, and history record.

**Status Flow:**
```
Submitted → DH Approval → For Transfer → Transferred
```

All completed transfers are visible in a History tab for audit trail purposes.

---

### 6. Disposal Workspace

**Who:** Farm staff (request); Division Head (first approval); VP/SVP (final approval); Accounting (mark disposed)

**What it does:**

Manages the lifecycle endpoint for assets that are no longer serviceable, obsolete, or need to be written off. Every disposal goes through a multi-level approval chain before an asset is officially marked as Disposed.

**Workflow:**

1. A farm user submits a disposal request, specifying the asset, the reason for disposal, and optionally attaching supporting documentation (photo, service report, etc.).
2. The request routes to the **Division Head** for first-level approval.
3. After Division Head approval, it goes to the **VP/SVP** for final authorization — this is mandatory for all disposals regardless of asset value.
4. Once VP-approved, the asset status changes to **"For Disposal"**. Accounting then processes the final disposal and marks the asset as **"Disposed"** in the system.

**Status Flow:**
```
Submitted → Pending Division Head Approval → Pending VP Approval → VP Approved → Disposed
```

If the requester has the Accounting role, the Division Head step is skipped and the request goes directly to VP Approval.

A **Disposal Form** can be printed as an official document at any stage.

---

### 7. SME Workspace

**Who:** Users with `sme.view` and `sme.review` (typically Purchasing or designated SME personnel)

**What it does:**

The Subject Matter Expert workspace is a clearance-review tool used when an employee is leaving or being reviewed. It is explicitly labeled as a clearance-only tool — not a general asset inspection workflow.

**How it works:**

1. The SME selects an employee from a filtered list (by farm and department).
2. The system loads all assets currently assigned to that employee.
3. For each asset, the SME fills out:
   - **Condition Note** — Their assessment of the current condition (Good, Repair, Defective, Replace, For Disposal, Lost)
   - **Remarks** — Free-text notes
   - **Recommended Flag** — If the asset warrants a flag (e.g., Pending Clearances, Damaged Asset)
   - **Flag employee directly** — Checkbox to immediately create the flag on the employee's record from this review
4. Saving creates an `AssetSmeReview` record for each asset. If flagged, it creates or reuses an existing Flag record (duplicates are suppressed).

SME review history is visible from the Employee detail view.

---

### 8. Audit Module

**Who:** Users with `assets.audit`

**What it does:**

The Audit module records physical verification entries for assets. An audit entry documents that a specific person physically confirmed the existence and condition of an asset at a specific point in time.

Each audit entry captures:
- Farm and location where the asset was found
- The auditor's name (auto-populated from logged-in user)
- Date and time of the audit
- Next scheduled audit date
- Condition finding (observation notes)
- Optional file attachment (photo or report)

Audits are accessible from within the Asset Management form (via the audit mode) or directly via QR scan. The latest audit entry for each asset is always surfaced on the asset detail view, and a full audit history is available per asset.

An **Audit Log Export** is available as a filtered Excel download for compliance reporting.

---

### 9. System Records

**Who:** Users with `audit.view`, `activity.view`, or `users.view` (any one is sufficient to access the page)

**What it does:**

A read-only records center with three sub-sections:

**Audit Trail:**
Every significant action in the system (create, update, delete, approve, assign, etc.) is logged automatically. The audit trail shows who did what and when, with date/time filters and pagination. Exportable to Excel.

**User Activity Log (Access Logs):**
Tracks every login attempt — successful or failed — recording the email used, success/failure, IP address, and user agent (browser/device). This is the security log for detecting unauthorized access attempts.

**User Management:**
(When user has `users.view` permission) Shows all system users, their assigned roles, and provides controls to:
- Grant or revoke system access
- Promote a user to Admin
- Update user details
- Sync with the external authentication API

---

### 10. Settings

**Who:** Users with `settings.view`

**What it does:**

The Settings module is split into two areas:

**System Configuration:**
- **Snipe-IT Sync Toggle** — The system can optionally sync asset data to a Snipe-IT instance (external asset management platform). This toggle enables or disables automatic synchronization. Enabling requires confirmation; disabling is immediate.

**Dynamic Value Management:**
Administrators can manage the master data lists that populate dropdowns throughout the system:
- **Categories** — Top-level asset categories (e.g., IT Equipment, Vehicles, Office Furniture) with icons and codes
- **Sub-categories** — Sub-items under each category, each tagged as either IT or NON-IT type
- **Departments** — The list of departments employees can belong to
- **Input Fields** — Custom dynamic fields that appear on IT asset forms (e.g., new technical specifications)

All four lists follow the same CRUD pattern: add, edit, delete, with immediate reflection across the system.

---

### 11. IT Analytics

**Who:** Users with `analytics.view`

**Current Status:** This module is in active development. The page exists and is accessible to authorized users, but live data visualizations are still being built.

**Planned Scope:**
The IT Analytics module is intended to give IT management a specialized view of the IT asset portfolio specifically — separate from the general dashboard. Planned metrics include:
- Total IT assets and utilization rate
- Average lifecycle cost per IT asset category
- Department-level and farm-level IT asset performance
- Trend data over time (acquisitions, disposals, repairs)

The underlying data infrastructure (category taxonomy, seeded test data) is already in place to support these visualizations when development is complete.

---

### 12. Trash / Archive

**Who:** Admin and authorized users

**What it does:**

Assets that are deleted from the main asset table are not permanently removed — they are soft-deleted (marked with `is_deleted = true`) and moved to the Trash. The Trash module shows all soft-deleted assets and gives administrators two options per asset:
- **Restore** — Returns the asset to the active asset table
- **Permanently Delete** — Removes the record from the database entirely (irreversible)

This ensures no accidental data loss and provides a recovery path for mistakes.

---

## Cross-Cutting Features

### Notifications

Every significant action in the system — saving, approving, failing a validation — triggers an in-app toast notification. Notifications have a type (success or failure), a header, and a message. They appear in the corner of the screen and auto-dismiss.

### Confirmation Modals

Any destructive or irreversible action (delete, approve, dispose, transfer completion) is gated behind a confirmation modal. The user must explicitly confirm before the action executes.

### History Tracking

Every time an asset changes status, condition, assigned employee, farm, or department, a history record is created automatically. This gives a complete timeline of where an asset has been and who has held it — visible from the asset detail view.

### Farm Scoping

Most users are scoped to their assigned farm. When a user with farm scoping enabled views the asset list, transfer workspace, or other modules, they only see assets belonging to their farm. Admin, Accounting, and IT roles operate across all farms with no scope restriction.

### Authentication

The system supports two authentication modes:
- **Local mode** — Username and password stored in the system's own database
- **External API mode** — Credentials are validated against an external authentication API (company-wide identity provider), then the system looks up the corresponding FAMS user record

Both modes enforce a **3-strike lockout**: after 3 consecutive failed login attempts, the account is locked for 15 minutes. All login attempts (success and failure) are logged.

### QR-to-Audit Public Flow

Any asset's QR code can be scanned by anyone with a camera — no login required to view basic asset info. Auditors who scan a code are guided through a login prompt that drops them directly on the audit form for that exact asset after signing in, with no need to search or navigate.

---

## Data Architecture Summary

| Entity | Purpose |
|---|---|
| `assets` | The core inventory — every physical asset |
| `employees` | Asset custodians (not system users) |
| `users` | System login accounts with roles |
| `roles` / `permissions` | Role-based access control |
| `audits` | Physical verification records per asset |
| `audit_trail` | System action log (who did what) |
| `access_logs` | Login attempt log |
| `disposal_requests` | Disposal approval chain per asset |
| `transfer_requests` | Transfer approval chain per asset |
| `asset_sme_reviews` | SME clearance review records |
| `flags` | Employee-level flags (Pending Clearances, etc.) |
| `history` | Asset assignment/status change timeline |
| `repairs` | Repair records per asset |
| `categories` / `subcategories` | Master taxonomy (14 categories, IT/NON-IT typed) |
| `departments` | Department master list |
| `generated_forms` | Accountability and transfer form snapshots |
| `dynamic_fields` | Extensible IT asset custom fields |
