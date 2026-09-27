-- RBAC System Verification Script
-- Executes ALL required MySQL verifications for the permissions system

-- 1. DESCRIBE permissions table structure
SELECT '1. DESCRIBE permissions table structure' AS STEP;
DESCRIBE permissions;

-- 2. Count permissions
SELECT '2. Count of permissions' AS STEP;
SELECT COUNT(*) as total_permissions FROM permissions;

-- 3. Show all permissions
SELECT '3. All permissions in database' AS STEP;
SELECT id, name FROM permissions ORDER BY id;

-- 4. Check roles table
SELECT '4. Check roles table exists and has roles' AS STEP;
SELECT * FROM roles;

-- 5. Check user_roles table
SELECT '5. Check user_roles table structure and data' AS STEP;
SELECT * FROM user_roles;

-- 6. Check role_permissions table
SELECT '6. Check role_permissions table structure and data' AS STEP;
SELECT * FROM role_permissions;

-- 7. Verify SuperAdministrador role has all permissions
SELECT '7. SuperAdministrador role permission count' AS STEP;
SELECT r.name as role_name, COUNT(rp.permission_id) as permission_count
FROM roles r
LEFT JOIN role_permissions rp ON r.id = rp.role_id
WHERE r.name = 'SuperAdministrador'
GROUP BY r.id, r.name;

-- 8. Check for duplicate permissions
SELECT '8. Duplicate permissions check' AS STEP;
SELECT name, COUNT(*) as count 
FROM permissions 
GROUP BY name 
HAVING COUNT(*) > 1;

-- 9. Check for duplicate role_permissions
SELECT '9. Duplicate role_permissions check' AS STEP;
SELECT role_id, permission_id, COUNT(*) as count 
FROM role_permissions 
GROUP BY role_id, permission_id 
HAVING COUNT(*) > 1;

-- 10. Check foreign key constraints in information_schema
SELECT '10. FK constraints for role_permissions table' AS STEP;
SELECT 
       CONSTRAINT_NAME,
       TABLE_NAME,
       COLUMN_NAME,
       REFERENCED_TABLE_NAME,
       REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'pccurico_hosting'
AND TABLE_NAME = 'role_permissions'
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- 11. Check users table for SuperAdministrador user
SELECT '11. SuperAdministrador user' AS STEP;
SELECT id, name, email FROM users WHERE name = 'SuperAdministrador';

-- 12. Verify User::permissions() logic with actual query
-- Get SuperAdministrador user_id first
SELECT '12. User::permissions() equivalent for SuperAdministrador' AS STEP;
SET @user_id = (SELECT id FROM users WHERE name = 'SuperAdministrador');

SELECT CONCAT('User ID for SuperAdministrador: ', @user_id) AS INFO;

SELECT DISTINCT
    p.id,
    p.name
FROM permissions p
INNER JOIN role_permissions rp
    ON rp.permission_id = p.id
INNER JOIN roles r
    ON r.id = rp.role_id
INNER JOIN user_roles ur
    ON ur.role_id = r.id
WHERE ur.user_id = @user_id
ORDER BY p.name;