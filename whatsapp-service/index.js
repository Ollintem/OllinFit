/**
 * Microservicio no oficial de WhatsApp para OllinFit.
 *
 * - Se vincula al WhatsApp del gimnasio escaneando un QR en la terminal (una sola vez).
 * - Expone POST /send protegido por un token, que Laravel llama para enviar
 *   un mensaje de bienvenida + el código QR (dibujado aquí a partir del folio).
 *
 * AVISO: usa la librería no oficial Baileys. Va contra los Términos de WhatsApp;
 * el número puede ser bloqueado. Úsalo bajo tu propio riesgo y con bajo volumen.
 */
require('dotenv').config();

const express = require('express');
const QRCode = require('qrcode');
const qrcodeTerminal = require('qrcode-terminal');
const pino = require('pino');
const {
  default: makeWASocket,
  useMultiFileAuthState,
  DisconnectReason,
} = require('@whiskeysockets/baileys');

const PORT = process.env.PORT || 3000;
const API_TOKEN = process.env.API_TOKEN || '';

let sock = null;
let conectado = false;

async function iniciarWhatsapp() {
  // La sesión se guarda en ./auth: al reiniciar NO vuelve a pedir el QR.
  const { state, saveCreds } = await useMultiFileAuthState('auth');

  sock = makeWASocket({
    auth: state,
    logger: pino({ level: 'silent' }),
    printQRInTerminal: false,
  });

  sock.ev.on('creds.update', saveCreds);

  sock.ev.on('connection.update', (update) => {
    const { connection, lastDisconnect, qr } = update;

    if (qr) {
      console.log('\n================ VINCULA TU WHATSAPP ================');
      console.log('Abre WhatsApp > Dispositivos vinculados > Vincular un dispositivo');
      console.log('y escanea este código QR:\n');
      qrcodeTerminal.generate(qr, { small: true });
    }

    if (connection === 'open') {
      conectado = true;
      console.log('\n✅ WhatsApp conectado. El servicio está listo para enviar mensajes.');
    }

    if (connection === 'close') {
      conectado = false;
      const code = lastDisconnect?.error?.output?.statusCode;
      const cerroSesion = code === DisconnectReason.loggedOut;
      console.log(`⚠️  Conexión cerrada (code ${code}).` + (cerroSesion ? ' Sesión cerrada: borra la carpeta auth y vuelve a vincular.' : ' Reconectando...'));
      if (!cerroSesion) {
        iniciarWhatsapp();
      }
    }
  });
}

const app = express();
app.use(express.json({ limit: '1mb' }));

// Autenticación por token compartido con Laravel (WHATSAPP_API_TOKEN).
app.use((req, res, next) => {
  if (!API_TOKEN) return next(); // si no configuras token, no exige (solo para pruebas locales)
  const auth = req.headers.authorization || '';
  const token = auth.startsWith('Bearer ') ? auth.slice(7) : '';
  if (token !== API_TOKEN) {
    return res.status(401).json({ error: 'Token inválido' });
  }
  next();
});

app.get('/status', (req, res) => {
  res.json({ connected: conectado });
});

app.post('/send', async (req, res) => {
  try {
    const { to, message, qr } = req.body || {};

    if (!to || !message) {
      return res.status(422).json({ error: 'Faltan campos: to y message son obligatorios.' });
    }
    if (!conectado || !sock) {
      return res.status(503).json({ error: 'WhatsApp no está conectado todavía.' });
    }

    const numero = String(to).replace(/\D+/g, '');

    // Dejamos que WhatsApp nos diga el JID real del número (resuelve el 52 vs 521 de México).
    let jid = `${numero}@s.whatsapp.net`;
    try {
      const [info] = await sock.onWhatsApp(numero);
      if (info?.exists && info.jid) {
        jid = info.jid;
      } else if (info && !info.exists) {
        return res.status(422).json({ error: 'El número no tiene WhatsApp.' });
      }
    } catch (_) {
      // Si la verificación falla, seguimos con el JID por defecto.
    }

    if (qr) {
      // Dibuja el QR del folio como PNG y lo manda como imagen con el mensaje de caption.
      const pngBuffer = await QRCode.toBuffer(String(qr), { width: 400, margin: 1 });
      await sock.sendMessage(jid, { image: pngBuffer, caption: message });
    } else {
      await sock.sendMessage(jid, { text: message });
    }

    res.json({ ok: true });
  } catch (e) {
    console.error('Error al enviar:', e?.message || e);
    res.status(500).json({ error: 'No se pudo enviar el mensaje.' });
  }
});

app.listen(PORT, () => {
  console.log(`Microservicio WhatsApp escuchando en http://127.0.0.1:${PORT}`);
});

iniciarWhatsapp();
