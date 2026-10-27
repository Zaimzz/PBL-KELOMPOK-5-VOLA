import { test, expect } from '@playwright/test';

const REGISTER_URL = '/register';
const VALID_PASSWORD = 'VolaTest123';


const QA_CREW_EMAIL = process.env.TEST_REG_CREW_EMAIL;
const QA_EO_EMAIL = process.env.TEST_REG_EO_EMAIL;

// Akun yang sudah ada untuk pengujian duplikasi email.
const EXISTING_EMAIL = process.env.TEST_VOLUNTEER_EMAIL;

async function openRegister(page) {
  await page.goto(REGISTER_URL);
  await expect(page).toHaveURL(/\/register(?:\?.*)?$/);
}

async function selectRole(page, role) {
  const radio = page.locator(`input[name="role"][value="${role}"]`);

  // Input menggunakan sr-only, jadi klik label pembungkus.
  await radio.locator('xpath=ancestor::label[1]').click();

  await expect(radio).toBeChecked();
}

async function fillRegister(page, {
  role = 'volunteer',
  name = 'QA Vola',
  email = 'qa@example.test',
  password = VALID_PASSWORD,
  confirmation = password,
} = {}) {
  await selectRole(page, role);
  await page.locator('#name').fill(name);
  await page.locator('#email').fill(email);
  await page.locator('#password').fill(password);
  await page.locator('#password_confirmation').fill(confirmation);
}

async function submitRegister(page) {
  await page.locator('form[action$="/register"] button[type="submit"]').click();
}

async function expectError(page, message) {
  await expect(page.getByText(message, { exact: true })).toBeVisible();
}

test.describe('Tampilan Registrasi', () => {
  test('halaman registrasi tampil', async ({ page }) => {
    await openRegister(page);

    await expect(page.getByText('Gabung di VOLA')).toBeVisible();
    await expect(page.locator('#name')).toBeVisible();
    await expect(page.locator('#email')).toBeVisible();
    await expect(page.locator('#password')).toBeVisible();
    await expect(page.locator('#password_confirmation')).toBeVisible();
  });

  test('pilihan role Crew dan EO', async ({ page }) => {
    await openRegister(page);

    await selectRole(page, 'eo');
    await expect(page.locator('label[for="name"]'))
      .toHaveText('Nama Organisasi');

    await selectRole(page, 'volunteer');
    await expect(page.locator('label[for="name"]'))
      .toHaveText('Nama Lengkap');
  });
});

test.describe('Registrasi Berhasil', () => {
  test('REG-03: registrasi Crew valid', async ({ page }) => {
    test.skip(!QA_CREW_EMAIL,
      'TEST_REG_CREW_EMAIL belum dikonfigurasi');

    await openRegister(page);

    await fillRegister(page, {
      role: 'volunteer',
      name: 'QA Crew Vola',
      email: QA_CREW_EMAIL,
    });

    await submitRegister(page);

    await expect(page).toHaveURL(/\/dashboard(?:\?.*)?$/);
  });

  test('REG-04: registrasi EO valid', async ({ page }) => {
    test.skip(!QA_EO_EMAIL,
      'TEST_REG_EO_EMAIL belum dikonfigurasi');

    await openRegister(page);

    await fillRegister(page, {
      role: 'eo',
      name: 'QA Organizer Vola',
      email: QA_EO_EMAIL,
    });

    await submitRegister(page);

    await expect(page).toHaveURL(/\/eo\/dashboard(?:\?.*)?$/);
  });
});

test.describe('Validasi Registrasi', () => {
  test('email sudah terdaftar', async ({ page }) => {
    test.skip(!EXISTING_EMAIL,
      'TEST_VOLUNTEER_EMAIL belum dikonfigurasi');

    await openRegister(page);

    await fillRegister(page, {
      email: EXISTING_EMAIL,
    });

    await submitRegister(page);

    await expect(page).toHaveURL(/\/register(?:\?.*)?$/);
    await expectError(page, 'Email sudah terdaftar.');
  });

  test('nama kosong', async ({ page }) => {
    await openRegister(page);

    await fillRegister(page, { name: '' });
    await submitRegister(page);

    await expectError(page, 'Nama lengkap wajib diisi.');
  });

  test('email kosong', async ({ page }) => {
    await openRegister(page);

    await fillRegister(page, { email: '' });
    await submitRegister(page);

    await expectError(page, 'Email wajib diisi.');
  });

  test('format email salah', async ({ page }) => {
    await openRegister(page);

    await fillRegister(page, { email: 'email-tidak-valid' });
    await submitRegister(page);

    await expectError(page, 'Format email tidak valid.');
  });

  test('password kosong', async ({ page }) => {
    await openRegister(page);

    await fillRegister(page, {
      password: '',
      confirmation: '',
    });

    await submitRegister(page);

    await expectError(page, 'Kata sandi wajib diisi.');
  });

  test('password terlalu pendek', async ({ page }) => {
    await openRegister(page);

    await fillRegister(page, {
      password: 'Vola12',
      confirmation: 'Vola12',
    });

    await submitRegister(page);

    await expectError(
      page,
      'Kata sandi harus minimal 8 karakter dan mengandung huruf serta angka.'
    );
  });

  test('konfirmasi password berbeda', async ({ page }) => {
    await openRegister(page);

    await fillRegister(page, {
      password: VALID_PASSWORD,
      confirmation: 'VolaBeda123',
    });

    await submitRegister(page);

    await expectError(page, 'Konfirmasi kata sandi tidak sesuai.');
  });

  test('seluruh field kosong', async ({ page }) => {
    await openRegister(page);

    await fillRegister(page, {
      name: '',
      email: '',
      password: '',
      confirmation: '',
    });

    await submitRegister(page);

    await expectError(page, 'Nama lengkap wajib diisi.');
    await expectError(page, 'Email wajib diisi.');
    await expectError(page, 'Kata sandi wajib diisi.');
    await expectError(page, 'Konfirmasi kata sandi wajib diisi.');
  });
});
