import { expect, test } from '@playwright/test';

test('participant can submit receipt and see status', async ({ page }) => {
  await page.goto('/login');

  await page.getByLabel('Email').fill('participant@fortis.test');
  await page.getByLabel('Password').fill('Password123!');
  await page.getByRole('button', { name: 'Log in' }).click();

  await expect(page).toHaveURL(/participant\/dashboard/);

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

test('admin can access fraud and winner exports views', async ({ page }) => {
  await page.goto('/login');

  await page.getByLabel('Email').fill('admin@fortis.test');
  await page.getByLabel('Password').fill('Password123!');
  await page.getByRole('button', { name: 'Log in' }).click();

  await expect(page).toHaveURL(/admin\/dashboard/);

  await page.getByRole('link', { name: 'Fraud' }).click();
  await expect(page).toHaveURL(/admin\/fraud-reviews/);
  await expect(page.getByRole('heading', { name: 'Fraud Review' })).toBeVisible();

  await page.getByRole('link', { name: 'Eksporty' }).click();
  await expect(page).toHaveURL(/admin\/winner-exports/);
  await expect(page.getByRole('heading', { name: 'Eksporty zwycięzców' })).toBeVisible();
});
