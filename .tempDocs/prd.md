Product Requir ements Document: Web-Based Rental Management System
1. Introduction:
2. 1. Purpose and Scope of the Product Requirements Document This document
meticulously details the product requirements for a web-based Rental Management
System. The primary purpose of this system is to empower landlords to efficiently
manage their rental properties independently, thereby reducing their reliance on
traditional property management agencies. The scope of this document encompasses
all functional and non-functional requirements for the system, covering features
designed for both landlords and tenants, as well as essential administrative
functionalities. This PRD serves as the guiding document for the development team
throughout the entire software development lifecycle, ensuring that the final product
aligns with the defined objectives and user needs. 2. Overview of the Rental Management System The Rental Management System is
envisioned as a comprehensive web-based platform designed to streamline the process
of managing rental properties. The system's core functionality will include facilitating
online rent and utility payments, efficiently managing maintenance and repair
requests, providing robust financial tracking capabilities, enabling the scheduling of
property renovations, and offering a user-friendly interface for landlords to list their
vacant units to attract prospective tenants. By providing these features in a centralized
online platform, the system aims to simplify property management for landlords and
enhance the overall rental experience for tenants. 3. Target Users and Their Needs
1. Landlords/Property Owners: The primary target users are individual landlords or
small businesses that own and manage rental properties. These users require tools to
efficiently handle various aspects of property management, including:
1. Collecting rent and managing their finances, including tracking income and expenses. 2. Communicating effectively with their tenants regarding payment reminders, maintenance updates, and other tenancy-related inquiries. 3. Gaining clear insights into the performance of their properties, such as occupancy
rates, income trends, and expense breakdowns. 4. Planning, scheduling, and tracking property maintenance and renovation projects to
maintain and improve their assets. 5. Easily listing their vacant properties online to attract a wider pool of potential tenants
and minimize vacancy periods. 2. Tenants: The secondary target users are individuals who reside in properties managed
by landlords utilizing the system. Their primary needs include:
1. A secure and convenient online platform for making rent, utility, and fee payments, offering flexibility and eliminating the need for traditional payment methods like
checks or cash.
2. Transparent access to their complete payment history, allowing them to easily track
their financial obligations and payment records. 3. A simple and effective way to report maintenance and repair issues to their landlords, along with the ability to track the progress of their requests and receive timely updates. 4. Technology Stack Overview The Rental Management System will be built using a
modern and robust technology stack, carefully selected for its capabilities and
suitability for the project requirements:
1. Lar avel: This PHP framework has been chosen as the foundational backend
technology due to its elegant syntax, extensive built-in features, strong security
measures, and a large, supportive community. Laravel's Model-View-Controller
(MVC) architecture will promote code organization and maintainability, providing a
solid and scalable foundation for the application. 2. Livewir e: To create a dynamic and interactive user interface without the complexity
of traditional JavaScript frameworks, Livewire, a full-stack framework for Laravel, has been selected. Livewire allows developers to build reactive frontend components
using the familiarity of Blade templates and PHP, resulting in a smoother and more
engaging user experience for both landlords and tenants. 3. TailwindCSS: For rapid and consistent styling of the application's frontend, TailwindCSS, a utility-first CSS framework, will be utilized. Tailwind's pre-designed
utility classes will enable the development team to quickly create a modern, responsive, and visually appealing design while maintaining consistency across all
components of the system. 4. Spatie Roles & Permissions: To implement granular control over user access and
authorization, the Spatie Roles & Permissions package for Laravel will be integrated. This package provides a flexible and powerful way to manage user roles (e.g., Landlord, Tenant, Secretary) and their associated permissions, ensuring that different
user types have appropriate levels of access to the system's features and
functionalities. 3. Goals and Objectives:
4. 1. Primary Goals for Landlords and Tenants Using the System
1. Landlords:
1. The primary goal for landlords is to achieve a significant enhancement in their
operational efficiency by leveraging the system's automation capabilities for routine
tasks such as rent collection, sending payment reminders, and managing tenant
communications. This automation will reduce the time spent on administrative
overhead, allowing landlords to focus on more strategic aspects of their rental
business. 2. Another key goal is to reduce the overall costs associated with managing rental
properties. By providing a platform for independent management, the system aims to
minimize or eliminate the fees typically charged by property management agencies.
Additionally, the automation of tasks will lead to savings in time and resources
compared to manual processes. 3. Improving tenant relations is also a crucial goal. The system will provide tenants with
convenient online services, such as easy payment options and efficient
communication channels for maintenance requests and other inquiries. This enhanced
tenant experience is expected to lead to higher tenant satisfaction levels and improved
tenant retention rates. 4. The system aims to empower landlords with data-driven insights into the performance
of their rental properties. By offering comprehensive analytics and reporting tools, landlords will be able to track key performance indicators (KPIs), understand trends
in occupancy, income, and expenses, and make informed decisions to optimize their
rental business strategies. 5. Finally, a primary goal is to simplify the often complex process of financial
management for rental properties. The system will provide an intuitive and robust
platform for tracking rental income, categorizing and managing expenses (including
repair costs), and generating financial reports, thereby improving overall financial
control and streamlining tax preparation. 2. Tenants:
1. The primary goal for tenants is to provide a secure and user-friendly platform that
offers convenient online payment options for rent, utilities, and other associated fees. This will eliminate the need for traditional, often cumbersome, payment methods and
provide tenants with greater flexibility in managing their payments. 2. Another key goal is to ensure information transparency. The system will provide
tenants with easy and direct access to their complete payment history, allowing them
to review past transactions and understand their financial obligations. Additionally, important lease-related documents will be readily accessible within the tenant portal. 3. Facilitating seamless communication with landlords is also a primary goal. The
system will offer straightforward channels for tenants to report maintenance and
repair issues, submit notices of vacating, and make other tenancy-related inquiries, ensuring efficient and timely responses from their landlords. 2. Key Objectives and Success Metrics for the System The success of the Rental
Management System will be measured against several key objectives and their
corresponding metrics:
1. Landlord Adoption Rate: The objective is to achieve a 50% adoption rate among the
targeted landlord demographic within the first year following the system's launch. This will be measured by tracking the number of registered landlords who actively
use the system to manage their properties. 2. Tenant Engagement: A key objective is to drive active tenant engagement with the
platform. Success will be measured by achieving an average of 2 logins per tenant per
month and a 70% utilization rate for the online payment features within the first six
months of launch. 3. Payment Collection Rate: The system aims to improve the efficiency of rent
collection. The objective is to increase the percentage of rent collected on time to 95%
within the first year of operation, as tracked through the system's payment records.
4. Maintenance Request Resolution Time: A significant objective is to streamline the
handling of maintenance issues. Success will be measured by achieving a 30%
reduction in the average time taken to resolve reported repair issues (from submission
to completion) within the first six months. 5. System Uptime: To ensure a reliable service, the objective is to maintain a system
uptime of 99.9%, minimizing any disruptions to property management activities for
both landlords and tenants. This will be monitored through system logs and uptime
monitoring tools. 6. User Satisfaction Scor es: The overall satisfaction of both landlords and tenants is
paramount. The objective is to achieve an average satisfaction score of 4 out of 5 stars
based on feedback collected through quarterly user surveys and in-app feedback
mechanisms. 5. Detailed System Featur e Requir ements:
6. 1. User Authentication and Authorization:
2. 1. Landlord Registration, Login, and Profile Management The system will provide a
user-friendly registration form accessible to prospective landlords. This form will
collect essential information, including the landlord's full name, a valid email address
for communication, a primary phone number, and an initial indication of the number
of properties they intend to manage using the platform. During the registration
process, landlords will be required to create a secure password that meets predefined
complexity requirements, such as a minimum length and the inclusion of a mix of
character types. They will also be strongly encouraged to utilize strong, unique
passwords for enhanced security. Upon successful registration, landlords will be able
to log in to their accounts using their registered email address and the password they
created. Once logged in, landlords will have access to a dedicated profile management
section where they can update their personal information, such as contact details, and
add or modify details about the properties they manage, including property addresses, the number of units within each property, and other relevant information. 2. Tenant Registration, Login, and Profile Management Tenants can be registered into
the system by their landlords. Landlords will have the ability to add tenants by
providing their basic contact information, including name, email address, and phone
number. Upon registration by the landlord, the system will automatically generate an
invitation email containing initial login credentials (a temporary password and
instructions on how to set a permanent password) and a link to access the tenant portal. In scenarios where the system includes a public rental listing portal, there might be an
option for prospective tenants to directly sign up if they are interested in a specific
property. This self-registration flow would require them to provide necessary
identification and contact details, which would then be reviewed and approved by the
landlord. Tenants will be able to log in to their personalized portal using the
credentials provided by their landlord or the credentials they created during their self-
registration process. Once logged in, tenants will have access to a profile management
section where they can update their personal contact details, such as their phone
number or email address, ensuring their information remains current. 3. Admin User Roles (Landlord/Secretary) and Permissions Management using Spatie
Roles & Permissions The Rental Management System will implement a robust role- based access control (RBAC) system utilizing the Spatie Roles & Permissions
package for Laravel. This package offers a flexible and efficient way to manage user
roles and their associated permissions within the application. A primary Landlord role
will be defined, granting comprehensive permissions to manage all aspects of their
properties and tenancies. These permissions will include the ability to create, read, update, and delete tenant records, manage financial transactions related to their
properties, handle maintenance and repair requests submitted by tenants, create and
manage property listings on the public portal, and assign and manage user roles (such
as the Secretary role) for their managed properties. Landlords will have the ability to
assign a Secretary role to other users, such as administrative staff or trusted
individuals who assist with property management tasks. The permissions granted to
the Secretary role will be configurable by the landlord, allowing them to delegate
specific responsibilities while maintaining control over sensitive data and critical
functionalities. For example, a landlord might grant a secretary permission to review
and confirm utility charges before they are included in tenant invoices but restrict
their access to financial reports or the ability to modify property listings. The Spatie
Roles & Permissions package will be instrumental in defining and enforcing these
roles and their associated permissions throughout the application, ensuring secure and
appropriate access control to various features and functionalities based on the user's
assigned role within the system.1
4. Security Requirements: Secure Password Management and Session Handling
(incorporating Research Task 1)
1. Secur e Password Management:
1. Laravel's built-in Hash facade, which utilizes the bcrypt hashing algorithm, will be the
primary method for securely hashing all user passwords before storing them in the
system's database.11 This ensures that even if the database were to be compromised, the stored passwords would remain protected and extremely difficult to decipher. 2. During the password creation process, the system will enforce strong password
policies. Users will be required to create passwords with a minimum length of 8
characters and will be strongly encouraged to include a combination of uppercase and
lowercase letters, numbers, and special symbols to maximize password strength.11
3. A secure and user-friendly password reset functionality will be implemented. This
will allow users who have forgotten their passwords to regain access to their accounts
through a secure process involving the generation and sending of a unique, time- sensitive password reset link to their registered email address. 4. The system will never store user passwords in plain text format, adhering to
fundamental security best practices and minimizing the risk of password exposure. 5. Insight: To further enhance the security of user accounts and promote better password
management practices, the system should provide clear guidance and readily
accessible links to reputable password manager applications such as Bitwarden 14 and
Proton Pass 16 during the user registration process and within the account profile
settings. These tools offer valuable features like the generation of strong, unique
passwords, secure encrypted storage of credentials, and automatic form filling, which
can significantly reduce the risk of users relying on weak or reused passwords and
simplify the login process. 1. Chain of Thought: Many users find it challenging to create and remember strong, unique passwords for every online service they use, often leading to the adoption of
insecure practices like password reuse. By proactively recommending and providing
direct links to trusted password manager applications, the Rental Management System
can empower users to adopt more secure password management habits, thereby
significantly reducing the overall risk of password-related vulnerabilities and
enhancing the security posture of the platform. 2. Secur e Session Handling:
1. Laravel's robust session management will be utilized to generate secure session
identifiers (IDs) for all authenticated users. These IDs will be created using
cryptographically secure pseudo-random number generators, ensuring a high level of
uniqueness and unpredictability, making them extremely difficult for malicious actors
to guess or forge.18
2. Session cookies, which are used to store the session ID on the user's web browser, will be configured with several critical security flags. The HttpOnly flag will be set to
prevent client-side scripts, such as JavaScript, from accessing the cookie, effectively
mitigating the risk of cross-site scripting (XSS) attacks that could potentially steal
session IDs. The Secure flag will be enabled to ensure that the session cookie is only
transmitted over HTTPS connections, encrypting the data in transit and protecting it
from eavesdropping. Additionally, the SameSite attribute will be set to Strict to
provide robust protection against cross-site request forgery (CSRF) attacks, ensuring
that the session cookie is only sent with requests originating from the same domain.11
3. Appropriate session expiration times will be configured to strike a balance between
security and user convenience. An idle timeout of 2 hours will be implemented, automatically terminating a user's session after a period of inactivity to prevent
unauthorized access if the user forgets to log out. An absolute timeout of 8 hours will
also be set, limiting the maximum duration of a session, even if the user remains
active, further reducing the window of opportunity for potential session hijacking.20
4. All communication involving the transfer of session data between the user's browser
and the server will be conducted over HTTPS (Hypertext Transfer Protocol Secure) to
ensure end-to-end encryption, safeguarding the session ID and other sensitive
information from interception by malicious parties.20 HTTP Strict Transport Security
(HSTS) will be implemented to instruct web browsers to always access the
application via HTTPS, even if the user attempts to use HTTP, providing an
additional layer of security. 5. Upon successful user login, the system will regenerate the session ID to prevent
session fixation attacks, a type of vulnerability where an attacker attempts to hijack a
user's session by tricking them into using a pre-established session ID.11
Regenerating the ID after login ensures that the session is tied to the authentication
event.
6. For enhanced security and improved scalability, session data will be stored in the
system's database, as configured through the .env file, rather than relying on file- based session storage, which can be less secure and more challenging to manage in a
multi-server environment.11 As an alternative for potentially even better performance, Redis, an in-memory data store, could be considered for storing session data in future
development. 7. As an optional but strongly recommended security enhancement, multi-factor
authentication (MFA) using time-based one-time passwords (TOTP) generated by
authenticator applications on users' smartphones will be offered to both landlords and
tenants. Enabling MFA provides an additional layer of security beyond the traditional
username and password, making it significantly more difficult for unauthorized
individuals to gain access to user accounts.11
8. Insight: To provide an extra layer of security for landlords, particularly when they are
performing sensitive actions within their portal, such as managing financial
information or modifying user permissions, the system should implement short idle
timeouts (e.g., 15 to 30 minutes) that require them to re-authenticate their session. This would help mitigate the risk of unauthorized access in situations where a
landlord might temporarily leave their computer unattended while logged into a
sensitive area of the application. 1. Chain of Thought: Landlord accounts within the Rental Management System have
access to highly sensitive financial and property-related data, making them a prime
target for potential cyberattacks. By implementing short idle timeouts specifically for
critical actions within the landlord portal, the system can significantly reduce the risk
of unauthorized access and data breaches in scenarios where a landlord might step
away from their computer without explicitly logging out of their session. This
targeted security measure provides an additional layer of protection for high-risk
operations without unduly inconveniencing users during their normal, less critical
interactions with the system. 3. Subscription Management (Landlords):
4. 1. Tiered Subscription Packages: Features and Limitations (incorporating Research Task
2)
1. The Rental Management System will implement a tiered subscription model to cater
to the diverse needs of landlords based on the size and complexity of their rental
property portfolios. The initial subscription tiers will include:
1. Basic: Priced at $19 per month, this tier is designed for individual landlords or those
managing a small number of properties. It will support the management of up to 5
distinct properties and a total of 20 individual rental units. The Basic tier will include
core features such as comprehensive tenant management (allowing landlords to add, edit, and archive tenant records), a standard online rent collection system with typical
transaction processing times, a basic analytics dashboard providing key insights into
overall occupancy rates and total income, and standard email support available during
regular business hours.
2. Pro: Offered at $49 per month, the Pro tier is tailored for landlords with a growing
portfolio. It will support the management of up to 20 properties and a total of 100
rental units. This tier will encompass all the features included in the Basic tier, along
with enhanced analytics capabilities providing deeper insights into property
performance and tenant behavior, a dedicated tool for scheduling and tracking unit
renovation projects, priority email support with guaranteed faster response times, and
the functionality to assign a Secretary role to another user with configurable
permissions to assist with administrative tasks. 3. Pr emium: Available for $99 per month, the Premium tier is designed to meet the
needs of small to medium-sized property management businesses with larger
portfolios. It will support the management of an unlimited number of properties and
rental units. This tier will include all the features offered in the Pro tier, along with
dedicated support via both phone and email channels, future integration capabilities
with popular third-party accounting software platforms (such as QuickBooks and
Xero), and the ability to generate customizable financial and operational reports to
gain more granular insights into their business performance. 2. To attract new users to the platform and allow them to experience its fundamental
functionalities, the system could consider offering a free tier with certain limitations. This tier might include the ability to manage a single property with up to 5 rental units, access to basic tenant management features, manual rent tracking capabilities, and
limited access to the analytics dashboard (23). 3. Landlords will have a dedicated section within their account settings where they can
view the specific details of their current subscription plan, manage their billing
information (including updating payment methods), and easily upgrade or downgrade
their subscription tier based on their evolving property management needs and the
growth of their portfolio. 4. Insight: To further enhance user engagement and potentially increase the conversion
rate of free tier users to paid subscriptions, the system could implement feature-based
trials. This would allow users on the free tier to temporarily access the premium
features of the paid tiers for a limited duration, providing them with a direct
experience of the added value and encouraging them to upgrade to a paid plan to
retain access to those advanced functionalities. 1. Chain of Thought: By providing potential subscribers with temporary, hands-on
access to the advanced features available in the paid subscription tiers, they can
directly experience the benefits and efficiencies these features offer in the context of
their own real-world property management activities. This direct exposure to the
added value can be significantly more persuasive than simply listing the features and
may lead to a higher likelihood of users upgrading to a paid plan once the trial period
concludes. 5. Table: Landlord Subscription Packages
Featur e
Basic
(Monthly $19)
Pro (Monthly
$49)
Pr emium
(Monthly $99)
Number of Properties Up to 5 Up to 20 Unlimited
Total Units Up to 20 Up to 100 Unlimited
Tenant Management Yes Yes Yes
Online Rent
Collection
Standard
Processing
Standard
Processing
Standard
Processing
Basic Analytics
Dashboard Yes Yes Yes
Advanced Analytics No Yes Yes
Unit Renovation
Scheduling
No Yes Yes
Secretary Role No Yes Yes
Priority Support No Yes (Email) Yes (Phone &
Email)
Accounting Software
Integration
No No Future
Customizable
Reporting
No No Yes
* Billing Cycles and Payment Gateway Integration (Simulated M-Pesa)
(incorporating Research Task 2 & 3)
* The standard billing cycle for all landlord subscription tiers will be monthly, providing landlords with flexibility and predictable recurring costs. Subscription fees
will be automatically charged to the landlord's designated payment method on the
same calendar day each month, aligning with their initial subscription date.[29, 30]
* For the initial pilot phase of the Rental Management System, the platform will
incorporate a simulated integration with M-Pesa, a widely adopted mobile money
transfer service prevalent in specific geographical regions.[31, 32, 33, 34] This
simulated integration will allow landlords to experience the user flow of linking their
(simulated) M-Pesa accounts to the platform and making subscription payments
within the system's environment, without involving actual financial transactions. * The system's simulated M-Pesa integration will mimic the essential
functionalities of a real online payment system, including a secure interface for the
input of payment details (within the simulated context), the recording of subscription
payment transactions within the landlord's account history, and the generation of
simulated payment confirmation notifications and receipts that landlords can view and
download for their records. * Recognizing the importance of supporting a wider range of payment options
for broader accessibility and future scalability, subsequent development efforts will
focus on integrating with established and globally recognized payment gateways such
as Stripe [35, 36] and PayPal.[36, 37] This future integration will enable real-world
financial transactions for subscription payments and will support various payment
methods preferred by landlords, including major credit cards, debit cards, and popular
digital wallet services. * *Insight:* To further enhance the user experience and provide greater
flexibility in managing their subscription payments, the system should, in a future
development phase, consider offering various billing frequencies for subscription fees, such as quarterly or annual payment options. These alternative billing cycles could
potentially be offered with discounted rates compared to the standard monthly
subscription, incentivizing longer-term commitments from landlords and providing a
more predictable revenue stream for the system. * **Chain of Thought:** While a monthly billing cycle offers landlords the
greatest flexibility and lower upfront commitment, providing alternative billing
frequencies like quarterly or annual plans with discounted rates can offer significant
benefits for both the landlords and the platform. For landlords, it can lead to cost
savings and reduced administrative overhead related to monthly payments. For the
platform, it can foster greater customer loyalty, improve cash flow predictability, and
reduce customer churn over the long term. * **Tenant Portal**:
* Dashboard Overview
* Upon successful login to their personalized portal, tenants will be presented
with an intuitive dashboard providing a clear and concise overview of their current
tenancy. Key information prominently displayed will include the full address of the
property they are renting, the specific date on which their next rent payment is due, the total amount of any outstanding balance on their account, a brief summary of their
most recent payment history, and the current status of any active maintenance or
repair requests they have previously submitted to their landlord. * The dashboard will also feature easily accessible quick action buttons or
prominent links that will allow tenants to navigate directly to frequently used
functionalities, such as making a rent payment, submitting a new repair request, and
viewing the details of their digital lease agreement. * Online Rent, Water Bills, and Litter Collection Fees Payment (M-Pesa
Simulation) (incorporating Research Task 3)
* Within their portal, tenants will have access to a dedicated payment section
where they can view a detailed breakdown of all charges currently due for their
tenancy. This breakdown will clearly itemize the base rent amount, any applicable
water bills for the billing period, and any litter collection fees that are their
responsibility. * For the pilot phase of the system, the platform will provide a simulated M- Pesa payment interface. Within this simulated environment, tenants will be able to
enter a fictitious M-Pesa mobile phone number and a dummy personal identification
number (PIN) to initiate and complete a simulated payment transaction. * Following the simulated payment process, tenants will receive an immediate
on-screen confirmation message indicating that their transaction has been successfully
processed within the simulated environment. The system will also generate a
simulated payment receipt, which tenants can view on their screen and download as a
PDF document for their personal records. * Payment History (incorporating Research Task 4)
* The tenant portal will include a comprehensive and easily navigable payment
history section. This section will display a chronological list of all payments made by
the tenant throughout their tenancy, including rent payments, utility payments (such
as water bills), and any other fees they have paid. * Tenants will have the ability to filter their payment history by specifying a
particular date range (e.g., payments made within the last month, payments made
during a specific year) and by selecting the type of payment they wish to view (e.g., rent payments only, utility payments only). * Each entry in the payment history will clearly show the exact date on which
the payment was made, the total amount paid, the method of payment used (which
will be the simulated M-Pesa during the pilot phase), and the current status of the
payment (e.g., Paid, Pending if applicable). * For each individual payment transaction listed in their history, tenants will
have the option to view and download an official receipt in PDF format for their
personal record-keeping. * *Insight:* To further enhance the clarity and usefulness of the payment
history section for tenants, the system should also clearly indicate the specific rental
period that each rent payment covers (e.g., "Rent for the month of July 2025"). This
additional information will help tenants easily understand which rental period their
payments correspond to and avoid any potential confusion or discrepancies in their
payment records. * **Chain of Thought:** Clearly associating each rent payment with the
specific month or period it covers provides tenants with a more complete and
understandable view of their payment obligations and history. This level of detail can
significantly reduce the likelihood of misunderstandings or disputes regarding rent
payments and ensures that tenants have a clear record of their financial transactions
related to their tenancy. * One-Month Notice of Vacating Submission (incorporating Research Task 4)
* The tenant portal will feature a straightforward and easily accessible form that
tenants can use to submit their official one-month notice of their intention to vacate
the property, adhering to the standard terms outlined in their rental agreement. * The notice submission form will require tenants to clearly specify the exact
date on which they intend to move out of the property. * Upon successful submission of the notice through the portal, the tenant will
receive an automated confirmation message acknowledging the receipt of their notice. Simultaneously, the landlord will be immediately notified of the tenant's intent to
vacate, both through a notification within their landlord portal and potentially via
email for prompt awareness. * Tenants may also be provided with the option to upload a scanned copy or
photograph of a formal written notice of vacating, especially if this is explicitly
required by the terms of their individual lease agreement or for additional record- keeping purposes. * Repair Issue Reporting (incorporating Research Task 4)
* The tenant portal will include an intuitive and user-friendly form designed to
facilitate the reporting of any maintenance or repair issues that tenants may encounter
within their rented property. The form will feature the following key elements:
* A clearly labeled dropdown menu allowing tenants to select a relevant
category for the repair issue they are reporting. Common categories might include
Plumbing (e.g., leaky faucets, clogged drains), Electrical (e.g., faulty outlets, light
fixtures not working), Appliances (e.g., malfunctioning refrigerator, broken oven), Heating/Cooling (e.g., no heat, air conditioning not working), and an "Other" category
for issues that do not fit into the predefined options. * A free-text field will be provided where tenants can enter a detailed
description of the maintenance or repair problem they are experiencing, allowing
them to provide specific information and context. * Tenants will have the option to indicate the urgency of their repair request
by selecting a priority level from a predefined set of options, such as "Routine" for
non-emergency issues, "Urgent" for problems that require prompt attention but are not
immediately hazardous, or "Emergency" for issues that pose an immediate risk to
safety or property. * The form will also allow tenants to upload up to three relevant images or a
short video clip that visually illustrates the repair issue they are reporting. This visual
evidence can be invaluable in helping the landlord understand the problem more
clearly and assess the necessary course of action. * Upon successful submission of a repair request through the tenant portal, the
system will automatically generate and display a confirmation message to the tenant, which will include a unique ticket number for tracking the progress of their request. Simultaneously, the landlord will be immediately notified of the new repair request
through a notification within their landlord portal and potentially via email for prompt
attention. * Tenants will be able to view the current status of their submitted repair
requests within the portal. The status will be updated by the landlord as the issue is
reviewed, approved for repair, scheduled for maintenance, currently in progress, and
ultimately marked as completed. This transparency will keep tenants informed about
the progress of their requests. * *Insight:* To further enhance the repair reporting process and potentially
reduce the volume of minor, easily resolvable inquiries, the system should include a
readily accessible section containing frequently asked questions (FAQs) or basic
troubleshooting tips for common maintenance issues. This resource could provide
tenants with guidance on how to address simple problems themselves (e.g., resetting a
tripped circuit breaker, unclogging a minor drain), potentially saving time and effort
for both tenants and landlords. * **Chain of Thought:** By providing tenants with self-service
troubleshooting resources for common minor maintenance issues, the Rental
Management System can empower them to resolve certain problems independently. This can lead to a reduction in the number of repair requests submitted for easily
fixable issues, freeing up the landlord's time and resources to focus on more
significant maintenance needs. Additionally, it can improve tenant satisfaction by
providing immediate solutions for certain common problems. * UI/UX Design Considerations for Tenant Features (incorporating Research Task
4)
* The tenant portal will be meticulously designed with a strong emphasis on
user-friendliness and intuitive navigation, ensuring that tenants with varying levels of
technical expertise can easily and effectively utilize all of its features and
functionalities. The information architecture will be structured in a clear and logical
manner, allowing tenants to quickly locate the specific information or function they
need without confusion or frustration. * The visual design of the tenant portal will be fully responsive, meaning that
the layout and elements will automatically adapt and adjust seamlessly to different
screen sizes and devices, including desktop computers, laptops, tablets, and
smartphones. This ensures a consistent and optimal user experience regardless of how
tenants choose to access the portal, providing maximum accessibility and convenience
for tenants on the go.
* A consistent design language, incorporating the Rental Management System's
branding elements such as color schemes, typography, and icons, will be applied
throughout the tenant portal. This will create a cohesive, professional, and trustworthy
user experience, making the portal feel like a unified and reliable platform. * All labels, instructions, and informational messages presented within the
tenant portal will utilize clear, concise, and easily understandable language, avoiding
any technical jargon or complex terminology that might confuse or intimidate tenants. The goal is to make the portal accessible and straightforward for all users. * To ensure a positive and reassuring user experience, the system will provide
immediate and clear visual feedback to tenants for all their actions taken within the
portal. For example, when a tenant successfully submits a payment, confirms their
notice of vacating, or reports a repair request, they will receive a clear on-screen
confirmation message or visual cue indicating that their action has been successfully
processed. This immediate feedback will help tenants feel confident that their
interactions with the portal are being handled correctly. * **Landlord Portal**:
* Dashboard Overview and Key Analytics (Income, Occupancy, Repairs)
(incorporating Research Task 7)
* Upon logging into their personalized portal, landlords will be presented with a
comprehensive and customizable dashboard providing a real-time overview of their
entire property portfolio's performance through a variety of key performance
indicators (KPIs) and insightful data visualizations. * **Income Analytics**: The dashboard will display crucial financial metrics
related to income, including the total rental income received for the current month and
the year-to-date total. It will also feature a visual trend line illustrating the monthly
income generated over the past 12 months, allowing landlords to easily identify
seasonal patterns or growth trends. Additionally, the dashboard will include a detailed
breakdown of the income generated by each individual property managed by the
landlord, providing a clear understanding of which properties are contributing the
most to their revenue. * **Occupancy Analytics**: Landlords will be able to see the current overall
occupancy rate across their entire portfolio of rental properties, giving them an
immediate sense of how many of their units are currently occupied. The dashboard
will also provide a clear list of all units that are currently vacant, allowing landlords to
quickly identify and address any vacancies. Furthermore, a historical trend chart will
display the occupancy rates over time, enabling landlords to track changes and
identify potential issues or successes in their leasing strategies.[38, 39, 40, 41, 42, 43, 44]
* **Repair Analytics**: The dashboard will offer a snapshot of the current
maintenance and repair situation across the landlord's portfolio. This will include the
total number of open repair requests that have been submitted by tenants and are
awaiting action, the average time it takes to resolve repair requests from the moment
they are submitted until they are marked as complete, and a categorized breakdown of
all repair requests by the type of issue reported (e.g., plumbing, electrical, appliances), allowing landlords to identify common maintenance problems.[45, 46, 47, 48, 49, 50]
* Landlords will have the flexibility to customize their dashboard by selecting
the specific KPIs and data visualizations that are most relevant to their individual
needs and property management priorities. This personalization will allow them to
tailor the dashboard to focus on the metrics that matter most to their specific business
goals and operational oversight. * *Insight:* To enable landlords to delve deeper into the data presented on their
dashboard and gain a more granular understanding of their property performance, the
system should incorporate drill-down capabilities for key visualizations. For example, if a landlord notices a particularly low occupancy rate for a specific property on the
occupancy overview chart, they should be able to click on that property to view a
detailed breakdown of the vacancy status for each individual unit within that property, along with potential reasons for the vacancies (e.g., recently vacated, currently
undergoing renovation, awaiting a new tenant). * **Chain of Thought:** The implementation of drill-down functionality
will transform the landlord dashboard from a static overview into an interactive and
powerful analytical tool. By allowing landlords to explore the underlying data with a
simple click, they can quickly identify the root causes of trends and issues that are
highlighted on the dashboard. This deeper level of data exploration will empower
landlords to make more targeted and effective decisions regarding their property
management strategies, ultimately leading to improved outcomes in areas such as
occupancy rates, income generation, and expense management. * **Table: Landlord Dashboard KPIs and Visualizations**
KPI Description Calculation
Recommended
Visualization
Occupancy
Rate
Percentage of total
units currently
occupied
(Number of
Occupied Units /
Total Units) * 100
Percentage
Gauge
Total
Income
(Current
Month)
Sum of all rent and
fee payments
received in the
current month
Sum of all
recorded income
transactions for
the current month
Number Card
Overdue
Rent
Total amount of
rent that is past its
due date across all
properties
Sum of all
outstanding rent
balances with due
dates in the past
Number Card
Average
Repair
Resolution
Time
The average time
taken to resolve
maintenance
requests completed
in the last 30 days
(Sum of
Resolution Times
for Closed
Requests) /
(Number of
Closed Requests)
Line Chart
(Trend)
* Rental Income and Expense Tracking (including Repair Costs) (incorporating
Research Task 5)
* The landlord portal will feature an intuitive and efficient interface that allows
landlords to easily record all rental income received from their tenants. This will
include tracking base rent payments, any late fees collected, and income from other
sources such as parking fees or pet fees. * A comprehensive expense tracking system will be implemented, enabling
landlords to log and categorize all expenses associated with their rental properties. The system will provide a set of predefined expense categories, including
maintenance costs, utility bills (if paid by the landlord), property taxes, insurance
premiums, repair expenses, and other operational costs. Landlords will also have the
flexibility to create custom expense categories to suit their specific needs and
accounting practices.[51, 52, 53, 54, 55, 56, 57, 58, 59, 60]
* To ensure accurate and organized record-keeping, landlords will have the
ability to upload digital copies of receipts, invoices, and other relevant supporting
documents directly to the system, linking them to the corresponding income or
expense transactions. * The system will offer robust reporting capabilities, allowing landlords to
generate various financial reports for their rental properties. These reports will include
income statements (also known as profit and loss reports) and cash flow statements, which can be generated for user-defined periods such as monthly, quarterly, annually, or even custom date ranges. Landlords will also have the option to generate these
reports for individual properties within their portfolio or for their entire portfolio as a
whole.[61, 62, 63, 64, 65, 66, 67, 68, 69]
* *Insight:* To further streamline the process of financial tracking and reduce
the administrative burden on landlords, the system should offer the functionality to set
up recurring income and expense entries. For example, landlords could schedule
automatic monthly entries for regular expenses like mortgage payments or property
taxes, as well as recurring rent payments for long-term tenants, minimizing the need
for manual data entry each month.
* **Chain of Thought:** Many aspects of rental property financial
management involve recurring transactions that occur on a regular basis. By allowing
landlords to set up these recurring income and expense entries within the system, the
platform can significantly improve efficiency and accuracy in their financial record- keeping. This automation will save landlords valuable time and ensure that these
regular financial activities are consistently accounted for in their reports, leading to
more reliable and insightful financial overviews of their rental business. * Unit Renovation Scheduling and Tracking (incorporating Research Task 5)
* Landlords will be provided with an intuitive and visually clear calendar
interface within their portal that will allow them to schedule renovation projects for
specific rental units within their properties. This calendar view will provide an at-a- glance overview of planned and ongoing renovation activities. * For each renovation project scheduled, landlords will have the ability to
define individual tasks that need to be completed (e.g., painting the unit, replacing the
flooring, remodeling the kitchen), set planned start and end dates for each task to
establish a project timeline, and track both the initial estimated budget for the
renovation and the actual costs incurred as the project progresses.[70, 71, 72, 73, 74, 75, 76, 77, 78, 79]
* The system will enable landlords to update the status of individual renovation
tasks, marking them as completed as the work is done. This will allow landlords to
easily monitor the overall progress of each renovation project against the planned
timeline and budget, helping them stay organized and on track. * *Insight:* To facilitate better organization and management of renovation
projects, the system should include a file upload feature within the renovation
tracking module. This would allow landlords to store all relevant documents related to
a specific renovation project in one centralized location. Such documents could
include contractor quotes and bids, before-and-after photographs of the renovation
work, material purchase receipts, and any other pertinent files, providing a
comprehensive record of each renovation project for future reference. * **Chain of Thought:** Centralizing all documentation related to
renovation projects within the system offers significant advantages for landlords. It
simplifies the process of managing and overseeing these projects by keeping all
essential information readily accessible in one place. This can help landlords easily
compare contractor bids, track expenses against the budget, and document the visual
transformation of the unit, leading to better organization and accountability
throughout the renovation process. * Repair Request Management (Review, Approval Workflow) (incorporating
Research Task 5)
* Landlords will be presented with a centralized list of all repair requests that
have been submitted by their tenants through the tenant portal. This list will provide
key information at a glance, such as the property and unit number where the issue was
reported, a brief description of the maintenance problem, the date and time the request
was submitted, and the current status of the request (e.g., Pending Review, Approved, In Progress, Completed, Rejected). * By clicking on a specific repair request in the list, landlords will be able to
access a detailed view containing all the information provided by the tenant, including
a full description of the issue, the urgency level indicated by the tenant, and any
accompanying photos or videos that were uploaded. * Landlords will have the ability to review each repair request and take
appropriate action. They can choose to approve the request for repair, indicating that
they will address the issue. Alternatively, they can reject the request if it is deemed
invalid, not the landlord's responsibility according to the lease agreement, or if further
information is required from the tenant. If a repair request is rejected, the landlord will
be prompted to provide a clear and concise reason for the rejection, which will then be
communicated back to the tenant through the portal. * The status of each repair request will be automatically updated within the
system based on the landlord's actions. This status update will be visible in real-time
to both the landlord within their portal and the tenant within their own portal, ensuring
transparency and keeping everyone informed about the progress of the repair. * *Insight:* To further enhance the efficiency of the repair management
process and facilitate clear communication, the system should incorporate a dedicated
communication log within each individual repair request. This log would allow
landlords and tenants to exchange messages directly regarding the specific repair
issue, providing a documented history of all communication related to that particular
request. This could be used for asking clarifying questions, providing updates on
repair scheduling, or confirming completion of the work. * **Chain of Thought:** A centralized communication log for each repair
request will significantly improve the flow of information between landlords and
tenants. It will provide a clear and easily accessible record of all conversations related
to a specific maintenance issue, helping to avoid misunderstandings, track
commitments, and ensure that both parties are kept informed about the status and
details of the repair process. This feature will contribute to a more efficient and
transparent repair management workflow, ultimately leading to greater tenant
satisfaction. * Automated Payment Reminders (Rent Due Soon, Overdue Notices via
Email/SMS) (incorporating Research Task 6)
* Landlords will have the capability to configure automated payment reminders
to be sent to their tenants through both email and Short Message Service (SMS) text
messages. This feature will help ensure timely rent payments and reduce the incidence
of overdue balances. * Landlords will be able to customize the schedule for sending these reminders, specifying the number of days before the rent due date that the initial reminder should
be dispatched. They will also have granular control over the frequency and timing of
subsequent reminders for tenants with overdue rent payments. For example, a
landlord might choose to send an email reminder 7 days before the due date and an
SMS reminder 3 days before, followed by a daily email reminder for the first 3 days
after the due date, and then an SMS reminder every other day for a specified
period.[80, 81, 82, 83, 84, 85, 86, 87]
* The system will provide a set of customizable message templates for both
"rent due soon" notifications and "overdue payment" notices. These templates will
allow landlords to tailor the tone, content, and specific details included in the
messages, such as the tenant's name, property address, rent amount due, and payment
due date.[80, 88]
* The system will automatically track which payment reminders have been sent
to each tenant, along with the exact date and time of transmission and the current
payment status of the tenant, providing landlords with a clear audit trail of their
communication efforts. * *Insight:* To further enhance the effectiveness of automated payment
reminders and improve the likelihood of prompt payment, the system should allow
landlords to include a direct, clickable link to the online payment portal within both
the email and SMS reminder messages. This will provide tenants with immediate and
convenient access to the payment interface, reducing any potential barriers to making
timely payments. * **Chain of Thought:** By embedding a direct link to the online payment
portal within the payment reminder messages, the system can significantly streamline
the payment process for tenants. This eliminates the need for tenants to separately log
in to the portal to make a payment, reducing friction and making it as easy as possible
for them to fulfill their rent obligations on time. This convenience is expected to lead
to faster payment times and a noticeable reduction in the number of overdue rent
payments. * **Table: Automated Payment Reminder Schedule Options**
Reminder
Type
Channel Timing Options
Customizable
Message
Template
Rent Due
Soon
Email
7 days before due date, 5
days before due date, 3 days
before due date
Yes
Rent Due
Soon
SMS
3 days before due date, 1
day before due date
Yes
Overdue Email
On due date, 1 day after due
date, 2 days after due date, 3
days after due date, 5 days
after due date
Yes
Overdue SMS On due date, 1 day after due
date, 3 days after due date
Yes
* **Public Rental Listing Portal**:
* Search Functionality for Vacant Units (Filters, Sorting) (incorporating Research
Task 8)
* Prospective tenants will be able to easily search for available rental units
through a prominent and intuitive search bar located on the portal's homepage. This
search functionality will allow users to enter keywords such as the desired city, neighborhood, or specific property names to quickly find relevant listings. * To further refine their search, prospective tenants will have access to a
comprehensive set of filtering options. These filters will include criteria such as the
type of property (e.g., apartment, house, condominium, townhouse), the desired price
range for rent (allowing users to specify minimum and maximum monthly rent), the
number of bedrooms and bathrooms required, and a selection of available amenities
(e.g., pet-friendly policies, on-site parking, in-unit laundry facilities, balcony or
patio).[89, 90]
* The search results will be presented in a clear and visually appealing format, typically as a list or grid of property listings. Prospective tenants will have the option
to sort these search results based on various criteria, including the monthly rent (from
lowest to highest or highest to lowest) and the date the listing was initially posted
(with the newest listings appearing first). * Property Details Display (Images, Descriptions, Amenities, Location)
(incorporating Research Task 8)
* Each individual rental listing on the public portal will feature a visually
prominent gallery of high-quality images showcasing the property's exterior, the
interior of the available unit(s), and any notable features or communal spaces. * Detailed and engaging descriptions of the property and the specific unit(s)
will be provided, including information about the size (square footage), layout, lease
terms, and any unique selling points. The description will also include relevant details
about the surrounding neighborhood, such as proximity to local amenities, transportation options, and schools. * A clearly formatted list of all available amenities will be presented for each
listing. This will include both amenities within the rental unit itself (e.g., appliances,
air conditioning, internet access) and any community amenities offered by the
property (e.g., swimming pool, fitness center, shared laundry facilities). * The exact location of the property will be displayed on an integrated and
interactive map interface, allowing prospective tenants to easily visualize its location
and explore the surrounding area. * Clear and readily accessible contact information for the landlord or a direct
online inquiry form will be prominently displayed on each listing, enabling interested
prospective tenants to easily reach out to the landlord to ask questions, request
additional information, or schedule a property viewing. * User Interaction for Prospective Tenants (Inquiries, Contact Options)
(incorporating Research Task 8)
* Each property listing on the public portal will include a user-friendly contact
form that allows prospective tenants to directly send inquiries to the landlord
regarding the specific rental unit or property they are interested in. This form will
typically include fields for the tenant's name, email address, phone number, and a
message box for their specific questions or requests. * The system should, in a future development phase, incorporate the
functionality for prospective tenants to schedule property viewings online directly
through the portal. This could involve integrating a calendar feature that displays the
landlord's availability and allows tenants to select a convenient time slot for a visit. * A dedicated section or a clear link will provide prospective tenants with
detailed information about the landlord's tenant application process, including a list of
any required documents (e.g., identification, proof of income), details about
application fees, and the criteria used for tenant screening (e.g., credit score
requirements, background checks). * **Admin Functionality**:
* Utility Charge Confirmation Workflow Before Invoice Generation
* A dedicated administrative section will be accessible within the landlord
portal, providing a specific workflow for reviewing and confirming utility charges
(specifically water bills and litter collection fees) before these charges are included in
the monthly invoices sent to tenants. * Admin users, which include landlords and any designated secretaries with the
appropriate permissions, will have access to a list of all utility charges that have been
submitted for each rental unit. These charges might be entered by tenants themselves
through their portal (if this functionality is implemented) or potentially retrieved
through future integrations with local utility providers.
* For each submitted utility charge, admin users will be able to review the
details, such as the usage period, the total amount of the charge, and any supporting
documentation (e.g., meter readings, utility bills). Based on their review, they will
have the option to either confirm that the charge is accurate and should be included in
the tenant's invoice or mark the charge for further review or adjustment if they
identify any discrepancies or have questions. * The system will be designed to ensure that only utility charges that have been
explicitly confirmed by an admin user will be included when the system generates the
monthly invoices for tenants. This workflow will help to ensure the accuracy of tenant
billing and minimize potential disputes related to utility charges. * A comprehensive audit log will be automatically maintained by the system, recording all actions related to the utility charge confirmation process. This log will
include details such as the admin user who performed the action (e.g., confirmed or
requested review), the date and time of the action, the specific utility charge that was
reviewed, and the final status of the charge (e.g., Submitted, Confirmed, Review
Requested, Adjusted). This audit trail will provide a valuable record for accountability
and troubleshooting purposes. 1. Critical Featur es Overlooked:
2. 5. Tenant Scr eening and Background Checks: The system should integrate with
reputable third-party tenant screening services to allow landlords to easily and
securely perform credit checks, criminal background checks, and eviction history
reports on prospective tenants who apply through the platform or express interest in
their listings.63 This is crucial for ensuring landlords can make informed decisions
about who they rent to, minimizing the risk of financial losses and potential issues
down the line. 6. Lease Agr eement Management: The system should provide functionality for
landlords to create and customize digital lease agreement templates, incorporating
standard clauses and allowing for property-specific terms. Once a lease is finalized, the system should facilitate the secure sending of the lease agreement to tenants for
electronic review and signature. Signed lease documents should then be securely
stored within the system, readily accessible to both landlords and tenants for reference
throughout the tenancy.61
7. Integr ation with Maintenance Vendor Network: To streamline the process of
handling repair requests, the system should allow landlords to build and manage a
network of trusted maintenance vendors (e.g., plumbers, electricians, HVAC
technicians, general handymen) within the platform. Landlords should be able to
assign specific repair requests to these vendors, track the progress of the work, communicate with them directly through the system, and manage payments upon
completion of the repairs. 8. Property Inspections: The system should include features for scheduling and
conducting property inspections, such as move-in inspections before a tenant occupies
a unit and move-out inspections after they vacate. Landlords should be able to create
customizable inspection checklists to ensure thoroughness and document the
condition of the property with the ability to upload accompanying photos and notes. These records can be invaluable for resolving security deposit disputes and tracking
property wear and tear.62
9. Community Featur es: To foster a sense of community and improve tenant
engagement, the system could include a dedicated section within the tenant portal for
landlords to post community-wide announcements, such as upcoming building
maintenance schedules, information about local events, or changes in property rules. Optionally, a moderated forum or message board could be included to allow tenants to
interact with each other, share information, and build a stronger sense of community
within the rental property. 10. Integr ation with Calendar Applications: To enhance organization and time
management for landlords, the system should offer seamless integration with popular
calendar applications such as Google Calendar or Outlook Calendar. This would
allow landlords to synchronize important dates and events related to their rental
properties, such as scheduled property viewings with prospective tenants, planned
renovation tasks, maintenance appointments with vendors, and rent due dates, ensuring they stay on top of their responsibilities and commitments. 11. Advanced Reporting and Analytics: While the landlord dashboard will provide a
valuable overview of key performance indicators, the system should also offer the
capability for landlords to generate more detailed and customizable reports on various
aspects of their property management activities. This could include in-depth financial
reports with customizable date ranges and expense categories, detailed analyses of
occupancy trends over specific periods, comprehensive breakdowns of maintenance
costs by property or issue type, and reports on tenant turnover rates and the reasons
for lease terminations. These advanced reporting features would provide landlords
with even deeper insights into their rental business, enabling them to make more
strategic and data-driven decisions. 3. Non-Functional Requir ements:
4. 12. Performance and Scalability
1. The system should be designed to provide a responsive user experience. All critical
user interactions, such as page loads, form submissions, and data retrieval, should
ideally complete within a maximum of 3 seconds under normal operating conditions
to ensure a smooth and efficient workflow for both landlords and tenants. 2. The application architecture must be highly scalable to accommodate a growing
number of users (both landlords and tenants) and an increasing volume of property
and unit data without experiencing significant performance degradation. This will
likely involve the implementation of load balancing techniques to distribute user
traffic across multiple servers and database optimization strategies to ensure efficient
data storage and retrieval as the system grows. 13. Security (Data Protection, Authorization)
1. All sensitive user data, including login credentials, personal identification information, and financial details (such as bank account information and payment history), must be
protected using robust encryption techniques. Data in transit between the user's
browser and the system's servers should be secured using Transport Layer Security
(TLS/SSL) encryption, ensuring that it cannot be intercepted or read by unauthorized
parties. Data at rest, stored within the system's database, should also be encrypted
using a strong encryption algorithm such as AES-256 to protect it from unauthorized
access. 2. A comprehensive authorization mechanism, leveraging the Spatie Roles &
Permissions package, must be implemented throughout the application. This will
ensure that users can only access the features, functionalities, and data that are
appropriate for their assigned roles and permissions within the system. Every user
action, from viewing information to performing transactions, should be subject to
strict authorization checks to prevent unauthorized access or modification of data. 3. To proactively identify and address any potential security vulnerabilities in the system, regular security vulnerability scanning and penetration testing should be conducted by
qualified cybersecurity professionals. The results of these assessments should be used
to implement necessary security patches and updates, ensuring the ongoing security
and integrity of the platform. 14. Usability and Accessibility
1. The system's user interface (UI) should be designed to be intuitive, user-friendly, and
easy to navigate for individuals with varying levels of technical proficiency. The
design should adhere to common UI/UX design principles for web applications, ensuring a consistent and efficient user experience across all features and
functionalities. 2. Accessibility should be a primary consideration in the system's design and
development. The application should strive to comply with the Web Content
Accessibility Guidelines (WCAG) 2.1 Level AA standards to ensure that it is usable
by people with disabilities, including those with visual, auditory, cognitive, or motor
impairments. This will involve adhering to guidelines for semantic HTML, keyboard
navigation, sufficient color contrast, and screen reader compatibility. 15. Maintainability and Extensibility
1. The codebase of the Rental Management System should be developed following
Laravel's established coding conventions and best practices. It should be thoroughly
documented using PHPDoc syntax for API documentation and clear, concise
comments within the code to facilitate ongoing maintenance, updates, and debugging
by the development team. 2. The system's architecture should be designed with modularity as a key principle. This
will allow for the addition of new features, functionalities, and integrations with third- party services in future development phases without causing significant disruption to
the existing codebase or compromising the stability of the system. A well-defined and
loosely coupled architecture will make the system easier to evolve and adapt to future
requirements. 5.
Technology Stack Requir ements:
6. 16. Laravel
1. The project will utilize Laravel version 11 or the latest stable Long-Term Support
(LTS) release to ensure access to the most up-to-date features, security patches, and
performance enhancements offered by the framework. 2. The Eloquent ORM (Object-Relational Mapper) will be the primary mechanism for
interacting with the system's database, providing a secure and efficient way to retrieve
and manipulate data. 3. Laravel's robust routing system will be used to define the application's URLs and map
them to specific controllers and actions. Middleware will be implemented to handle
request processing, including authentication, authorization, and other security-related
tasks. 17. Livewire
1. Livewire version 3 will be the chosen library for building dynamic and interactive
user interface components within the application. Livewire's ability to handle frontend
interactions using PHP will streamline development and reduce the need for extensive
custom JavaScript coding. 2. The development team will adhere to best practices for Livewire component design, including proper management of component state, efficient data binding between the
frontend and backend, and optimization of component rendering to ensure a
performant user experience. 18. TailwindCSS
1. The project will leverage TailwindCSS version 3.x for styling the application's
frontend. The utility-first approach of Tailwind will enable rapid development of a
visually appealing and responsive user interface by applying pre-designed CSS
classes directly within the HTML templates. 2. The Tailwind configuration file will be customized as needed to align with the
project's specific branding guidelines and design requirements, including defining the
primary and secondary color palettes, setting up typography styles, and configuring
responsive breakpoints to ensure the application adapts well to different screen sizes
and devices. 19. Spatie Roles & Permissions
1. Version 6.x of the Spatie Roles & Permissions package will be implemented to
manage user roles and their associated permissions within the Rental Management
System. This package provides a flexible and intuitive API for defining roles (e.g., Landlord, Tenant, Secretary), assigning permissions to these roles, and then assigning
roles to individual users. 2. The initial set of roles and their corresponding permissions will be clearly defined and
managed through database seeders. Seeders are PHP files that allow developers to
populate the database with initial data, including the definitions of roles and
permissions, ensuring a consistent and easily reproducible setup of the application's
authorization system across different environments (e.g., development, testing, production). 20. M-Pesa Simulation
1. A dedicated service or a set of well-organized classes will be developed within the
Laravel application to accurately simulate the core API request and response structure
of the M-Pesa payment gateway. This simulation will specifically focus on the
functionalities required during the pilot phase of the project, including the initiation of
payments from tenants for rent and utilities, the processing of subscription payments
from landlords, and the handling of transaction status updates (e.g., successful
payment, payment failure). 2. The simulation logic will be carefully designed to handle both successful payment
scenarios and potential failure cases, such as insufficient funds or incorrect PINs, to
provide a realistic testing environment for both landlords and tenants interacting with
the simulated payment gateway. The development team will consider referencing
existing open-source Laravel packages for M-Pesa integration (e.g., Iankumu/mpesa
91, gathuku/laravel_mpesa 92, ghostscypher/laravel-mpesa 93, akika/laravel-mpesa
94) as valuable references to ensure the simulation aligns closely with the actual M- Pesa API specifications and to facilitate a smoother transition to a real integration
with M-Pesa or other payment gateways in future development phases of the project. 7. Conclusions:
8. The Product Requirements Document outlines a comprehensive plan for the
development of a web-based Rental Management System. By focusing on the needs
of both landlords and tenants and leveraging a modern technology stack, the system
aims to provide an efficient, secure, and user-friendly platform for independent
property management. The detailed feature requirements, including user
authentication, subscription management, tenant and landlord portals, and a public
listing portal, address the core functionalities requested by the user. Furthermore, the
identification of critical overlooked features, such as tenant screening and lease
agreement management, highlights areas for future development that will enhance the
system's value and competitiveness. The non-functional requirements emphasize the
importance of performance, security, usability, and maintainability, ensuring a robust
and reliable application. The technology stack, with Laravel, Livewire, TailwindCSS, and Spatie Roles & Permissions at its core, provides a strong foundation for building
a modern and scalable solution. The inclusion of an M-Pesa simulation for the pilot
phase allows for initial testing of payment functionalities, with a clear path towards
integrating real payment gateways in the future. Overall, this PRD provides a detailed
roadmap for the development team to create a high-quality Rental Management
System that meets the needs of its target users. 9.
