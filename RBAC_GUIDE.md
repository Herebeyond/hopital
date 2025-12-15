# Role-Based Access Control (RBAC) - Medical Management System

## Overview

The medical management system implements role-based access control to ensure that users can only perform actions appropriate to their role within the hospital.

## Role Hierarchy

The system defines the following role hierarchy in [config/packages/security.yaml](config/packages/security.yaml):

```yaml
role_hierarchy:
    ROLE_ADMIN: [ROLE_DOCTOR, ROLE_NURSE, ROLE_USER]
    ROLE_DOCTOR: [ROLE_USER]
    ROLE_NURSE: [ROLE_USER]
```

### Role Definitions

- **ROLE_USER**: Base role for all authenticated users
  - ✅ View all medical data (patients, donors, transplants)
  - ✅ Search and filter records
  - ✅ View detailed information
  - ❌ Cannot add, edit, or delete data

- **ROLE_NURSE**: Healthcare staff with read-only access
  - Inherits all permissions from ROLE_USER
  - ✅ View patient records and transplant history
  - ✅ View donor information
  - ❌ Cannot modify any medical records

- **ROLE_DOCTOR**: Medical doctors with clinical data modification rights
  - Inherits all permissions from ROLE_USER
  - ✅ View all medical data
  - ✅ Add new patients, donors, and transplants
  - ✅ Edit existing medical records
  - ❌ Cannot delete records (reserved for ROLE_ADMIN due to data protection regulations)

- **ROLE_ADMIN**: System administrators with full permissions
  - Inherits all permissions from ROLE_DOCTOR, ROLE_NURSE, and ROLE_USER
  - ✅ View all medical data
  - ✅ Add new patients, donors, and transplants
  - ✅ Edit existing medical records
  - ✅ Delete records (with audit logging and appropriate safeguards)
  - ✅ Manage reference data
  - ✅ User and role management

## Implementation

### Controller Level Protection

All medical management pages require authentication:

```php
#[IsGranted('ROLE_USER')]
#[Route('/medical')]
class MedicalController extends AbstractController
```

Future create/update/delete methods will be role-protected:

```php
// Doctors can create and edit
#[Route('/patient/new', name: 'medical_patient_new')]
#[IsGranted('ROLE_DOCTOR')]
public function newPatient(Request $request): Response
{
    // Doctors and admins can create patients
}

// Only admins can delete
#[Route('/patient/{id}/delete', name: 'medical_patient_delete')]
#[IsGranted('ROLE_ADMIN')]
public function deletePatient(int $id): Response
{
    // Only admins can delete records
}
```

### Template Level Protection

Templates use Twig's `is_granted()` function to conditionally display role-based buttons:

```twig
{# Doctors can see add/edit buttons #}
{% if is_granted('ROLE_DOCTOR') %}
    <button class="btn btn-success">
        <i class="fas fa-plus"></i> Ajouter
    </button>
    <button class="btn btn-warning">
        <i class="fas fa-edit"></i> Modifier
    </button>
{% endif %}

{# Only admins can see delete buttons #}
{% if is_granted('ROLE_ADMIN') %}
    <button class="btn btn-danger">
        <i class="fas fa-trash"></i> Supprimer
    </button>
{% endif %}
```

## Current Status

### ✅ Implemented
- Role hierarchy configuration
- Controller-level authentication (ROLE_USER required)
- Template-level role checks for UI elements
- ROLE_DOCTOR for medical data modification (add/edit)
- ROLE_ADMIN for deletion and critical operations
- All roles properly configured in database

### 🚧 To Be Implemented
- Create/Edit/Delete functionality for:
  - Patients
  - Donors (living and deceased)
  - Transplants (Greffes)
  - Reference data
- Form validation and security
- Audit logging for data modifications

## Usage Examples

### Viewing Data (All Authenticated Users)

Any user with ROLE_USER or higher can:
- Access `/medical/` - Medical dashboard
- Access `/medical/patients` - View patient list
- Access `/medical/patient/{id}` - View patient details
- Access `/medical/donneurs` - View donor list
- Access `/medical/greffes` - View transplant list
- Access `/medical/references` - View reference data

### Modifying Data (ROLE_DOCTOR and Above)

When implemented, users with ROLE_DOCTOR or higher will be able to:
- Create new patients: `/medical/patient/new`
- Edit patients: `/medical/patient/{id}/edit`
- Create new donors: `/medical/donneur/new`
- Edit donors: `/medical/donneur/{id}/edit`
- Register transplants: `/medical/greffe/new`
- Edit transplants: `/medical/greffe/{id}/edit`

### Deleting Data (ROLE_ADMIN Only)

Only users with ROLE_ADMIN can delete records:
- Delete patients: `/medical/patient/{id}/delete`
- Delete donors: `/medical/donneur/{id}/delete`
- Delete transplants: `/medical/greffe/{id}/delete`
- Manage reference data

## Assigning Roles to Users

Roles are assigned through the `Etre` table (User-Profile relationship) in the database:

```sql
-- Example: Assign ROLE_ADMIN to a user
INSERT INTO Profil (id_profil, Role) VALUES ('admin', 'ROLE_ADMIN');
INSERT INTO Etre (id_utilisateur, id_profil) VALUES ('user123', 'admin');

-- Example: Assign ROLE_NURSE to a user
INSERT INTO Profil (id_profil, Role) VALUES ('nurse', 'ROLE_NURSE');
INSERT INTO Etre (id_utilisateur, id_profil) VALUES ('user456', 'nurse');
```

## Security Best Practices

1. **Always check roles in both controller AND template**
   - Controller: Prevents unauthorized access
   - Template: Hides UI elements from unauthorized users

2. **Use the role hierarchy**
   - ROLE_ADMIN automatically includes ROLE_USER permissions
   - No need to check for multiple roles

3. **Validate input even for admins**
   - Role checks prevent unauthorized access
   - Input validation prevents data corruption

4. **Audit important actions**
   - Log who modified what and when
   - Especially important for patient data (GDPR compliance)

## Testing Role-Based Access

To test the role-based access control:

1. **As a regular user/nurse (ROLE_USER or ROLE_NURSE):**
   - Login with a user account that only has ROLE_USER or ROLE_NURSE
   - Navigate to medical management pages
   - Verify you can view data but see no add/edit/delete buttons

2. **As a doctor (ROLE_DOCTOR):**
   - Login with a doctor account
   - Navigate to medical management pages
   - Verify you see add/edit buttons (disabled placeholders)
   - Verify you do NOT see delete buttons (reserved for admins)

3. **As an admin (ROLE_ADMIN):**
   - Login with an admin account
   - Navigate to medical management pages
   - Verify you see add/edit/delete buttons (disabled placeholders)
   - All buttons will become functional when CRUD operations are implemented

## Future Enhancements

- **Fine-grained ROLE_DOCTOR permissions**: Different specialties with specific access
- **ROLE_LAB**: Laboratory staff with access to test results only
- **ROLE_COORDINATOR**: Transplant coordinators with specific workflows
- **Attribute-based access control (ABAC)**: Context-aware permissions (e.g., only modify own patients)
- **Audit logging**: Track all modifications by doctors and admins
- **Data retention policies**: Automatic archiving instead of deletion
