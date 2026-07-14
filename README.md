# Portal de Mitos - Ajinomoto del Perú

Este repositorio contiene la estructura completa del sitio web **Portal de Mitos de Ajinomoto**, desarrollado a partir de la maquetación HTML estática y convertido a los estándares de desarrollo de temas personalizados de **WordPress**.

---

## 📌 Requisitos del Entorno
* **WordPress**: Versión 6.0 o superior.
* **PHP**: Versión 8.0 o superior.
* **Servidor**: Apache o Nginx con soporte para reescritura de URLs (`mod_rewrite`).
* **Base de datos**: MySQL 5.7+ o MariaDB 10.3+.

---

## 📂 Estructura del Tema
El tema personalizado se encuentra ubicado en:
`wp-content/themes/ajinomoto-mitos/`

### Archivos Clave:
* `style.css`: Cabecera de metadatos y declaración del tema.
* `functions.php`: Registro de scripts/estilos (AlpineJS, GSAP, Swiper), menús de navegación, CPTs y Taxonomías.
* `header.php` y `footer.php`: Cabecera y pie de página modularizados, menús dinámicos e integración de búsqueda.
* `front-page.php`: Portada interactiva de mitos del GMS (Sliders por categorías y modal Alpine.js).
* `page-mitos-cocina.php`: Página de listado de mitos de cocina con pestañas de filtrado dinámico.
* `single-mitos.php`: Vista de detalle de mitos (GMS y cocina) con soporte científico, referencias y revisor nutricional.
* `single-revisores.php`: Ficha o perfil del revisor/nutricionista del mito.
* `page-informacion.php` / `page-informacion-detalle.php` / `page-informacion-articulo.php`: Plantillas jerárquicas para la sección "Más información sobre GMS".
* `page-cuentanos.php`: Plantilla para la sección de envío de mitos integrada con WPForms.
* `search.php`: Resultados del motor de búsqueda nativo.

---

## ⚙️ Configuración en WordPress

### 1. Registro de Custom Post Types y Taxonomías
El código base en `functions.php` registra automáticamente los CPTs y Taxonomías necesarios. No es obligatorio usar CPT UI, pero puedes usarlo para administrar etiquetas visuales si lo deseas.

* **CPT: Revisores** (`revisores`)
* **CPT: Mitos** (`mitos`)
* **Taxonomía: Tipo de Mito** (`tipo_mito`) -> Asociado a Mitos. Términos recomendados:
  * `gms` (para mitos del Glutamato)
  * `cocina` (para mitos de la cocina)
* **Taxonomía: Categorías de Mito** (`categoria_mito`) -> Asociado a Mitos. Términos recomendados:
  * `seguridad` (Seguridad e impacto en la salud)
  * `evidencia` (Evidencia científica)
  * `origen` (Origen)
  * `factores` (Factores nutricionales)
  * `tecnicas` (Técnicas de cocina)
  * `salud` (Salud en la mesa)
  * `productos` (Productos y Conservación)
  * `nutricion` (Nutrición, Cuerpo y Dietas)

---

### 2. Configuración de Campos Personalizados (ACF)
Para que las vistas dinámicas cargen la información correspondiente, debes crear los siguientes grupos de campos en el plugin **Advanced Custom Fields (ACF)**:

#### Grupo 1: Campos de Mito (Asignar a CPT `mitos`)
| Nombre del Campo | Nombre Técnico | Tipo de Campo | Descripción |
| :--- | :--- | :--- | :--- |
| Subtítulo del Mito | `subtitulo_mito` | Texto | Título interno del mito en las páginas de detalle. |
| Realidad Corta | `realidad_corta` | Texto / Área de texto | Resumen rápido de la realidad para el modal y banners. |
| Sustento Científico | `sustento_cientifico` | Editor WYSIWYG | Texto detallado de la explicación científica (si se deja vacío, cargará `the_content()`). |
| Perspectiva Nutricional | `perspectiva_nutricional` | Editor WYSIWYG | Sección adicional nutricional en detalles. |
| Palatabilidad y Digestión | `palatabilidad_digestion` | Editor WYSIWYG | Sección adicional técnica. |
| Referencias | `referencias` | Área de texto | Citas y fuentes bibliográficas. |
| Revisor Asociado | `revisor` | Relación / Objeto de Entrada | Selección del revisor nutricionista (CPT `revisores`). |

#### Grupo 2: Campos de Revisor (Asignar a CPT `revisores`)
| Nombre del Campo | Nombre Técnico | Tipo de Campo | Descripción |
| :--- | :--- | :--- | :--- |
| Cargo del Revisor | `cargo_revisor` | Texto | Ejemplo: "Nutricionista-Dietista — CNP 4853". |
| Resumen Biográfico | `resumen_revisor` | Área de texto | Párrafo introductorio del perfil profesional. |
| Formación Académica | `formacion_academica` | Editor WYSIWYG | Detalle curricular de estudios y formación. |
| Experiencia Profesional | `experiencia_profesional` | Editor WYSIWYG | Detalle de trayectoria laboral. |
| Premios y Distinciones | `premios_distinciones` | Editor WYSIWYG | Reconocimientos. |
| Publicaciones | `publicaciones` | Editor WYSIWYG | Artículos o libros publicados. |

*Nota: La foto principal del mito y del revisor debe subirse como la **Imagen Destacada** de cada entrada.*

---

### 3. Integración con WPForms ("Cuéntanos tu mito")
1. Instala el plugin gratuito **WPForms**.
2. Crea un nuevo formulario llamado "Cuéntanos tu mito".
3. Agrega los siguientes campos al formulario:
   * **Nombre y apellidos** (Tipo: Single Line Text / Name)
   * **Número de DNI** (Tipo: Number / Single Line Text)
   * **Correo electrónico** (Tipo: Email)
   * **Número de celular** (Tipo: Phone / Single Line Text)
   * **Autorización tratamiento de datos** (Tipo: Checkbox - Requerido)
   * **Aceptación de Términos y Condiciones** (Tipo: Checkbox - Requerido)
   * **¿Cuál es tu mito?** (Tipo: Paragraph Text)
4. El tema está programado para **detectar y renderizar automáticamente** tu primer formulario creado en WPForms en las plantillas de detalle y la página "Cuéntanos tu mito". Si no hay formularios creados, cargará la maquetación HTML estática como alternativa visual.

---

## 🔄 Versionamiento en Múltiples Repositorios (GitHub & Azure DevOps)

De acuerdo a los requerimientos, el proyecto se gestionará en dos repositorios remotos simultáneamente.

### Configuración de Git en tu máquina local:

1. **Inicializar el repositorio** (si no se ha hecho previamente):
   ```bash
   git init
   git add .
   git commit -m "feat: inicialización del proyecto y estructura wordpress"
   ```

2. **Agregar los repositorios remotos**:
   * Configura el repositorio de GitHub como `origin` (primario):
     ```bash
     git remote add origin https://github.com/tu-usuario/ajinomoto-mitos.git
     ```
   * Configura el repositorio de Azure DevOps como `azure` (secundario):
     ```bash
     git remote add azure https://dev.azure.com/tu-organizacion/tu-proyecto/_git/ajinomoto-mitos
     ```

3. **Subir cambios a ambos repositorios**:
   * Subir cambios a GitHub:
     ```bash
     git push -u origin main
     ```
   * Subir cambios a Azure DevOps:
     ```bash
     git push -u azure main
     ```

---

## 🚀 Despliegue Continuo (CI/CD) en Azure DevOps

El archivo `azure-pipelines.yml` ubicado en la raíz del proyecto está configurado para realizar las siguientes acciones en cada cambio en la rama `main`:

1. **Configurar PHP 8.2**: Levanta el entorno de compilación.
2. **PHP Lint**: Valida sintácticamente cada uno de los archivos del tema personalizado para certificar que no haya errores fatales de código (`Fatal Errors`).
3. **Compresión**: Empaqueta el tema personalizado en un archivo ZIP listo para instalar en producción (`ajinomoto-mitos-theme.zip`).
4. **Publicar Artefacto**: Sube el ZIP a los artefactos de Azure DevOps para que pueda ser tomado por una Tarea de Despliegue de Release (ej. hacia un Servidor Web / Azure App Service).
