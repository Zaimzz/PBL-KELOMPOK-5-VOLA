import { test, expect } from '@playwright/test';

const accounts = {
  volunteer: {
    email: process.env.TEST_VOLUNTEER_EMAIL,
    password: process.env.TEST_VOLUNTEER_PASSWORD,
    dashboard: '/dashboard',
    login: '/login',
  },
  eo: {
    email: process.env.TEST_EO_EMAIL,
    password: process.env.TEST_EO_PASSWORD,
    dashboard: '/eo/dashboard',
    login: '/login',
  },
  admin: {
    email: process.env.TEST_ADMIN_EMAIL,
    password: process.env.TEST_ADMIN_PASSWORD,
    dashboard: '/admin/dashboard',
    login: '/admin/login',
  },
};

const emailField = (page) => page.locator('form input[name="email"]');
const passwordField = (page) => page.locator('form input[name="password"]');
const submitButton = (page) =>
  page.locator('form button[type="submit"]').first();

function accountFor(role) {
  const account = accounts[role];

  if (!account?.email || !account?.password) {
    throw new Error(
      `Kredensial testing ${role} belum diatur di .env.playwright`
    );
  }

  return account;
}

function exactPath(path) {
  return new RegExp(`${path.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}/?$`);
}

async function selectRole(page, role) {
  const radio = page.locator(
    `input[name="role"][value="${role}"]`
  );

  await radio.locator('xpath=ancestor::label[1]').click();

  await expect(radio).toBeChecked();
}

async function fillLogin(page, role, credentials) {
  const account = accounts[role];

  await page.goto(account.login);

  if (role !== 'admin') {
    await selectRole(page, role);
  }

  await emailField(page).fill(credentials.email);
  await passwordField(page).fill(credentials.password);
}

async function login(page, role, credentials = accountFor(role)) {
  await fillLogin(page, role, credentials);
  await submitButton(page).click();
}

async function expectDashboard(page, role) {
  await expect(page).toHaveURL(exactPath(accounts[role].dashboard));
}

async function expectError(page, message) {
  await expect(page.getByText(message, { exact: true })).toBeVisible();
}

async function logout(page, role) {
  let profileButton;

  if (role === 'volunteer') {
    profileButton = page.getByRole('button', {
      name: /samuel.*Indonesia/i,
    });
  } else if (role === 'eo') {
    profileButton = page.getByRole('button', {
      name: /PESTAPASTI/i,
    });
  } else if (role === 'admin') {
    profileButton = page.getByRole('button', {
      name: /Admin Vola/i,
    });
  } else {
    throw new Error(`Role tidak dikenal: ${role}`);
  }

  await profileButton.click();

  const logoutForm = page.locator('form[action$="/logout"]:visible');

  await expect(logoutForm).toBeVisible();

  await logoutForm.getByRole('button', { name: 'Keluar' }).click();

  await expect(page).toHaveURL(exactPath('/'));
}

test.describe('Tampilan Login', () => {
  test('LGN-01: halaman login Crew/EO tampil', async ({ page }) => {
    await page.goto('/login');

    await expect(
      page.getByText('Selamat Datang Kembali')
    ).toBeVisible();

    await expect(emailField(page)).toBeVisible();
    await expect(passwordField(page)).toBeVisible();
    await expect(submitButton(page)).toBeVisible();

    await expect(
      page.locator('input[name="role"][value="volunteer"]')
    ).toBeChecked();

    await expect(
      page.locator('input[name="role"][value="eo"]')
    ).toHaveCount(1);
  });

  test('LGN-02: halaman login Admin tampil', async ({ page }) => {
    await page.goto('/admin/login');

    await expect(
      page.getByText('Portal Administrator')
    ).toBeVisible();

    await expect(emailField(page)).toBeVisible();
    await expect(passwordField(page)).toBeVisible();
  });

  test('LGN-03: pilihan role dapat diubah', async ({ page }) => {
    await page.goto('/login');

    const crew = page.locator(
      'input[name="role"][value="volunteer"]'
    );

    const eo = page.locator(
      'input[name="role"][value="eo"]'
    );

    // Default adalah Crew
    await expect(crew).toBeChecked();

    // Pilih EO
    await selectRole(page, 'eo');
    await expect(eo).toBeChecked();
    await expect(crew).not.toBeChecked();

    // Kembali pilih Crew
    await selectRole(page, 'volunteer');
    await expect(crew).toBeChecked();
    await expect(eo).not.toBeChecked();
    });
  });

  test.describe('Login Berhasil', () => {
    for (const role of ['volunteer', 'eo', 'admin']) {
      test(`${role} berhasil login ke dashboard`, async ({ page }) => {
        await login(page, role);
        await expectDashboard(page, role);
      });
  }
});

test.describe('Kredensial Salah', () => {
  test('LGN-06: email belum terdaftar', async ({ page }) => {
    await login(page, 'volunteer', {
      email: 'email-tidak-terdaftar-01@vola.test',
      password: 'PasswordSalah123!',
    });

    await expect(page).toHaveURL(exactPath('/login'));
    await expectError(page, 'Email belum terdaftar.');
  });

  test('LGN-07: password salah', async ({ page }) => {
    const account = accountFor('volunteer');

    await login(page, 'volunteer', {
      email: account.email,
      password: 'PasswordSalah123!',
    });

    await expect(page).toHaveURL(exactPath('/login'));
    await expectError(page, 'Kata sandi salah.');
  });

  test('LGN-08: email Admin tidak terdaftar', async ({ page }) => {
    await login(page, 'admin', {
      email: 'admin-tidak-terdaftar-01@vola.test',
      password: 'PasswordSalah123!',
    });

    await expect(page).toHaveURL(exactPath('/admin/login'));
    await expectError(page, 'Kredensial tidak valid.');
  });

  test('LGN-09: password Admin salah', async ({ page }) => {
    const account = accountFor('admin');

    await login(page, 'admin', {
      email: account.email,
      password: 'PasswordSalah123!',
    });

    await expect(page).toHaveURL(exactPath('/admin/login'));
    await expectError(page, 'Kata sandi salah.');
  });
});

test.describe('Validasi Form Login', () => {
  for (const loginPath of ['/login', '/admin/login']) {
    test(`email kosong pada ${loginPath}`, async ({ page }) => {
      await page.goto(loginPath);
      await passwordField(page).fill('Password123!');
      await submitButton(page).click();

      await expectError(page, 'Email wajib diisi.');
      await expect(page).toHaveURL(exactPath(loginPath));
    });

    test(`password kosong pada ${loginPath}`, async ({ page }) => {
      await page.goto(loginPath);
      await emailField(page).fill('contoh@vola.test');
      await submitButton(page).click();

      await expectError(page, 'Kata sandi wajib diisi.');
      await expect(page).toHaveURL(exactPath(loginPath));
    });

    test(`semua field kosong pada ${loginPath}`, async ({ page }) => {
      await page.goto(loginPath);
      await submitButton(page).click();

      await expectError(page, 'Email wajib diisi.');
      await expectError(page, 'Kata sandi wajib diisi.');
    });

    test(`format email salah pada ${loginPath}`, async ({ page }) => {
      await page.goto(loginPath);
      await emailField(page).fill('email-tidak-valid');
      await passwordField(page).fill('Password123!');
      await submitButton(page).click();

      await expectError(page, 'Format email tidak valid.');
    });
  }
});

test.describe('Validasi Role', () => {
  test('Crew memilih role EO', async ({ page }) => {
    await login(page, 'eo', accountFor('volunteer'));

    await expect(page).toHaveURL(exactPath('/login'));
    await expectError(
      page,
      'Role yang dipilih tidak sesuai dengan akun.'
    );
  });

  test('EO memilih role Crew', async ({ page }) => {
    await login(page, 'volunteer', accountFor('eo'));

    await expect(page).toHaveURL(exactPath('/login'));
    await expectError(
      page,
      'Role yang dipilih tidak sesuai dengan akun.'
    );
  });

  test('Admin ditolak pada login umum', async ({ page }) => {
    await login(page, 'volunteer', accountFor('admin'));

    await expect(page).toHaveURL(exactPath('/login'));

    await expectError(
      page,
      'Email atau kata sandi tidak valid.'
    );

    await expect(page.getByText(
      'Admin tidak perlu memilih role.',
      { exact: false}
    )).toHaveCount(0);
  });

  for (const role of ['volunteer', 'eo']) {
    test(`${role} ditolak pada login Admin`, async ({ page }) => {
      await login(page, 'admin', accountFor(role));

      await expect(page).toHaveURL(exactPath('/admin/login'));
      await expectError(
        page,
        'Akses ditolak. Hanya admin yang dapat masuk.'
      );
    });
  }
});

test.describe('Akses Dashboard Tanpa Login', () => {
  for (const role of ['volunteer', 'eo', 'admin']) {
    test(`guest tidak bisa mengakses dashboard ${role}`, async ({ page }) => {
      await page.goto(accounts[role].dashboard);

      await expect(page).toHaveURL(exactPath('/login'));
    });
  }
});

test.describe('Authorization Antar Role', () => {
  const forbiddenRoutes = {
    volunteer: ['/eo/dashboard', '/admin/dashboard'],
    eo: ['/dashboard', '/admin/dashboard'],
    admin: ['/dashboard', '/eo/dashboard'],
  };

  for (const role of ['volunteer', 'eo', 'admin']) {
    for (const route of forbiddenRoutes[role]) {
      test(`${role} tidak bisa membuka ${route}`, async ({ page }) => {
        await login(page, role);
        await expectDashboard(page, role);

        const response = await page.goto(route);

        expect(response.status()).toBe(403);
      });
    }
  }
});

test.describe('Logout dan Session', () => {
  for (const role of ['volunteer', 'eo', 'admin']) {
    test(`${role} berhasil logout`, async ({ page }) => {
      await login(page, role);
      await expectDashboard(page, role);

      await logout(page, role);

      await page.goto(accounts[role].dashboard);
      await expect(page).toHaveURL(exactPath('/login'));
    });
  }
});

test.describe('Komponen Login', () => {
  for (const loginPath of ['/login', '/admin/login']) {
    test(`toggle password pada ${loginPath}`, async ({ page }) => {
      await page.goto(loginPath);

      const password = passwordField(page);
      const toggle = page.getByRole('button', {
        name: 'Toggle password visibility',
      });

      await expect(password).toHaveAttribute('type', 'password');

      await toggle.click();
      await expect(password).toHaveAttribute('type', 'text');

      await toggle.click();
      await expect(password).toHaveAttribute('type', 'password');
    });

    test(`Remember Me tersedia pada ${loginPath}`, async ({ page }) => {
      await page.goto(loginPath);

      const remember = page.locator('input[name="remember"]');

      await expect(remember).toBeVisible();
      await remember.check();
      await expect(remember).toBeChecked();

      await remember.uncheck();
      await expect(remember).not.toBeChecked();
    });
  }
});
