-- Temporarily disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- Clear all related tables first
TRUNCATE TABLE `oddaje`;
TRUNCATE TABLE `ucitelji_predmeti`;
TRUNCATE TABLE `ucenci_predmeti`;
TRUNCATE TABLE `uporabniki`;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Add admin user with password 'admin123'
INSERT INTO `uporabniki` (`uporabnisko_ime`, `geslo`, `ime`, `priimek`, `email`, `vloga`) VALUES
('admin', '$2y$10$8WrFULAqUWgj6oWP8aqgUenp8QJGoP6i3qVHcPvQXHMcMH1BtdLgG', 'Administrator', 'Sistema', 'admin@school.com', 'admin');