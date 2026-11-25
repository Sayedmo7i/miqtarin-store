# AI Agent Rule: MUQTARIN Store Design System Enforcement


> This rule is automatically applied to all AI agents working on code in `mo7i-store`.

## Invariant Design Rules

1. **Brand Colors**:
   - Primary: `#0F4C3A` (Pine) (Flutter: `primaryColor` from `core/theme/colors.dart`)
   - Accent: `#22C55E` (Emerald); text-safe emerald on white is `#16A34A`
   - Store accent: `#F6C667` (Gold) — offers, ratings, badges
   - Secondary / Ink: `#1F2937` (Flutter: `secondaryColor`)
   - Do NOT introduce unapproved shades of pink, purple, or generic blues. The old Crimson `#B52046` is retired.
2. **Typography**:
   - Arabic text must use font-family `'Tajawal'` (fallback `'Cairo'`).
   - English text must use font-family `'Inter'` (`'Space Grotesk'` for display / wordmark only).
   - Never use generic serif fonts for UI elements. Old fonts `Rubik` / `Poppins` are retired.
3. **Bilingual / RTL**:
   - Use logical properties in CSS: `margin-inline-start`, `padding-inline-end`, `inset-inline-start`.
   - In Flutter, use `EdgeInsetsDirectional` instead of `EdgeInsets.only(left: ...)`.
4. **Icons & Logos**:
   - Use `backend/public/assets/brand/logo.svg` (`/assets/brand/logo.svg`) for brand banners and headers on light backgrounds.
   - Use `backend/public/assets/brand/logo-dark.svg` (`/assets/brand/logo-dark.svg`) on dark headers.
   - Use `backend/public/assets/brand/icon.svg` (`/assets/brand/icon.svg`) for marks and icons.
   - Use `backend/public/assets/img/default_full_logo.png` (`/assets/img/default_full_logo.png`) or `/assets/brand/icon.svg` as fallback images.
   - The store wordmark is always `MUQTARIN Store` / `مقترن ستور`, never `MUQTARIN` alone.
5. **Specification Reference**:
   - Consult `/DESIGN.md` for all measurements, paddings, and status colors.
   - **There is no `design-tokens.json` in this project and there must never be one** — the owner forbade it. Colours and fonts live in `/DESIGN.md`, in this rule file, and in CSS variables / Flutter theme files in code.
