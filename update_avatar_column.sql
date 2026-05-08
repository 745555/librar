-- Add avatar column to library_staff table
ALTER TABLE library_staff ADD COLUMN avatar VARCHAR(255) NULL AFTER phone;

-- Add index for better performance
ALTER TABLE library_staff ADD INDEX idx_avatar (avatar);
