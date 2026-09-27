PROYECTO: PCCURICO Hosting Panel

UBICACIÓN DEL PROYECTO:
Linux:
    /var/www/pccurico-hosting-panel

Windows/VS Code:
    Z:\pccurico-hosting-panel

IMPORTANTE:
Trabaja directamente sobre el proyecto existente.
NO CREES OTRO PROYECTO.
NO CAMBIES LA ARQUITECTURA BASE.
NO HAGAS UN PROYECTO DEMO.
NO ELIMINES FUNCIONALIDAD EXISTENTE.
NO MODIFIQUES OTROS SITIOS DEL SERVIDOR.
NO MODIFIQUES CLOUDFLARE TUNNEL.
NO HAGAS git commit.
NO HAGAS git push.

REGLA PRINCIPAL:
QUIERO QUE IMPLEMENTES TODO LO FALTANTE DIRECTAMENTE.
NO QUIERO UNA AUDITORÍA PREVIA.
NO QUIERO UN PLAN PARA QUE YO LO APRUEBE.
NO QUIERO QUE ME PREGUNTES SI PUEDES CONTINUAR.
NO QUIERO QUE TE DETENGAS PARA MOSTRARME CAMBIOS PARCIALES.

Primero escribe/modifica todos los archivos necesarios.
Después realiza una única revisión técnica completa y corrige automáticamente los problemas encontrados.
Al final recién informa qué se hizo, qué quedó funcionando y qué quedó pendiente.

============================================================
1. OBJETIVO GENERAL
============================================================

Convertir el proyecto actual en un panel de hosting profesional llamado:

PCCURICO Hosting Panel

Debe funcionar como un panel de administración de hosting similar conceptualmente a cPanel/Plesk, pero desarrollado específicamente para el servidor PCCURICO.

El panel debe permitir administrar:

- Sitios web
- Dominios
- DNS
- Apache
- PHP
- MySQL
- Bases de datos
- Usuarios
- Roles
- Permisos
- Correo
- SSL
- Backups
- Logs
- Configuración
- Herramientas del servidor
- Auditoría

Debe tener una interfaz profesional, oscura, limpia y administrativa.

REFERENCIA VISUAL:
Corona Admin / panel administrativo profesional.

NO utilizar estética:
- infantil
- futurista exagerada
- gaming
- neon
- dashboards con exceso de efectos

Debe parecer un producto comercial de administración de servidores.

============================================================
2. REGLA ABSOLUTA SOBRE BASE DE DATOS
============================================================

NO CREAR TABLAS.
NO ALTERAR TABLAS.
NO EJECUTAR MIGRACIONES.
NO INSERTAR DATOS DE CONFIGURACIÓN.
NO CREAR PERMISOS EN BD.
NO CREAR ROLES EN BD.
NO MODIFICAR EL ESQUEMA.

Durante esta fase la base de datos debe permanecer intacta.

La creación/modificación definitiva de estructura de BD se realizará AL FINAL, cuando toda la interfaz y funcionalidades del panel estén terminadas.

Sí puedes utilizar las tablas existentes únicamente cuando sea necesario para que las pantallas existentes funcionen.

NO agregues ninguna nueva estructura de BD para resolver un problema de interfaz.

============================================================
3. ARQUITECTURA EXISTENTE
============================================================

Mantener la arquitectura PHP MVC/Layered existente:

Controllers
Services
Repositories
Models
Views
Core
Middleware
Routes

No reemplazar PHP por React.
No convertir el proyecto en SPA.
No introducir Laravel.
No introducir otro framework.

Utilizar PHP 8.3 compatible con el servidor actual.

Apache:
Apache 2.4

PHP:
PHP 8.3 FPM

MySQL:
MySQL 8

Sistema:
Ubuntu Server 24.04

============================================================
4. NO ROMPER LO QUE YA FUNCIONA
============================================================

Conservar y mejorar:

- Login
- Sesiones
- CSRF
- Dashboard
- Sites
- Users
- Apache
- PHP
- MySQL
- DNS
- Cloudflare existente
- rutas existentes
- helpers existentes
- servicios existentes

Si una implementación existente está incompleta, intégrala.
No dupliques clases o sistemas.

Antes de crear una clase nueva:
buscar si ya existe una clase equivalente.

Antes de crear una vista:
buscar si existe una vista equivalente.

Antes de crear una ruta:
comprobar la ruta existente y reutilizarla.

============================================================
5. LAYOUT GLOBAL
============================================================

Todo el panel debe utilizar un único layout profesional.

Archivo principal:

app/Views/layouts/app.php

El layout debe contener:

SIDEBAR:

PCCURICO
Hosting Panel

Principal
- Dashboard

Hosting
- Sitios
- Dominios
- DNS
- Correo
- SSL
- Backups

Servidor
- Apache
- PHP
- MySQL
- Bases de datos
- Logs

Administración
- Usuarios
- Roles
- Permisos
- Auditoría

Sistema
- Configuración
- Herramientas

TOPBAR:

- título de página
- subtítulo
- usuario actual
- avatar/iniciales
- botón logout

Contenido:

Cada módulo debe renderizarse dentro del mismo shell.

No permitir que cada pantalla tenga un diseño diferente.

============================================================
6. DASHBOARD
============================================================

Completar el Dashboard real.

Mostrar:

CPU
RAM
Disco
Load Average
Uptime

Servicios:

Apache
PHP-FPM
MySQL
Cloudflare Tunnel
Ollama
Fail2ban

Mostrar estado:

ONLINE
OFFLINE
WARNING

Mostrar información:

Hostname
Sistema operativo
Kernel
Arquitectura
PHP
Apache
MySQL

Agregar tarjetas:

Sitios activos
Dominios
Bases de datos
Usuarios
Backups
SSL

Si algún dato todavía no existe en BD, obtenerlo directamente del sistema cuando sea seguro.

No inventar datos.

No utilizar datos demo.

============================================================
7. SITIOS WEB
============================================================

Completar el módulo:

/sites

Debe leer los VirtualHosts reales de:

/etc/apache2/sites-enabled/

Debe detectar múltiples VirtualHosts dentro del mismo archivo.

Mostrar:

- dominio
- aliases
- DocumentRoot
- PHP
- archivo de configuración
- estado Apache
- SSL
- tipo de sitio

Acciones:

Ver
Crear

La creación debe conservar el sistema existente:

/usr/local/sbin/pccurico-create-vhost

No modificar este helper salvo que sea estrictamente necesario.

No permitir que el panel pueda modificar accidentalmente:

hosting.pccurico.cl
hosting.local

El panel jamás debe poder eliminarse a sí mismo.

============================================================
8. DOMINIOS
============================================================

Crear/completar:

/domains

Debe permitir visualizar:

- dominios encontrados
- dominio principal
- aliases
- configuración Apache relacionada
- DocumentRoot
- estado

Preparar la interfaz para futuras funciones:

- agregar dominio
- eliminar dominio
- alias
- redirección

Pero NO modificar todavía BD.

Las operaciones reales deben utilizar servicios del sistema cuando corresponda.

============================================================
9. DNS
============================================================

Completar:

/dns

Utilizar el sistema DNS existente.

Actualmente existe dnsmasq.

Mostrar:

- estado dnsmasq
- configuración
- archivos relevantes
- dominios locales
- resolución
- servidor DNS

Permitir visualizar las entradas administradas por PCCURICO.

No romper:

/etc/dnsmasq.d/

No tocar Cloudflare Tunnel.

No modificar DNS público automáticamente.

============================================================
10. APACHE
============================================================

Completar:

/apache

Mostrar:

- versión Apache
- estado servicio
- VirtualHosts
- módulos cargados
- configuración principal
- puertos
- archivos enabled
- archivos available

Acciones seguras:

- reload Apache
- restart Apache

Antes de cualquier reload/restart:

apachectl configtest

Si falla:

NO reiniciar Apache.

Mostrar el error.

Las operaciones privilegiadas deben utilizar helpers sudoers específicos.

NO dar sudo general a www-data.

============================================================
11. PHP
============================================================

Completar:

/php

Mostrar:

- versión PHP CLI
- PHP-FPM
- estado PHP-FPM
- socket FPM
- extensiones
- configuración relevante
- memory_limit
- upload_max_filesize
- post_max_size
- max_execution_time

Mostrar versiones PHP instaladas.

No modificar configuración automáticamente en esta fase.

============================================================
12. MYSQL
============================================================

Completar:

/mysql

Mostrar:

- versión MySQL
- estado
- puerto
- uptime
- conexiones
- tamaño aproximado
- variables importantes

No cambiar configuración.

No crear usuarios.

No crear bases de datos todavía.

============================================================
13. BASES DE DATOS
============================================================

Completar:

/databases

Debe mostrar las bases existentes disponibles para el panel.

Mostrar:

- nombre
- tamaño si puede obtenerse
- tablas
- estado

Preparar interfaz para:

Crear base de datos
Eliminar base de datos
Crear usuario
Asignar usuario

PERO NO IMPLEMENTAR todavía operaciones destructivas ni creación de BD.

No modificar esquema.

============================================================
14. CORREO
============================================================

Completar:

/mail

Detectar servicios de correo instalados.

Mostrar:

- Postfix
- Dovecot
- Exim
- estado
- puertos
- configuración detectada

Si no están instalados:

mostrar "No instalado".

NO instalar software automáticamente.

============================================================
15. SSL
============================================================

Completar:

/ssl

Detectar certificados existentes.

Mostrar:

- dominio
- issuer
- fecha inicio
- fecha expiración
- días restantes
- estado

Estados:

Válido
Por vencer
Expirado

Detectar certificados Let's Encrypt si existen.

Preparar interfaz para renovación futura.

NO ejecutar certbot automáticamente todavía.

============================================================
16. BACKUPS
============================================================

Completar:

/backups

Mostrar:

- backups existentes
- fecha
- tamaño
- ubicación
- tipo

Preparar acciones:

Crear backup
Descargar
Eliminar

Durante esta fase no ejecutar operaciones destructivas.

No realizar mysqldump automáticamente.

No asumir contraseña root MySQL.

============================================================
17. LOGS
============================================================

Completar:

/logs

Mostrar:

Apache access
Apache error
PHP-FPM
Cloudflare
Sistema

Debe existir:

- selección de log
- últimas líneas
- búsqueda
- refresh

Nunca cargar archivos gigantes completos.

Usar tail.

No permitir path traversal.

No permitir que el usuario solicite:

/etc/passwd
/root/*
/home/*
/etc/shadow

Limitar logs a una whitelist explícita.

============================================================
18. USUARIOS
============================================================

Completar:

/users

Mantener usuario actual.

Mostrar:

- nombre
- email
- estado
- rol
- fecha

Acciones:

Crear
Activar
Desactivar

No permitir desactivar al usuario actualmente conectado.

Mantener CSRF.

Validar correctamente formularios.

No modificar la estructura de BD.

============================================================
19. ROLES
============================================================

Completar:

/roles

Mostrar roles existentes.

Preparar interfaz para:

- crear rol
- editar rol
- eliminar rol

Pero durante esta fase no alterar BD.

Debe existir la estructura visual para administrar permisos.

============================================================
20. PERMISOS
============================================================

Completar:

/permissions

Crear una interfaz profesional de permisos.

Agrupar por módulo:

Dashboard
Sites
Domains
DNS
Apache
PHP
MySQL
Databases
Mail
SSL
Backups
Logs
Users
Roles
Permissions
Settings
Tools
Audit

Permisos ejemplo:

view
create
edit
delete
execute

No crear registros en BD todavía.

============================================================
21. AUDITORÍA
============================================================

Completar:

/audit

Mostrar los registros existentes.

Columnas:

Fecha
Usuario
Acción
Tipo
ID
IP
Detalles

Agregar:

búsqueda
filtros
paginación visual

No eliminar registros.

No modificar estructura de audit_logs.

============================================================
22. CONFIGURACIÓN
============================================================

Completar:

/settings

Mostrar configuración del panel:

Nombre
URL
Zona horaria
Idioma
Moneda
Servidor
PHP
Apache
MySQL

No almacenar nuevas configuraciones en BD todavía.

Si existe configuración en .env:

utilizarla.

No mostrar secretos.

Nunca mostrar:

DB_PASSWORD
tokens
API keys
Cloudflare tokens
SSH private keys
passwords

============================================================
23. HERRAMIENTAS
============================================================

Completar:

/tools

Crear sección para:

- comprobar Apache
- comprobar PHP
- comprobar MySQL
- comprobar DNS
- comprobar permisos
- comprobar espacio
- comprobar servicios

Debe existir salida visual clara:

OK
WARNING
ERROR

Las herramientas deben utilizar whitelist de comandos.

Nunca permitir ejecutar comandos arbitrarios introducidos por el usuario.

============================================================
24. SEGURIDAD
============================================================

Mantener:

CSRF
sesiones
HttpOnly
SameSite
validación de entrada
escape HTML
protección contra path traversal
protección contra command injection
whitelist de comandos
whitelist de logs
validación de dominios

No usar:

shell_exec($_POST...)
system($_POST...)
exec($_POST...)

Nunca concatenar directamente entrada del usuario en comandos shell.

Para operaciones privilegiadas:

usar helpers específicos en:

/usr/local/sbin/

y sudoers limitado.

============================================================
25. SERVICIOS
============================================================

Crear/reutilizar servicios:

ApacheService
DnsService
PhpService
MysqlService
MailService
SslService
BackupService
LogService
SystemService

Si alguno ya existe:

REUTILIZARLO.

No crear duplicados.

Los Controllers deben ser delgados.

La lógica del sistema debe estar en Services.

============================================================
26. CSS / INTERFAZ
============================================================

Unificar completamente los CSS.

Usar:

public/assets/css/panel.css
public/assets/css/pccurico-shell.css
public/assets/css/pccurico-modules.css
public/assets/css/pccurico-server-modules.css

Eliminar reglas duplicadas si interfieren.

No crear 20 CSS innecesarios.

Diseño:

sidebar oscuro
cards
tablas
badges
botones
formularios
alerts
breadcrumbs
modales si son necesarios

Responsive:

desktop
tablet
mobile

Inputs:

fondo oscuro
texto claro
labels visibles
focus visible
placeholders legibles

Tablas:

header oscuro
filas diferenciadas
hover
badges de estado

============================================================
27. ICONOS
============================================================

Utilizar iconos consistentes.

Preferentemente Lucide/Bootstrap Icons si ya existe dependencia.

No agregar una enorme librería si no es necesaria.

No utilizar emojis como iconos principales del panel.

============================================================
28. ERRORES
============================================================

Crear manejo visual profesional.

No mostrar:

stack traces
passwords
variables de entorno
rutas sensibles innecesarias
credenciales

Registrar errores en:

storage/logs/

Mostrar al usuario:

"Se produjo un error al realizar la operación."

y un identificador de error si corresponde.

============================================================
29. ROUTER
============================================================

Revisar routes/web.php.

Todas las rutas deben apuntar a Controllers reales.

No crear rutas duplicadas.

No dejar:

return $router;

antes de registrar rutas.

El return debe estar al final.

============================================================
30. VISTAS
============================================================

Todas las vistas autenticadas deben utilizar:

app/Views/layouts/app.php

No duplicar:

<html>
<head>
<body>
sidebar
topbar

dentro de cada módulo.

El layout debe ser responsable del shell.

Los módulos deben entregar solamente su contenido.

============================================================
31. AUTENTICACIÓN
============================================================

Mantener login actual.

Todas las rutas administrativas deben requerir autenticación.

No permitir acceder directamente a:

/dashboard
/sites
/domains
/dns
/apache
/php
/mysql
/databases
/mail
/ssl
/backups
/logs
/users
/roles
/permissions
/audit
/settings
/tools

sin sesión.

Redirigir a:

/login

============================================================
32. NO CAMBIAR INFRAESTRUCTURA EXISTENTE
============================================================

NO modificar:

Cloudflare Tunnel
cloudflared
DNS público
otros VirtualHosts
otros sitios web
n8n
Open WebUI
Ollama
Samba
XRDP
Fail2ban
MySQL global
Apache global

salvo que una operación específica del panel lo requiera.

No tocar aplicaciones externas.

============================================================
33. ARCHIVOS DEL PROYECTO
============================================================

Los scripts que crees para este proyecto deben quedar SIEMPRE en:

/var/www/pccurico-hosting-panel/script/

En Windows:

Z:\pccurico-hosting-panel\script\

No crear scripts temporales repartidos por el proyecto.

No crear backups dentro del proyecto.

Los backups de cambios pueden quedar temporalmente en:

/root/

============================================================
34. NO CREAR ARCHIVOS BASURA
============================================================

No crear:

test123.php
debug.php
tmp.php
index-old.php
backup.php
archivo-final-final.php
copias numeradas
archivos .bak dentro del proyecto

Si necesitas respaldo antes de modificar archivos:

usar /root/pccurico-hosting-panel-backup-YYYYMMDD-HHMMSS/

============================================================
35. IMPLEMENTACIÓN
============================================================

AHORA HAZ TODO LO SIGUIENTE SIN PREGUNTAR:

1. Inspecciona el código existente.
2. Identifica las implementaciones actuales.
3. Conserva lo funcional.
4. Completa el layout global.
5. Integra Dashboard.
6. Integra Sites.
7. Integra Users.
8. Integra Domains.
9. Integra DNS.
10. Integra Apache.
11. Integra PHP.
12. Integra MySQL.
13. Integra Databases.
14. Integra Mail.
15. Integra SSL.
16. Integra Backups.
17. Integra Logs.
18. Integra Roles.
19. Integra Permissions.
20. Integra Audit.
21. Integra Settings.
22. Integra Tools.
23. Unifica CSS.
24. Unifica navegación.
25. Corrige rutas.
26. Corrige Controllers.
27. Corrige Views.
28. Corrige Services.
29. Mantén seguridad.
30. NO modificar BD estructuralmente.

NO detenerte después de cada módulo.

Trabaja todo el bloque completo.

============================================================
36. COMPROBACIÓN FINAL
============================================================

CUANDO HAYAS TERMINADO TODA LA IMPLEMENTACIÓN:

Revisa automáticamente:

- PHP syntax
- rutas
- clases
- namespaces
- includes
- require
- vistas
- layout
- CSS
- Apache config
- permisos
- servicios
- errores PHP
- errores Apache
- sesión
- CSRF

Corrige automáticamente cualquier error encontrado.

No me muestres primero los errores para que yo los corrija.

Tú los corriges.

============================================================
37. PRUEBAS FINALES
============================================================

Realiza al final pruebas HTTP de:

/login
/dashboard
/sites
/domains
/dns
/apache
/php
/mysql
/databases
/mail
/ssl
/backups
/logs
/users
/roles
/permissions
/audit
/settings
/tools

Las rutas autenticadas pueden devolver:

302 /login

si la prueba no tiene sesión.

Eso no debe considerarse un error.

Comprueba además:

Apache configtest
PHP syntax
PHP-FPM activo
Apache activo
MySQL activo

NO reinicies servicios innecesariamente.

============================================================
38. BASE DE DATOS
============================================================

IMPORTANTE:

NO HACER AHORA:

CREATE DATABASE
CREATE TABLE
ALTER TABLE
DROP TABLE
INSERT
UPDATE
DELETE
TRUNCATE
migraciones
seeds

La BD se resolverá completamente AL FINAL del desarrollo.

Si alguna funcionalidad requiere una tabla que todavía no existe:

NO CREES LA TABLA.

Implementa primero la interfaz y deja la operación preparada para la fase final.

============================================================
39. RESULTADO ESPERADO
============================================================

Al terminar debe existir un PCCURICO Hosting Panel coherente, profesional y navegable.

El usuario debe poder entrar y encontrar:

PCCURICO Hosting Panel

Dashboard
Sites
Domains
DNS
Apache
PHP
MySQL
Databases
Mail
SSL
Backups
Logs
Users
Roles
Permissions
Audit
Settings
Tools

Todo debe utilizar el mismo diseño.

No quiero pantallas independientes con estilos diferentes.

No quiero datos demo.

No quiero módulos vacíos que solamente digan "próximamente".

Cada pantalla debe mostrar información real disponible en el servidor o indicar claramente que una función todavía está bloqueada por la fase de implementación de BD.

============================================================
40. INFORME FINAL
============================================================

NO ME HAGAS INFORMES ANTES DE IMPLEMENTAR.

AL FINAL entrega únicamente:

1. Archivos creados/modificados.
2. Funcionalidades terminadas.
3. Funcionalidades que dependen de BD y quedaron preparadas.
4. Resultado de las comprobaciones finales.
5. Errores encontrados y corregidos.
6. Pendientes reales, si quedan.

NO HAGAS git commit.
NO HAGAS git push.

El trabajo debe quedar directamente aplicado en el proyecto para continuar desde VS Code.