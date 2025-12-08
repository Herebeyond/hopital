# Database Schema Changes - Applied Successfully ✅

## Date: December 1, 2025

## Summary
The database schema has been successfully updated with CHECK constraints, triggers, cascade rules, and improved junction table naming. All changes have been applied to the PostgreSQL database.

---

## 1. CHECK Constraints Added

### Donneur Table
- **G_sanguin**: Limited to valid blood types ('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-')
- **Sexe**: Boolean constraint (FALSE=Femme, TRUE=Homme)

### Donneur_v Table (Living Donor)
- **Robot**: Boolean constraint for robotic surgery (FALSE=Non, TRUE=Oui)

### Donneur_d Table (Deceased Donor)
- **Donneur_criteres_etendus**: Boolean constraint (extended criteria donor)
- **Arret_cardiaque**: Boolean constraint (cardiac arrest)
- **Duree_cardiaque**: Must be >= 00:00:00
- **PA_moyenne**: Must be >= 0 (mean arterial pressure)
- **Amines**: Boolean constraint (vasopressor support)
- **CGR, CPA, PFC**: Boolean constraints (blood product transfusions)
- **DFG**: Must be >= 0 (glomerular filtration rate)
- **Plaques_aorte**: Boolean constraint

### Patient Table
- **Date_naissance**: Must be <= CURRENT_DATE (birth date in the past)

### Greffe Table (Transplant)
- **Date_greffe**: Must be <= CURRENT_DATE
- **Rang_greffe**: Must be >= 1 (1st, 2nd, 3rd transplant, etc.)
- **Type_donneur**: Limited to ('vivant', 'décédé')
- **Greffon_fonctionnel**: Boolean constraint
- **Cote_prelevement_rein**: Limited to ('Gauche', 'Droit')
- **Cote_transplantation_rein**: Limited to ('Gauche', 'Droit')
- **Ischémie_total**: Must be >= 00:00:00
- **Duree_anastomoses**: Must be >= 0 (duration in minutes)
- **Sonde_jj, Protocole, Dialyse**: Boolean constraints

---

## 2. Cascade Rules Applied

### ON DELETE CASCADE
Applied to child tables that should be automatically deleted when parent is deleted:
- **Valeur_groupe_HLA** → Donneur
- **Personnel_medical** → Utilisateur
- **Autre** → Utilisateur
- **Donneur_v** → Donneur
- **Donneur_d** → Donneur
- **Etre** (junction table)
- **Participer** (junction table)
- **Greffe_Statut_Virologique** (junction table)
- **Greffe_Incompatibilite_HLA** (junction table)
- **Donneur_Serologie** (junction table)

### ON DELETE RESTRICT
Applied to prevent deletion when dependencies exist:
- **Patient** → Utilisateur (cannot delete user if patients exist)
- **Greffe** → Donneur (cannot delete donor if transplants exist)
- **Greffe** → Patient (cannot delete patient if transplants exist)

### ON UPDATE CASCADE
Applied to all foreign keys to propagate ID changes automatically.

---

## 3. Triggers Implemented

### ✅ check_donneur_type_insert_v
- **Table**: Donneur_v
- **Timing**: BEFORE INSERT
- **Purpose**: Prevents a donor from being both living AND deceased
- **Error Message**: "Un donneur ne peut pas être à la fois vivant et décédé"

### ✅ check_donneur_type_insert_d
- **Table**: Donneur_d
- **Timing**: BEFORE INSERT
- **Purpose**: Prevents a donor from being both deceased AND living
- **Error Message**: "Un donneur ne peut pas être à la fois vivant et décédé"

### ✅ check_greffe_fin_fonctionnement
- **Table**: Greffe
- **Timing**: BEFORE INSERT
- **Purpose**: Ensures that if a graft is non-functional, both Date_heure_fin and Cause_fin_fonct_gref must be provided
- **Error Message**: "Date et cause de fin doivent être renseignées si le greffon n'est plus fonctionnel"

### ✅ check_greffe_fin_fonctionnement_update
- **Table**: Greffe
- **Timing**: BEFORE UPDATE
- **Purpose**: Same validation as above for updates
- **Error Message**: "Date et cause de fin doivent être renseignées si le greffon n'est plus fonctionnel"

### ✅ prevent_patient_deletion
- **Table**: Patient
- **Timing**: BEFORE DELETE
- **Purpose**: Prevents deletion of patients who have associated transplants
- **Error Message**: "Impossible de supprimer un patient ayant des greffes associées"

---

## 4. Junction Tables Renamed

Better naming for clarity and maintainability:

| Old Name | New Name | Purpose |
|----------|----------|---------|
| **Toto** | **Greffe_Statut_Virologique** | Links transplant to virological statuses (CMV, EBV, etc.) |
| **Tota** | **Greffe_Incompatibilite_HLA** | Links transplant to HLA incompatibilities |
| **Tatu** | **Donneur_Serologie** | Links donor to serology results |

---

## 5. Comments Added

Comprehensive table-level comments have been added to all tables for documentation purposes:
- Each table now has a COMMENT describing its purpose
- Comments are visible in database tools and help with understanding schema

---

## 6. Database Structure

### Total Tables Created: 28

**Main Entity Tables:**
- Profil, Personnel_ope, Donneur, Utilisateur, Patient, Greffe

**Reference/Lookup Tables:**
- Lien_parente, Voie_abord, Cause_deces
- Statut_virologique, Valeur_viriologique
- Incompatibilite_HLA, Valeur_I_HLA
- Risque_immunologique, Conditionnement_immunosupresseur
- Groupe_HLA, Valeur_groupe_HLA
- Groupe_serologie, Valeur_serologie

**Specialization Tables:**
- Personnel_medical, Autre
- Donneur_v (living donor), Donneur_d (deceased donor)

**Junction Tables:**
- Etre (User-Profile)
- Participer (Transplant-Operating Personnel)
- Greffe_Statut_Virologique (Transplant-Virology)
- Greffe_Incompatibilite_HLA (Transplant-HLA)
- Donneur_Serologie (Donor-Serology)

### Total Triggers: 5
### Total Functions: 4

---

## 7. Files Modified/Created

1. ✅ **database.txt** - Updated with comments, CHECK constraints, triggers, and renamed junction tables
2. ✅ **database_postgres.sql** - PostgreSQL-compatible SQL script with all changes
3. ✅ **DATABASE_CHANGES.md** - This documentation file

---

## 8. Verification Commands

### List all tables:
```bash
docker compose exec -T database psql -U app -d app -c "\dt"
```

### List all triggers:
```bash
docker compose exec -T database psql -U app -d app -c "SELECT trigger_name, event_object_table FROM information_schema.triggers WHERE trigger_schema = 'public';"
```

### View table details (e.g., Greffe):
```bash
docker compose exec -T database psql -U app -d app -c "\d greffe"
```

---

## 9. Next Steps (Awaiting Approval)

The following improvements were identified but **NOT implemented** as per your request:

### Additional Indexes (Not Applied)
- Index on `Greffe.Date_greffe` for date range queries
- Index on `Patient.Ndossier` for frequent lookups
- Index on `Utilisateur.Nom, Prenom` for search functionality

### Additional Tables (Not Applied)
- Audit_log table for change tracking
- Patient_HLA table for recipient HLA typing
- Greffe_complications table for post-transplant complications
- Suivi_medical table for follow-up appointments

---

## Status: ✅ SUCCESSFULLY APPLIED

All approved changes (CHECK constraints, triggers, cascades, and junction table renaming) have been successfully applied to the PostgreSQL database.

The database is now running with enhanced data integrity and validation rules.
