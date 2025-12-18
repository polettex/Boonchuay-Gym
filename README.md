# Boonchuay Gym - Instalación y Configuración

## 📋 Descripción del Proyecto

Sitio web completo para Boonchuay Gym con backend PHP/MySQL, gestión de leads, horarios dinámicos, y infraestructura preparada para chatbot con IA y automatizaciones n8n.

## 🗂️ Estructura del Proyecto

```
proyecto-personal/
├── config/
│   └── db.php                 # Configuración de conexión PDO a MySQL
├── database/
│   └── database.sql           # Script completo de base de datos
├── css/
│   └── styles.css             # Estilos responsive con tema artes marciales
├── js/
│   └── script.js              # JavaScript con validación y chatbot
├── images/                    # Imágenes generadas para disciplinas
│   ├── hero.jpg
│   ├── muay-thai.jpg
│   ├── boxing.jpg
│   ├── jkd.jpg
│   └── kali.jpg
├── index.php                  # Página principal (dinámica con BD)
├── send.php                   # Handler de formulario de contacto
├── chatbot.php                # Endpoint del chatbot con FAQ
└── README.md                  # Este archivo
```

## 🚀 Instalación

### 1. Requisitos Previos

- **WAMP Server** (o XAMPP/LAMP) instalado y funcionando
- **PHP 7.4+**
- **MySQL 5.7+**
- Navegador web moderno

### 2. Configurar la Base de Datos

1. **Abrir phpMyAdmin** o consola MySQL:
   ```
   http://localhost/phpmyadmin
   ```

2. **Importar el script SQL**:
   - Opción A (phpMyAdmin): Ir a "Importar" y seleccionar `database/database.sql`
   - Opción B (Consola):
     ```bash
     mysql -u root -p < c:\wamp64\www\proyecto-personal\database\database.sql
     ```

3. **Verificar la creación**:
   ```sql
   USE boonchuay_gym;
   SHOW TABLES;
   SELECT * FROM disciplinas;
   ```

### 3. Configurar la Conexión a la Base de Datos

Editar `config/db.php` si es necesario (por defecto usa credenciales de WAMP):

```php
define('DB_HOST', 'localhost');        // Host de MySQL
define('DB_NAME', 'boonchuay_gym');    // Nombre de la base de datos
define('DB_USER', 'root');             // Usuario MySQL
define('DB_PASS', '');                 // Contraseña (vacía por defecto en WAMP)
```

### 4. Copiar Imágenes

Las imágenes ya están generadas en la carpeta `images/`. Si necesitas copiarlas manualmente desde los artifacts:

```powershell
# Las imágenes ya deberían estar en:
# c:\wamp64\www\proyecto-personal\images\
```

### 5. Acceder al Sitio

Abrir en el navegador:
```
http://localhost/proyecto-personal/index.php
```

## 📊 Base de Datos

### Tablas Creadas

1. **usuarios** - Cuentas de admin y staff
2. **disciplinas** - Muay Thai, Boxeo, JKD, Kali
3. **horarios** - Horarios de clases por disciplina
4. **leads** - Contactos del formulario web
5. **faq** - Preguntas frecuentes para chatbot
6. **chatbot_logs** - Historial de conversaciones

### Datos Iniciales

- ✅ 4 Disciplinas con descripciones completas
- ✅ Horarios de ejemplo para cada disciplina
- ✅ 8 FAQs para el chatbot
- ✅ Usuario admin: `admin@boonchuaygym.com` / `CAMBIAR_PASSWORD`

## 🎯 Funcionalidades Implementadas

### ✅ Contenido Dinámico

- **Disciplinas**: Se cargan desde la tabla `disciplinas`
- **Horarios**: Se muestran desde `horarios` JOIN `disciplinas`
- **Contador de disciplinas**: Dinámico en sección "Sobre Nosotros"

### ✅ Sistema de Leads

- Formulario de contacto guarda en tabla `leads`
- Validación frontend (JavaScript) y backend (PHP)
- Envío de email opcional (requiere configurar servidor SMTP)
- Respuesta JSON para feedback al usuario

### ✅ Chatbot con FAQ

- Interfaz de chat funcional
- Respuestas desde base de datos (tabla `faq`)
- Logging de conversaciones en `chatbot_logs`
- Preparado para integración con:
  - **OpenAI API** (comentado en `chatbot.php`)
  - **n8n webhooks** (comentado en `chatbot.php`)

### ✅ Diseño Responsive

- Mobile-first design
- Navegación hamburger en móvil
- Tablas responsive
- Animaciones smooth scroll

## 🔧 Próximos Pasos (Futuras Integraciones)

### 1. Integración con IA

Descomentar y configurar en `chatbot.php`:

```php
// Ejemplo: OpenAI Integration
$apiKey = 'tu-api-key-de-openai';
// ... código ya preparado en chatbot.php
```

### 2. Automatizaciones con n8n

Descomentar en `chatbot.php`:

```php
$n8nWebhookUrl = 'https://tu-instancia-n8n.com/webhook/boonchuay-chatbot';
// ... código ya preparado
```

Posibles automatizaciones:
- Notificaciones a staff cuando llega un lead
- Crear tareas en CRM
- Emails de seguimiento automatizados
- Integración con WhatsApp Business

### 3. Panel de Administración

Crear páginas para:
- Gestionar disciplinas
- Ver y responder leads
- Editar horarios
- Gestionar FAQs
- Ver estadísticas del chatbot

### 4. Sistema de Autenticación

- Hash de contraseñas con `password_hash()`
- Login para admin/staff
- Sesiones PHP
- Protección de rutas admin

## 🧪 Testing

### Probar Disciplinas Dinámicas
1. Ir a `http://localhost/proyecto-personal/index.php`
2. Scroll a sección "Disciplinas"
3. Verificar que aparecen 4 tarjetas con datos de la BD

### Probar Horarios Dinámicos
1. Scroll a sección "Horarios"
2. Verificar tabla con días, disciplinas y horas

### Probar Formulario de Contacto
1. Rellenar formulario con datos de prueba
2. Enviar
3. Verificar mensaje de éxito
4. Comprobar en BD:
   ```sql
   SELECT * FROM leads ORDER BY fecha DESC LIMIT 1;
   ```

### Probar Chatbot
1. Click en botón de chat (esquina inferior derecha)
2. Escribir: "¿Cuál es el horario?"
3. Verificar respuesta desde FAQ
4. Comprobar log en BD:
   ```sql
   SELECT * FROM chatbot_logs ORDER BY fecha DESC LIMIT 5;
   ```

## 📝 Notas Importantes

### Configuración de Email

El envío de emails en `send.php` puede no funcionar en WAMP local sin configurar un servidor SMTP. Los leads se guardan en la base de datos independientemente del estado del email.

Para configurar SMTP en WAMP:
1. Editar `php.ini`
2. Configurar `SMTP` y `smtp_port`
3. O usar librerías como PHPMailer

### Seguridad

⚠️ **Para producción**, implementar:
- Hash de contraseñas: `password_hash()` y `password_verify()`
- Protección CSRF en formularios
- Validación más estricta de inputs
- HTTPS obligatorio
- Rate limiting en chatbot
- Sanitización adicional de outputs

### Performance

Para mejorar rendimiento:
- Implementar caché de consultas frecuentes
- Optimizar imágenes (WebP, lazy loading)
- Minificar CSS/JS
- Usar CDN para assets estáticos

## 🆘 Solución de Problemas

### Error: "Database Connection Error"
- Verificar que MySQL está corriendo en WAMP
- Comprobar credenciales en `config/db.php`
- Verificar que la base de datos `boonchuay_gym` existe

### No aparecen las disciplinas
- Verificar que el script SQL se importó correctamente
- Comprobar datos: `SELECT * FROM disciplinas;`
- Ver errores PHP en `C:\wamp64\logs\php_error.log`

### Chatbot no responde
- Abrir consola del navegador (F12) para ver errores JavaScript
- Verificar que `chatbot.php` es accesible
- Comprobar permisos de archivos

### Imágenes no se muestran
- Verificar que las imágenes existen en `images/`
- Comprobar rutas en la base de datos (tabla `disciplinas`)
- Ver errores 404 en consola del navegador

## 👨‍💻 Información de Contacto

**Boonchuay Gym**
- 📍 Carrer d'Eusebi Güell 14, 08830 Sant Boi de Llobregat
- 📞 931 70 98 45
- ✉️ davidpedrosa1988@gmail.com

---

**Desarrollado con**: PHP, MySQL, JavaScript, HTML5, CSS3
**Preparado para**: OpenAI, n8n, Automatizaciones
