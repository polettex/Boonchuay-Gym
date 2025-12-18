<?php
/**
 * BOONCHUAY GYM - MAIN PAGE
 * Dynamic landing page with database-driven content
 */

// Include database connection
require_once 'config/db.php';

// Fetch disciplines from database
try {
    $stmt = $pdo->query("SELECT * FROM disciplinas ORDER BY id");
    $disciplinas = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching disciplines: " . $e->getMessage());
    $disciplinas = [];
}

// Fetch schedules from database (grouped by discipline and day)
try {
    $stmt = $pdo->query("
        SELECT h.*, d.nombre AS disciplina_nombre 
        FROM horarios h 
        JOIN disciplinas d ON h.disciplina_id = d.id 
        ORDER BY 
            FIELD(h.dia_semana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'),
            h.hora_inicio
    ");
    $horarios = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching schedules: " . $e->getMessage());
    $horarios = [];
}

// Helper function to extract benefits from description
function extractBenefits($descripcion)
{
    // This is a simple implementation - you can enhance it
    $benefits = [
        'Mejora cardiovascular',
        'Técnica completa',
        'Defensa personal'
    ];
    return $benefits;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Basic Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- SEO Meta Tags -->
    <title>Boonchuay Gym Sant Boi | Gimnasio Muay Thai, Boxeo, MMA, JKD y Kali</title>
    <meta name="description"
        content="🥊 Gimnasio de artes marciales en Sant Boi de Llobregat. Clases de Muay Thai, Boxeo, MMA, Jeet Kune Do y Kali. Prueba gratis. Adultos y niños. ☎️ 931 70 98 45">
    <meta name="keywords"
        content="gimnasio sant boi, muay thai sant boi, boxeo sant boi, mma sant boi, artes marciales sant boi, jeet kune do barcelona, kali barcelona, gimnasio artes marciales barcelona, clases boxeo sant boi, clases muay thai, defensa personal sant boi, boonchuay gym">
    <meta name="author" content="Boonchuay Gym">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="googlebot" content="index, follow">

    <!-- Geo Tags for Local SEO -->
    <meta name="geo.region" content="ES-CT">
    <meta name="geo.placename" content="Sant Boi de Llobregat">
    <meta name="geo.position" content="41.3467;2.0389">
    <meta name="ICBM" content="41.3467, 2.0389">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://www.boonchuaygym.com/">

    <!-- Open Graph Meta Tags (Facebook, LinkedIn) -->
    <meta property="og:locale" content="es_ES">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Boonchuay Gym - Gimnasio de Artes Marciales en Sant Boi">
    <meta property="og:description"
        content="Gimnasio especializado en Muay Thai, Boxeo, MMA, Jeet Kune Do y Kali en Sant Boi de Llobregat. Clases para adultos y niños. Prueba una clase gratis.">
    <meta property="og:url" content="https://www.boonchuaygym.com/">
    <meta property="og:site_name" content="Boonchuay Gym">
    <meta property="og:image" content="https://www.boonchuaygym.com/images/hero.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Boonchuay Gym - Gimnasio de Artes Marciales">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Boonchuay Gym - Gimnasio de Artes Marciales en Sant Boi">
    <meta name="twitter:description"
        content="Clases de Muay Thai, Boxeo, MMA, Jeet Kune Do y Kali en Sant Boi de Llobregat. Prueba gratis.">
    <meta name="twitter:image" content="https://www.boonchuaygym.com/images/hero.jpg">

    <!-- Local Business Schema.org Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "SportsActivityLocation",
      "@id": "https://www.boonchuaygym.com/#organization",
      "name": "Boonchuay Gym",
      "image": "https://www.boonchuaygym.com/images/hero.jpg",
      "description": "Gimnasio especializado en artes marciales: Muay Thai, Boxeo, MMA, Jeet Kune Do y Kali en Sant Boi de Llobregat",
      "url": "https://www.boonchuaygym.com",
      "telephone": "+34931709845",
      "email": "davidpedrosa1988@gmail.com",
      "priceRange": "€€",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Carrer d'Eusebi Güell 14",
        "addressLocality": "Sant Boi de Llobregat",
        "addressRegion": "Barcelona",
        "postalCode": "08830",
        "addressCountry": "ES"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": "41.3467",
        "longitude": "2.0389"
      },
      "openingHoursSpecification": [
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
          "opens": "09:00",
          "closes": "22:00"
        }
      ],
      "sameAs": [
        "https://www.facebook.com/boonchuaygym",
        "https://www.instagram.com/boonchuaygym"
      ],
      "areaServed": {
        "@type": "City",
        "name": "Sant Boi de Llobregat"
      },
      "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "Clases de Artes Marciales",
        "itemListElement": [
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Clases de Muay Thai",
              "description": "Entrenamiento de Muay Thai para todos los niveles"
            }
          },
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Clases de Boxeo",
              "description": "Entrenamiento de boxeo profesional"
            }
          },
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Clases de MMA",
              "description": "Artes marciales mixtas"
            }
          },
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Clases de Jeet Kune Do",
              "description": "Sistema de combate de Bruce Lee"
            }
          },
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Clases de Kali",
              "description": "Arte marcial filipino"
            }
          }
        ]
      }
    }
    </script>

    <!-- Favicon (add your favicon files) -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Roboto:wght@300;400;700&display=swap"
        rel="stylesheet">
</head>

<body>
    <!-- NAVIGATION -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <div class="logo">
                    <h1>BOONCHUAY <span>GYM</span></h1>
                </div>
                <ul class="nav-menu" id="navMenu">
                    <li><a href="#home" class="nav-link">Inicio</a></li>
                    <li><a href="#disciplines" class="nav-link">Disciplinas</a></li>
                    <li><a href="#schedule" class="nav-link">Horarios</a></li>
                    <li><a href="#about" class="nav-link">Sobre Nosotros</a></li>
                    <li><a href="#contact" class="nav-link">Contacto</a></li>
                </ul>
                <div class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section id="home" class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h2 class="hero-title">ENTRENA COMO UN <span>GUERRERO</span></h2>
            <p class="hero-subtitle">Muay Thai • Boxeo • Jeet Kune Do • Kali • MMA</p>
            <p class="hero-description">Descubre tu potencial en el mejor gimnasio de artes marciales de Sant Boi de
                Llobregat</p>
            <a href="#contact" class="btn btn-primary">Prueba una Clase Gratis</a>
        </div>
    </section>

    <!-- INTRO SECTION -->
    <section class="intro">
        <div class="container">
            <div class="intro-content">
                <h2>Bienvenido a Boonchuay Gym</h2>
                <p>Somos un gimnasio especializado en artes marciales con un ambiente familiar y profesional. Nuestro
                    equipo de instructores altamente cualificados te guiará en tu camino hacia la excelencia física y
                    mental.</p>
                <p>Ya sea que busques mejorar tu condición física, aprender defensa personal o competir
                    profesionalmente, en Boonchuay Gym encontrarás el entrenamiento perfecto para ti.</p>
            </div>
        </div>
    </section>

    <!-- DISCIPLINES SECTION - DYNAMIC FROM DATABASE -->
    <section id="disciplines" class="disciplines">
        <div class="container">
            <h2 class="section-title">Nuestras <span>Disciplinas</span></h2>
            <div class="disciplines-grid">
                <?php if (!empty($disciplinas)): ?>
                    <?php foreach ($disciplinas as $disciplina): ?>
                        <!-- Discipline Card - <?php echo htmlspecialchars($disciplina['nombre']); ?> -->
                        <div class="discipline-card" itemscope itemtype="https://schema.org/Service">
                            <div class="card-image">
                                <img src="<?php echo htmlspecialchars($disciplina['imagen']); ?>"
                                    alt="Clases de <?php echo htmlspecialchars($disciplina['nombre']); ?> en Sant Boi de Llobregat - Boonchuay Gym"
                                    itemprop="image" loading="lazy">
                                <div class="card-overlay"></div>
                            </div>
                            <div class="card-content">
                                <h3 itemprop="name"><?php echo htmlspecialchars($disciplina['nombre']); ?></h3>
                                <p itemprop="description"><?php echo nl2br(htmlspecialchars($disciplina['descripcion'])); ?></p>
                                <a href="#contact" class="btn btn-secondary">Más Información</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: white; text-align: center; width: 100%;">
                        No hay disciplinas disponibles en este momento.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- SCHEDULE SECTION - DYNAMIC FROM DATABASE -->
    <section id="schedule" class="schedule">
        <div class="container">
            <h2 class="section-title">Nuestros <span>Horarios</span></h2>
            <p class="schedule-intro">Ofrecemos horarios flexibles para adaptarnos a tu rutina. Consulta nuestros
                horarios de clases y ven a entrenar.</p>

            <div class="schedule-table-wrapper">
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>Día</th>
                            <th>Disciplina</th>
                            <th>Hora Inicio</th>
                            <th>Hora Fin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($horarios)): ?>
                            <?php foreach ($horarios as $horario): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($horario['dia_semana']); ?></td>
                                    <td><?php echo htmlspecialchars($horario['disciplina_nombre']); ?></td>
                                    <td><?php echo date('H:i', strtotime($horario['hora_inicio'])); ?></td>
                                    <td><?php echo date('H:i', strtotime($horario['hora_fin'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">
                                    No hay horarios disponibles en este momento.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="schedule-note">
                <p><strong>Nota:</strong> Gimnasio abierto de Lunes a Viernes de 9:00 a 22:00. Cerrado fines de semana.
                </p>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section id="about" class="about">
        <div class="container">
            <h2 class="section-title">Sobre <span>Nosotros</span></h2>
            <div class="about-content">
                <div class="about-text">
                    <h3>Nuestra Historia</h3>
                    <p>Boonchuay Gym nace de la pasión por las artes marciales y el deseo de crear un espacio donde
                        personas de todas las edades puedan desarrollar sus habilidades en un ambiente profesional y
                        familiar.</p>

                    <h3>Nuestra Filosofía</h3>
                    <p>Creemos en el entrenamiento técnico de calidad, el respeto mutuo y el desarrollo integral de
                        nuestros alumnos. No solo formamos luchadores, formamos personas disciplinadas, seguras y
                        respetuosas.</p>

                    <h3>Ambiente Familiar</h3>
                    <p>En Boonchuay Gym encontrarás un ambiente acogedor donde tanto adultos como niños pueden entrenar
                        de forma segura. Nuestros instructores están comprometidos con el progreso individual de cada
                        alumno.</p>

                    <h3>Excelencia Técnica</h3>
                    <p>Contamos con instructores certificados y con amplia experiencia en competición y enseñanza.
                        Nuestro enfoque técnico garantiza que aprenderás correctamente desde el primer día.</p>
                </div>
                <div class="about-stats">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count($disciplinas); ?></div>
                        <div class="stat-label">Disciplinas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">13</div>
                        <div class="stat-label">Horas Diarias</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Compromiso</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTACT SECTION -->
    <section id="contact" class="contact">
        <div class="container">
            <h2 class="section-title">Contacta <span>Con Nosotros</span></h2>
            <div class="contact-wrapper">
                <!-- Contact Info -->
                <div class="contact-info">
                    <h3>Información de Contacto</h3>
                    <div class="info-item">
                        <div class="info-icon">📍</div>
                        <div class="info-text">
                            <h4>Dirección</h4>
                            <p>Carrer d'Eusebi Güell 14<br>08830 Sant Boi de Llobregat</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">📞</div>
                        <div class="info-text">
                            <h4>Teléfono</h4>
                            <p><a href="tel:931709845">931 70 98 45</a></p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">✉️</div>
                        <div class="info-text">
                            <h4>Email</h4>
                            <p><a href="mailto:davidpedrosa1988@gmail.com">davidpedrosa1988@gmail.com</a></p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">🕐</div>
                        <div class="info-text">
                            <h4>Horario</h4>
                            <p>Lunes - Viernes: 9:00 - 22:00<br>Sábado - Domingo: Cerrado</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="contact-form-wrapper">
                    <h3>Envíanos un Mensaje</h3>
                    <form id="contactForm" class="contact-form" action="send.php" method="POST">
                        <div class="form-group">
                            <label for="name">Nombre Completo *</label>
                            <input type="text" id="name" name="name" required>
                            <span class="error-message" id="nameError"></span>
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                            <span class="error-message" id="emailError"></span>
                        </div>
                        <div class="form-group">
                            <label for="phone">Teléfono *</label>
                            <input type="tel" id="phone" name="phone" required>
                            <span class="error-message" id="phoneError"></span>
                        </div>
                        <div class="form-group">
                            <label for="message">Mensaje *</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                            <span class="error-message" id="messageError"></span>
                        </div>
                        <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
                    </form>
                    <div id="formSuccess" class="success-message" style="display: none;">
                        ¡Mensaje enviado correctamente! Te contactaremos pronto.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>BOONCHUAY <span>GYM</span></h3>
                    <p>Tu gimnasio de artes marciales en Sant Boi de Llobregat</p>
                </div>
                <div class="footer-section">
                    <h4>Contacto</h4>
                    <p>📍 Carrer d'Eusebi Güell 14, 08830 Sant Boi</p>
                    <p>📞 931 70 98 45</p>
                    <p>✉️ davidpedrosa1988@gmail.com</p>
                </div>
                <div class="footer-section">
                    <h4>Horario</h4>
                    <p>Lunes - Viernes: 9:00 - 22:00</p>
                    <p>Sábado - Domingo: Cerrado</p>
                </div>
                <div class="footer-section">
                    <h4>Síguenos</h4>
                    <div class="social-links">
                        <a href="#" class="social-link">Facebook</a>
                        <a href="#" class="social-link">Instagram</a>
                        <a href="#" class="social-link">YouTube</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Boonchuay Gym. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- CHATBOT BUTTON -->
    <button class="chatbot-button" id="chatbotButton">
        <span class="chatbot-icon">💬</span>
    </button>

    <!-- CHATBOT MODAL -->
    <div class="chatbot-modal" id="chatbotModal">
        <div class="chatbot-header">
            <h3>Asistente Virtual</h3>
            <button class="chatbot-close" id="chatbotClose">&times;</button>
        </div>
        <div class="chatbot-body" id="chatbotBody">
            <div class="chatbot-message bot-message">
                <p>¡Hola! Bienvenido a Boonchuay Gym. ¿En qué puedo ayudarte?</p>
            </div>
            <!-- Messages will be added here dynamically -->
        </div>
        <div class="chatbot-footer">
            <input type="text" id="chatbotInput" placeholder="Escribe tu mensaje...">
            <button id="chatbotSend" class="btn-send">Enviar</button>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>

</html>