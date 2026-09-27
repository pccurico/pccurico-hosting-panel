-- Add permissions table for fine-grained access control
-- This table stores individual permissions that can be assigned to roles

CREATE TABLE IF NOT EXISTS permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert the real hosting panel permissions (15 total)
INSERT INTO permissions (name, description) VALUES
('domains', 'Manage domains (create, read, update, delete)'),
('dns', 'Manage DNS records (create, read, update, delete)'),
('apache', 'Manage Apache configurations (create, read, update, delete)'),
('php', 'Manage PHP settings and versions (create, read, update, delete)'),
('mysql', 'Manage MySQL databases and users (create, read, update, delete)'),
('databases', 'Manage MySQL database content (create, read, update, delete)'),
('mail', 'Manage mail configurations and accounts (create, read, update, delete)'),
('ssl', 'Manage SSL certificates and configurations (create, read, update, delete)'),
('backups', 'Manage system backups (create, read, update, delete)'),
('logs', 'Manage system logs (create, read, update, delete)'),
('settings', 'Manage system settings (create, read, update, delete)'),
('tools', 'Manage server administration tools (create, read, update, delete)'),
('audit', 'Manage audit logs (create, read, update, delete)'),
('users', 'Manage users (create, read, update, delete)'),
('roles', 'Manage roles (create, read, update, delete)');

-- Create role_permissions table to link roles with permissions
-- This establishes the many-to-many relationship between roles and permissions

CREATE TABLE IF NOT EXISTS role_permissions (
    role_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,
    granted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (role_id, permission_id),
    CONSTRAINT fk_role_permissions_role
        FOREIGN KEY (role_id) REFERENCES roles(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_role_permissions_permission
        FOREIGN KEY (permission_id) REFERENCES permissions(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Grant permissions to SuperAdministrador role (all permissions)
-- Insert into role_permissions for the SuperAdministrador role
INSERT INTO role_permissions (role_id, permission_id) 
SELECT r.id, p.id 
FROM roles r 
JOIN permissions p 
WHERE r.name = 'SuperAdministrador';