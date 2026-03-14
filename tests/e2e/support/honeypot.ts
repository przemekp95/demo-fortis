import { Page } from '@playwright/test';

export async function satisfyHoneypot(page: Page): Promise<void> {
  const startedAtField = page.locator('input[type="hidden"]').first();

  await startedAtField.evaluate((element) => {
    const input = element as HTMLInputElement;
    input.value = String(Date.now() - 1200);
    input.dispatchEvent(new Event('input', { bubbles: true }));
    input.dispatchEvent(new Event('change', { bubbles: true }));
  });
}
