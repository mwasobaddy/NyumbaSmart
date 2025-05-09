Rental Management System - Product Requirements Document
1. Executive Summary
Web-based platform enabling Kenyan landlords to manage properties digitally.
Key Objectives:

Automate rent collection (M-Pesa focus)
Streamline tenant-property owner communications
Provide financial tracking/reporting
Reduce dependency on property agencies

Target Users:
Landlords (Primary)
Tenants (Secondary)
Property Secretaries
Admin Users

2. Market Opportunity
Kenyan Rental Landscape
80% Nairobi residents rent
$1.2B+ annual rental market

Gaps in Existing Solutions:
Poor service charge transparency
Limited mobile-first experiences
High agency fees (5-12% of rent)
Competitive Analysis
Feature	Our System	Bomahut	EazzyRent
M-Pesa Integration	Full	Partial	None
Tenant Screening	API Ready	Manual	Basic
Custom Branding	Yes	No	Paid Add-on
Maintenance Workflow	Automated	Email	SMS

3. Core Features
3.1 Subscription Packages
Tier	Price (KES)	Properties	Key Features
Mtaa	500-1,000	≤10	Basic reporting, 1 user
Chaguo	2,000-3,500	11-50	+ Maintenance tracking, 3 users
Premium	4,000-6,000	51-100	+ API access, custom reports
Enterprise	Custom	100+	White-label, dedicated support
Payment Options:
Phase 1: M-Pesa (Simulated → Live via STK Push)
Phase 2: Stripe/PayPal/Card
3.2 Landlord Portal
Property Management:
Add properties/units with photos
Set individual rents + utility rates
Track vacancies (calendar view)
Financials:
// Rent calculation logicpublic function calculateRentDue(
  float $baseRent, 
  float $utilities, 
  int $lateDays): float {
  return $baseRent + $utilities + ($lateDays * $baseRent * 0.05);}

Maintenance:
Tenant-reported issues → Vendor dispatch
Budget tracking with photo evidence
Analytics:
Real-time dashboards (Occupancy, Cash Flow)
Exportable reports (PDF/Excel)

3.3 Tenant Portal
Key Flows:
Rent Payment → M-Pesa (Simulated)
Repair Requests → Photo upload + status tracking
Vacate Notice → 30-day counter
Document Access → Lease agreements + receipts
Mobile Optimization:
USSD fallback for feature phones
SMS notifications
o
3.4 Admin System

RBAC Implementation:
php artisan permission:create-role landlord
php artisan permission:create-role secretary
php artisan permission:create-role tenant

Audit Trails:
Track all financial transactions
Login attempts logging

4. Technical Architecture
4.1 Stack Components
Layer	Technology	Version
Backend	Laravel	12.x
Frontend	Livewire Volt + Alpine	3.x
Styling	TailwindCSS	3.3.x
Database	MySQL (Prod)	8.0
Security	Spatie Permissions	6.x
Animations	GSAP	3.12.x
4.2 Key Integrations
M-Pesa API:
STK Push for payments
C2B transaction confirmation

SMS Gateways:
Africa's Talking (Primary)
Twilio (Fallback)

Third-Party Services:
Google Maps → Property locations
DocuSign → Lease agreements
Xero/QuickBooks → Accounting
o
4.3 Security Measures
Data Protection:
AES-256 encryption (database)
TLS 1.3+ (in transit)

Access Control
Session timeout: 15m (landlords)
2FA via TOTP (Google Authenticator)

Compliance:
GDPR (EU tenants)
Kenya Data Protection Act
o

5. Implementation Roadmap
Phase 1: Core System (8 Weeks)
Auth System (2w)
Property/Tenant CRUD (2w)
M-Pesa Simulation (1w)
Basic Reporting (1w)
Testing/QA (2w)

Phase 2: Advanced Features (6 Weeks)
Vendor API Integrations
Mobile App (React Native)
Swahili Localization
Load Testing (1k+ users)

6. Deployment Strategy
6.1 Environments
Environment	Database	URL	Purpose
Local	SQLite	http://localhost:8000	Development
Staging	MySQL	stage.kenrent.co.ke	Client Reviews
Production	MySQL	app.kenrent.co.ke	Live Users
6.2 Server Requirements

Ubuntu 22.04 LTS


PHP 8.2+ with Extensions:

bash

Copy

Download

sudo apt install php8.2 php8.2-{mbstring,curl,xml,mysql}


Nginx Configuration:

nginx

Copy

Download

location / {
    try_files $uri $uri/ /index.php?$query_string;
    add_header X-Content-Type-Options "nosniff";}

6.3 CI/CD Pipeline
bash
Copy
Download
# Deployment Scriptcomposer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

7. Testing & Validation
7.1 Test Cases
Module	Test Type	Tools Used
Payment Gateway	E2E	PestPHP + Mockoon
Load Handling	Performance	k6 (1k+ VUs)
Mobile Views	Responsiveness	BrowserStack
7.2 Success Metrics
95% on-time rent collection
30% faster maintenance resolution
4/5 User Satisfaction Score
99.9% system uptime

8. Appendices
8.1 Entity Relationship Diagram
8.2 API Endpoints
Endpoint	Method	Description
/api/rent/pay	POST	Initiate M-Pesa payment
/api/maintenance	GET	List open repair tickets
/api/reports	GET	Generate financial PDF


