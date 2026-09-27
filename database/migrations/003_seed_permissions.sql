-- Seed permissions into the permissions table
-- 15 permissions for the hosting panel

INSERT IGNORE INTO permissions (name) VALUES
('domains'),
('dns'),
('apache'),
('php'),
('mysql'),
('databases'),
('mail'),
('ssl'),
('backups'),
('logs'),
('settings'),
('tools'),
('audit'),
('users'),
('roles');