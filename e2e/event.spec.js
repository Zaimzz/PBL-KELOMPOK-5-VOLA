import { test, expect } from '@playwright/test';

async function loginEO(page) {
    const email = process.env.TEST_EO_EMAIL;
    const password = process.env.TEST_EO_PASSWORD;

    if (!email || !password) {
        throw new Error(
            'TEST_EO_EMAIL atau TEST_EO_PASSWORD belum tersedia di .env.playwright'
        );
    }

    await page.goto('/login');

    const eoRadio = page.locator('input[name="role"][value="eo"]');

    await eoRadio.locator('xpath=ancestor::label[1]').click();
    await expect(eoRadio).toBeChecked();

    await page.locator('form input[name="email"]').fill(email);
    await page.locator('form input[name="password"]').fill(password);

    await page.locator('form button[type="submit"]').first().click();

    await expect(page).toHaveURL(/\/eo\/dashboard\/?$/);
}

test.describe('EVT — CRUD Event EO', () => {

    test('EVT-01: EO dapat membuka halaman daftar event', async ({ page }) => {

        await loginEO(page);

        const response = await page.goto('/eo/events');

        expect(response?.status()).toBe(200);

        await expect(page).toHaveURL(/\/eo\/events\/?$/);

        await expect(page.locator('body')).not.toBeEmpty();

    });
    
    test('EVT-02: EO dapat membuka form tambah event', async ({ page }) => {

        await loginEO(page);

        const response = await page.goto('/eo/events/create');

        expect(response?.status()).toBe(200);

        await expect(page).toHaveURL(/\/eo\/events\/create\/?$/);

        const createForm = page.locator('form[action$="/eo/events"]');

        await expect(createForm).toBeVisible();

    });

    test('EVT-03: EO berhasil membuat event dengan data valid', async ({ page }) => {

        await loginEO(page);

        await page.goto('/eo/events/create');

        const form = page.locator('form[action$="/eo/events"]');

        await expect(form).toBeVisible();

        const eventTitle = `QA Playwright Event ${Date.now()}`;

        await form.locator('[name="title"]').fill(eventTitle);

        const categorySelect = form.locator('[name="category_id"]');

        const categoryOptions = categorySelect.locator('option:not([value=""])');

        const categoryCount = await categoryOptions.count();

        expect(
            categoryCount,
            'Database harus memiliki minimal satu kategori event'
        ).toBeGreaterThan(0);

        const categoryId = await categoryOptions.first().getAttribute('value');

        await categorySelect.selectOption(categoryId);

        await form.locator('[name="description"]').fill(
            'Event khusus pengujian otomatis Playwright untuk fitur CRUD Event Vola.'
        );

        await form.locator('[name="location"]').fill('Gedung Serbaguna Malang');

        await form.locator('[name="city"]').fill('Malang');

        await form.locator('[name="contact_info"]').fill('qa-vola@example.com');

        await form.locator('[name="pic_name"]').fill('QA Playwright');

        const dateAfterDays = (days) => {
            const date = new Date();
            date.setDate(date.getDate() + days);

            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        };

        await form.locator('[name="registration_deadline"]').fill(dateAfterDays(20));
        await form.locator('[name="start_date"]').fill(dateAfterDays(30));
        await form.locator('[name="end_date"]').fill(dateAfterDays(31));

        await form.locator('[name="start_time"]').fill('08:00');
        await form.locator('[name="end_time"]').fill('17:00');

        await form.locator(
            'input[name="benefits[]"][value="Konsumsi & Snack"]'
        ).check();

        await form.getByRole('button', {
            name: 'Simpan sebagai Draft'
        }).click();

        await expect(page).toHaveURL(
            /\/eo\/events\/[^/]+\/edit\/?$/
        );

        await expect(
            page.getByText('Event berhasil disimpan sebagai draft.', {
                exact: false
            })
        ).toBeVisible();

        await expect(
            page.locator('input[name="title"]')
        ).toHaveValue(eventTitle);

    });

    test('EVT-04: Sistem menolak pembuatan event dengan field wajib kosong', async ({ page }) => {

        await loginEO(page);

        await page.goto('/eo/events/create');

        const form = page.locator('form[action$="/eo/events"]');

        await expect(form).toBeVisible();

        await form.locator('[name="title"]').fill('');
        await form.locator('[name="category_id"]').selectOption('');

        await form.getByRole('button', {
            name: 'Simpan sebagai Draft'
        }).click();

        await expect(page).toHaveURL(/\/eo\/events\/create\/?$/);

        await expect(
            page.locator('.text-red-600').filter({
                hasText: /wajib|required|harus|dipilih/i
            }).first()
        ).toBeVisible();

    });

    test('EVT-05: Sistem menampilkan validasi tanggal event tidak valid', async ({ page }) => {
        await loginEO(page);

        await page.goto('/eo/events/create');

        const form = page.locator('form[action$="/eo/events"]');
        await expect(form).toBeVisible();

        await expect.poll(async () => {
            return form.evaluate(el =>
                typeof window.Alpine?.$data(el)?.validateEndDate === 'function'
            );
        }).toBe(true);

        const startDate = form.locator('[name="start_date"]');
        const endDate = form.locator('[name="end_date"]');

        await startDate.fill('2027-06-20');
        await startDate.blur();

        await endDate.fill('2027-06-19');
        await endDate.blur();

        await expect(startDate).toHaveValue('2027-06-20');
        await expect(endDate).toHaveValue('2027-06-19');

        const errorMessage = form.locator('p[x-text="endDateError"]');

        await expect(errorMessage).toBeVisible();
        await expect(errorMessage).toHaveText(
            'Tanggal selesai tidak boleh sebelum tanggal mulai.'
        );

        await expect(page).toHaveURL(/\/eo\/events\/create\/?$/);
    });

});