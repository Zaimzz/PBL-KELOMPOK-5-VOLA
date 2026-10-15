import { test, expect } from '@playwright/test';

const emailField = (page) => page.locator('input[name="email"]');
const passwordField = (page) => page.locator('input[name="password"]');
const loginButton = (page) => page.getByRole('button', { name: /log in|masuk/i });

test.describe('Login Volunteer dan EO (/login)', () => {
  test('halaman login tampil', async ({ page }) => {
    await page.goto('/login');
    await expect(page).toHaveURL(/login/);
    await expect(emailField(page)).toBeVisible();
    await expect(passwordField(page)).toBeVisible();
  });

  test('login ditolak dengan kredensial salah', async ({ page }) => {
    await page.goto('/login');
    await emailField(page).fill('tidak.ada@vola.test');
    await passwordField(page).fill('password-salah');
    await loginButton(page).click();

    await expect(page).toHaveURL(/login/);
    await expect(page).not.toHaveURL(/dashboard/);
    await expect(emailField(page)).toBeVisible();
  });
});

test.describe('Login Admin (/admin/login)', () => {
  test('halaman login admin tampil', async ({ page }) => {
    await page.goto('/admin/login');
    await expect(page).toHaveURL(/admin\/login/);
    await expect(emailField(page)).toBeVisible();
    await expect(passwordField(page)).toBeVisible();
  });

  test('admin bisa login', async ({ page }) => {
    await page.goto('/admin/login');
    await emailField(page).fill('admin@vola.test');
    await passwordField(page).fill('password');
    await loginButton(page).click();

    await expect(page).toHaveURL(/admin\/dashboard/);
  });

  test('login admin ditolak dengan kredensial salah', async ({ page }) => {
    await page.goto('/admin/login');
    await emailField(page).fill('tidak.ada@vola.test');
    await passwordField(page).fill('password-salah');
    await loginButton(page).click();

    await expect(page).toHaveURL(/admin\/login/);
    await expect(page).not.toHaveURL(/admin\/dashboard/);
  });
});