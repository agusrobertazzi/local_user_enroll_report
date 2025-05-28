![Moodle Plugin](https://img.shields.io/badge/Moodle-Plugin-brightgreen)
![License](https://img.shields.io/badge/License-GPLv3-blue)
![Moodle Version](https://img.shields.io/badge/Moodle-3.11-orange)

# Plugin de Informe de Matriculaciones activas para Moodle
## local_user_enroll_report

El plugin proporciona un report básico de matriculaciones activas en Moodle, con capacidad de filtrado por curso y exportación en múltiples formatos.

Características Destacadas
- Reporte detallado de matriculaciones activas
- Filtrado por curso específico
- Exportación múltiple a 5 formatos
	- CSV
	- Excel (XLSX)
	- ODS
	- JSON
	- HTML
- Configuración personalizable
	- Filas por página (2, 5, 10, 20) | Por defecto 10
- Diseño responsive adaptable a temas
- Control de acceso basado en permisos

## Requisitos
- Versión mínima de Moodle: 3.11 (2021051700)
- PHP 7.4

## Uso del reporte
Administración del sitio > Informes > Informe de matriculaciones

El usuario con permisos podrá acceder desde el sidebar de la plataforma Moodle desde la frontpage o podrá hacerlo a través de un curso (opción: filtrado por curso)

## Estructura de archivos
local/user_enroll_report/
├── classes/
│   └── report.php         # Lógica principal del reporte
├── lang/
│   └── en/
│       └── local_user_enroll_report.php   # Traducciones al inglés
│   └── es/
│       └── local_user_enroll_report.php   # Traducciones al español
├── index.php              # Vista principal
├── settings.php           # Configuración del plugin
├── version.php            # Control de versiones
└── README.md              # Este archivo
