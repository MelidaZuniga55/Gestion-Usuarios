# 🚀 Guía Rápida - Probar API en Postman

## ✅ Servidor Iniciado

El servidor está corriendo en: **http://127.0.0.1:8000**

---

## 📋 Pruebas en Postman

### 1️⃣ Registro de Usuario

**Método:** `POST`  
**URL:** `http://127.0.0.1:8000/api/auth/register`  
**Headers:**
```
Content-Type: application/json
```

**Body (JSON):**
```json
{
  "nombre": "jont",
  "apellido": "Martinez",
  "email": "jont@example.com",
  "password": "123456",
  "telefono": "1234567890",
  "fecha_nacimiento": "1990-01-01",
  "direccion": "Calle Principal 123"
}
```

**Respuesta Esperada (201):**
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

### 2️⃣ Login

**Método:** `POST`  
**URL:** `http://127.0.0.1:8000/api/auth/login`  
**Headers:**
```
Content-Type: application/json
```

**Body (JSON):**
```json
{
  "email": "juan@example.com",
  "password": "password123"
}
```

**Respuesta Esperada (200):**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "nombre": "Juan",
    ...
  },
  "token": "1|abc123xyz...",
  "expires_at": "2025-11-28T20:56:10-06:00",
  "expires_in": 300,
  "status": 200
}
```

> **⚠️ IMPORTANTE:** Copia el `token` de la respuesta, lo necesitarás para los siguientes endpoints.

---

### 3️⃣ Verificar Token

**Método:** `GET`  
**URL:** `http://127.0.0.1:8000/api/auth/check`  
**Headers:**
```
Authorization: Bearer {TU_TOKEN_AQUI}
```

**Respuesta Esperada (200):**
```json
{
  "message": "Token is valid",
  "valid": true,
  "expires_at": "2025-11-28T20:56:10-06:00",
  "expires_in": 180,
  "user": {...},
  "status": 200
}
```

---

### 4️⃣ Obtener Usuario Actual

**Método:** `GET`  
**URL:** `http://127.0.0.1:8000/api/auth/me`  
**Headers:**
```
Authorization: Bearer {TU_TOKEN_AQUI}
```

**Respuesta Esperada (200):**
```json
{
  "user": {
    "id": 1,
    "nombre": "Juan",
    "apellido": "Pérez",
    ...
  },
  "status": 200
}
```

---

### 5️⃣ Refrescar Token

**Método:** `POST`  
**URL:** `http://127.0.0.1:8000/api/auth/refresh`  
**Headers:**
```
Authorization: Bearer {TU_TOKEN_AQUI}
```

**Respuesta Esperada (200):**
```json
{
  "message": "Token refreshed successfully",
  "token": "2|newtoken123...",
  "expires_at": "2025-11-28T21:01:10-06:00",
  "expires_in": 300,
  "status": 200
}
```

---

### 6️⃣ Listar Usuarios (Protegido)

**Método:** `GET`  
**URL:** `http://127.0.0.1:8000/api/usuarios`  
**Headers:**
```
Authorization: Bearer {TU_TOKEN_AQUI}
```

**Respuesta Esperada (200):**
```json
{
  "data": [
    {
      "id": 1,
      "nombre": "Juan",
      "apellido": "Pérez",
      ...
    }
  ],
  "message": "Usuarios retrieved successfully",
  "status": 200
}
```

---

### 7️⃣ Estadísticas Generales (Público)

**Método:** `GET`  
**URL:** `http://127.0.0.1:8000/api/estadisticas`  
**Headers:**
```
Content-Type: application/json
```

**Respuesta Esperada (200):**
```json
{
  "data": {
    "total_usuarios": 1,
    "usuarios_activos": 1,
    "usuarios_inactivos": 0,
    "registros_hoy": 1,
    "registros_esta_semana": 1,
    "registros_este_mes": 1
  },
  "message": "Estadísticas generales obtenidas exitosamente",
  "status": 200
}
```

---

### 8️⃣ Logout

**Método:** `POST`  
**URL:** `http://127.0.0.1:8000/api/auth/logout`  
**Headers:**
```
Authorization: Bearer {TU_TOKEN_AQUI}
```

**Respuesta Esperada (200):**
```json
{
  "message": "User logged out successfully",
  "status": 200
}
```

---

## 🎯 Flujo de Prueba Recomendado

1. **Registrar usuario** → Obtener datos del usuario
2. **Login** → Obtener token
3. **Verificar token** → Confirmar que el token es válido
4. **Obtener usuario actual** → Ver datos del usuario autenticado
5. **Listar usuarios** → Ver todos los usuarios (requiere token)
6. **Estadísticas** → Ver estadísticas generales (público)
7. **Refrescar token** → Obtener nuevo token antes de que expire
8. **Logout** → Cerrar sesión

---

## 💡 Consejos para Postman

### Configurar Variable de Entorno
1. En Postman, crea un Environment llamado "Gestion Usuarios"
2. Agrega estas variables:
   - `base_url`: `http://127.0.0.1:8000`
   - `token`: (se llenará después del login)

3. Después del login, en la pestaña "Tests" del request, agrega:
```javascript
pm.test("Save token", function () {
    var jsonData = pm.response.json();
    pm.environment.set("token", jsonData.token);
});
```

4. Ahora en los headers usa: `Bearer {{token}}`

### Crear Colección
1. Crea una colección llamada "Gestion Usuarios API"
2. Agrega todos los endpoints organizados por carpetas:
   - 📁 Auth
   - 📁 Estadísticas
   - 📁 Usuarios

---

## ⚠️ Recordatorios Importantes

- ✅ El token expira en **5 minutos**
- ✅ Usa `expires_in` para saber cuántos segundos quedan
- ✅ Endpoints protegidos requieren header `Authorization: Bearer {token}`
- ✅ Si el token expira, haz login nuevamente o usa refresh

---

## 🔗 Endpoints Disponibles

### Autenticación (6)
- POST `/api/auth/register`
- POST `/api/auth/login`
- POST `/api/auth/logout` 🔒
- POST `/api/auth/refresh` 🔒
- GET `/api/auth/check` 🔒
- GET `/api/auth/me` 🔒

### Estadísticas (4)
- GET `/api/estadisticas`
- GET `/api/estadisticas/diarias`
- GET `/api/estadisticas/semanales`
- GET `/api/estadisticas/mensuales`

### Usuarios CRUD (6) 🔒
- GET `/api/usuarios`
- POST `/api/usuarios`
- GET `/api/usuarios/{id}`
- PUT `/api/usuarios/{id}`
- PATCH `/api/usuarios/{id}`
- DELETE `/api/usuarios/{id}`

🔒 = Requiere autenticación

---

## 🎉 ¡Listo para Probar!

Tu API está corriendo en **http://127.0.0.1:8000**

Para detener el servidor: Presiona `Ctrl + C` en la terminal
