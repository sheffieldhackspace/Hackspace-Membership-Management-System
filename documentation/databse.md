# Database

Bellow is the database schema for the Hackspace Membership Management System. This schema is designed to be used with a MySQL database.

The databse also contains tables defined by laravel and some packages namely which are not shown here ie:
- migrations
- jobs
- roles
- permissions

```mermaid
erDiagram
    users ||--o{ members : has
    users {
        uuid id PK
        string email
        datetime email_verified_at
        string password
        string remember_token
        datetime created_at
        datetime updated_at
    }
    members {
        uuid id PK
        string user_id FK
        string name
        string known_as
        string discord_id
        datetime created_at
        datetime updated_at
    }
    members ||--o{ membership_histories : has
    membership_histories {
        uuid id PK
        string member_id FK
        string membership_type
        datetime created_at
        datetime updated_at
    }
    members ||--o{ membership_rate_modifier : has
    membership_rate_modifier {
        uuid id PK
        string member_id FK
        float percentage
        period discount_start
        period discount_end
        datetime created_at
        datetime updated_at
    }
    membership_rates {
        uuid id PK
        int membership_type_id FK
        int rate
        datetime from
        datetime created_at
        datetime updated_at
    }
    members ||--|{ email_addresses : has
    email_addresses {
        uuid id PK
        string member_id FK
        string email_address
        datetime verified_at
        boolean is_primary
        datetime created_at
        datetime updated_at
    }
    members ||--|| postal_addresses : has
    postal_addresses {
        uuid id PK
        string member_id FK
        string line_1
        string line_2
        string line_3
        string city
        string county
        string postcode
        datetime created_at
        datetime updated_at
    }
    members ||--o| access_control_items : has
    access_control_items {
        uuid id PK
        string member_id FK
        string key_id
        string fob_id
        datetime given_at
        datetime returned_at
        datetime created_at
        datetime updated_at
    }
    members ||--o{ bank_references : has
    bank_references {
        uuid id PK
        string member_id FK
        string acc_num_sort_code_hash
        string bank_account_name
        string payment_reference
        datetime created_at
        datetime updated_at
    }
    members ||--|| keyholder_applications : has
    keyholder_applications {
        uuid id PK
        string member_id FK
        uuid proposed_by_member_id FK
        uuid seconded_by_member_id FK
        boolean id_verified
        boolean approved        
        datetime created_at
        datetime updated_at
    }
    members ||--|{ trustee_histories : has
    trustee_histories {
        uuid id PK
        string member_id FK
        string date_elected
        string date_resigned
        datetime created_at
        datetime updated_at
    }
    tools {
        uuid id PK
        string name
        string description
        string training_offered
        string training_required
        datetime created_at
        datetime updated_at
    }
    tools ||--o{ tool_trainers : has
    tool_trainers ||--o{ members : has
    tool_trainers {
        uuid id PK
        string tool_id FK
        string member_id FK
        datetime created_at
        datetime updated_at
    }
    tool_trainers ||--o{ training_sessions : has
    training_sessions {
        uuid id PK
        string tool_trainer_id FK
        datetime when
        int cost
        datetime created_at
        datetime updated_at
    }
    training_sessions ||--o{ training_signups : has
    training_signups ||--|{ members : has
    training_signups {
        uuid id PK
        string training_session_id FK
        string member_id FK
        boolean attended
        boolean paid
        boolean completed
        datetime created_at
        datetime updated_at
    }
    members ||--|{ training_requests : has
    training_requests ||--|{ tools : has
    training_requests {
        uuid id PK
        string member_id FK
        string tool FK
        string avaliablity
        datetime created_at
        datetime updated_at
    }
    members ||--o{ member_transactions : has
    member_transactions {
        uuid id PK
        string member_id FK
        string bank_transaction_id FK
        datetime created_at
        datetime updated_at
    }
    bank_transactions ||--|| member_transactions : has
    bank_transactions {
        uuid id PK
        float debit_amount
        float credit_amount
        string acc_num_sort_code_hash
        string description
        datetime date
        string transaction_type
        datetime created_at
        datetime updated_at
    }
```
