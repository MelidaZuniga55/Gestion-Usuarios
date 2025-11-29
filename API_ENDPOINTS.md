# 📚 Documentación de Endpoints - API Gestión de Usuarios

## 🔐 Autenticación

Todos los endpoints protegidos requieren un token Bearer en el header:
```
Authorization: Bearer {token}
```

---

## 🔑 AUTH - Autenticación

### 1. Registro de Usuario
**POST** `/api/auth/register`

**Público:** ✅ Sí

**Body:**
```json
{
  "nombre": "Juan",
  "apellido": "Pérez",
  "email": "juan@example.com",
  "password": "password123",
  "telefono": "1234567890",
  "fecha_nacimiento": "1990-01-01",
  "direccion": "Calle Principal 123"
}
```

**Respuesta (201):**
```json
{
  "message": "Usuario registered successfully",
  "data": {
    "id": 1,
    "nombre": "Juan",
    "apellido": "Pérez",
    "email": "juan@example.com",
    ...
  },
  "status": 201
}
```

---

### 2. Login
**POST** `/api/auth/login`

**Público:** ✅ Sí

**Body:**
```json
{
  "email": "juan@example.com",
  "password": "password123"
}
```

**Respuesta (200):**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "nombre": "Juan",
    "apellido": "Pérez",
    "email": "juan@example.com",
    ...
  },
  "token": "1|abc123...",
  "expires_at": "2025-11-28T20:28:10-06:00",
  "expires_in": 300,
  "status": 200
}
```

---

### 3. Logout
**POST** `/api/auth/logout`

**Protegido:** 🔒 Sí

**Respuesta (200):**
```json
{
  "message": "User logged out successfully",
  "status": 200
}
```

---

### 4. Refrescar Token
**POST** `/api/auth/refresh`

**Protegido:** 🔒 Sí

**Respuesta (200):**
```json
{
  "message": "Token refreshed successfully",
  "token": "2|xyz789...",
  "expires_at": "2025-11-28T20:33:10-06:00",
  "expires_in": 300,
  "status": 200
}
```

---

### 5. Verificar Token
**GET** `/api/auth/check`

**Protegido:** 🔒 Sí

**Respuesta (200):**
```json
{
  "message": "Token is valid",
  "valid": true,
  "expires_at": "2025-11-28T20:28:10-06:00",
  "expires_in": 180,
  "user": {
    "id": 1,
    "nombre": "Juan",
    ...
  },
  "status": 200
}
```

---

### 6. Obtener Usuario Actual
**GET** `/api/auth/me`

**Protegido:** 🔒 Sí

**Respuesta (200):**
```json
{
  "user": {
    "id": 1,
    "nombre": "Juan",
    "apellido": "Pérez",
    "email": "juan@example.com",
    ...
  },
  "status": 200
}
```

---

## 📊 ESTADÍSTICAS

### 1. Estadísticas Generales
**GET** `/api/estadisticas`

**Público:** ✅ Sí

**Respuesta (200):**
```json
{
  "data": {
    "total_usuarios": 150,
    "usuarios_activos": 120,
    "usuarios_inactivos": 30,
    "registros_hoy": 5,
    "registros_esta_semana": 25,
    "registros_este_mes": 80
  },
  "message": "Estadísticas generales obtenidas exitosamente",
  "status": 200
}
```

---

### 2. Estadísticas Diarias
**GET** `/api/estadisticas/diarias`

**Público:** ✅ Sí

**Respuesta (200):**
```json
{
  "data": [
    {
      "fecha": "2025-11-28",
      "total_registros": 5
    },
    {
      "fecha": "2025-11-27",
      "total_registros": 8
    }
  ],
  "periodo": "Últimos 30 días",
  "message": "Estadísticas diarias obtenidas exitosamente",
  "status": 200
}
```

---

### 3. Estadísticas Semanales
**GET** `/api/estadisticas/semanales`

**Público:** ✅ Sí

**Respuesta (200):**
```json
{
  "data": [
    {
      "año": 2025,
      "semana": 48,
      "total_registros": 25,
      "fecha_inicio": "2025-11-24",
      "fecha_fin": "2025-11-28"
    }
  ],
  "periodo": "Últimas 12 semanas",
  "message": "Estadísticas semanales obtenidas exitosamente",
  "status": 200
}
```

---

### 4. Estadísticas Mensuales
**GET** `/api/estadisticas/mensuales`

**Público:** ✅ Sí

**Respuesta (200):**
```json
{
  "data": [
    {
      "año": 2025,
      "mes": 11,
      "total_registros": 80,
      "nombre_mes": "November"
    }
  ],
  "periodo": "Últimos 12 meses",
  "message": "Estadísticas mensuales obtenidas exitosamente",
  "status": 200
}
```

---

## 👥 USUARIOS - CRUD

### 1. Listar Usuarios
**GET** `/api/usuarios`

**Protegido:** 🔒 Sí

**Respuesta (200):**
```json
{
  "data": [
    {
      "id": 1,
      "nombre": "Juan",
      "apellido": "Pérez",
      "email": "juan@example.com",
      ...
    }
  ],
  "message": "Usuarios retrieved successfully",
  "status": 200
}
```

---

### 2. Crear Usuario
**POST** `/api/usuarios`

**Protegido:** 🔒 Sí

**Body:**
```json
{
  "nombre": "María",
  "apellido": "González",
  "email": "maria@example.com",
  "password": "password123",
  "telefono": "9876543210",
  "fecha_nacimiento": "1995-05-15",
  "direccion": "Avenida Central 456",
  "activo": true
}
```

**Respuesta (201):**
```json
{
  "data": {
    "id": 2,
    "nombre": "María",
    "apellido": "González",
    ...
  },
  "message": "Usuario created successfully",
  "status": 201
}
```

---

### 3. Obtener Usuario
**GET** `/api/usuarios/{id}`

**Protegido:** 🔒 Sí

**Respuesta (200):**
```json
{
  "data": {
    "id": 1,
    "nombre": "Juan",
    "apellido": "Pérez",
    ...
  },
  "message": "Usuario retrieved successfully",
  "status": 200
}
```

---

### 4. Actualizar Usuario
**PUT/PATCH** `/api/usuarios/{id}`

**Protegido:** 🔒 Sí

**Body:**
```json
{
  "nombre": "Juan Carlos",
  "telefono": "1111111111"
}
```

**Respuesta (200):**
```json
{
  "data": {
    "id": 1,
    "nombre": "Juan Carlos",
    ...
  },
  "message": "Usuario updated successfully",
  "status": 200
}
```

---

### 5. Eliminar Usuario
**DELETE** `/api/usuarios/{id}`

**Protegido:** 🔒 Sí

**Respuesta (200):**
```json
{
  "message": "Usuario deleted successfully",
  "status": 200
}
```

---

## 🔄 Flujo de Autenticación para Frontend

### Paso 1: Login
1. Usuario ingresa credenciales
2. Frontend hace POST a `/api/auth/login`
3. Guarda el token y configura timer de 5 minutos

### Paso 2: Uso de la API
- Incluir token en header: `Authorization: Bearer {token}`
- Hacer peticiones a endpoints protegidos

### Paso 3: Renovación de Token (cada 5 minutos)
1. A los 4:30 minutos, mostrar modal: "¿Mantener sesión activa?"
2. **Si selecciona "Sí":**
   - POST a `/api/auth/refresh`
   - Actualizar token guardado
   - Reiniciar timer
3. **Si selecciona "No":**
   - POST a `/api/auth/logout`
   - Redirigir a login

### Paso 4: Verificación (opcional)
- Usar GET `/api/auth/check` al recargar página
- Verificar si token sigue válido

---

## 📝 Códigos de Estado HTTP

| Código | Significado |
|--------|-------------|
| 200 | OK - Operación exitosa |
| 201 | Created - Recurso creado |
| 401 | Unauthorized - Token inválido o expirado |
| 404 | Not Found - Recurso no encontrado |
| 500 | Internal Server Error - Error del servidor |

---

## ⏱️ Configuración de Tokens

- **Tiempo de expiración:** 5 minutos (300 segundos)
- **Tipo de token:** Bearer Token (Laravel Sanctum)
- **Renovación:** Mediante endpoint `/api/auth/refresh`
