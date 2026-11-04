# OllinFit — Microservicio de WhatsApp (Baileys)

Envía por WhatsApp el **mensaje de bienvenida + código QR** cuando se crea un socio.
Laravel llama a este servicio por HTTP; el QR se dibuja aquí a partir del folio.

> ⚠️ Usa la librería **no oficial** [Baileys](https://github.com/WhiskeySockets/Baileys).
> Va contra los Términos de WhatsApp y el número puede ser **bloqueado**. Úsalo con
> bajo volumen y bajo tu propio riesgo. Puedes apagarlo en cualquier momento con
> `WHATSAPP_ENABLED=false` en el `.env` de Laravel.

## Puesta en marcha

1. Entra a la carpeta e instala dependencias:
   ```bash
   cd whatsapp-service
   npm install
   ```

2. Configura el entorno:
   ```bash
   cp .env.example .env
   ```
   - `PORT`: el puerto (por defecto 3000). Debe coincidir con `WHATSAPP_API_URL` de Laravel.
   - `API_TOKEN`: un texto secreto. Debe ser **igual** a `WHATSAPP_API_TOKEN` del `.env` de Laravel.

3. Arranca el servicio:
   ```bash
   npm start
   ```

4. **Vincula el número (una sola vez):** al arrancar, la terminal mostrará un **código QR**.
   En el teléfono del gimnasio abre **WhatsApp → Dispositivos vinculados → Vincular un dispositivo**
   y escanéalo. Cuando veas `✅ WhatsApp conectado`, ya está listo.
   - La sesión se guarda en `whatsapp-service/auth/`; al reiniciar **no** vuelve a pedir el QR.
   - Solo volverá a pedirlo si borras esa carpeta o cierras sesión desde el teléfono.

## En Laravel

En el `.env` de Laravel:
```dotenv
WHATSAPP_ENABLED=true
WHATSAPP_API_URL=http://127.0.0.1:3000
WHATSAPP_API_TOKEN=el-mismo-token-del-servicio
WHATSAPP_COUNTRY_CODE=52
```
Y deja corriendo el worker de colas (el envío va encolado):
```bash
php artisan queue:work
```

## Endpoints

- `GET /status` → `{ "connected": true|false }`
- `POST /send` (requiere `Authorization: Bearer <API_TOKEN>`)
  ```json
  { "to": "5214491234567", "message": "Hola...", "qr": "MX-9012" }
  ```
  Si `qr` viene, envía una imagen (el QR) con el mensaje como pie; si no, solo texto.

## Dejarlo corriendo en producción

Usa un gestor de procesos, por ejemplo **pm2**:
```bash
npm install -g pm2
pm2 start index.js --name ollinfit-whatsapp
pm2 save
```
