-- Assign all permissions to the SuperAdministrador role
-- This grants full access to the hosting panel

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p
WHERE r.name = 'SuperAdministrador';