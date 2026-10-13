import { test, expect } from '@playwright/test';

test('halaman login tampil', async ({ page }) => {
  await page.goto('/login');
  await expect(page).toHaveURL(/login/);
  await expect(page.locator('input[name="email"]')).toBeVisible();
  await expect(page.locator('input[name="password"]')).toBeVisible();
});

test('admin bisa login', async ({ page }) => {
  await page.goto('/login');
  await page.locator('input[name="email"]').fill('admin@vola.test');
  await page.locator('input[name="password"]').fill('password');
  await page.getByRole('button', { name: /log in|masuk/i }).click();
  await expect(page).toHaveURL(/dashboard/);
});