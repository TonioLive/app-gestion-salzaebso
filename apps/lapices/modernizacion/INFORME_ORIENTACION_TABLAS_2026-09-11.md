# Protección de orientación para tablas

Fecha: 11/09/2026

## Objetivo

Evitar que una tabla con demasiadas columnas quede recortada en móviles o tabletas. Cuando una pantalla no puede presentar todos sus datos correctamente en vertical, el programa muestra un aviso a pantalla completa y solicita girar el dispositivo a horizontal.

## Cobertura

- Encabezado moderno compartido del panel de gestión.
- Sistema compartido de compatibilidad para las pantallas antiguas.
- 336 páginas PHP antiguas actualizadas a la nueva versión del recurso común.
- Los 39 destinos principales registrados en el menú del panel fueron revisados; todas las rutas activas que contienen tablas cargan el sistema mediante el encabezado moderno o el encabezado antiguo compartido.
- `formulario/listado.php` (Inscripciones web) queda marcado expresamente porque su tabla contiene catorce columnas.

## Criterio de activación

El aviso solo se activa cuando se cumplen simultáneamente estas condiciones:

1. El dispositivo está en orientación vertical.
2. El ancho visible es de 1024 píxeles o menos.
3. Existe una tabla de información visible con cinco o más columnas que desborda el espacio disponible, o la tabla está marcada expresamente como panorámica.

No se bloquean las pantallas cuyas tablas ya se transforman correctamente en tarjetas móviles, los menús construidos con tablas, el encabezado ni las tablas de maquetación antiguas. El aviso tampoco aparece al imprimir.

## Comprobaciones realizadas

- Inscripciones web a 390 × 844: aviso visible.
- Inscripciones web a 768 × 1024: aviso visible.
- Inscripciones web a 844 × 390: aviso oculto y tabla disponible.
- Inscripciones web a 1440 × 900: aviso oculto.
- Listado simplificado a 390 × 844: se mantiene la vista de tarjetas sin bloqueo.
- Almacén general a 390 × 844: se mantiene la vista móvil sin bloqueo.
- Consola del navegador: sin errores ni avisos durante las pruebas.
- JavaScript: validación sintáctica correcta.
- PHP 5.6 y PHP 8.5: validación sintáctica correcta en los archivos PHP modificados.
- Apache: configuración sintácticamente correcta.

## Control por pantalla

- Para forzar una tabla panorámica: atributo `data-requires-landscape` en la tabla.
- Para excluir una pantalla o contenedor: atributo `data-orientation-guard="off"`.
- Para forzar una pantalla completa: atributo `data-orientation-guard="force"` en `body`.
