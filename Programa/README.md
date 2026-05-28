# Delicias del Barrio

## Descripción general del sistema
Delicias del Barrio es una aplicación web dinámica orientada a cafeterías, panaderías y negocios pequeños que desean mostrar su catálogo de productos y facilitar la compra directa a través de WhatsApp.

## Objetivo de la aplicación
Permitir la visualización de productos, el registro e inicio de sesión de usuarios, la administración básica de productos desde un dashboard y la compra con redirección hacia WhatsApp para la comunicación con el vendedor.

## Lenguaje y tecnologías utilizadas
- PHP 8
- SQLite como base de datos local
- Arquitectura MVC monolítica sencilla
- HTML, CSS y JavaScript básico para la interfaz

## Arquitectura implementada
Se implementó una **arquitectura monolítica MVC** con separación de responsabilidades en controladores, modelos, vistas y bootstrap principal.

## Módulos y funcionalidades
- **Inicio dinámico**: catálogo visual de productos.
- **Autenticación**: login y registro de usuarios.
- **Dashboard administrativo**: panel del dueño con métricas, carga de catálogos y control de pedidos.
- **Gestión de productos**: creación manual de nuevos productos.
- **Carga masiva de catálogos**: importación desde CSV para menú completo.
- **Administración de pedidos**: seguimiento de estado y actualización de pedidos.
- **Compra por WhatsApp**: enlaces directos por producto.

## Capturas de pantalla
Incluye capturas del catálogo, login, registro y dashboard en la presentación final.

## Instrucciones de instalación y ejecución
1. Clonar o descargar el repositorio.
2. Asegurar que PHP 8 esté disponible.
3. Ejecutar el proyecto desde la raíz con un servidor local, por ejemplo:
   `php -S localhost:8000`
4. Acceder a `http://localhost:8000`.

## Integrantes del grupo
- Equipo de desarrollo

## Conclusiones del proyecto
La aplicación demuestra el uso de una arquitectura MVC en PHP, la administración básica de productos y el flujo de compra con WhatsApp, creando una solución funcional y adaptable para negocios pequeños.
