
import { test, expect } from '@playwright/test';

const accounts = {
    eo: {
        email: process.env.TEST_EO_EMAIL,
        password: process.env.TEST_EO_PASSWORD,
    },
};

async function loginAdmin(page) {
    const email = process.env.TEST_ADMIN_EMAIL;
    const password = process.env.TEST_ADMIN_PASSWORD;

    if (!email || !password) {
        throw new Error(
            'Kredensial Admin belum tersedia di .env.playwright'
        );
    }

    const response = await page.goto('/admin/login');

    expect(response?.status()).toBe(200);

    await page.locator('input[name="email"]').fill(email);
    await page.locator('input[name="password"]').fill(password);

    await page.locator('button[type="submit"]').click();

    await expect(page).toHaveURL(
        /\/admin\/dashboard\/?$/
    );

    console.log('Login Admin berhasil.');
}

test.describe('VER — Verifikasi Organisasi EO', () => {

    test('VER-01: EO dapat melihat status verifikasi organisasi', async ({ page }) => {
        await loginEO(page);

        const response = await page.goto('/eo/organization');

        expect(response?.status()).toBe(200);

        await expect(page).toHaveURL(/\/eo\/organization\/?$/);

        await expect(
            page.getByRole('heading', {
                name: 'Profil Organisasi',
                exact: true,
            })
        ).toBeVisible();

        const noProfile = page.getByText(
            'Profil Organisasi Belum Diisi',
            { exact: true }
        );

        if (await noProfile.isVisible()) {
            await expect(noProfile).toBeVisible();

            console.log(
                'VER-01: EO belum memiliki profil organisasi.'
            );

            return;
        }

        const statuses = [
            'Menunggu Verifikasi',
            'Terverifikasi Resmi',
            'Verifikasi Ditolak',
            'Akun Belum Diajukan untuk Verifikasi',
        ];

        const statusLocator = page.locator('p.font-semibold').filter({
            hasText: new RegExp(statuses.join('|')),
        });

        await expect(statusLocator.first()).toBeVisible();

        const status = await statusLocator.first().innerText();

        console.log('Status verifikasi EO:', status.trim());
    });

    test('VER-02: EO dengan profil belum lengkap tidak dapat mengajukan verifikasi', async ({ page }) => {
        await loginEO(page);

        const response = await page.goto('/eo/organization');
        expect(response?.status()).toBe(200);

        await expect(
            page.getByText('Akun Belum Diajukan untuk Verifikasi', {
                exact: true,
            })
        ).toBeVisible();

        await expect(
            page.getByText('Dokumen legal belum diunggah', {
                exact: true,
            }).first()
        ).toBeVisible();

        const verifyForm = page.locator(
            'form[action$="/eo/organization/verify"]'
        );

        await expect(verifyForm).toBeVisible();

        const verifyButton = verifyForm.locator('button[type="submit"]');
        await expect(verifyButton).toBeDisabled();

        const csrfToken = await verifyForm
            .locator('input[name="_token"]')
            .inputValue();

        expect(csrfToken).toBeTruthy();

        const result = await page.evaluate(async (token) => {
            const response = await fetch('/eo/organization/verify', {
                method: 'POST',
                credentials: 'same-origin',
                redirect: 'follow',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'text/html',
                },
            });

            return {
                status: response.status,
                redirected: response.redirected,
                finalUrl: response.url,
                html: await response.text(),
            };
        }, csrfToken);

        expect(result.status).toBe(200);
        expect(result.redirected).toBe(true);
        expect(result.finalUrl).toMatch(/\/eo\/organization\/?$/);

        expect(result.html).toContain(
            'Data berikut harus dilengkapi sebelum mengajukan verifikasi:'
        );

        await page.goto('/eo/organization');

        await expect(
            page.getByText('Akun Belum Diajukan untuk Verifikasi', {
                exact: true,
            })
        ).toBeVisible();

        await expect(
            page.getByText('Menunggu Verifikasi', {
                exact: true,
            })
        ).toHaveCount(0);

        await expect(
            page.locator(
                'form[action$="/eo/organization/verify"] button[type="submit"]'
            )
        ).toBeDisabled();
    });

    test('VER-03: EO dengan profil lengkap berhasil mengajukan verifikasi', async ({ page }) => {
        await loginEO(page);

        const response = await page.goto('/eo/organization');
        expect(response?.status()).toBe(200);

        await expect(
            page.getByText('Akun Belum Diajukan untuk Verifikasi', {
                exact: true,
            })
        ).toBeVisible();

        await expect(
            page.getByText('Dokumen sudah diunggah', {
                exact: true,
            })
        ).toBeVisible();

        const verifyForm = page.locator(
            'form[action$="/eo/organization/verify"]'
        );

        await expect(verifyForm).toBeVisible();

        const submitButton = verifyForm.locator(
            'button[type="submit"]'
        );

        await expect(submitButton).toBeEnabled();

        await Promise.all([
            page.waitForURL(/\/eo\/organization\/?$/),
            submitButton.click(),
        ]);

        await expect(
            page.getByText('Menunggu Verifikasi', {
                exact: true,
            })
        ).toBeVisible();

        await expect(
            page.getByText('● Pending', {
                exact: true,
            })
        ).toBeVisible();

        await expect(
            page.locator(
                'form[action$="/eo/organization/verify"]'
            )
        ).toHaveCount(0);

        await page.reload();

        await expect(
            page.getByText('Menunggu Verifikasi', {
                exact: true,
            })
        ).toBeVisible();

        console.log(
            'VER-03 PASS: Status verifikasi EO berhasil berubah menjadi Pending.'
        );
    });

    test('VER-04: EO berstatus Pending tidak dapat mengajukan verifikasi ulang', async ({ page }) => {
        await loginEO(page);

        const response = await page.goto('/eo/organization');
        expect(response?.status()).toBe(200);

        await expect(
            page.getByText('Menunggu Verifikasi', {
                exact: true,
            })
        ).toBeVisible();

        await expect(
            page.locator('form[action$="/eo/organization/verify"]')
        ).toHaveCount(0);

        const csrfToken = await page.locator(
            'meta[name="csrf-token"]'
        ).getAttribute('content');

        expect(csrfToken).toBeTruthy();

        const result = await page.evaluate(async (token) => {
            const response = await fetch('/eo/organization/verify', {
                method: 'POST',
                credentials: 'same-origin',
                redirect: 'follow',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'text/html',
                },
            });

            return {
                status: response.status,
                redirected: response.redirected,
                finalUrl: response.url,
                html: await response.text(),
            };
        }, csrfToken);

        expect(result.status).toBe(200);
        expect(result.redirected).toBe(true);
        expect(result.finalUrl).toMatch(/\/eo\/organization\/?$/);

        expect(result.html).toContain(
            'Profil Anda sudah dalam antrian verifikasi.'
        );

        await page.goto('/eo/organization');

        await expect(
            page.getByText('Menunggu Verifikasi', {
                exact: true,
            })
        ).toBeVisible();

        await expect(
            page.locator('form[action$="/eo/organization/verify"]')
        ).toHaveCount(0);

        console.log('VER-04 PASS: Pengajuan ulang EO Pending ditolak.');
    });

    test('VER-05: Admin dapat melihat daftar pengajuan verifikasi EO', async ({ page }) => {
        await loginAdmin(page);

        const response = await page.goto('/admin/eo-verifications');

        expect(response?.status()).toBe(200);

        await expect(page).toHaveURL(
            /\/admin\/eo-verifications\/?$/
        );

        await expect(
            page.getByRole('heading', {
                name: 'Verifikasi EO',
                exact: true,
            })
        ).toBeVisible();

        const table = page.locator('table');

        await expect(table).toBeVisible();

        await expect(
            table.getByRole('columnheader', {
                name: 'Nama Organisasi',
            })
        ).toBeVisible();

        await expect(
            table.getByRole('columnheader', {
                name: 'Status',
            })
        ).toBeVisible();

        const searchInput = page.locator('input[name="search"]');

        await expect(searchInput).toBeVisible();

        await searchInput.fill('FEST.');

        await searchInput.press('Enter');

        await expect(page).toHaveURL(/search=FEST/);

        const festRow = table.locator('tbody tr').filter({
            has: page.locator('td').first().getByText('FEST.', {
                exact: true,
            }),
        });

        await expect(festRow).toHaveCount(1);

        await expect(
            festRow.locator('td').nth(4)
        ).toContainText('Menunggu');

        await expect(
            festRow.getByRole('button', {
                name: 'Setujui',
            })
        ).toBeVisible();

        await expect(
            festRow.getByRole('button', {
                name: 'Tolak',
                exact: true,
            })
        ).toBeVisible();

        console.log(
            'VER-05 PASS: Admin berhasil melihat pengajuan FEST. dengan status Pending.'
        );
    });

    test('VER-06: Admin berhasil menyetujui pengajuan verifikasi EO', async ({ page }) => {
        await loginAdmin(page);

        await page.goto('/admin/eo-verifications?search=FEST.&status=pending');

        await expect(
            page.getByRole('heading', {
                name: 'Verifikasi EO',
                exact: true,
            })
        ).toBeVisible();

        const festRow = page.locator('table tbody tr').filter({
            has: page.locator('td').first().getByText('FEST.', {
                exact: true,
            }),
        });

        await expect(festRow).toHaveCount(1);

        await expect(
            festRow.locator('td').nth(4)
        ).toContainText('Menunggu');

        const approveButton = festRow.getByRole('button', {
            name: 'Setujui',
            exact: true,
        });

        await expect(approveButton).toBeEnabled();

        const approveResponsePromise = page.waitForResponse(
            response =>
                /\/admin\/eo-verifications\/\d+\/approve$/.test(
                    response.url()
                ) &&
                response.request().method() === 'POST'
        );

        await approveButton.click();

        const approveResponse = await approveResponsePromise;

        expect(approveResponse.status()).toBe(302);

        await expect(festRow).toHaveCount(0);

        await page.goto(
            '/admin/eo-verifications?search=FEST.&status=verified'
        );

        const verifiedRow = page.locator('table tbody tr').filter({
            has: page.locator('td').first().getByText('FEST.', {
                exact: true,
            }),
        });

        await expect(verifiedRow).toHaveCount(1);

        await expect(
            verifiedRow.locator('td').nth(4)
        ).toContainText('Terverifikasi');

        await expect(
            verifiedRow.getByRole('button', {
                name: 'Setujui',
                exact: true,
            })
        ).toHaveCount(0);

        await expect(
            verifiedRow.getByRole('button', {
                name: 'Tolak',
                exact: true,
            })
        ).toHaveCount(0);

        console.log(
            'VER-06 PASS: Admin berhasil memverifikasi organisasi FEST.'
        );
    });
 
    test('VER-07: Admin menolak pengajuan EO dengan alasan penolakan', async ({ page }) => {
        const organizationName = process.env.TEST_EO_REJECT_ORGANIZATION;
        const rejectionReason = 'Dokumen legalitas organisasi belum memenuhi persyaratan verifikasi QA.';

        if (!organizationName) {
            throw new Error(
                'TEST_EO_REJECT_ORGANIZATION belum diatur di .env.playwright'
            );
        }

        await loginAdmin(page);

        const params = new URLSearchParams({
            search: organizationName,
            status: 'pending',
        });

        const response = await page.goto(
            `/admin/eo-verifications?${params.toString()}`
        );

        expect(response?.status()).toBe(200);

        const organizationRow = page.locator('table tbody tr').filter({
            has: page.locator('td').first().getByText(
                organizationName,
                { exact: true }
            ),
        });

        await expect(organizationRow).toHaveCount(1);

        await expect(
            organizationRow.locator('td').nth(4)
        ).toContainText('Menunggu');

        await organizationRow.getByRole('button', {
            name: 'Tolak',
            exact: true,
        }).click();

        const rejectForm = page.locator(
            'form[action$="/reject"]'
        ).filter({
            has: page.getByRole('heading', {
                name: `Tolak Organisasi ${organizationName}?`,
                exact: true,
            }),
        });

        await expect(rejectForm).toBeVisible();

        await rejectForm.locator(
            'textarea[name="rejection_reason"]'
        ).fill(rejectionReason);

        const rejectResponsePromise = page.waitForResponse(
            response =>
                /\/admin\/eo-verifications\/\d+\/reject$/.test(
                    response.url()
                ) &&
                response.request().method() === 'POST'
        );

        await rejectForm.getByRole('button', {
            name: 'Kirim Penolakan',
        }).click();

        const rejectResponse = await rejectResponsePromise;

        expect(rejectResponse.status()).toBe(302);

        await expect(organizationRow).toHaveCount(0);

        const rejectedParams = new URLSearchParams({
            search: organizationName,
            status: 'rejected',
        });

        await page.goto(
            `/admin/eo-verifications?${rejectedParams.toString()}`
        );

        const rejectedRow = page.locator('table tbody tr').filter({
            has: page.locator('td').first().getByText(
                organizationName,
                { exact: true }
            ),
        });

        await expect(rejectedRow).toHaveCount(1);

        await expect(
            rejectedRow.locator('td').nth(4)
        ).toContainText('Ditolak');

        await rejectedRow.getByRole('button', {
            name: 'Detail',
        }).click();

        await expect(
            page.getByText(rejectionReason, {
                exact: false,
            })
        ).toBeVisible();

        console.log(
            'VER-07 PASS: Admin berhasil menolak organisasi dan alasan penolakan tersimpan.'
        );
    });

    test('VER-08: EO dapat melihat keputusan penolakan Admin', async ({ page }) => {
        const email = process.env.TEST_EO_REJECT_EMAIL;
        const password = process.env.TEST_EO_REJECT_PASSWORD;

        const rejectionReason =
            'Dokumen legalitas organisasi belum memenuhi persyaratan verifikasi QA.';

        if (!email || !password) {
            throw new Error(
                'Kredensial EO kedua belum tersedia di .env.playwright'
            );
        }

        await page.goto('/login');

        const radio = page.locator(
            'input[name="role"][value="eo"]'
        );

        await radio.locator('xpath=ancestor::label[1]').click();
        await expect(radio).toBeChecked();

        await page.locator('form input[name="email"]').fill(email);
        await page.locator('form input[name="password"]').fill(password);

        await page.locator('form button[type="submit"]').first().click();

        await expect(page).toHaveURL(/\/eo\/dashboard\/?$/);

        const response = await page.goto('/eo/organization');

        expect(response?.status()).toBe(200);

        await expect(
            page.getByText('Verifikasi Ditolak', {
                exact: true,
            })
        ).toBeVisible();

        await expect(
            page.getByText(rejectionReason, {
                exact: false,
            })
        ).toBeVisible();

        const resubmitButton = page.getByRole('button', {
            name: 'Ajukan Kembali',
            exact: true,
        });

        await expect(resubmitButton).toBeVisible();
        await expect(resubmitButton).toBeEnabled();

        await page.reload();

        await expect(
            page.getByText('Verifikasi Ditolak', {
                exact: true,
            })
        ).toBeVisible();

        await expect(
            page.getByText(rejectionReason, {
                exact: false,
            })
        ).toBeVisible();

        console.log(
            'VER-08 PASS: EO berhasil melihat status Rejected dan alasan penolakan.'
        );

    });

});
