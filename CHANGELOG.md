# Changelog

## 2025-10-31

### Added
- Subject Dashboard for Teachers
  - Added responsive grid layout of subject cards
  - Each card shows subject name, enrollment code, and statistics
  - Quick access buttons for materials, assignments, and overview
  - Visual feedback on hover

### Fixed
- Fixed error in `profesor_page_new.php` regarding undefined `nalozeno_ob` column
  - Updated to use correct column name `nalozen_ob`
  - Removed non-existent 'opis' field references
  - Updated file upload logic to match database structure

- Fixed access control in `naloge.php` and `gradiva.php`
  - Added proper permission checking
  - Added redirect for invalid subject access
  - Improved security with proper JOIN queries
  - Added proper error handling

### Changed
- Updated navigation in `profesor_page_new.php`
  - Added subject selection dropdown
  - Added smart navigation buttons
  - Improved user flow for accessing materials and assignments
  - Added clear error messages for missing subject selection

### Security
- Implemented proper access control for subjects
  - Teachers can only access their assigned subjects
  - Students can only access enrolled subjects
  - Added verification checks before displaying content

### Database Structure
Current table structures:

#### gradiva (Materials)
```sql
CREATE TABLE `gradiva` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `id_predmet` BIGINT UNSIGNED NOT NULL,
    `naslov` VARCHAR(255) NOT NULL,
    `pot_datoteke` VARCHAR(512) NOT NULL,
    `izvirno_ime_datoteke` VARCHAR(255) NOT NULL,
    `nalozen_ob` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_predmet`) REFERENCES `predmeti`(`id`) ON DELETE CASCADE
);
```

#### predmeti (Subjects)
```sql
CREATE TABLE `predmeti` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ime` VARCHAR(255) NOT NULL,
    `opis` TEXT DEFAULT NULL,
    `kljuc_za_vpis` VARCHAR(8) NOT NULL UNIQUE
);
```

### File Structure Updates
- `profesor_page_new.php`: New dashboard interface
- `naloge.php`: Updated assignment management
- `gradiva.php`: Updated materials management
- `login_register.php`: Updated to use new professor page

### User Interface Improvements
1. Subject Cards
   - At-a-glance view of all subjects
   - Student count, material count, assignment count
   - Quick access buttons for common tasks
   - Visual feedback on interaction

2. Navigation
   - Centralized subject selection
   - Improved error handling
   - Clear user feedback
   - Consistent styling

3. Materials Management
   - Streamlined upload interface
   - Better file handling
   - Improved error messages
   - Proper access control

### Next Steps
1. Consider adding:
   - Batch upload for materials
   - Better file type validation
   - Preview functionality for materials
   - Search functionality for large subject lists
   - Sorting options for materials and assignments

2. Potential Improvements:
   - Add material categories
   - Implement material versioning
   - Add student progress tracking
   - Enhance notification system