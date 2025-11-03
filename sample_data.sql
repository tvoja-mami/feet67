-- ============================================
-- Default password for all accounts is: password
-- The hash used is the same for all accounts: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- ============================================

-- Clear existing data
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

-- Insert 180 students (100 original + 80 new)
INSERT INTO uporabniki (uporabnisko_ime, geslo, ime, priimek, email, vloga, razred) VALUES
-- Original students 1-15
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

-- New additional students (80 more)
('ucenec101', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Martin', 'Koren', 'martin.koren@student.si', 'ucenec', '1.A'),
('ucenec102', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Petra', 'Breznik', 'petra.breznik@student.si', 'ucenec', '1.A'),
('ucenec103', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nejc', 'Medved', 'nejc.medved@student.si', 'ucenec', '1.B'),
('ucenec104', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tjaša', 'Vidmar', 'tjasa.vidmar@student.si', 'ucenec', '1.B'),
('ucenec105', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Urban', 'Erjavec', 'urban.erjavec@student.si', 'ucenec', '1.C'),
('ucenec106', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Klara', 'Rozman', 'klara.rozman@student.si', 'ucenec', '2.A'),
('ucenec107', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jure', 'Mlakar', 'jure.mlakar@student.si', 'ucenec', '2.A'),
('ucenec108', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Zala', 'Kalan', 'zala.kalan@student.si', 'ucenec', '2.B'),
('ucenec109', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Vid', 'Petek', 'vid.petek@student.si', 'ucenec', '2.B'),
('ucenec110', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Neža', 'Logar', 'neza.logar@student.si', 'ucenec', '2.C'),

-- Continue with more students for each class
('ucenec111', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Blaž', 'Žagar', 'blaz.zagar@student.si', 'ucenec', '3.A'),
('ucenec112', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maša', 'Petrič', 'masa.petric@student.si', 'ucenec', '3.A'),
('ucenec113', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gal', 'Kobal', 'gal.kobal@student.si', 'ucenec', '3.B'),
('ucenec114', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tina', 'Bajc', 'tina.bajc@student.si', 'ucenec', '3.B'),

-- Rest of the original students
('ucenec98', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Laura', 'Vidic', 'laura.vidic@student.si', 'ucenec', '4.B'),
('ucenec99', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Matej', 'Oblak', 'matej.oblak@student.si', 'ucenec', '4.B'),
('ucenec100', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tina', 'Rus', 'tina.rus@student.si', 'ucenec', '4.B'),

-- More new students (continuing)
('ucenec115', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alex', 'Kern', 'alex.kern@student.si', 'ucenec', '4.A'),
('ucenec116', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mia', 'Pirc', 'mia.pirc@student.si', 'ucenec', '4.A'),
('ucenec117', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jan', 'Šuštar', 'jan.sustar@student.si', 'ucenec', '4.A'),
('ucenec118', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Eva', 'Zorman', 'eva.zorman@student.si', 'ucenec', '4.B'),
('ucenec119', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rok', 'Pečnik', 'rok.pecnik@student.si', 'ucenec', '4.B'),
('ucenec120', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pia', 'Urankar', 'pia.urankar@student.si', 'ucenec', '4.C'),
('ucenec121', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Matevž', 'Bevc', 'matevz.bevc@student.si', 'ucenec', '1.A'),
('ucenec122', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lucija', 'Rupnik', 'lucija.rupnik@student.si', 'ucenec', '1.B'),
('ucenec123', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Filip', 'Jenko', 'filip.jenko@student.si', 'ucenec', '2.A'),
('ucenec124', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tara', 'Pušnik', 'tara.pusnik@student.si', 'ucenec', '2.B'),
('ucenec125', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Aleks', 'Bohinc', 'aleks.bohinc@student.si', 'ucenec', '3.A'),
('ucenec126', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tilen', 'Hribar', 'tilen.hribar@student.si', 'ucenec', '1.A'),
('ucenec127', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Zoja', 'Koželj', 'zoja.kozelj@student.si', 'ucenec', '1.A'),
('ucenec128', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Matic', 'Mežnar', 'matic.meznar@student.si', 'ucenec', '1.A'),
('ucenec129', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Luna', 'Mrak', 'luna.mrak@student.si', 'ucenec', '1.A'),
('ucenec130', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Žiga', 'Lavrič', 'ziga.lavric@student.si', 'ucenec', '1.B'),
('ucenec131', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lana', 'Erjavec', 'lana.erjavec@student.si', 'ucenec', '1.B'),
('ucenec132', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Val', 'Škrlj', 'val.skrlj@student.si', 'ucenec', '1.B'),
('ucenec133', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ela', 'Kravos', 'ela.kravos@student.si', 'ucenec', '1.B'),
('ucenec134', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Anže', 'Černe', 'anze.cerne@student.si', 'ucenec', '1.C'),
('ucenec135', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kaja', 'Bogataj', 'kaja.bogataj@student.si', 'ucenec', '1.C'),
('ucenec136', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gašper', 'Mihelič', 'gasper.mihelic@student.si', 'ucenec', '2.A'),
('ucenec137', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Vita', 'Sever', 'vita.sever@student.si', 'ucenec', '2.A'),
('ucenec138', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Taj', 'Demšar', 'taj.demsar@student.si', 'ucenec', '2.A'),
('ucenec139', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mila', 'Ferjančič', 'mila.ferjancic@student.si', 'ucenec', '2.B'),
('ucenec140', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lan', 'Čebular', 'lan.cebular@student.si', 'ucenec', '2.B'),
('ucenec141', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ajda', 'Svetlin', 'ajda.svetlin@student.si', 'ucenec', '2.B'),
('ucenec142', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bor', 'Gregorič', 'bor.gregoric@student.si', 'ucenec', '2.C'),
('ucenec143', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tia', 'Majcen', 'tia.majcen@student.si', 'ucenec', '2.C'),
('ucenec144', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maks', 'Gorenc', 'maks.gorenc@student.si', 'ucenec', '3.A'),
('ucenec145', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Iza', 'Rus', 'iza.rus@student.si', 'ucenec', '3.A'),
('ucenec146', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mai', 'Zadravec', 'mai.zadravec@student.si', 'ucenec', '3.A'),
('ucenec147', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pika', 'Klančar', 'pika.klancar@student.si', 'ucenec', '3.B'),
('ucenec148', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nace', 'Skok', 'nace.skok@student.si', 'ucenec', '3.B'),
('ucenec149', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Manca', 'Bajec', 'manca.bajec@student.si', 'ucenec', '3.B'),
('ucenec150', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jaka', 'Tomšič', 'jaka.tomsic@student.si', 'ucenec', '3.C'),
('ucenec151', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ula', 'Bizjak', 'ula.bizjak@student.si', 'ucenec', '3.C'),
('ucenec152', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Niko', 'Strgar', 'niko.strgar@student.si', 'ucenec', '4.A'),
('ucenec153', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lili', 'Knez', 'lili.knez@student.si', 'ucenec', '4.A'),
('ucenec154', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Oskar', 'Stare', 'oskar.stare@student.si', 'ucenec', '4.A'),
('ucenec155', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Zara', 'Hvala', 'zara.hvala@student.si', 'ucenec', '4.B'),
('ucenec156', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nik', 'Lunar', 'nik.lunar@student.si', 'ucenec', '4.B'),
('ucenec157', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gaja', 'Murn', 'gaja.murn@student.si', 'ucenec', '4.B'),
('ucenec158', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Leo', 'Rupar', 'leo.rupar@student.si', 'ucenec', '4.C'),
('ucenec159', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kiara', 'Zalokar', 'kiara.zalokar@student.si', 'ucenec', '4.C'),
('ucenec160', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Erik', 'Žnidar', 'erik.znidar@student.si', 'ucenec', '1.A'),
('ucenec161', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Iris', 'Červ', 'iris.cerv@student.si', 'ucenec', '1.B'),
('ucenec162', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dan', 'Nose', 'dan.nose@student.si', 'ucenec', '1.C'),
('ucenec163', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ela', 'Žužek', 'ela.zuzek@student.si', 'ucenec', '2.A'),
('ucenec164', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ian', 'Toplak', 'ian.toplak@student.si', 'ucenec', '2.B'),
('ucenec165', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mia', 'Pirc', 'mia.pirc@student.si', 'ucenec', '2.C'),
('ucenec166', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bine', 'Železnik', 'bine.zeleznik@student.si', 'ucenec', '3.A'),
('ucenec167', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lara', 'Čop', 'lara.cop@student.si', 'ucenec', '3.B'),
('ucenec168', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Teo', 'Lebar', 'teo.lebar@student.si', 'ucenec', '3.C'),
('ucenec169', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alja', 'Rupar', 'alja.rupar@student.si', 'ucenec', '4.A'),
('ucenec170', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Arne', 'Zajc', 'arne.zajc@student.si', 'ucenec', '4.B'),
('ucenec171', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Uma', 'Vidic', 'uma.vidic@student.si', 'ucenec', '4.C'),
('ucenec172', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tine', 'Oman', 'tine.oman@student.si', 'ucenec', '1.A'),
('ucenec173', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Neja', 'Leban', 'neja.leban@student.si', 'ucenec', '1.B'),
('ucenec174', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lin', 'Dolenc', 'lin.dolenc@student.si', 'ucenec', '1.C'),
('ucenec175', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Erin', 'Kavčič', 'erin.kavcic@student.si', 'ucenec', '2.A'),
('ucenec176', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Urban', 'Šuštar', 'urban.sustar@student.si', 'ucenec', '2.B'),
('ucenec177', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Zoja', 'Cesar', 'zoja.cesar@student.si', 'ucenec', '2.C'),
('ucenec178', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rene', 'Zorko', 'rene.zorko@student.si', 'ucenec', '3.A'),
('ucenec179', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maja', 'Logar', 'maja.logar@student.si', 'ucenec', '3.B'),
('ucenec180', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tai', 'Podobnik', 'tai.podobnik@student.si', 'ucenec', '3.C'),
('ucenec181', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ema', 'Petrič', 'ema.petric@student.si', 'ucenec', '4.A'),
('ucenec182', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Val', 'Mavec', 'val.mavec@student.si', 'ucenec', '4.B'),
('ucenec183', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nina', 'Bratož', 'nina.bratoz@student.si', 'ucenec', '4.C'),
('ucenec184', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tim', 'Kobal', 'tim.kobal@student.si', 'ucenec', '4.A'),
('ucenec185', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Ferjančič', 'ana.ferjancic@student.si', 'ucenec', '4.B');

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
