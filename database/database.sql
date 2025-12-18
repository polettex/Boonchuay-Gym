-- ============================================
-- BOONCHUAY GYM - DATABASE SCHEMA
-- MySQL Database for Gym Management System
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS boonchuay_gym CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE boonchuay_gym;

-- ============================================
-- TABLE CREATION (without foreign keys)
-- ============================================

-- Table: usuarios
-- Stores admin and staff user accounts
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'staff') DEFAULT 'staff',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: disciplinas
-- Stores martial arts disciplines offered by the gym
CREATE TABLE disciplinas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    imagen VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: horarios
-- Stores class schedules for each discipline
CREATE TABLE horarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    disciplina_id INT NOT NULL,
    dia_semana ENUM('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: leads
-- Stores contact form submissions and potential clients
CREATE TABLE leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150),
    telefono VARCHAR(20),
    mensaje TEXT,
    origen ENUM('web','chatbot','landing') DEFAULT 'web',
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: faq
-- Stores frequently asked questions and answers for chatbot
CREATE TABLE faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pregunta VARCHAR(255) NOT NULL,
    respuesta TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: chatbot_logs
-- Stores chatbot conversation history
CREATE TABLE chatbot_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario TEXT,
    bot TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- FOREIGN KEY RELATIONSHIPS
-- ============================================

-- Add foreign key for horarios.disciplina_id -> disciplinas.id
ALTER TABLE horarios
ADD CONSTRAINT fk_horarios_disciplina
FOREIGN KEY (disciplina_id) REFERENCES disciplinas(id)
ON DELETE CASCADE
ON UPDATE CASCADE;

-- ============================================
-- SEED DATA - Initial Data Population
-- ============================================

-- Insert Disciplines
INSERT INTO disciplinas (nombre, descripcion, imagen) VALUES
('Muay Thai', 'El arte de las ocho extremidades. Desarrolla potencia, resistencia y técnica con uno de los deportes de combate más completos del mundo.\n\n🥊 ¿Quieres formarte en esta gran disciplina?\nNo dudes en venir a probar una clase sin ningún compromiso, ¡te engancharás!\n\n🗓 Miércoles y Viernes\n19h a 20:30h\n📍 Boonchuay Gym', 'images/muay-thai.jpg'),
('Boxeo', 'La noble arte. Perfecciona tu técnica de puños, velocidad y movimiento de pies con entrenadores experimentados.\n\n🥊 ¿Quieres formarte en esta gran disciplina?\nNo dudes en venir a probar una clase sin ningún compromiso, ¡te engancharás!\n\n🗓 Martes y Jueves\n19h a 20:30h\n📍 Boonchuay Gym', 'images/boxing.jpg'),
('Jeet Kune Do', 'El camino del puño interceptor creado por Bruce Lee. Un sistema de combate directo, eficiente y sin formas rígidas.\n\n🥊 ¿Quieres formarte en esta gran disciplina?\nNo dudes en venir a probar una clase sin ningún compromiso, ¡te engancharás!\n\n🗓 Lunes y Miércoles\n20h a 21:30h\n📍 Boonchuay Gym', 'images/jkd.jpg'),
('Kali', 'Arte marcial filipino que enfatiza el combate con armas y mano vacía. Desarrolla coordinación, timing y fluidez de movimiento.\n\n🥊 ¿Quieres formarte en esta gran disciplina?\nNo dudes en venir a probar una clase sin ningún compromiso, ¡te engancharás!\n\n🗓 Martes y Jueves\n17h a 18:30h\n📍 Boonchuay Gym', 'images/kali.jpg'),
('MMA', 'Artes Marciales Mixtas. Combina técnicas de striking y grappling de diversas disciplinas. Entrenamiento completo que desarrolla todas las áreas del combate.\n\n🥊 ¿Quieres formarte en esta gran disciplina?\nNo dudes en venir a probar una clase sin ningún compromiso, ¡te engancharás!\n\n🗓 Miércoles y Viernes\n19h a 20:30h\n📍 Boonchuay Gym', 'images/mma.jpg');

-- Insert Sample Schedules
-- Muay Thai schedule (disciplina_id = 1)
INSERT INTO horarios (disciplina_id, dia_semana, hora_inicio, hora_fin) VALUES
(1, 'Lunes', '18:00:00', '19:30:00'),
(1, 'Miércoles', '18:00:00', '19:30:00'),
(1, 'Viernes', '18:00:00', '19:30:00');

-- Boxeo schedule (disciplina_id = 2)
INSERT INTO horarios (disciplina_id, dia_semana, hora_inicio, hora_fin) VALUES
(2, 'Martes', '19:00:00', '20:30:00'),
(2, 'Jueves', '19:00:00', '20:30:00');

-- Jeet Kune Do schedule (disciplina_id = 3)
INSERT INTO horarios (disciplina_id, dia_semana, hora_inicio, hora_fin) VALUES
(3, 'Lunes', '20:00:00', '21:30:00'),
(3, 'Miércoles', '20:00:00', '21:30:00');

-- Kali schedule (disciplina_id = 4)
INSERT INTO horarios (disciplina_id, dia_semana, hora_inicio, hora_fin) VALUES
(4, 'Martes', '17:00:00', '18:30:00'),
(4, 'Jueves', '17:00:00', '18:30:00');

-- MMA schedule (disciplina_id = 5)
INSERT INTO horarios (disciplina_id, dia_semana, hora_inicio, hora_fin) VALUES
(5, 'Miércoles', '19:00:00', '20:30:00'),
(5, 'Viernes', '19:00:00', '20:30:00');

-- Insert FAQs for chatbot
INSERT INTO faq (pregunta, respuesta) VALUES
('¿Cuál es el horario del gimnasio?', 'Nuestro horario es de Lunes a Viernes de 9:00 a 22:00. Los fines de semana (Sábado y Domingo) estamos cerrados.'),
("¿Dónde están ubicados?", "Estamos ubicados en Carrer d\'Eusebi Güell 14, 08830 Sant Boi de Llobregat. Puedes encontrarnos fácilmente en el centro de Sant Boi."),
('¿Tienen clases para niños?', 'Sí, tenemos clases para niños en un ambiente seguro y familiar. Nuestros instructores están especializados en enseñanza infantil. Contacta con nosotros para más detalles sobre horarios y grupos de edad.'),
('¿Qué disciplinas ofrecen?', 'Ofrecemos cuatro disciplinas principales: Muay Thai (el arte de las ocho extremidades), Boxeo (la noble arte), Jeet Kune Do (el sistema de Bruce Lee) y Kali/Eskrima (arte marcial filipino). Todas las clases son para adultos y niños.'),
('¿Cuánto cuesta la mensualidad?', 'Para información sobre precios y cuotas, por favor contacta con nosotros al 931 70 98 45 o envía un mensaje a través del formulario de contacto. Tenemos diferentes planes adaptados a tus necesidades.'),
('¿Ofrecen clase de prueba gratuita?', 'Sí, ofrecemos una clase de prueba gratuita para que puedas conocer nuestras instalaciones y el estilo de enseñanza. Rellena el formulario de contacto o llámanos al 931 70 98 45 para reservar tu plaza.'),
('¿Necesito experiencia previa?', 'No, aceptamos alumnos de todos los niveles, desde principiantes hasta avanzados. Nuestros instructores adaptarán las clases a tu nivel y progreso individual.'),
('¿Qué necesito traer a la primera clase?', 'Para la primera clase solo necesitas ropa deportiva cómoda y una botella de agua. El gimnasio proporciona el equipo básico para las clases de prueba. Después podrás adquirir tu propio equipo.');

-- Insert Admin User (password should be hashed in production)
INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Administrador', 'admin@boonchuaygym.com', 'CAMBIAR_PASSWORD', 'admin');

-- ============================================
-- VERIFICATION QUERIES (optional - for testing)
-- ============================================

-- Uncomment to verify data insertion:
-- SELECT * FROM disciplinas;
-- SELECT * FROM horarios;
-- SELECT * FROM faq;
-- SELECT * FROM usuarios;
-- SELECT h.id, d.nombre AS disciplina, h.dia_semana, h.hora_inicio, h.hora_fin 
-- FROM horarios h 
-- JOIN disciplinas d ON h.disciplina_id = d.id 
-- ORDER BY h.dia_semana, h.hora_inicio;
