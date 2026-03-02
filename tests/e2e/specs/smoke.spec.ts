import { expect, test } from '@playwright/test';

test('home page and login page are reachable', async ({ page }) => {
  await page.goto('/');
  await expect(page).toHaveTitle(/Fortis|Laravel/i);

  await page.goto('/login');
  await expect(page.getByRole('button', { name: /log in|zaloguj/i })).toBeVisible();
});
