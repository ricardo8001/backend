/**
 * atualizar_cookie.js — Serviço Node + Puppeteer (light, sem UI extra)
 * Porta: 3210
 * Requisitos: npm i puppeteer express
 */
const fs = require('fs');
const path = require('path');
const express = require('express');
const app = express();
app.use(express.json());

const PORT = 3210;
const COOKIES_FILE = path.join(__dirname, 'cookies_atualizado.json');

const SIGNIN_URL = 'https://www.amazon.com/ap/signin?ie=UTF8&openid.pape.max_auth_age=0&signInRedirectToFPPThreshold=5&csrf=130-5128768-5727231&language=pt&useSHuMAWorkflow=true&pageId=usflex&ignoreAuthState=1&aaToken=%7B%26%2334%3BuniqueValidationId%26%2334%3B%3A%26%2334%3Bfc860d1c-1c41-4e4f-84d2-3116b0405ddd%26%2334%3B%7D&prevRID=11V186SJG6AAVCCK3TTP&openid.return_to=https%3A%2F%2Fwww.amazon.com%2F%3Fref_%3Dnav_ya_signin&openid.assoc_handle=usflex&openid.mode=checkid_setup&prepopulatedLoginId=eyJjaXBoZXIiOiJHUmVWU2VFU3J3VWZoeTJKeFdSNVArOWtpclhmNTl3TDVGTm5OVk1vY2dJPSIsInZlcnNpb24iOjIsIklWIjoiK21TWXpvM3FXQnVYUTloVjRQdGVsZz09In0%3D&switch_account=picker&switcher_type=filtered&openid.ns=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0&timestamp=1755366244000';
const WALLET_URL  = 'https://www.amazon.com/cpe/yourpayments/wallet?ref_=ya_d_c_pmt_mpo';

let puppeteer;
try { puppeteer = require('puppeteer'); }
catch (e) {
  console.error('Instale dependências: npm i puppeteer express');
  process.exit(1);
}

let browser = null;
let busy = false;

function readCookiesFile() {
  try {
    if (!fs.existsSync(COOKIES_FILE)) return {};
    return JSON.parse(fs.readFileSync(COOKIES_FILE, 'utf8'));
  } catch { return {}; }
}
function writeCookiesFile(obj) {
  try {
    fs.writeFileSync(COOKIES_FILE, JSON.stringify(obj, null, 2));
  } catch (e) {
    console.error('Erro ao salvar cookies:', e);
  }
}

async function ensureBrowser() {
  if (browser) return;
  browser = await puppeteer.launch({
    headless: false,
    defaultViewport: null,
    args: ['--start-maximized'],
    userDataDir: path.join(__dirname, 'chrome_profile')
  });
}

function buildHeaderCookieFromStored(data) {
  // aceita "cookie: ..." OU "name=value; ..." OU JSON de array de cookies
  const parts = [];
  const pushHeader = (val) => {
    if (!val) return;
    const cleaned = String(val).trim().replace(/^cookie\s*:\s*/i, '');
    if (cleaned) parts.push(cleaned);
  };
  const tryArr = (arr) => {
    try { return arr.map(c => `${c.name}=${c.value}`).join('; '); } catch { return ''; }
  };
  if (data.cookies1) {
    try {
      const parsed = JSON.parse(data.cookies1);
      if (Array.isArray(parsed)) pushHeader(tryArr(parsed)); else pushHeader(data.cookies1);
    } catch { pushHeader(data.cookies1); }
  }
  if (data.cookies2) pushHeader(data.cookies2);
  return parts.filter(Boolean).join('; ');
}

async function performRefresh() {
  busy = true;
  try {
    await ensureBrowser();
    // Abre NOVA aba e mantém aberta
    const page = await browser.newPage();

    // aplica header de cookie inicial (o que está colado no index)
    const data = readCookiesFile();
    const headerCookie = buildHeaderCookieFromStored(data);
    if (headerCookie) await page.setExtraHTTPHeaders({ 'cookie': headerCookie });

    // 1) login
    await page.goto(SIGNIN_URL, { waitUntil: 'networkidle2' });

    // 2) business
    try {
      await page.waitForSelector('input[name="accountType"][value="business"]', { timeout: 5000 });
      await page.click('input[name="accountType"][value="business"]');
      await page.click('#continue');
    } catch {}

    // 3) wallet
    await page.goto(WALLET_URL, { waitUntil: 'networkidle2' });

    // 4) captura
    const newCookies = await page.cookies();
    const header = newCookies.map(c => `${c.name}=${c.value}`).join('; ');

    const toSave = readCookiesFile();
    toSave.cookies1 = header;              // substitui cookies1
    toSave.lastUpdate = new Date().toISOString();
    writeCookiesFile(toSave);

    return { ok: true, count: newCookies.length };
  } catch (e) {
    return { ok: false, error: e.message || String(e) };
  } finally {
    busy = false;
  }
}

// HTTP API
const appInst = app;
appInst.get('/status', (req, res) => {
  const d = readCookiesFile();
  res.json({ ok: true, busy, lastUpdate: d.lastUpdate || d.lastManualUpdate || null });
});
appInst.post('/refresh', async (req, res) => {
  if (busy) return res.json({ ok: false, busy: true });
  const out = await performRefresh();
  res.json(out);
});

appInst.listen(PORT, () => console.log('Atualizador ativo na porta', PORT));
