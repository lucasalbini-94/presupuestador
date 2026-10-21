Presupuestador Web

Es un sistema web de gestión para profesionales y contratistas que permite administrar clientes, listas de precios personalizadas y generar presupuestos comerciales de forma rápida y sencilla. (en esta primera etapa abocado especialmente a electricistas)

Características Actuales

Dashboard de Inicio: Resumen general con acceso rápido a los presupuestos más recientes y su estado (Borrador, Aprobado, etc.).
Gestión de Presupuestos:
•	Creación y edición de presupuestos numéricos correlativos.
•	Filtro rápido por cliente, número de presupuesto o estado.
•	Envío por Correo Electrónico:
•	 Envío del presupuesto generado directamente al cliente vía mail.
•	Gestión de Clientes Ficha de clientes con datos de contacto (Teléfono, Email, Dirección) e historial completo de presupuestos anteriores.
•	Administración de catálogo propio organizado por categoría, tipo de trabajo, unidad de medida y precio unitario.
•	Configuración y Cuenta: Gestión de sesión de usuario y opciones de cuenta.

Estructura de Directorios:
El proyecto se encuentra organizado de la siguiente manera:

/api
  /models
    Presupuesto.php
  auth.php
  categorias.php
  clientes.php
  conexion.php
  config.php
  login.php
  logout.php
  precios.php
  presupuestos.php
  registro.php
  unidades.php
  usuarios.php
/frontend
  /css
  /includes
  /js
  clientes.php
  index.php
  login.php
  nuevo_cliente.php
  nuevo_presupuesto.php
  precios.php
  presupuestos.php
  registro.php
  usuarios.php
/sql
  datos-prueba.sql
  script-creacion-db.sql
index.php

Próximas Mejoras (Versión 2.0)
Para la siguiente versión de la aplicación se planifican los siguientes desarrollos:
•	 Precios por defecto al registrarse: Carga automática de una plantilla o lista base de precios referenciales al crear un nuevo usuario para agilizar el inicio de uso.
•	Integración de nuevos servicios externos: Ampliar las integraciones más allá de la funcionalidad actual de envío por mail.
•	Mejoras en la estructura de Backend: Creación de la estructura de los modelos, para que en la carpeta API solamente estén los endpoint, y en models esté toda la estructura de las diferentes entidades junto con las sentencias SQL contra la base de datos.
•	Mejorar la comunicación con el cliente: Al momento de enviar por Whatsapp el presupuesto que envié automáticamente el archivo pdf, ahora se hace manualmente. Y a su vez que al momento de enviar el mensaje el mismo tome el nombre del cliente. En el cual estamos trabajando. 
