@echo off
REM ==============================================================
REM  Inicia el microservicio de WhatsApp de OllinFit (solo local/Windows).
REM  Doble clic para arrancarlo. Deja esta ventana abierta mientras lo usas.
REM  En el servidor de produccion NO se usa esto: alli corre como servicio
REM  de systemd (ollinfit-whatsapp) y arranca solo.
REM ==============================================================
cd /d "%~dp0"

REM Instala dependencias la primera vez (si falta la carpeta node_modules).
if not exist "node_modules" (
    echo Instalando dependencias por primera vez...
    call npm install
)

echo.
echo Iniciando el microservicio de WhatsApp de OllinFit...
echo (No cierres esta ventana mientras uses WhatsApp. Cierra con Ctrl+C.)
echo.
node index.js

pause
