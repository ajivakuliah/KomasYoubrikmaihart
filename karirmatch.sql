use karirmatch;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    phone VARCHAR(20),
    class ENUM('10','11','12'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    subject VARCHAR(150),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE mbti_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10),       -- contoh: INTJ
    name VARCHAR(100),      -- contoh: Architect
    description TEXT
);

-- pertanyaan MBTI
CREATE TABLE mbti_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT,
    dimension ENUM('E-I','S-N','T-F','J-P')
);

CREATE TABLE careers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    description TEXT,
    category VARCHAR(100)
);

CREATE TABLE mbti_careers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mbti_code VARCHAR(10),
    career_id INT,
    FOREIGN KEY (career_id) REFERENCES careers(id)
);

CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    mbti VARCHAR(10),
    riasec VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE riasec_scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    result_id INT,
    R INT,
    I INT,
    A INT,
    S INT,
    E INT,
    C INT,

    FOREIGN KEY (result_id) REFERENCES results(id)
);

-- pertanyaan RIASEC
CREATE TABLE riasec_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT,
    type ENUM('R','I','A','S','E','C')
);

-- metode tes
CREATE TABLE test_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    description TEXT
);

-- FAQ
CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT,
    answer TEXT
);

-- fitur unggulan
CREATE TABLE features (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100),
    description TEXT,
    icon VARCHAR(100)
);

-- homepage content
CREATE TABLE homepage (
    id INT PRIMARY KEY,
    hero_title VARCHAR(200),
    hero_desc TEXT
);

-- footer
CREATE TABLE footer (
    id INT PRIMARY KEY,
    content TEXT
);

ALTER TABLE users ADD role ENUM('admin','user') DEFAULT 'user';

-- buat admin baru (password TANPA HASH dulu)
INSERT INTO users (name, email, password, role)
VALUES ('Admin', 'admin@gmail.com', 'admin123', 'admin');

select * from users;