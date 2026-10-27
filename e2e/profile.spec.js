
import { test, expect } from '@playwright/test';

const accounts = {
    volunteer: {
        email: process.env.TEST_VOLUNTEER_EMAIL,
        password: process.env.TEST_VOLUNTEER_PASSWORD,
        dashboard: '/dashboard',
    },
    eo: {
        email: process.env.TEST_EO_EMAIL,
        password: process.env.TEST_EO_PASSWORD,
        dashboard: '/eo/dashboard',
    },
};

function exactPath(path) {
    return new RegExp(
        `${path.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}/?$`
    );
}

async function login(page, role) {
    const account = accounts[role];

    if (!account?.email || !account?.password) {
        throw new Error(
            `Kredensial ${role} belum tersedia di .env.playwright`
        );
    }

    await page.goto('/login');

    const radio = page.locator(
        `input[name="role"][value="${role}"]`
    );

    await radio.locator('xpath=ancestor::label[1]').click();
    await expect(radio).toBeChecked();

    await page.locator('form input[name="email"]').fill(account.email);
    await page.locator('form input[name="password"]').fill(account.password);

    await page.locator('form button[type="submit"]').first().click();

    await expect(page).toHaveURL(exactPath(account.dashboard));
}

test.describe('PRF — Profile Management', () => {

    test('PRF-01: Crew dapat membuka halaman profil', async ({ page }) => {
        await login(page, 'volunteer');

        const response = await page.goto('/profile');

        expect(response?.status()).toBe(200);
        await expect(page).toHaveURL(exactPath('/profile'));
    });

    test('PRF-02: Crew dapat membuka form edit profil', async ({ page }) => {
        await login(page, 'volunteer');

        await page.goto('/profile/edit');

        await expect(
            page.getByRole('heading', { name: 'Edit Profil' })
        ).toBeVisible();

        await expect(page.locator('textarea[name="bio"]')).toBeVisible();
        await expect(page.locator('input[name="institution"]')).toBeVisible();
        await expect(page.locator('input[name="city"]')).toBeVisible();

        await expect(
            page.getByRole('button', { name: 'Simpan Perubahan' })
        ).toBeVisible();
    });

    test('PRF-03: EO dapat membuka halaman profil organisasi', async ({ page }) => {
        await login(page, 'eo');

        const response = await page.goto('/eo/organization');

        expect(response?.status()).toBe(200);
        await expect(page).toHaveURL(exactPath('/eo/organization'));
    });

    test('PRF-04: EO dapat membuka form edit profil organisasi', async ({ page }) => {
        await login(page, 'eo');

        await page.goto('/eo/organization/edit');

        await expect(
            page.getByRole('heading', { name: 'Edit Profil Organisasi' })
        ).toBeVisible();

        const fields = [
            '#organization_name',
            '#organization_type',
            '#description',
            '#city',
            '#pic_name',
            '#pic_phone',
            '#social_link',
            '#document',
        ];

        for (const selector of fields) {
            await expect(page.locator(selector)).toHaveCount(1);
        }

        await expect(
            page.getByRole('button', { name: 'Simpan Profil' })
        ).toBeVisible();
    });

    test('PRF-05: Crew tidak dapat mengakses profil organisasi EO', async ({ page }) => {
        await login(page, 'volunteer');

        const response = await page.goto('/eo/organization/edit');

        expect(response?.status()).toBe(403);
    });

    test('PRF-06: EO tidak dapat mengakses form edit profil Crew', async ({ page }) => {
        await login(page, 'eo');

        const response = await page.goto('/profile/edit');

        expect(response?.status()).toBe(403);
    });

    test('PRF-07: Guest tidak dapat mengakses halaman profil', async ({ page }) => {
        await page.goto('/profile/edit');

        await expect(page).toHaveURL(exactPath('/login'));

        await page.goto('/eo/organization/edit');

        await expect(page).toHaveURL(exactPath('/login'));
    });

});


test.describe('PRF — Validasi Profil Crew', () => {

    test('PRF-08: Bio Crew melebihi 1000 karakter ditolak', async ({ page }) => {
        await login(page, 'volunteer');
        await page.goto('/profile/edit');

        await page.locator('textarea[name="bio"]').fill('A'.repeat(1001));

        await page.getByRole('button', {
            name: 'Simpan Perubahan',
        }).click();

        await expect(page).toHaveURL(exactPath('/profile/edit'));

        await expect(
            page.locator('p.text-red-600')
        ).toContainText(/bio.*1000/i);
    });

    test('PRF-09: Institusi melebihi 255 karakter ditolak', async ({ page }) => {
        await login(page, 'volunteer');
        await page.goto('/profile/edit');

        await page.locator('input[name="institution"]').fill('A'.repeat(256));

        await page.getByRole('button', {
            name: 'Simpan Perubahan',
        }).click();

        await expect(page).toHaveURL(exactPath('/profile/edit'));

        await expect(
            page.locator('p.text-red-600')
        ).toContainText(/institution.*255/i);
    });

    test('PRF-10: Kota melebihi 255 karakter ditolak', async ({ page }) => {
        await login(page, 'volunteer');
        await page.goto('/profile/edit');

        await page.locator('input[name="city"]').fill('A'.repeat(256));

        await page.getByRole('button', {
            name: 'Simpan Perubahan',
        }).click();

        await expect(page).toHaveURL(exactPath('/profile/edit'));

        await expect(
            page.locator('p.text-red-600')
        ).toContainText(/city.*255/i);
    });

});

test.describe('PRF - Penyimpanan Profil Crew', () => {

    test('PRF-11: Crew berhasil menyimpan perubahan profil', async ({ page }) => {
        await login(page, 'volunteer');
        await page.goto('/profile/edit');

        const bio = page.locator('textarea[name="bio"]');
        const institution = page.locator('input[name="institution"]');
        const city = page.locator('input[name="city"]');

        // Simpan nilai awal akun QA
        const original = {
            bio: await bio.inputValue(),
            institution: await institution.inputValue(),
            city: await city.inputValue(),
        };

        const testData = {
            bio: 'QA Playwright - Pengujian perubahan profil Crew',
            institution: 'Universitas QA',
            city: 'Malang',
        };

        async function fillProfile(data) {
            await page.goto('/profile/edit');
            await bio.fill(data.bio);
            await institution.fill(data.institution);
            await city.fill(data.city);
            await page.getByRole('button', {
                name: 'Simpan Perubahan',
            }).click();
            await expect(page).toHaveURL(exactPath('/profile'));
        }

        try {
            // Ubah profil menggunakan data QA
            await fillProfile(testData);

            // Pastikan perubahan tersimpan di database
            await page.goto('/profile/edit');

            await expect(bio).toHaveValue(testData.bio);
            await expect(institution).toHaveValue(testData.institution);
            await expect(city).toHaveValue(testData.city);
        } finally {
            // Kembalikan profil seperti sebelum pengujian
            await fillProfile(original);

            // Pastikan data awal kembali
            await page.goto('/profile/edit');

            await expect(bio).toHaveValue(original.bio);
            await expect(institution).toHaveValue(original.institution);
            await expect(city).toHaveValue(original.city);
        }
    });
});


test.describe('PRF — Validasi Profil EO', () => {

    test.beforeEach(async ({ page }) => {
        await login(page, 'eo');
        await page.goto('/eo/organization/edit');
    });

    test('PRF-12: Nama organisasi wajib diisi', async ({ page }) => {
        await page.locator('#organization_name').fill('');

        await page.getByRole('button', { name: 'Simpan Profil' }).click();

        await expect(
            page.getByText('Nama organisasi wajib diisi.')
        ).toBeVisible();
    });

    test('PRF-13: Jenis organisasi wajib dipilih', async ({ page }) => {
        await page.locator('#organization_type').selectOption('');

        await page.getByRole('button', { name: 'Simpan Profil' }).click();

        await expect(
            page.getByText('Jenis organisasi wajib dipilih.')
        ).toBeVisible();
    });

    test('PRF-14: Deskripsi lebih dari 2000 karakter ditolak', async ({ page }) => {
        await page.locator('#description').fill('A'.repeat(2001));

        await page.getByRole('button', { name: 'Simpan Profil' }).click();

        await expect(
            page.getByText('Deskripsi maksimal 2000 karakter.')
        ).toBeVisible();
    });

    test('PRF-15: Kota wajib diisi', async ({ page }) => {
        await page.locator('#city').fill('');

        await page.getByRole('button', { name: 'Simpan Profil' }).click();

        await expect(
            page.getByText('Kota/domisili wajib diisi.')
        ).toBeVisible();
    });

    test('PRF-16: Nama PIC wajib diisi', async ({ page }) => {
        await page.locator('#pic_name').fill('');

        await page.getByRole('button', { name: 'Simpan Profil' }).click();

        await expect(
            page.getByText('Nama PIC wajib diisi.')
        ).toBeVisible();
    });

    test('PRF-17: Nomor telepon PIC wajib diisi', async ({ page }) => {
        await page.locator('#pic_phone').fill('');

        await page.getByRole('button', { name: 'Simpan Profil' }).click();

        await expect(
            page.getByText('Nomor telepon PIC wajib diisi.')
        ).toBeVisible();
    });

});


test.describe('PRF — Validasi URL dan Dokumen EO', () => {

    test.beforeEach(async ({ page }) => {
        await login(page, 'eo');
        await page.goto('/eo/organization/edit');
    });

    test('PRF-18: Social link tidak valid ditolak', async ({ page }) => {
        const socialLink = page.locator('#social_link');

        await socialLink.fill('bukan-url-valid');

        // HTML input type="url" menolak format yang tidak valid
        const valid = await socialLink.evaluate(
            (element) => element.checkValidity()
        );

        expect(valid).toBe(false);
    });

    test('PRF-19: Dokumen dengan format tidak valid ditolak', async ({ page }) => {
        const alpineStatus = await page.evaluate(() => ({
            alpineLoaded: typeof window.Alpine !== 'undefined',
            docUploadAvailable: typeof window.docUpload === 'function',
        }));

        console.log('Status Alpine:', alpineStatus);

        const document = page.locator('#document');

        await document.setInputFiles({
            name: 'dokumen-invalid.txt',
            mimeType: 'text/plain',
            buffer: Buffer.from('Dokumen pengujian Playwright'),
        });

        // File tidak valid seharusnya dibersihkan oleh Alpine.js
        await expect(document).toHaveValue('');
    });

    test('PRF-20: Dokumen lebih dari 5 MB ditolak', async ({ page }) => {
        const document = page.locator('#document');

        await document.setInputFiles({
            name: 'dokumen-besar.pdf',
            mimeType: 'application/pdf',
            buffer: Buffer.alloc(5 * 1024 * 1024 + 1),
        });

        // File terlalu besar seharusnya dibersihkan oleh Alpine.js
        await expect(document).toHaveValue('');
    });

});


test.describe('PRF — Penyimpanan Profil EO', () => {

    test('PRF-21: EO berhasil menyimpan perubahan profil organisasi', async ({ page }) => {
        await login(page, 'eo');
        await page.goto('/eo/organization/edit');

        const fields = [
            'organization_name',
            'organization_type',
            'description',
            'city',
            'pic_name',
            'pic_phone',
            'social_link',
        ];

        const original = {};

        for (const field of fields) {
            original[field] = await page.locator(`#${field}`).inputValue();
        }

        const testData = {
            organization_name: 'Organisasi QA Playwright',
            organization_type: 'Komunitas',
            description: 'Pengujian otomatis penyimpanan profil Event Organizer.',
            city: 'Malang',
            pic_name: 'PIC QA Playwright',
            pic_phone: '081234567890',
            social_link: 'https://example.com',
        };

        async function fillAndSave(data) {
            await page.goto('/eo/organization/edit');

            for (const field of fields) {
                const input = page.locator(`#${field}`);

                if (field === 'organization_type') {
                    await input.selectOption(data[field]);
                } else {
                    await input.fill(data[field]);
                }
            }

            await page.getByRole('button', {
                name: 'Simpan Profil',
            }).click();

            console.log('URL setelah submit:', page.url());

            const errors = await page.locator('p.text-red-600').allTextContents();

            console.log('Pesan validasi:', errors.map(e => e.trim()));

            await expect(page).toHaveURL(
                exactPath('/eo/organization')
            );
        }

        try {
            // Simpan data pengujian
            await fillAndSave(testData);

            // Buka ulang halaman edit untuk memastikan persistensi
            await page.goto('/eo/organization/edit');

            for (const field of fields) {
                await expect(
                    page.locator(`#${field}`)
                ).toHaveValue(testData[field]);
            }

        } finally {
            // Pulihkan profil asli
            await fillAndSave(original);

            // Pastikan data asli kembali
            await page.goto('/eo/organization/edit');

            for (const field of fields) {
                await expect(
                    page.locator(`#${field}`)
                ).toHaveValue(original[field]);
            }
        }
    });

});
