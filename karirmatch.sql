create database karirmatch;

USE karirmatch;

-- ================
-- USERS
-- =========================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    phone VARCHAR(20),
    class ENUM('10','11','12'),
    gender ENUM('L','P'),
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- CONTACT
-- =========================
CREATE TABLE contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    subject VARCHAR(150),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- MBTI TYPES
-- sesuai MBTI_DATA
-- =========================
CREATE TABLE mbti_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) UNIQUE,
    name VARCHAR(100),
    description TEXT
);

CREATE TABLE mbti_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,

    mbti_code VARCHAR(10),
    field_name VARCHAR(150),

    FOREIGN KEY (mbti_code)
    REFERENCES mbti_types(code)
    ON DELETE CASCADE
);

-- =========================
-- RIASEC TYPES
-- sesuai RIASEC_INFO
-- =========================
CREATE TABLE riasec_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(2) UNIQUE,
    label VARCHAR(100)
);

CREATE TABLE riasec_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT,
    riasec_type CHAR(1)
);

-- =========================
-- CAREERS
-- =========================
CREATE TABLE careers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    career_name VARCHAR(150) UNIQUE
);

-- =========================
-- MAJORS / JURUSAN
-- sesuai JURUSAN
-- =========================
CREATE TABLE majors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    major_name VARCHAR(150) UNIQUE
);

-- =========================
-- MBTI -> CAREERS
-- sesuai:
-- MBTI_DATA.careers
-- =========================
CREATE TABLE mbti_careers (
    id INT AUTO_INCREMENT PRIMARY KEY,

    mbti_code VARCHAR(10),
    career_id INT,

    FOREIGN KEY (mbti_code)
    REFERENCES mbti_types(code)
    ON DELETE CASCADE,

    FOREIGN KEY (career_id)
    REFERENCES careers(id)
    ON DELETE CASCADE
);

-- =========================
-- MBTI -> RIASEC
-- sesuai:
-- MBTI_DATA.riasec
-- =========================
CREATE TABLE mbti_riasec (
    id INT AUTO_INCREMENT PRIMARY KEY,

    mbti_code VARCHAR(10),
    riasec_code VARCHAR(2),

    FOREIGN KEY (mbti_code)
    REFERENCES mbti_types(code)
    ON DELETE CASCADE,

    FOREIGN KEY (riasec_code)
    REFERENCES riasec_types(code)
    ON DELETE CASCADE
);

-- =========================
-- RIASEC -> CAREERS
-- sesuai:
-- RIASEC_INFO.careers
-- =========================
CREATE TABLE riasec_careers (
    id INT AUTO_INCREMENT PRIMARY KEY,

    riasec_code VARCHAR(2),
    career_id INT,

    FOREIGN KEY (riasec_code)
    REFERENCES riasec_types(code)
    ON DELETE CASCADE,

    FOREIGN KEY (career_id)
    REFERENCES careers(id)
    ON DELETE CASCADE
);

-- =========================
-- RIASEC -> MAJORS
-- sesuai:
-- JURUSAN
-- =========================
CREATE TABLE riasec_majors (
    id INT AUTO_INCREMENT PRIMARY KEY,

    riasec_code VARCHAR(2),
    major_id INT,

    FOREIGN KEY (riasec_code)
    REFERENCES riasec_types(code)
    ON DELETE CASCADE,

    FOREIGN KEY (major_id)
    REFERENCES majors(id)
    ON DELETE CASCADE
);

-- =========================
-- USER TEST RESULTS
-- hasil quiz user
-- =========================
CREATE TABLE test_results (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT,

    mbti_code VARCHAR(10),

    riasec_r DECIMAL(5,2),
    riasec_i DECIMAL(5,2),
    riasec_a DECIMAL(5,2),
    riasec_s DECIMAL(5,2),
    riasec_e DECIMAL(5,2),
    riasec_c DECIMAL(5,2),

    top_riasec VARCHAR(50),
    
    recommended_major VARCHAR(255),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE,

    FOREIGN KEY (mbti_code)
    REFERENCES mbti_types(code)
    ON DELETE SET NULL
);

ALTER TABLE test_results
ADD recommended_careers TEXT;

-- =========================
-- DEFAULT ADMIN
-- =========================
INSERT INTO users (
    name,
    email,
    password,
    role
)
VALUES (
    'Admin',
    'admin@gmail.com',
    'admin123',
    'admin'
);

-- =========================
-- INSERT MBTI TYPES
-- =========================

INSERT INTO mbti_types (code, name, description) VALUES
('INTJ', 'Arsitek', 'Strategis, mandiri, dan logis'),
('INTP', 'Logisi', 'Analitis, kreatif, dan objektif'),
('ENTJ', 'Komandan', 'Tegas, ambisius, dan pemimpin'),
('ENTP', 'Debater', 'Inovatif, energetik, dan argumentatif'),
('INFJ', 'Advokat', 'Idealis, empati, dan visioner'),
('INFP', 'Mediator', 'Empatik, kreatif, dan idealistis'),
('ENFJ', 'Protagonis', 'Karismatik, inspiratif, dan pemimpin'),
('ENFP', 'Juru Kampanye', 'Antusias, kreatif, dan sosial'),
('ISTJ', 'Logisi', 'Bertanggung jawab, teratur, dan loyal'),
('ISFJ', 'Pembela', 'Peduli, teliti, dan setia'),
('ESTJ', 'Eksekutif', 'Terorganisir, tegas, dan efisien'),
('ESFJ', 'Konsul', 'Peduli, sosial, dan populer'),
('ISTP', 'Virtuoso', 'Praktis, analitis, dan tenang'),
('ISFP', 'Petualang', 'Fleksibel, ramah, dan artistik'),
('ESTP', 'Pengusaha', 'Energetik, adaptif, dan praktis'),
('ESFP', 'Penghibur', 'Spontan, energik, dan sosial');

INSERT INTO mbti_fields (mbti_code, field_name) VALUES
('ENFP', 'Komunikasi'),
('ENFP', 'Seni'),
('ENFP', 'Bisnis'),
('ENFP', 'Psikologi'),

('INTJ', 'Teknologi'),
('INTJ', 'Riset'),
('INTJ', 'Keuangan'),
('INTJ', 'Hukum'),
('INTJ', 'Arsitektur'),

('INTP','Teknologi'),
('INTP','Sains'),
('INTP','Pendidikan'),
('INTP','Matematika'),

('ENTJ','Bisnis'),
('ENTJ','Hukum'),
('ENTJ','Keuangan'),
('ENTJ','Manajemen'),

('ENTP','Bisnis'),
('ENTP','Teknologi'),
('ENTP','Hukum'),
('ENTP','Jurnalisme'),

('INFJ','Psikologi'),
('INFJ','Pendidikan'),
('INFJ','Seni'),
('INFJ','Sosial'),

('INFP','Seni'),
('INFP','Sastra'),
('INFP','Psikologi'),
('INFP','Sosial'),

('ENFJ','Pendidikan'),
('ENFJ','Psikologi'),
('ENFJ','Komunikasi'),
('ENFJ','Sosial'),

('ISTJ','Akuntansi'),
('ISTJ','Hukum'),
('ISTJ','Militer'),
('ISTJ','Administrasi'),
('ISTJ','IT'),

('ISFJ','Kesehatan'),
('ISFJ','Pendidikan'),
('ISFJ','Administrasi'),
('ISFJ','Sosial'),

('ESTJ','Manajemen'),
('ESTJ','Hukum'),
('ESTJ','Bisnis'),
('ESTJ','Militer'),

('ESFJ','Kesehatan'),
('ESFJ','Pendidikan'),
('ESFJ','Sosial'),
('ESFJ','Hospitality'),

('ISTP','Teknik'),
('ISTP','Teknologi'),
('ISTP','Mekanik'),
('ISTP','Sains'),

('ISFP','Seni'),
('ISFP','Desain'),
('ISFP','Kesehatan'),
('ISFP','Kuliner'),

('ESTP','Bisnis'),
('ESTP','Olahraga'),
('ESTP','Pariwisata'),
('ESTP','Sales'),

('ESFP','Seni'),
('ESFP','Hiburan'),
('ESFP','Hospitality'),
('ESFP','Olahraga');

-- =========================
-- INSERT RIASEC TYPES
-- =========================

INSERT INTO riasec_types (code, label) VALUES
('R', 'Realistis'),
('I', 'Investigatif'),
('A', 'Artistik'),
('S', 'Sosial'),
('E', 'Enterprising'),
('C', 'Konvensional');

-- =========================
-- INSERT MBTI -> RIASEC
-- =========================

INSERT INTO mbti_riasec (mbti_code, riasec_code) VALUES

('INTJ', 'I'),
('INTJ', 'C'),
('INTJ', 'E'),

('INTP', 'I'),
('INTP', 'A'),
('INTP', 'C'),

('ENTJ', 'E'),
('ENTJ', 'C'),
('ENTJ', 'I'),

('ENTP', 'E'),
('ENTP', 'I'),
('ENTP', 'A'),

('INFJ', 'S'),
('INFJ', 'I'),
('INFJ', 'A'),

('INFP', 'A'),
('INFP', 'S'),
('INFP', 'I'),

('ENFJ', 'S'),
('ENFJ', 'E'),
('ENFJ', 'A'),

('ENFP', 'A'),
('ENFP', 'S'),
('ENFP', 'E'),

('ISTJ', 'C'),
('ISTJ', 'R'),
('ISTJ', 'I'),

('ISFJ', 'S'),
('ISFJ', 'C'),
('ISFJ', 'R'),

('ESTJ', 'E'),
('ESTJ', 'C'),
('ESTJ', 'R'),

('ESFJ', 'S'),
('ESFJ', 'E'),
('ESFJ', 'C'),

('ISTP', 'R'),
('ISTP', 'I'),
('ISTP', 'C'),

('ISFP', 'A'),
('ISFP', 'R'),
('ISFP', 'S'),

('ESTP', 'E'),
('ESTP', 'R'),
('ESTP', 'S'),

('ESFP', 'S'),
('ESFP', 'A'),
('ESFP', 'E');

-- =========================
-- INSERT RIASEC QUESTIONS
-- =========================

INSERT INTO riasec_questions (question, riasec_type) VALUES
('Saya senang memperbaiki atau membuat sesuatu secara fisik (mesin, bangunan, kerajinan tangan)', 'R'),
('Saya suka bekerja di luar ruangan dan menggunakan alat atau mesin', 'R'),
('Saya senang menganalisis data, memecahkan masalah matematika, atau bereksperimen', 'I'),
('Saya tertarik mempelajari konsep ilmiah dan melakukan penelitian', 'I'),
('Saya senang menggambar, menulis, bermain musik, atau berekspresi kreatif', 'A'),
('Saya suka pekerjaan yang tidak memiliki aturan ketat dan memberikan kebebasan berkreasi', 'A'),
('Saya senang membantu orang lain, mengajar, atau memberikan dukungan emosional', 'S'),
('Saya peduli dengan kesejahteraan orang lain dan senang bekerja dalam tim', 'S'),
('Saya senang memimpin, membujuk, atau memengaruhi orang lain untuk mencapai tujuan', 'E'),
('Saya tertarik pada bisnis, politik, atau posisi yang memiliki pengaruh dan kekuasaan', 'E'),
('Saya senang pekerjaan yang teratur, sistematis, dan mengikuti prosedur yang jelas', 'C'),
('Saya teliti dalam mengorganisir data, dokumen, atau hal-hal yang berhubungan dengan angka', 'C');

-- =========================
-- INSERT CAREERS
-- =========================

INSERT INTO careers (career_name) VALUES

('Insinyur Sipil'),
('Teknisi Mesin'),
('Pilot'),
('Petani Modern'),
('Mekanik'),
('Operator CNC'),

('Data Scientist'),
('Peneliti'),
('Dokter'),
('Ilmuwan'),
('Programmer'),
('Ahli Bioteknologi'),

('Desainer Grafis'),
('Penulis'),
('Arsitek'),
('Seniman'),
('Fotografer'),
('UI/UX Designer'),

('Psikolog'),
('Guru'),
('Perawat'),
('Konselor'),
('HR Manager'),
('Pekerja Sosial'),

('Entrepreneur'),
('Manajer Bisnis'),
('Pengacara'),
('Sales Director'),
('Marketing Manager'),
('CEO'),

('Akuntan'),
('Auditor'),
('Analis Data'),
('Banker'),
('Manajer Operasional'),
('Notaris');

-- =========================
-- INSERT RIASEC -> CAREERS
-- =========================

INSERT INTO riasec_careers (riasec_code, career_id)

SELECT 'R', id FROM careers
WHERE career_name IN (
'Insinyur Sipil',
'Teknisi Mesin',
'Pilot',
'Petani Modern',
'Mekanik',
'Operator CNC'
);

INSERT INTO riasec_careers (riasec_code, career_id)

SELECT 'I', id FROM careers
WHERE career_name IN (
'Data Scientist',
'Peneliti',
'Dokter',
'Ilmuwan',
'Programmer',
'Ahli Bioteknologi'
);

INSERT INTO riasec_careers (riasec_code, career_id)

SELECT 'A', id FROM careers
WHERE career_name IN (
'Desainer Grafis',
'Penulis',
'Arsitek',
'Seniman',
'Fotografer',
'UI/UX Designer'
);

INSERT INTO riasec_careers (riasec_code, career_id)

SELECT 'S', id FROM careers
WHERE career_name IN (
'Psikolog',
'Guru',
'Perawat',
'Konselor',
'HR Manager',
'Pekerja Sosial'
);

INSERT INTO riasec_careers (riasec_code, career_id)

SELECT 'E', id FROM careers
WHERE career_name IN (
'Entrepreneur',
'Manajer Bisnis',
'Pengacara',
'Sales Director',
'Marketing Manager',
'CEO'
);

INSERT INTO riasec_careers (riasec_code, career_id)

SELECT 'C', id FROM careers
WHERE career_name IN (
'Akuntan',
'Auditor',
'Analis Data',
'Banker',
'Manajer Operasional',
'Notaris'
);

-- =========================
-- INSERT MAJORS
-- =========================

INSERT INTO majors (major_name) VALUES

('Teknik Mesin'),
('Teknik Sipil'),
('Teknik Elektro'),
('Pertanian'),
('Kehutanan'),
('Teknik Penerbangan'),

('Matematika'),
('Fisika'),
('Informatika'),
('Statistika'),
('Kedokteran'),
('Bioteknologi'),

('Desain Komunikasi Visual'),
('Seni Rupa'),
('Arsitektur'),
('Sastra'),
('Film & TV'),
('Fotografi'),

('Psikologi'),
('Pendidikan'),
('Sosiologi'),
('Keperawatan'),
('Ilmu Komunikasi'),
('Pekerjaan Sosial'),

('Manajemen'),
('Bisnis Internasional'),
('Hukum'),
('Ilmu Politik'),
('Ekonomi'),
('Marketing'),

('Akuntansi'),
('Keuangan'),
('Administrasi Bisnis'),
('Sistem Informasi'),
('Perpajakan');

-- =========================
-- INSERT RIASEC -> MAJORS
-- =========================

INSERT INTO riasec_majors (riasec_code, major_id)

SELECT 'R', id FROM majors
WHERE major_name IN (
'Teknik Mesin',
'Teknik Sipil',
'Teknik Elektro',
'Pertanian',
'Kehutanan',
'Teknik Penerbangan'
);

INSERT INTO riasec_majors (riasec_code, major_id)

SELECT 'I', id FROM majors
WHERE major_name IN (
'Matematika',
'Fisika',
'Informatika',
'Statistika',
'Kedokteran',
'Bioteknologi'
);

INSERT INTO riasec_majors (riasec_code, major_id)

SELECT 'A', id FROM majors
WHERE major_name IN (
'Desain Komunikasi Visual',
'Seni Rupa',
'Arsitektur',
'Sastra',
'Film & TV',
'Fotografi'
);

INSERT INTO riasec_majors (riasec_code, major_id)

SELECT 'S', id FROM majors
WHERE major_name IN (
'Psikologi',
'Pendidikan',
'Sosiologi',
'Keperawatan',
'Ilmu Komunikasi',
'Pekerjaan Sosial'
);

INSERT INTO riasec_majors (riasec_code, major_id)

SELECT 'E', id FROM majors
WHERE major_name IN (
'Manajemen',
'Bisnis Internasional',
'Hukum',
'Ilmu Politik',
'Ekonomi',
'Marketing'
);

INSERT INTO riasec_majors (riasec_code, major_id)

SELECT 'C', id FROM majors
WHERE major_name IN (
'Akuntansi',
'Keuangan',
'Administrasi Bisnis',
'Sistem Informasi',
'Perpajakan'
);

INSERT INTO mbti_careers (mbti_code, career_id)
SELECT 'INTJ', id FROM careers
WHERE career_name IN (
'Software Engineer',
'Data Scientist',
'Analis Keuangan',
'Pengacara',
'Arsitek',
'Peneliti',
'Product Manager'
);

INSERT INTO mbti_careers (mbti_code, career_id)
SELECT 'INTP', id FROM careers
WHERE career_name IN (
'Programmer',
'Matematikawan',
'Dosen',
'Peneliti AI',
'Analis Sistem',
'Ilmuwan Data'
);

INSERT INTO mbti_careers (mbti_code, career_id)
SELECT 'ENTJ', id FROM careers
WHERE career_name IN (
'CEO',
'Pengacara',
'Manajer Proyek',
'Konsultan',
'Entrepreneur',
'Direktur'
);

INSERT INTO mbti_careers (mbti_code, career_id)
SELECT 'ENTP', id FROM careers
WHERE career_name IN (
'Entrepreneur',
'Pengacara',
'Konsultan Inovasi',
'Jurnalis',
'Pengembang Produk',
'Marketer'
);

INSERT INTO mbti_careers (mbti_code, career_id)
SELECT 'INFJ', id FROM careers
WHERE career_name IN (
'Psikolog',
'Konselor',
'Penulis',
'Guru',
'Pekerja Sosial',
'HR Manager'
);

INSERT INTO mbti_careers (mbti_code, career_id)
SELECT 'INFP', id FROM careers
WHERE career_name IN (
'Penulis',
'Desainer',
'Psikolog',
'Guru',
'Jurnalis',
'Seniman'
);

INSERT INTO mbti_careers (mbti_code, career_id)
SELECT 'ENFJ', id FROM careers
WHERE career_name IN (
'Guru',
'Konselor',
'HR Manager',
'Public Relations',
'Diplomat'
);

INSERT INTO mbti_careers (mbti_code, career_id)
SELECT 'ENFP', id FROM careers
WHERE career_name IN (
'Jurnalis',
'PR Specialist',
'Guru',
'Konselor',
'Desainer',
'Marketer'
);

INSERT INTO mbti_careers (mbti_code, career_id)
SELECT 'ISTJ', id FROM careers
WHERE career_name IN (
'Akuntan',
'Manajer Keuangan',
'Auditor',
'Manajer Proyek',
'Analis Sistem'
);

INSERT INTO mbti_careers (mbti_code, career_id)
SELECT 'ISFJ', id FROM careers
WHERE career_name IN (
'Perawat',
'Dokter',
'Guru',
'Apoteker',
'Konselor'
);

INSERT INTO mbti_careers (mbti_code, career_id)
SELECT 'ESTJ', id FROM careers
WHERE career_name IN (
'Manajer',
'Pengacara',
'Direktur',
'Financial Planner',
'Pegawai Pemerintah'
);

INSERT INTO mbti_careers (mbti_code, career_id)
SELECT 'ESFJ', id FROM careers
WHERE career_name IN (
'Perawat',
'Guru',
'HR Manager',
'Event Organizer',
'Sales Manager'
);

INSERT INTO mbti_careers (mbti_code, career_id)
SELECT 'ISTP', id FROM careers
WHERE career_name IN (
'Engineer Mesin',
'Teknisi',
'Pilot',
'Programmer',
'Mekanik'
);

INSERT INTO mbti_careers (mbti_code, career_id)
SELECT 'ISFP', id FROM careers
WHERE career_name IN (
'Desainer',
'Seniman',
'Chef',
'Perawat',
'Fotografer',
'Fashion Designer'
);

INSERT INTO mbti_careers (mbti_code, career_id)
SELECT 'ESTP', id FROM careers
WHERE career_name IN (
'Sales Manager',
'Entrepreneur',
'Marketer',
'Event Organizer',
'Broker'
);

INSERT INTO mbti_careers (mbti_code, career_id)
SELECT 'ESFP', id FROM careers
WHERE career_name IN (
'Aktor',
'MC',
'Event Organizer',
'Guru SD',
'Entertainer'
);
