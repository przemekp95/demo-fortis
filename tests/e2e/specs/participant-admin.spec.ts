import { expect, test } from '@playwright/test';
import { loginAsAdmin, loginAsParticipant } from '../support/auth';
import { satisfyHoneypot } from '../support/honeypot';

test('participant can register and is redirected to email verification notice', async ({ page }) => {
  const suffix = Date.now();

  await page.goto('/register');
  await page.getByLabel('Imię').fill('E2E');
  await page.getByLabel('Nazwisko').fill('Participant');
  await page.getByLabel('Telefon').fill(`+4855500${suffix.toString().slice(-5)}`);
  await page.getByLabel('Data urodzenia').fill('1994-04-14');
  await page.getByLabel('Adres', { exact: true }).fill('Testowa 15');
  await page.getByLabel('Adres (linia 2)').fill('m. 3');
  await page.getByLabel('Miasto').fill('Warszawa');
  await page.getByLabel('Kod pocztowy').fill('00-123');
  await page.getByLabel('Kraj (ISO2)').fill('PL');
  await page.getByLabel('E-mail').fill(`fresh-${suffix}@fortis.test`);
  await page.getByLabel('Hasło', { exact: true }).fill('Password123!');
  await page.getByLabel('Potwierdź hasło').fill('Password123!');
  await page.locator('input[type="checkbox"]').nth(0).check();
  await page.locator('input[type="checkbox"]').nth(1).check();
  await satisfyHoneypot(page);
  await page.getByRole('button', { name: 'Zarejestruj' }).click();

  await expect(page).toHaveURL(/verify-email/);
  await expect(page.getByText('Before getting started')).toBeVisible();
});

test('participant can log in and submit a receipt', async ({ page }) => {
  await loginAsParticipant(page);

  await page.getByRole('link', { name: 'Paragony' }).click();
  await expect(page).toHaveURL(/participant\/receipts/);

  const receiptNumber = `E2E-${Date.now()}`;
  const today = new Date().toISOString().slice(0, 10);

  await page.getByLabel('Nr paragonu').fill(receiptNumber);
  await page.getByLabel('Kwota').fill('99.90');
  await page.getByLabel('Data zakupu').fill(today);
  await page.getByRole('button', { name: 'Wyślij zgłoszenie' }).click();

  await expect(page.getByText('Zgłoszenie zapisane.')).toBeVisible();
  await expect(page.getByRole('cell', { name: receiptNumber })).toBeVisible();
});

test('participant sees duplicate receipt validation for an existing receipt', async ({ page }) => {
  await loginAsParticipant(page);

  await page.getByRole('link', { name: 'Paragony' }).click();
  await expect(page).toHaveURL(/participant\/receipts/);

  const today = new Date().toISOString().slice(0, 10);

  await page.getByLabel('Nr paragonu').fill('DEMO-APPROVED-001');
  await page.getByLabel('Kwota').fill('29.90');
  await page.getByLabel('Data zakupu').fill(today);
  await page.getByRole('button', { name: 'Wyślij zgłoszenie' }).click();

  await expect(page.getByText('Paragon został już zgłoszony w tej kampanii.')).toBeVisible();
});

test('participant can file a DSR request and admin can review it', async ({ page, browser }) => {
  await loginAsParticipant(page);

  await page.getByRole('button', { name: 'Zamów eksport danych' }).click();
  await expect(page.getByText('Wniosek RODO został przyjęty.')).toBeVisible();
  await expect(page.getByRole('cell', { name: 'export' }).first()).toBeVisible();

  const adminPage = await browser.newPage();
  await loginAsAdmin(adminPage);
  await adminPage.getByRole('link', { name: 'RODO' }).click();

  await expect(adminPage).toHaveURL(/admin\/dsr-requests/);
  await expect(adminPage.getByRole('heading', { name: 'Wnioski RODO' })).toBeVisible();
  await expect(adminPage.getByText('participant@fortis.test')).toBeVisible();
  await expect(adminPage.getByRole('cell', { name: 'export' }).first()).toBeVisible();
});

test('admin can review fraud entries and generate winner exports', async ({ page }) => {
  await loginAsAdmin(page);

  await page.getByRole('link', { name: 'Fraud' }).click();
  await expect(page).toHaveURL(/admin\/fraud-reviews/);
  await expect(page.getByRole('heading', { name: 'Fraud Review' })).toBeVisible();
  await page.getByRole('button', { name: 'Approve' }).first().click();
  await expect(page.getByText('Ocena fraud zaktualizowana.')).toBeVisible();

  await page.getByRole('link', { name: 'Eksporty' }).click();
  await expect(page).toHaveURL(/admin\/winner-exports/);
  await expect(page.getByRole('heading', { name: 'Eksporty zwycięzców' })).toBeVisible();
  await page.getByLabel('Kampania do eksportu').selectOption({ index: 1 });
  await page.getByRole('button', { name: 'Generuj eksport' }).click();
  await expect(page.getByText('Eksport wygenerowany:')).toBeVisible();

  const downloadPromise = page.waitForEvent('download');
  await page.getByRole('link', { name: 'Pobierz' }).first().click();
  const download = await downloadPromise;

  await expect(download.suggestedFilename()).toContain('.csv');
});
