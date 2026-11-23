// ponytail: E2E nyata untuk alur kritis NesaCourt + regresi D-01/D-02.
// Server harus hidup di baseURL (php artisan serve :8123). Data dari seeder.
const { test, expect } = require('@playwright/test');

const ACC = {
  buyer:  { login: 'aditya.23048@mhs.unesa.ac.id', pw: 'password' },
  seller: { login: 'barokah@nesacourt.ac.id',      pw: 'password' },
  admin:  { login: 'admin@nesacourt.ac.id',        pw: 'password' },
};

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

test.describe('NesaCourt E2E', () => {
  test('health halaman publik', async ({ page }) => {
    await page.goto('/');
    await expect(page).not.toHaveTitle(/error/i);
    const res = await page.request.get('/katalog');
    expect([200, 302]).toContain(res.status());
  });

  test('buyer: login -> katalog tampil menu', async ({ page }) => {
    await login(page, ACC.buyer);
    await page.goto('/katalog');
    await expect(page.locator('h3').first()).toBeVisible();
    const cards = page.locator('button:has-text("Tambah ke Keranjang")');
    expect(await cards.count()).toBeGreaterThan(0);
  });

  test('buyer: tambah ke keranjang -> drawer & badge bertambah', async ({ page }) => {
    await login(page, ACC.buyer);
    await page.evaluate(() => localStorage.removeItem('nesacourt_cart'));
    await page.goto('/katalog');
    await page.locator('button:has-text("Tambah ke Keranjang")').first().click();
    await expect(page.getByText('Keranjang Pesanan').first()).toBeVisible();
    const cart = await page.evaluate(() => JSON.parse(localStorage.getItem('nesacourt_cart')));
    expect(cart.items.length).toBe(1);
    expect(cart.items[0].qty).toBe(1);
  });

  test('buyer: keranjang persist lintas halaman', async ({ page }) => {
    await login(page, ACC.buyer);
    await page.evaluate(() => localStorage.removeItem('nesacourt_cart'));
    await page.goto('/katalog');
    await page.locator('button:has-text("Tambah ke Keranjang")').first().click();
    await page.goto('/keranjang');
    await page.waitForLoadState('load');
    const cart = await page.evaluate(() => JSON.parse(localStorage.getItem('nesacourt_cart')));
    expect(cart.items.length).toBe(1);
  });

  test('D-01 regresi: seller buka edit menu -> form terisi', async ({ page }) => {
    await login(page, ACC.seller);
    await page.goto('/penjual/menu');
    // klik link edit baris pertama pada tabel menu
    const editLink = page.locator('a[title="Ubah Menu"]').first();
    await expect(editLink).toBeVisible();
    await editLink.click();
    await page.waitForLoadState('load');
    await expect(page).toHaveURL(/\/penjual\/menu\/\d+\/edit$/);
    // isEdit true -> judul form berubah; dulu (bug) form kosong & berjudul "Tambah"
    await expect(page.getByText('Ubah Informasi Menu')).toBeVisible();
    const name = await page.locator('input[name="name"]').inputValue();
    expect(name.trim().length).toBeGreaterThan(0);
  });

  test('D-02 regresi: admin order detail id benar -> 200, id ngawur -> 404', async ({ page }) => {
    await login(page, ACC.admin);
    const ok = await page.goto('/admin/pesanan/1');
    expect(ok.status()).toBe(200);
    await expect(page.getByText(/NC-\d{8}-[A-Z0-9]{4}/).first()).toBeVisible();
    const missing = await page.goto('/admin/pesanan/99999');
    expect(missing.status()).toBe(404);
  });

  test('admin: dashboard & daftar pesanan tampil', async ({ page }) => {
    await login(page, ACC.admin);
    await page.goto('/admin/dashboard');
    await expect(page).toHaveURL(/\/admin\/dashboard$/);
    await page.goto('/admin/pesanan');
    await expect(page.locator('a:has-text("Audit Detail")').first()).toBeVisible();
  });

  test('AI service: /health hidup', async ({ request }) => {
    const res = await request.get('http://127.0.0.1:8001/health');
    expect(res.status()).toBe(200);
  });
});
