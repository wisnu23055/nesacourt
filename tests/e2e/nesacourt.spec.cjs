// ponytail: E2E nyata untuk alur kritis NesaCourt + regresi D-01/D-02.
// Server harus hidup di baseURL (php artisan serve :8123). Data dari seeder.
const { test, expect } = require('@playwright/test');

const ACC = {
  buyer:  { login: 'aditya.23048@mhs.unesa.ac.id', pw: 'password' },
  seller: { login: 'barokah@nesacourt.ac.id',      pw: 'password' },
  admin:  { login: 'admin@nesacourt.ac.id',        pw: 'password' },
};

const CART_KEY = 'nesacourt_cart';

const readCart = (page) =>
  page.evaluate((k) => JSON.parse(localStorage.getItem(k) || 'null'), CART_KEY);

async function login(page, who) {
  await page.goto('/login');
  await page.fill('input[name="login"]', who.login);
  await page.fill('input[name="password"]', who.pw);
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'load' }),
    page.click('button[type="submit"]'),
  ]);
  await expect(page).not.toHaveURL(/\/login$/);
}

// Tambah satu menu pertama di katalog, lalu tutup drawer.
async function addFirstMenu(page) {
  await page.evaluate((k) => localStorage.removeItem(k), CART_KEY);
  await page.goto('/katalog');
  await page.locator('button:has-text("Tambah ke Keranjang")').first().click();
  await expect(page.getByText('Keranjang Pesanan').first()).toBeVisible();
}

test.describe('NesaCourt E2E', () => {
  // E2E-01
  test('E2E-01 health halaman publik', async ({ page }) => {
    await page.goto('/');
    await expect(page).not.toHaveTitle(/error/i);
    const res = await page.request.get('/katalog');
    expect([200, 302]).toContain(res.status());
  });

  // E2E-02
  test('E2E-02 buyer login berhasil', async ({ page }) => {
    await login(page, ACC.buyer);
    await expect(page).toHaveURL(/\/dashboard$/);
  });

  // E2E-03
  test('E2E-03 buyer membuka katalog, kartu menu tampil', async ({ page }) => {
    await login(page, ACC.buyer);
    await page.goto('/katalog');
    await expect(page.locator('h3').first()).toBeVisible();
    const cards = page.locator('button:has-text("Tambah ke Keranjang")');
    expect(await cards.count()).toBeGreaterThan(0);
  });

  // E2E-04
  test('E2E-04 buyer membuka halaman rekomendasi', async ({ page }) => {
    await login(page, ACC.buyer);
    const res = await page.goto('/rekomendasi');
    expect(res.status()).toBe(200);
    await expect(page.getByRole('heading', { name: /Rekomendasi Menu Kantin/i })).toBeVisible();
    await expect(page.getByText('Filter & Kriteria Rekomendasi AI')).toBeVisible();
  });

  // E2E-05
  test('E2E-05 tambah menu ke keranjang -> badge & localStorage', async ({ page }) => {
    await login(page, ACC.buyer);
    await addFirstMenu(page);
    const cart = await readCart(page);
    expect(cart.items.length).toBe(1);
    expect(cart.items[0].qty).toBe(1);
  });

  // E2E-06
  test('E2E-06 tambah kuantitas menu di halaman keranjang', async ({ page }) => {
    await login(page, ACC.buyer);
    await addFirstMenu(page);
    await page.goto('/keranjang');
    await page.locator('button:has-text("+")').first().click();
    await expect.poll(async () => (await readCart(page)).items[0].qty).toBe(2);
  });

  // E2E-07
  test('E2E-07 hapus item dengan mengurangi kuantitas sampai nol', async ({ page }) => {
    await login(page, ACC.buyer);
    await addFirstMenu(page);
    await page.goto('/keranjang');
    await page.locator('button:has-text("+")').first().click();
    await expect.poll(async () => (await readCart(page)).items[0].qty).toBe(2);
    await page.locator('button:has-text("-")').first().click();
    await expect.poll(async () => (await readCart(page)).items[0].qty).toBe(1);
    await page.locator('button:has-text("-")').first().click();
    await expect.poll(async () => {
      const c = await readCart(page);
      return c.items.length;
    }).toBe(0);
  });

  // E2E-08
  test('E2E-08 buyer membuka halaman checkout', async ({ page }) => {
    await login(page, ACC.buyer);
    await addFirstMenu(page);
    await page.goto('/checkout');
    await expect(page.getByText('Konfirmasi Pesanan').first()).toBeVisible();
    await expect(page.getByText('Tipe Pemesanan Makanan')).toBeVisible();
  });

  // E2E-09
  test('E2E-09 buat pesanan -> menuju pembayaran QRIS', async ({ page }) => {
    await login(page, ACC.buyer);
    await addFirstMenu(page);
    await page.goto('/checkout');
    await page.locator('button:has-text("Buat Pesanan & Bayar QRIS")').click();
    await page.waitForURL(/\/pesanan\/bayar\?order=NC-/, { timeout: 20_000 });
    expect(page.url()).toMatch(/order=NC-\d{8}-[A-Z0-9]{4}/);
    await expect(page.getByText('Batalkan Pesanan').first()).toBeVisible();
  });

  // E2E-10
  test('E2E-10 seller mengakses halaman pengelolaan', async ({ page }) => {
    await login(page, ACC.seller);
    await page.goto('/penjual/dashboard');
    await expect(page).toHaveURL(/\/penjual\/dashboard$/);
    await page.goto('/penjual/menu');
    await expect(page.locator('a[title="Ubah Menu"]').first()).toBeVisible();
  });

  // E2E-11
  test('E2E-11 admin mengakses halaman pengelolaan', async ({ page }) => {
    await login(page, ACC.admin);
    await page.goto('/admin/dashboard');
    await expect(page).toHaveURL(/\/admin\/dashboard$/);
    await page.goto('/admin/pesanan');
    await expect(page.locator('a:has-text("Audit Detail")').first()).toBeVisible();
  });

  // E2E-12
  test('E2E-12 keranjang tetap tersimpan saat berpindah halaman', async ({ page }) => {
    await login(page, ACC.buyer);
    await addFirstMenu(page);
    await page.goto('/keranjang');
    await page.waitForLoadState('load');
    await page.goto('/katalog');
    await page.waitForLoadState('load');
    const cart = await readCart(page);
    expect(cart.items.length).toBe(1);
  });

  // E2E-13
  test('E2E-13 regresi D-01: form sunting menu terisi data', async ({ page }) => {
    await login(page, ACC.seller);
    await page.goto('/penjual/menu');
    const editLink = page.locator('a[title="Ubah Menu"]').first();
    await expect(editLink).toBeVisible();
    await editLink.click();
    await page.waitForLoadState('load');
    await expect(page).toHaveURL(/\/penjual\/menu\/\d+/);
    await expect(page.getByText('Ubah Informasi Menu')).toBeVisible();
    const name = await page.locator('input[name="name"]').inputValue();
    expect(name.trim().length).toBeGreaterThan(0);
  });

  // E2E-14
  test('E2E-14 regresi D-02: detail pesanan admin, id salah -> 404', async ({ page }) => {
    await login(page, ACC.admin);
    const ok = await page.goto('/admin/pesanan/1');
    expect(ok.status()).toBe(200);
    await expect(page.getByText(/NC-\d{8}-[A-Z0-9]{4}/).first()).toBeVisible();
    const missing = await page.goto('/admin/pesanan/99999');
    expect(missing.status()).toBe(404);
  });

  // E2E-15
  test('E2E-15 layanan FastAPI merespons pemeriksaan kesehatan', async ({ request }) => {
    const res = await request.get('http://127.0.0.1:8001/health');
    expect(res.status()).toBe(200);
    const body = await res.json();
    expect(body.status).toBe('ok');
  });

  // E2E-16
  test('E2E-16 batalkan pesanan -> status Dibatalkan & stok dikembalikan', async ({ page }) => {
    await login(page, ACC.buyer);
    await addFirstMenu(page);
    await page.goto('/checkout');
    await page.locator('button:has-text("Buat Pesanan & Bayar QRIS")').click();
    await page.waitForURL(/\/pesanan\/bayar\?order=NC-/, { timeout: 20_000 });
    const orderNo = new URL(page.url()).searchParams.get('order');
    expect(orderNo).toMatch(/^NC-\d{8}-[A-Z0-9]{4}$/);

    // batalkan (form onsubmit memakai confirm())
    page.once('dialog', (d) => d.accept());
    await page.locator('form[action*="batal"] button[type="submit"]').first().click();
    await page.waitForURL(/\/riwayat|\/pesanan/, { timeout: 20_000 });

    // status pesanan kini dibatalkan
    await page.goto(`/pesanan/status?order=${orderNo}`);
    await expect(page.getByText('Pesanan Telah Dibatalkan').first()).toBeVisible();
    await expect(page.getByText(/stok porsi menu telah otomatis dikembalikan/i).first()).toBeVisible();
  });
});