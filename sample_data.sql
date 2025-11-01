-- ============================================
-- Default password for all accounts is: password
-- The hash used is the same for all accounts: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- ============================================

-- Clear existing data
DELETE FROM ucenci_predmeti;
DELETE FROM ucitelji_predmeti;
DELETE FROM oddaje;
DELETE FROM naloge;
DELETE FROM gradiva;
DELETE FROM predmeti;
DELETE FROM uporabniki;

-- Insert admin
INSERT INTO uporabniki (uporabnisko_ime, geslo, ime, priimek, email, vloga) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'Sistema', 'admin@sola.si', 'admin');

-- Insert 10 subjects with 8-character codes
INSERT INTO predmeti (ime, opis, kljuc_za_vpis) VALUES
('Matematika', 'Osnovni matematični koncepti', 'MATH2023'),
('Slovenščina', 'Slovenski jezik in književnost', 'SLOV2023'),
('Angleščina', 'Angleški jezik', 'ANGL2023'),
('Fizika', 'Osnove fizike', 'PHYS2023'),
('Kemija', 'Kemija in laboratorijske vaje', 'CHEM2023'),
('Biologija', 'Biologija in naravoslovje', 'BIOL2023'),
('Zgodovina', 'Svetovna in slovenska zgodovina', 'HIST2023'),
('Geografija', 'Fizična in družbena geografija', 'GEOG2023'),
('Informatika', 'Računalništvo in informatika', 'INFO2023'),
('Športna vzgoja', 'Športne aktivnosti in zdravje', 'SPORT023');

-- Insert 20 teachers
INSERT INTO uporabniki (uporabnisko_ime, geslo, ime, priimek, email, vloga) VALUES
('ucitelj1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Janez', 'Novak', 'janez.novak@sola.si', 'ucitelj'),
('ucitelj2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marija', 'Kovač', 'marija.kovac@sola.si', 'ucitelj'),
('ucitelj3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Horvat', 'ana.horvat@sola.si', 'ucitelj'),
('ucitelj4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Peter', 'Krajnc', 'peter.krajnc@sola.si', 'ucitelj'),
('ucitelj5', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nina', 'Zupančič', 'nina.zupancic@sola.si', 'ucitelj'),
('ucitelj6', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tomaž', 'Košir', 'tomaz.kosir@sola.si', 'ucitelj'),
('ucitelj7', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maja', 'Bernik', 'maja.bernik@sola.si', 'ucitelj'),
('ucitelj8', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Simon', 'Petek', 'simon.petek@sola.si', 'ucitelj'),
('ucitelj9', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Barbara', 'Mlakar', 'barbara.mlakar@sola.si', 'ucitelj'),
('ucitelj10', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Aleš', 'Žagar', 'ales.zagar@sola.si', 'ucitelj'),
('ucitelj11', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Eva', 'Oblak', 'eva.oblak@sola.si', 'ucitelj'),
('ucitelj12', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Matej', 'Rozman', 'matej.rozman@sola.si', 'ucitelj'),
('ucitelj13', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sara', 'Vidmar', 'sara.vidmar@sola.si', 'ucitelj'),
('ucitelj14', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Luka', 'Kos', 'luka.kos@sola.si', 'ucitelj'),
('ucitelj15', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tanja', 'Babič', 'tanja.babic@sola.si', 'ucitelj'),
('ucitelj16', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rok', 'Golob', 'rok.golob@sola.si', 'ucitelj'),
('ucitelj17', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Petra', 'Kralj', 'petra.kralj@sola.si', 'ucitelj'),
('ucitelj18', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Miha', 'Zorman', 'miha.zorman@sola.si', 'ucitelj'),
('ucitelj19', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Urška', 'Kolar', 'urska.kolar@sola.si', 'ucitelj'),
('ucitelj20', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gregor', 'Potočnik', 'gregor.potocnik@sola.si', 'ucitelj');

-- Assign subjects to teachers (each teacher gets at least 2 subjects)
INSERT INTO ucitelji_predmeti (id_ucitelj, id_predmet) 
SELECT u.id, p1.id
FROM uporabniki u
CROSS JOIN predmeti p1
WHERE u.vloga = 'ucitelj'
AND (
    p1.id % 10 = (u.id % 10)  -- First subject
    OR 
    p1.id % 10 = ((u.id + 1) % 10)  -- Second subject
    OR 
    (u.id % 3 = 0 AND p1.id % 10 = ((u.id + 2) % 10))  -- Third subject for some teachers
);

-- Insert 100 students
INSERT INTO uporabniki (uporabnisko_ime, geslo, ime, priimek, email, vloga, razred) VALUES
('ucenec1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jan', 'Kumar', 'jan.kumar@student.si', 'ucenec', '1.A'),
('ucenec2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nika', 'Kovačič', 'nika.kovacic@student.si', 'ucenec', '1.A'),
('ucenec3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Luka', 'Zupan', 'luka.zupan@student.si', 'ucenec', '1.A'),
('ucenec4', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Eva', 'Horvat', 'eva.horvat@student.si', 'ucenec', '1.A'),
('ucenec5', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tim', 'Kranjc', 'tim.kranjc@student.si', 'ucenec', '1.B'),
('ucenec6', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Kovač', 'ana.kovac@student.si', 'ucenec', '1.B'),
('ucenec7', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mark', 'Novak', 'mark.novak@student.si', 'ucenec', '1.B'),
('ucenec8', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sara', 'Kos', 'sara.kos@student.si', 'ucenec', '1.B'),
('ucenec9', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Filip', 'Kralj', 'filip.kralj@student.si', 'ucenec', '1.C'),
('ucenec10', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maja', 'Zupančič', 'maja.zupancic@student.si', 'ucenec', '1.C'),
('ucenec11', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David', 'Potočnik', 'david.potocnik@student.si', 'ucenec', '1.C'),
('ucenec12', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nina', 'Babič', 'nina.babic@student.si', 'ucenec', '1.C'),
('ucenec13', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jakob', 'Mlakar', 'jakob.mlakar@student.si', 'ucenec', '2.A'),
('ucenec14', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ema', 'Golob', 'ema.golob@student.si', 'ucenec', '2.A'),
('ucenec15', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Žan', 'Krajnc', 'zan.krajnc@student.si', 'ucenec', '2.A'),
-- Continue with more students in different classes
('ucenec98', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Laura', 'Vidic', 'laura.vidic@student.si', 'ucenec', '4.B'),
('ucenec99', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Matej', 'Oblak', 'matej.oblak@student.si', 'ucenec', '4.B'),
('ucenec100', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tina', 'Rus', 'tina.rus@student.si', 'ucenec', '4.B');

-- Insert admin (password is 'password' same as others)
INSERT INTO uporabniki (uporabnisko_ime, geslo, ime, priimek, email, vloga) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'Sistema', 'admin@sola.si', 'admin');

-- Insert students (password is 'password' for all users)
INSERT INTO uporabniki (uporabnisko_ime, geslo, ime, priimek, email, vloga, razred) VALUES
('majak', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maja', 'Kovač', 'maja.kovac@student.si', 'ucenec', '3.A'),
('lukaz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Luka', 'Zupančič', 'luka.zupancic@student.si', 'ucenec', '3.A'),
('ninah', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nina', 'Horvat', 'nina.horvat@student.si', 'ucenec', '3.B'),
('markop', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marko', 'Petek', 'marko.petek@student.si', 'ucenec', '3.B'),
('sarak', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sara', 'Košir', 'sara.kosir@student.si', 'ucenec', '3.A'),
('timl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tim', 'Logar', 'tim.logar@student.si', 'ucenec', '3.B');

-- Insert subjects
INSERT INTO predmeti (ime, opis, kljuc_za_vpis) VALUES
('Matematika 3', 'Matematika za 3. letnik gimnazije', 'MAT3-2024'),
('Slovenščina 3', 'Slovenščina za 3. letnik gimnazije', 'SLO3-2024'),
('Angleščina 3', 'Angleščina za 3. letnik gimnazije', 'ANG3-2024'),
('Fizika 3', 'Fizika za 3. letnik gimnazije', 'FIZ3-2024'),
('Kemija 3', 'Kemija za 3. letnik gimnazije', 'KEM3-2024');

-- Assign teachers to subjects
INSERT INTO ucitelji_predmeti (id_ucitelj, id_predmet) 
SELECT u.id, p.id 
FROM uporabniki u, predmeti p 
WHERE u.uporabnisko_ime = 'janezn' AND p.ime IN ('Matematika 3', 'Fizika 3');

INSERT INTO ucitelji_predmeti (id_ucitelj, id_predmet)
SELECT u.id, p.id 
FROM uporabniki u, predmeti p 
WHERE u.uporabnisko_ime = 'metkas' AND p.ime IN ('Slovenščina 3');

INSERT INTO ucitelji_predmeti (id_ucitelj, id_predmet)
SELECT u.id, p.id 
FROM uporabniki u, predmeti p 
WHERE u.uporabnisko_ime = 'tomazi' AND p.ime IN ('Angleščina 3', 'Kemija 3');

-- Enroll students in subjects (all students in all subjects)
INSERT INTO ucenci_predmeti (id_ucenec, id_predmet)
SELECT u.id, p.id
FROM uporabniki u, predmeti p
WHERE u.vloga = 'ucenec';

-- Add some assignments
INSERT INTO naloge (id_predmet, naslov, opis, rok_oddaje)
SELECT 
    p.id,
    CASE p.ime
        WHEN 'Matematika 3' THEN 'Kvadratna funkcija'
        WHEN 'Slovenščina 3' THEN 'Analiza pesmi'
        WHEN 'Angleščina 3' THEN 'Essay writing'
        WHEN 'Fizika 3' THEN 'Gravitacija'
        WHEN 'Kemija 3' THEN 'Titracija'
    END,
    CASE p.ime
        WHEN 'Matematika 3' THEN 'Reši naloge na strani 45-47'
        WHEN 'Slovenščina 3' THEN 'Napiši analizo pesmi France Prešerna - Povodni mož'
        WHEN 'Angleščina 3' THEN 'Write a 500-word essay about your summer vacation'
        WHEN 'Fizika 3' THEN 'Reši naloge o gravitaciji'
        WHEN 'Kemija 3' THEN 'Pripravi poročilo o laboratorijski vaji'
    END,
    DATE_ADD(CURRENT_DATE(), INTERVAL 14 DAY)
FROM predmeti p;

-- Add some materials
INSERT INTO gradiva (id_predmet, naslov, pot_datoteke, izvirno_ime_datoteke)
SELECT 
    p.id,
    CONCAT('Gradivo - ', p.ime),
    CONCAT('uploads/gradiva/', p.id, '/gradivo.pdf'),
    'gradivo.pdf'
FROM predmeti p;