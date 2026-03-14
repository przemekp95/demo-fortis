import { expect, Page } from '@playwright/test';
import { satisfyHoneypot } from './honeypot';

export async function login(page: Page, email: string, password: string): Promise<void> {
  await page.goto('/login');
  await page.getByLabel('Email').fill(email);
  await page.getByLabel('Password').fill(password);
  await satisfyHoneypot(page);
  await page.getByRole('button', { name: 'Log in' }).click();
}

export async function loginAsParticipant(page: Page): Promise<void> {
  await login(page, 'participant@fortis.test', 'Password123!');
  await expect(page).toHaveURL(/participant\/dashboard/);
}

export async function loginAsAdmin(page: Page): Promise<void> {
  await login(page, 'admin@fortis.test', 'Password123!');
  await expect(page).toHaveURL(/admin\/dashboard/);
}
