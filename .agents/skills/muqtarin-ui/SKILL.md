---
name: muqtarin-ui
description: UI/UX workflow for MUQTARIN Store (مقترن ستور) across the Blade storefront, the admin panel, and the Flutter apps. Use for any layout, component, motion, RTL, dark-mode, or visual-polish task.
---

# MUQTARIN UI/UX

Run this for any visual change. Rules about branches, PRs, security, and migrations live in `AGENTS.md`; colors, fonts, spacing, and radii live in `DESIGN.md`. This file only covers how to do UI work here.

## 1. Read first

1. `DESIGN.md` and `.agents/rules/design-system.md`. Never invent a hex color. Never create `design-tokens.json`.
2. `git status`, then the files for the surface you are touching:

| Surface | Files |
|---|---|
| Admin | `backend/public/assets/admin/custom/muqtarin-admin.css`, `backend/resources/views/admin/` |
| Storefront | `backend/resources/views/livewire/elegant/`, `backend/resources/views/components/` |
| Storefront CSS | `backend/public/frontend/elegant/css/muqtarin-storefront.css` (uses `--mq-*` tokens, scoped under `.mq-*`) |
| Brand assets | `backend/public/assets/brand/` (`logo.svg`, `logo-dark.svg`, `icon.svg`) |
| Existing JS to reuse | `backend/public/assets/brand/muqtarin-toast.js`, `backend/public/assets/brand/muqtarin-cursor.js` |
| Mobile | `mobile-apps/customer`, `seller`, `deliveryboy` |

Extend what exists before adding a new file, class, or script.

## 2. Skills

Check `.agents/skills/` first (project-local backup copies live there). Do not stop to install a missing skill; if one below is unavailable, follow this file instead.

- **Always:** `frontend-design`, `impeccable`, `verification-before-completion`.
- **Motion or micro-interactions:** `design-motion-principles`.
- **Flutter work:** `flutter-expert`, `dart-flutter-patterns`.
- **Visible Arabic or English copy:** `unslop`.
- **Something is broken, not just ugly:** `diagnosing-bugs`.
- **Browser checks:** `webapp-testing`.

## 3. References

Pick the one to three that fit the component and name them in your plan. Take the idea only. Do not paste React code and do not load an external CDN.

| Need | Look at |
|---|---|
| Tables, cards, forms, dialogs | ui.shadcn.com |
| Inputs, buttons, alerts, toasts | originui.com |
| Page sections and compositions | 21st.dev |
| Shimmer, skeleton, bento, subtle motion | magicui.design |
| Heavier interactive cards (sparingly) | ui.aceternity.com |
| CSS-only loaders and micro-interactions | uiverse.io |
| Icons | lucide.dev (`muqtarin-lucide`) |
| Brand philosophy | app.vendoratravel.com (if it needs a login, rely on `DESIGN.md`) |

Rebuild each idea in Blade, CSS variables (`--pine` / `--mq-pine`, `--emerald` / `--mq-emerald`, `--gold` / `--mq-gold`), and vanilla JS, or in Flutter theme code for `mobile-apps/`.

## 4. Non-negotiables

- **RTL first.** Use logical properties (`margin-inline-start`, `padding-inline-end`, `inset-inline-start`). In Flutter use `EdgeInsetsDirectional`. Do not write separate left and right CSS.
- **Type.** Tajawal for Arabic, Inter for English. Zero letter-spacing on Arabic, with `font-feature-settings: "liga" 1, "calt" 1`.
- **Size.** Web buttons are 44 or 48px tall with an 8px radius. Mobile tap targets are at least 48px. Spacing comes from the 8pt scale.
- **Contrast.** White text on Pine. Ink or `#334155` on light surfaces. Never small white text on Emerald. Use `#16A34A` for emerald text on white.
- **Dark mode.** Every new component needs a dark variant.
- **Accessibility.** Visible `:focus-visible` state, and wrap motion in `prefers-reduced-motion`.
- **Images.** For any temporary, placeholder, or preview image (banners, products, categories, mockups), use the built-in `generate_image` tool (Nano Banana) aligned with MUQTARIN aesthetics instead of external stock URLs or repeating duplicate images. Dynamic media views must keep a fallback (`/assets/img/default_full_logo.png` or `/assets/brand/icon.svg`).
- **Copy.** User-facing strings go through the language files.
- **Naming.** The wordmark is always `MUQTARIN Store` / `مقترن ستور`.

## 5. Workflow

1. State the goal and the smallest change that meets it.
2. Name the 1 to 3 references you picked.
3. Edit files directly with the native editing tools.
4. Verify (section 6).
5. Open a Draft PR using the template in `AGENTS.md`, with the screenshots attached.

Test scripts (Playwright and similar) are allowed because they do not modify project files. Keep them outside the repo, for example in `/tmp`, and never commit them.

## 6. Verification

After a Blade change run `php8.2 artisan view:clear`, then confirm the page returns `HTTP 200`.

Capture screenshots for every combination that applies:

| Axis | Values |
|---|---|
| Direction | RTL (Arabic) and LTR (English) |
| Theme | Light and Dark |
| Viewport | Desktop and Mobile |

Also check:
- The browser console and network tab show no errors.
- The page works with reduced motion enabled.
- Flutter changes pass `flutter analyze`.

Tools: in Antigravity use `preview_open`, `preview_snapshot`, `preview_resize`, `preview_set_appearance`, `generate_image` (Nano Banana) for visual placeholders, and `device_screenshot` for Flutter. Elsewhere, use Playwright.

Use synthetic data only in screenshots. Never real customer names, phone numbers, or payment data.

## 7. Done means

- The screenshot matrix from section 6 is attached to the PR.
- The console is clean.
- The PR lists every file changed.
- Nothing was changed outside the stated task.
