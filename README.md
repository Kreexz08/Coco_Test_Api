# Test Laravel API

Este proyecto es una API desarrollada con Laravel que gestiona recursos compartidos y reservas. Está diseñada para ser modular y fácil de mantener, utilizando patrones de diseño como repositorios, factorias y servicios.

## Estructura y Diseño del Sistema

### Estructura del Proyecto

El proyecto sigue una estructura de directorios estándar de Laravel, con algunas carpetas adicionales para organizar mejor el código:

    
    ├── app
    │   ├── Contracts
    │   ├── Exceptions
    │   ├── Factories
    │   ├── Http
    │   ├── Models
    │   ├── Providers
    │   ├── Repositories
    │   ├── Services
    │   └── Traits
    ├── bootstrap
    ├── config
    ├── database
    ├── public
    ├── resources
    ├── routes
    ├── storage
    ├── tests
    └── vendor


### Diseño del Sistema

El sistema está diseñado utilizando una arquitectura basada en componentes clave como factorias, repositorios, controladores, interfaces y servicios. Este enfoque permite un alto grado de modularidad, escalabilidad y facilidad de mantenimiento.

### Componentes claves del sistema

- **Factory**: Se encarga de la creación de objetos complejos, simplificando y centralizando la inicialización de dependencias. Facilita la gestión de instancias y garantiza la coherencia en la configuración.
- **Repository**: Implementa el patrón de repositorio para gestionar la lógica de acceso a datos, desacoplándola de la lógica de negocio. Este patrón permite realizar operaciones CRUD de manera consistente y facilita la prueba unitaria.
- **Controller**: Actúa como intermediario entre las solicitudes HTTP y la lógica del sistema, delegando la lógica de negocio a los servicios y las operaciones de datos a los repositorios.
- **Interface**: Define contratos que aseguran que las implementaciones cumplan con las expectativas del sistema. Promueve la flexibilidad al permitir el intercambio de implementaciones sin impactar otras partes del sistema.
- **Service**: Contiene la lógica de negocio del sistema. Los servicios interactúan con los repositorios y otras dependencias para cumplir con los requisitos del dominio.

## Decisiones de Diseño

- **Repositorio y Servicio**: Se eligió este patrón para desacoplar la lógica de negocio de la lógica de acceso a datos, permitiendo una mayor flexibilidad y facilidad de prueba.
- **Inyección de Dependencias**: Utilizamos interfaces para definir contratos que las implementaciones deben cumplir, lo que permite cambiar fácilmente las implementaciones sin afectar el resto del sistema.

## Instrucciones de Configuración

### Requisitos Previos

- PHP 8.4.1
- Composer, en este caso se uso la version 2.8.4
- Base de datos PostgreSQL

### Instalación

1. Clona el repositorio:
   ```bash
   git clone <https://github.com/Kreexz08/Coco_Test_Api>
   cd Coco_Test_Api

2. Instala las dependencias:
    ```bash
    composer install
3. Copia el archivo de configuración por defecto y ajusta los valores necesarios:
    ```bash
    cp .env.example .env
4. Genera la clave de aplicación:
   ```bash
   php artisan key:generate
5. Configura la base de datos en el archivo .env.
6. Ejecuta las migraciones para crear las tablas necesarias, en este caso la tabla de resources para almacenar informacion de los resources disponibles y reservations que gestiona las reservaciones relacionadas a dichos recursos:
   ```bash
   php artisan migrate
7. Inicia el servidor de desarrollo:
   ```bash
   php artisan serve  
8. Ejecutar las pruebas:
   ```bash
   php artisan test

## Endpoints de la API

### Recursos

#### Obtener todos los recursos:
- **URL**: `/api/resources`
- **Método**: `GET`
- **Descripción**: Recupera una lista de todos los recursos disponibles.
- **Respuesta Exitosa**:
  - **Código**: `200 OK`
  - **Cuerpo**: 
    ```json
    [
      {
        "id": 1,
        "name": "Sala de reuniones",
        "description": "Una sala de reuniones para 10 personas",
        "capacity": 10
        "updated_at": "2024-12-18T00:56:55.000000Z",
        "created_at": "2024-12-18T00:56:55.000000Z"
      },
      ...
    ]
    ```

#### Crear un nuevo recurso
- **URL**: `/api/resources`
- **Método**: `POST`
- **Descripción**: Crea un nuevo recurso.
- **Parámetros**:
  - `name` (string, requerido): Nombre del recurso.
  - `description` (string, requerido): Descripción del recurso.
  - `capacity` (integer, opcional, por defecto 1): Capacidad del recurso.
- **Respuesta Exitosa**:
  - **Código**: `201 Created`
  - **Cuerpo**: 
    ```json
    {
      "id": 1,
      "name": "Sala de reuniones",
      "description": "Una sala de reuniones para 10 personas",
      "capacity": 10,
      "updated_at": "2024-12-18T01:01:32.000000Z",
      "created_at": "2024-12-18T01:01:32.000000Z"
    }
    ```
- **Respuestas erroneas**:
  - En caso de crear un recurso existente:
  - **Código**: `400 Bad Request`
  - **Cuerpo**:
    ```json
    {
     "error": {
         "message": "A resource with this name already exists.",
         "status": 400,
         "timestamp": "2024-12-18 01:02:32"
             }
     }
    ```
  - En caso de no pasar los parametros:
  - **Código**: `500 Internal Server Error`
  - **Cuerpo**:
    ```json
      {
     "success": false,
     "message": "Something went wrong."
     }
    ```

#### Actualizar un recurso
- **URL**: `/api/resources/{id}`
- **Método**: `PUT`
- **Descripción**: Actualiza un recurso existente.
- **Parámetros**:
  - `name` (string): Nuevo nombre del recurso.
  - `description` (string): Nueva descripción del recurso.
  - `capacity` (integer): Nueva capacidad del recurso.
- **Respuesta Exitosa**:
  - **Código**: `200 OK`
  - **Cuerpo**: 
    ```json
    {
    "id": 1,
    "name": "Sala de reuniones",
    "description": "Una sala de reuniones para 9 personas",
    "capacity": 9,
    "created_at": "2024-12-18T01:01:32.000000Z",
    "updated_at": "2024-12-18T01:10:23.000000Z"
    }
    ```
- **Respuesta erronea**:
   - **Código**: `200 OK`
   - **Cuerpo**: 
    ```json
    {
    "error": {
        "message": "Resource not found.",
        "status": 404,
        "timestamp": "2024-12-18 01:10:54"
        }
    }
    ```
    
#### Verificar disponibilidad de un recurso
- **URL**: `/api/resources/{id}/availability?datetime=YYYY-MM-DD%20HH:MM:SS&duration=HH:MM:SS`
- **Ejemplo de URL**: `http://127.0.0.1:8000/api/resources/1/availability?datetime=2024-12-17 14:00:00&duration=01:00:00`
- **Método**: `GET`
- **Descripción**: Verifica si un recurso está disponible en un horario dado, ignora las reservaciones canceladas.
- **Respuestas Exitosas**:
 - **Si esta disponible el recurso para esa fecha y hora**:     
   - **Código**: `200 OK`
   - **Cuerpo**: 
     ```json
     {
       "available": true
     }
     ```
 - ** Si no esta disponible el recurso para esa fecha y hora**:
   - **Código**: `200 OK`
   - **Cuerpo**: 
     ```json
     {
       "available": false
     }
     ```
 - **Respuestas erroneas segun el caso de que sea festivo o este fuera de horario para la reserva**
   - **Código**: `400 Bad Request`
   - **Cuerpo**:
     ```json
     {
     "error": {
         "message": "Resource is not available on weekends.",
         "status": 400,
         "timestamp": "2024-12-18 01:21:31"
         }
     }
     ```
 - **Tambien puede devolver un cuerpo como este segun el horario establecido, en este caso es de 9:00 AM a 6:00 PM**:
   - **Código**: `400 Bad Request`
   - **Cuerpo**:
     ```json
     {
       "error": {
       "message": "Resource is only available between 9:00 AM and 6:00 PM.",
       "status": 400,
       "timestamp": "2024-12-18 01:22:58"
         }
       }

### Reservas
#### Crear una nueva reserva
- **URL**: `/api/reservations`
- **Método**: `POST`
- **Descripción**: Crea una nueva reserva para un recurso.
- **Parámetros**:
  - `resource_id` (integer, requerido): ID del recurso a reservar.
  - `reserved_at` (datetime, requerido): Fecha y hora de la reserva.
  - `duration` (string, requerido): Duración de la reserva en formato `HH:MM:SS`.
- **Respuesta Exitosa**:
  - **Código**: `201 Created`
  - **Cuerpo**: 
     ```json
     {
       "id": 1,
       "resource_id": 1,
       "reserved_at": "2024-12-19T14:00:00",
       "duration": "01:00:00",
       "status": "pending",
       "updated_at": "2024-12-18T01:27:57.000000Z",
       "created_at": "2024-12-18T01:27:57.000000Z"
     }
     ```
- **Respuestas erroneas**:
  - En su mayoria son las mismas respuestas que devuelve el endpoint para validar si esta disponible en un horario, excepto si intentas reservar en un horario reservado
  - **Código** `400 Bad Request`
  - **Cuerpo**:
    ```json
    {
    "error": {
        "message": "The resource is not available at the selected time.",
        "status": 400,
        "timestamp": "2024-12-18 01:27:21"
        }
    }

#### Confirmar una reserva
- **URL**: `/api/reservations/{id}/confirm`
- **Método**: `PUT`
- **Descripción**: Confirma una reserva existente.
- **Respuesta Exitosa**:
  - **Código**: `200 OK`
  - **Cuerpo**: 
    ```json
    {
    "message": "Reservation confirmed successfully.",
    "reservation": {
        "id": 1,
        "resource_id": 1,
        "reserved_at": "2024-12-19 10:00:00",
        "duration": "05:00:00",
        "status": "confirmed",
        "created_at": "2024-12-18T01:19:52.000000Z",
        "updated_at": "2024-12-18T01:29:35.000000Z"
        }
    }
    ```
- **Respuestas erroneas en caso de que no exista la reservacion o ya este confirmada**:
  - **Código**: `400 Bad Request`
  - **Cuerpo**:
    ```json
    {
    "error": {
        "message": "The reservation is already confirmed.",
         "status": 400,
         "timestamp": "2024-12-18 01:30:19"
         }
     }
    ```
- **Si no existe la reservacion**:
  - **Código**: `400 Bad Request`
  - **Cuerpo**:
    ```json
     {
     "error": {
         "message": "Reservation not found.",
         "status": 400,
         "timestamp": "2024-12-18 01:32:27"
         }
     }
   ```
#### Cancelar una reserva
- **URL**: `/api/reservations/{id}`
- **Método**: `DELETE`
- **Descripción**: Aunque es un metodo delete no elimina la reserva, hace un softdelete cambiando el estado a cancelada.
- **Respuesta Exitosa**:
  - **Código**: `200 OK`
  - **Cuerpo**: 
    ```json
    {
      "message": "Reservation successfully canceled.",
      "success": true
    }
    ```
- **Respuesta erronea**
  - **Código**: `400 Bad Request`
  - **Cuerpo**:
    ```json
     {
     "error": {
         "message": "Reservation not found.",
         "status": 400,
         "timestamp": "2024-12-18 01:32:27"
         }
     }
    ```
