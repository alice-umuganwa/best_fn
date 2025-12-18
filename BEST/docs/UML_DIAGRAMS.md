# UML Diagrams - Disaster Relief Management System

This document contains comprehensive UML diagrams for the Disaster Relief Management System, including Use Case, Class, Activity, Data Flow, and Sequence diagrams.

---

## 1. Use Case Diagram

The Use Case diagram illustrates the interactions between different actors and the system.

```mermaid
graph TB
    subgraph "Disaster Relief Management System"
        UC1[Register/Login]
        UC2[View Disasters]
        UC3[Make Donation]
        UC4[Register as Volunteer]
        UC5[Manage Disasters]
        UC6[Manage Relief Camps]
        UC7[Manage Resources]
        UC8[Assign Volunteers]
        UC9[Generate Reports]
        UC10[View Dashboard]
        UC11[Track Donations]
        UC12[Request Resources]
    end
    
    Admin["👤 Admin"]
    Staff["👤 Staff"]
    Volunteer["👤 Volunteer"]
    Donor["👤 Donor"]
    Public["👤 Public User"]
    
    Admin --> UC1
    Admin --> UC5
    Admin --> UC6
    Admin --> UC7
    Admin --> UC8
    Admin --> UC9
    Admin --> UC10
    Admin --> UC11
    
    Staff --> UC1
    Staff --> UC5
    Staff --> UC6
    Staff --> UC7
    Staff --> UC12
    Staff --> UC10
    
    Volunteer --> UC1
    Volunteer --> UC4
    Volunteer --> UC10
    
    Donor --> UC1
    Donor --> UC3
    Donor --> UC11
    
    Public --> UC2
    Public --> UC3
    Public --> UC4
```

### Actors:
- **Admin**: Full system access, manages all aspects
- **Staff**: Manages disasters, camps, and resources
- **Volunteer**: Registers and views assignments
- **Donor**: Makes donations and tracks contributions
- **Public User**: Views disasters and makes donations

---

## 2. Class Diagram

The Class diagram shows the object-oriented structure of the system.

```mermaid
classDiagram
    class User {
        -int user_id
        -string username
        -string email
        -string password_hash
        -string full_name
        -string phone
        -string role
        -string status
        +register(data)
        +login(username, password)
        +updateUser(userId, data)
        +changePassword(userId, newPassword)
    }
    
    class Disaster {
        -int disaster_id
        -string disaster_name
        -string disaster_type
        -string location
        -string severity
        -string description
        -int affected_population
        -string status
        +create(data)
        +getById(disasterId)
        +getAll(filters)
        +update(disasterId, data)
        +getStatistics(disasterId)
    }
    
    class ReliefCamp {
        -int camp_id
        -int disaster_id
        -string camp_name
        -string location
        -int capacity
        -int current_occupancy
        -string status
        +create(data)
        +getById(campId)
        +update(campId, data)
        +getResources(campId)
    }
    
    class Resource {
        -int resource_id
        -int camp_id
        -string resource_name
        -string resource_type
        -decimal quantity
        -string unit
        -string status
        +create(data)
        +update(resourceId, data)
        +updateStatus(resourceId)
    }
    
    class Donation {
        -int donation_id
        -int donor_id
        -int disaster_id
        -string donation_type
        -decimal amount
        -string status
        +create(data)
        +getById(donationId)
        +updateStatus(donationId, status)
        +getStatistics(disasterId)
    }
    
    class Volunteer {
        -int volunteer_id
        -int user_id
        -string skills
        -string availability
        -string verification_status
        -decimal total_hours
        +create(data)
        +getById(volunteerId)
        +update(volunteerId, data)
    }
    
    class VolunteerAssignment {
        -int assignment_id
        -int volunteer_id
        -int camp_id
        -string role
        -date start_date
        -decimal hours_worked
        -string status
        +create(data)
        +update(assignmentId, data)
    }
    
    User "1" --> "*" Disaster : creates
    Disaster "1" --> "*" ReliefCamp : has
    ReliefCamp "1" --> "*" Resource : contains
    User "1" --> "*" Donation : makes
    Disaster "1" --> "*" Donation : receives
    User "1" --> "1" Volunteer : is
    Volunteer "*" --> "*" ReliefCamp : assigned to
    Volunteer "1" --> "*" VolunteerAssignment : has
    ReliefCamp "1" --> "*" VolunteerAssignment : has
```

---

## 3. Activity Diagram - Disaster Response Workflow

This diagram shows the complete workflow for responding to a disaster event.

```mermaid
flowchart TD
    Start([Disaster Occurs]) --> Report[Report Disaster to System]
    Report --> CreateDisaster[Admin Creates Disaster Record]
    CreateDisaster --> AssessSeverity{Assess Severity}
    
    AssessSeverity -->|Minor| Monitor[Monitor Situation]
    AssessSeverity -->|Moderate/Severe| EstablishCamps[Establish Relief Camps]
    
    EstablishCamps --> AllocateResources[Allocate Initial Resources]
    AllocateResources --> RequestVolunteers[Request Volunteers]
    
    RequestVolunteers --> VolunteerRegistration[Volunteers Register]
    VolunteerRegistration --> VerifyVolunteers{Verify Volunteers}
    
    VerifyVolunteers -->|Approved| AssignVolunteers[Assign to Camps]
    VerifyVolunteers -->|Rejected| NotifyRejection[Notify Rejection]
    
    AssignVolunteers --> OpenDonations[Open Donation Portal]
    OpenDonations --> ReceiveDonations[Receive Donations]
    
    ReceiveDonations --> ProcessDonations{Process Donations}
    ProcessDonations -->|Monetary| AllocateFunds[Allocate Funds]
    ProcessDonations -->|Material| DistributeSupplies[Distribute Supplies]
    
    AllocateFunds --> PurchaseSupplies[Purchase Needed Supplies]
    PurchaseSupplies --> DistributeSupplies
    
    DistributeSupplies --> MonitorResources{Check Resource Levels}
    MonitorResources -->|Low| RequestMore[Request More Resources]
    MonitorResources -->|Adequate| ContinueOperations[Continue Operations]
    
    RequestMore --> ReceiveDonations
    ContinueOperations --> CheckStatus{Disaster Status}
    
    CheckStatus -->|Ongoing| MonitorResources
    CheckStatus -->|Resolved| GenerateReport[Generate Final Report]
    
    Monitor --> CheckEscalation{Situation Escalates?}
    CheckEscalation -->|Yes| EstablishCamps
    CheckEscalation -->|No| GenerateReport
    
    GenerateReport --> CloseCamps[Close Relief Camps]
    CloseCamps --> End([End])
    NotifyRejection --> End
```

---

## 4. Data Flow Diagram (DFD) - Level 0

This diagram shows how data flows through the system.

```mermaid
flowchart LR
    subgraph External["External Entities"]
        Admin["👤 Admin"]
        Staff["👤 Staff"]
        Volunteer["👤 Volunteer"]
        Donor["👤 Donor"]
    end
    
    subgraph System["Disaster Relief Management System"]
        Auth["Authentication Module"]
        DisasterMgmt["Disaster Management"]
        CampMgmt["Camp Management"]
        ResourceMgmt["Resource Management"]
        DonationMgmt["Donation Management"]
        VolunteerMgmt["Volunteer Management"]
        Reporting["Reporting & Analytics"]
    end
    
    subgraph DataStores["Data Stores"]
        UserDB[("Users Database")]
        DisasterDB[("Disasters Database")]
        CampDB[("Camps Database")]
        ResourceDB[("Resources Database")]
        DonationDB[("Donations Database")]
        VolunteerDB[("Volunteers Database")]
    end
    
    Admin -->|Login Credentials| Auth
    Staff -->|Login Credentials| Auth
    Volunteer -->|Registration Data| Auth
    Donor -->|Registration Data| Auth
    
    Auth -->|User Data| UserDB
    UserDB -->|User Info| Auth
    
    Admin -->|Disaster Info| DisasterMgmt
    DisasterMgmt -->|Disaster Data| DisasterDB
    DisasterDB -->|Disaster Records| Reporting
    
    Staff -->|Camp Details| CampMgmt
    CampMgmt -->|Camp Data| CampDB
    
    Staff -->|Resource Info| ResourceMgmt
    ResourceMgmt -->|Resource Data| ResourceDB
    ResourceDB -->|Inventory Status| Reporting
    
    Donor -->|Donation Info| DonationMgmt
    DonationMgmt -->|Donation Data| DonationDB
    DonationDB -->|Donation Records| Reporting
    
    Volunteer -->|Availability| VolunteerMgmt
    VolunteerMgmt -->|Volunteer Data| VolunteerDB
    VolunteerDB -->|Assignment Info| Reporting
    
    Reporting -->|Reports| Admin
    Reporting -->|Statistics| Staff
```

---

## 5. Sequence Diagram - Donation Process

This diagram shows the sequence of interactions for making a donation.

```mermaid
sequenceDiagram
    participant Donor
    participant UI as Web Interface
    participant Controller as DonationController
    participant Model as Donation Model
    participant DB as Database
    participant Email as Email Service
    
    Donor->>UI: Access Donation Page
    UI->>Controller: Request Active Disasters
    Controller->>Model: getActiveDisasters()
    Model->>DB: SELECT disasters WHERE status='active'
    DB-->>Model: Disaster List
    Model-->>Controller: Return Disasters
    Controller-->>UI: Display Disasters
    UI-->>Donor: Show Donation Form
    
    Donor->>UI: Fill Donation Form
    Donor->>UI: Submit Donation
    UI->>Controller: POST donation data
    
    Controller->>Controller: Validate Input
    
    alt Validation Successful
        Controller->>Model: create(donationData)
        Model->>DB: INSERT INTO donations
        DB-->>Model: donation_id
        Model-->>Controller: Success + donation_id
        
        Controller->>Model: generateReceipt(donation_id)
        Model->>DB: UPDATE receipt_number
        DB-->>Model: Receipt Generated
        
        Controller->>Email: sendReceiptEmail(donor, receipt)
        Email-->>Donor: Email Receipt
        
        Controller-->>UI: Success Response
        UI-->>Donor: Show Success Message + Receipt
    else Validation Failed
        Controller-->>UI: Error Response
        UI-->>Donor: Show Error Message
    end
```

---

## 6. Sequence Diagram - Volunteer Assignment

This diagram shows how volunteers are assigned to relief camps.

```mermaid
sequenceDiagram
    participant Admin
    participant UI as Admin Dashboard
    participant Controller as VolunteerController
    participant VolModel as Volunteer Model
    participant CampModel as Camp Model
    participant DB as Database
    participant Notification as Notification Service
    
    Admin->>UI: Access Volunteer Management
    UI->>Controller: Request Verified Volunteers
    Controller->>VolModel: getVerifiedVolunteers()
    VolModel->>DB: SELECT * FROM volunteers WHERE status='verified'
    DB-->>VolModel: Volunteer List
    VolModel-->>Controller: Return Volunteers
    Controller-->>UI: Display Volunteers
    
    Admin->>UI: Select Volunteer
    Admin->>UI: Select Camp
    Admin->>UI: Assign Role & Dates
    Admin->>UI: Submit Assignment
    
    UI->>Controller: POST assignment data
    Controller->>Controller: Validate Assignment
    
    alt Assignment Valid
        Controller->>CampModel: checkCapacity(camp_id)
        CampModel->>DB: SELECT capacity, occupancy
        DB-->>CampModel: Camp Info
        CampModel-->>Controller: Capacity Available
        
        Controller->>VolModel: createAssignment(assignmentData)
        VolModel->>DB: INSERT INTO volunteer_assignments
        DB-->>VolModel: assignment_id
        
        VolModel->>DB: UPDATE volunteer total_hours
        DB-->>VolModel: Updated
        
        VolModel-->>Controller: Assignment Created
        
        Controller->>Notification: notifyVolunteer(volunteer_id, assignment)
        Notification-->>Admin: Notification Sent
        
        Controller-->>UI: Success Response
        UI-->>Admin: Show Success Message
    else Assignment Invalid
        Controller-->>UI: Error Response
        UI-->>Admin: Show Error Message
    end
```

---

## 7. Entity-Relationship Diagram (ERD)

```mermaid
erDiagram
    USERS ||--o{ DISASTERS : creates
    USERS ||--o{ DONATIONS : makes
    USERS ||--o| VOLUNTEERS : is
    USERS ||--o{ RELIEF_CAMPS : manages
    
    DISASTERS ||--o{ RELIEF_CAMPS : has
    DISASTERS ||--o{ DONATIONS : receives
    
    RELIEF_CAMPS ||--o{ RESOURCES : contains
    RELIEF_CAMPS ||--o{ VOLUNTEER_ASSIGNMENTS : has
    
    VOLUNTEERS ||--o{ VOLUNTEER_ASSIGNMENTS : has
    
    USERS {
        int user_id PK
        string username
        string email
        string password_hash
        string full_name
        string phone
        enum role
        enum status
        timestamp created_at
    }
    
    DISASTERS {
        int disaster_id PK
        string disaster_name
        enum disaster_type
        string location
        enum severity
        text description
        int affected_population
        enum status
        datetime start_date
        int created_by FK
    }
    
    RELIEF_CAMPS {
        int camp_id PK
        int disaster_id FK
        string camp_name
        string location
        int capacity
        int current_occupancy
        enum status
        int manager_id FK
    }
    
    RESOURCES {
        int resource_id PK
        int camp_id FK
        string resource_name
        enum resource_type
        decimal quantity
        string unit
        enum status
    }
    
    DONATIONS {
        int donation_id PK
        int donor_id FK
        int disaster_id FK
        enum donation_type
        decimal amount
        text material_description
        enum status
        timestamp donation_date
    }
    
    VOLUNTEERS {
        int volunteer_id PK
        int user_id FK
        text skills
        enum availability
        enum verification_status
        decimal total_hours
    }
    
    VOLUNTEER_ASSIGNMENTS {
        int assignment_id PK
        int volunteer_id FK
        int camp_id FK
        string role
        date start_date
        decimal hours_worked
        enum status
    }
```

---

## Diagram Descriptions

### Use Case Diagram
Shows all possible interactions between system actors (Admin, Staff, Volunteer, Donor, Public) and the system functionalities.

### Class Diagram
Illustrates the object-oriented structure with all model classes, their attributes, methods, and relationships following the MVC pattern.

### Activity Diagram
Depicts the complete workflow from disaster occurrence through relief operations to resolution, including decision points and parallel processes.

### Data Flow Diagram
Shows how data moves between external entities, system processes, and data stores, illustrating the information flow architecture.

### Sequence Diagrams
Detail the step-by-step interactions between components for specific use cases (donation process and volunteer assignment).

### Entity-Relationship Diagram
Displays the database schema with all entities, their attributes, and relationships, showing cardinality and foreign key constraints.

---

## Design Patterns Used

1. **MVC (Model-View-Controller)**: Separates business logic, data, and presentation
2. **Singleton**: Database connection class ensures single instance
3. **Repository Pattern**: Models act as repositories for data access
4. **Front Controller**: AuthController handles all authentication requests

---

## Notes

- All diagrams are created using Mermaid syntax for easy rendering in markdown viewers
- Diagrams follow UML 2.0 standards
- Color coding in diagrams represents different actor roles and process types
- These diagrams should be included in project presentations and documentation
