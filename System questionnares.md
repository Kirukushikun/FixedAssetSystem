# Architecture Discovery Questionnaire

Use this as a working doc — fill in what you know, leave blank what you don't, and we'll fill gaps together or make reasonable assumptions where needed. You don't need to answer everything before we start; even partial answers let me begin drafting.

---

## 1. System Overview & Business Context

- **What does the system do, in one paragraph? Who are the primary users?**
  FAMS (Fixed Asset Management System) is an internal web application that tracks every fixed asset owned by BFC Group across six farm locations — from acquisition through assignment, transfer, repair, audit, and disposal. Primary users are IT Admin, Accounting, Division Heads, Farm Staff, Auditors, SME reviewers, and Purchasing personnel.

- **Is this B2B, B2C, internal-only, or a mix?**
  Internal-only. Access is restricted to BFC Group employees via role-based accounts. One external-facing surface exists: the public QR scan page (`/viewasset/{id}`) which allows unauthenticated asset viewing without login.

- **What's the module you specifically need to design/document (since the module is the focus)? How does it fit into the larger system?**
  The entire FAMS platform — 12 modules total: Dashboard, Asset Management, QR Code Management, Employee Management, Transfer Workspace, Disposal Workspace, SME Workspace, Audit Module, System Records, Settings, IT Analytics, and Trash/Archive. The REST API layer is a newer addition that exposes asset and category data to the Purchasing system.

- **What business problem does this module solve? What happens today without it?**
  Without FAMS, asset tracking was manual — spreadsheets, paper forms, and no centralized record of who holds what, where assets are, or when they're due for disposal. The system enforces approval workflows (disposal, transfer) that previously had no audit trail, generates official accountability and transfer documents, and provides a QR-based physical audit trail.

- **Any hard deadlines, launch dates, or reasons this is happening now?**
  System is already live in production at `https://fams.bfcgroup.ph`. Current active work includes: REST API integration with the Purchasing system, IT Analytics module development, and pre-migration data validation with the inventory council before bulk asset import.

---

## 2. Current State

- **What does the existing architecture look like today?**
  Monolith — a single Laravel application serving all modules via server-side rendering with Livewire reactive components.

- **What's the current tech stack?**
  - **Backend:** Laravel 12 (PHP 8.3)
  - **Frontend:** Livewire 3, Alpine.js, Tailwind CSS, Font Awesome, Vite
  - **Database:** MySQL (Eloquent ORM)
  - **Cache:** Redis (`CACHE_STORE=redis`)
  - **Queue:** Redis
  - **Session:** Database (`SESSION_DRIVER=database`)
  - **File Storage:** Laravel Storage, public disk
  - **QR Generation:** SimpleSoftwareIO/simple-qrcode
  - **Excel:** Maatwebsite/Laravel-Excel
  - **External Auth API:** Company-wide identity provider (external REST API for credential validation)
  - **Snipe-IT:** Optional asset sync integration (toggleable)

- **Where is it hosted?**
  Containerized (Docker — confirmed by root@`<container-id>` shell), likely self-hosted on company infrastructure. Production URL: `https://fams.bfcgroup.ph`. Staging URL: `https://fams-staging.bfcgroup.ph`.

- **Do you have any existing diagrams, README docs, or wikis?**
  `SYSTEM_OVERVIEW.md` at the project root — generated as a full narrative module-by-module description. No formal architectural diagrams exist yet.

- **What's working well? What's actively painful?**
  - Working well: Livewire component architecture, role-permission system, QR scan flow, approval workflows
  - Actively addressed: Dashboard query performance (reduced from 18+ individual COUNTs to 3 GROUP BY queries with Redis caching); previously full table `->get()` calls on every render

---

## 3. Scale & Usage

- **Roughly how many users / requests per day?**
  Small internal user base — estimated 20–50 concurrent users at peak across all farms. Six farm locations. Load test baseline: 50–80 virtual users, p(95) latency target under 2s after optimization.

- **Expected growth over the next 6–12 months?**
  Moderate — additional farm staff onboarding as the system rolls out to all locations. No plans for external user growth.

- **Peak load patterns?**
  Business-hours weekday usage. No seasonal spikes. Peak likely around budget periods (annual and mid-year) when asset encoding and purchasing workflows are most active.

- **Current data volume and growth rate?**
  Currently seeded with ~360 test assets, 60 employees. Production data volume depends on migration import (pending inventory council sign-off). Expected eventual asset count: hundreds to low thousands.

---

## 4. Non-Functional Requirements

- **Performance:** Target p(95) < 2s for dashboard and asset list pages. API responses target < 500ms (aided by 1-hour Redis cache on aggregate endpoints).

- **Availability:** Internal tool — 99.5% uptime acceptable. Maintenance windows during off-hours are fine.

- **Scalability:** Vertical scaling sufficient for current scale. No horizontal scaling required at this time. Redis caching added to reduce DB load under concurrent use.

- **Consistency:** Strong consistency required — asset assignments, disposal approvals, and transfer completions must reflect immediately. No eventual consistency tolerance for write operations.

- **Security & Compliance:** Internal company data. No SOC2/HIPAA/GDPR/PCI-DSS requirements stated. Key controls in place: role-based access control, API key authentication for REST API, login attempt lockout (3-strike, 15-minute lockout), full access logging, audit trail on all write actions. `APP_DEBUG=false` enforced in production.

- **Cost constraints:** Self-hosted — no per-request cost model. Minimize external service dependencies.

- **Maintainability:** Small team (1–2 developers). Architecture must stay simple — no Kubernetes, no microservices. Monolith with clear module separation preferred.

---

## 5. Integrations & Dependencies

- **Internal systems:**
  - Purchasing system (new) — consumes FAMS REST API (`/api/v1/assets`, `/api/v1/categories`) to pull FA cards before PR creation
  - Snipe-IT — optional asset sync (toggleable per settings)

- **Third-party APIs:**
  - External Authentication API — company-wide identity provider; FAMS delegates credential validation to this API, then maps the returned user ID to a local FAMS user record
  - Google Fonts (CDN) — loaded in frontend views

- **Legacy systems:** None currently. Pre-migration asset data (spreadsheets) will be imported via the Migration Import tool once validated by the inventory council.

- **Sync or async?**
  - Auth API: synchronous (blocking login flow)
  - Snipe-IT sync: queued (async via Redis queue)
  - Purchasing API: synchronous REST (GET only, read-only)

---

## 6. Data

- **Core entities:**
  `assets`, `employees`, `users`, `roles`, `permissions`, `audits`, `audit_trail`, `access_logs`, `disposal_requests`, `transfer_requests`, `asset_sme_reviews`, `flags`, `history`, `asset_repairs`, `categories`, `subcategories`, `departments`, `generated_forms`, `dynamic_fields`

- **Existing schema:** Fully migrated. 20 Eloquent models with defined relationships. Key identifiers: `ref_id` (FA-YYYY-NNNNN format, auto-generated per asset), `employee_id` (EMP-NNNN format).

- **Data ownership:** Single database. FAMS owns all data. Purchasing system is read-only via API.

- **Reporting/analytics:**
  - Dashboard: real-time aggregate stats (conditions, statuses, farm distribution, alerts) — cached 2 minutes in Redis
  - IT Analytics module: in active development — planned to cover IT asset utilization, lifecycle cost, department/farm performance
  - Exports: Excel exports for assets, employees, audit logs, repair logs (filtered)

---

## 7. Team & Constraints

- **Team size and skill set:**
  1–2 developers. Stack: Laravel, Livewire, Alpine.js, Tailwind CSS, MySQL, Redis. Comfortable with the current monolith architecture.

- **Mandated tech choices:**
  Laravel + Livewire (existing codebase). MySQL (existing database). Redis (already provisioned). Docker (existing deployment).

- **Off the table:**
  No new databases. No microservice extraction. No infrastructure changes without explicit approval.

---

## 8. Trade-offs to Weigh

- **Monolith vs. extracting the API as a separate service:** Currently co-located in the Laravel monolith. Sufficient for current scale. Extraction only warranted if Purchasing system load significantly impacts FAMS response times — not a current concern.
- **SQL vs. NoSQL:** MySQL is the right fit — relational data with multi-table joins (assets ↔ employees ↔ approvals ↔ history). NoSQL not applicable.
- **Sync vs. async for disposal/transfer approvals:** Currently synchronous Livewire actions. Email notifications (planned) will move to async queued jobs.
- **Managed cache vs. self-hosted Redis:** Redis is self-hosted in the same container environment. Managed Redis (e.g., AWS ElastiCache) would improve reliability but adds cost — deferred.
- **Session driver (database vs. Redis):** Currently `SESSION_DRIVER=database`. Switching to Redis would reduce DB load and improve session read performance — recommended future improvement.

---

## 9. Deliverables

- **Format:** Markdown (primary), with supporting diagrams as needed.
- **Diagram types:**
  - [x] System context diagram (C4 Level 1)
  - [x] Container diagram (C4 Level 2)
  - [x] Sequence diagrams for key flows (QR scan-to-audit, disposal approval chain, transfer workflow)
  - [x] ER diagram / data model
  - [ ] Component diagram (C4 Level 3) — optional
  - [ ] Deployment diagram — optional
- **Audience:** Internal engineering team and technical stakeholders (IT management, Accounting lead, Division Heads reviewing workflows).
- **Template/style:** No external template. Follow the narrative style established in `SYSTEM_OVERVIEW.md`.

---

### Minimum to get started
1. **What does the module do?** Tracks full fixed asset lifecycle across 6 farm locations with role-based approvals, QR audit trail, and a read-only REST API for the Purchasing system.
2. **What's the current stack?** Laravel 12 + Livewire 3 + Alpine.js + MySQL + Redis, containerized, self-hosted.
3. **Rough scale?** 20–50 concurrent internal users, hundreds to low-thousands of assets, small team.
4. **Top 1–2 non-functional priorities?** Strong consistency on write operations (approvals, assignments). Maintainability — keep it simple, no new infra.
5. **Deliverable format?** Markdown docs + C4 diagrams + sequence diagrams for key workflows.
