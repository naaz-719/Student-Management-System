CREATE TABLE students (
  enrollment_no VARCHAR(50) PRIMARY KEY,
  student_name VARCHAR(100) NOT NULL,
  department VARCHAR(100) NOT NULL,
  phone VARCHAR(15)
);

CREATE TABLE subjects (
  subject_id VARCHAR(50) PRIMARY KEY,
  subject_name VARCHAR(100) NOT NULL,
  department VARCHAR(100) NOT NULL
);

CREATE TABLE attendance (
  enrollment_no VARCHAR(50),
  date DATE,
  status ENUM('Present', 'Absent') NOT NULL,
  PRIMARY KEY (enrollment_no, date),
  FOREIGN KEY (enrollment_no) REFERENCES students(enrollment_no) ON DELETE CASCADE
);


CREATE TABLE marks (
  enrollment_no VARCHAR(50),
  subject_id VARCHAR(50),
  marks INT NOT NULL,
  PRIMARY KEY (enrollment_no, subject_id),
  FOREIGN KEY (enrollment_no) REFERENCES students(enrollment_no) ON DELETE CASCADE,
  FOREIGN KEY (subject_id) REFERENCES subjects(subject_id) ON DELETE CASCADE
);


CREATE TABLE users (
  username VARCHAR(50) NOT NULL PRIMARY KEY,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);