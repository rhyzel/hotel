CREATE TABLE guest_feedback (
  id INT NOT NULL AUTO_INCREMENT,
  booking_id VARCHAR(50) NOT NULL,
  rating VARCHAR(20) NOT NULL,
  feedback_text TEXT,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample Data
INSERT INTO guest_feedback (booking_id, rating, feedback_text, submitted_at) VALUES
('101', 'Excellent', 'Very clean and comfortable stay.', NOW()),
('102', 'Good', 'Staff were friendly but room was a bit small.', NOW()),
('103', 'Poor', 'Aircon not working properly.', NOW()),
('104', 'Excellent', 'Perfect location and great breakfast.', NOW()),
('105', '', 'No comment.', NOW());
