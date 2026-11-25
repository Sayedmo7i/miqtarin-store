# AI Agent Rule: Mo7i Store Design System Enforcement

> This rule is automatically applied to all AI agents working on code in `mo7i-store`.

## Invariant Design Rules

1. **Brand Colors**:
   - Primary: `#B52046` (Flutter: `primaryColor` from `core/theme/colors.dart`)
   - Secondary: `#201A1A` (Flutter: `secondaryColor`)
   - Do NOT introduce unapproved shades of pink, purple, or generic blues.
2. **Typography**:
   - Arabic text must use font-family `'Cairo'` or `'Tajawal'`.
   - English text must use font-family `'Rubik'` or `'Poppins'`.
   - Never use generic serif fonts for UI elements.
3. **Bilingual / RTL**:
   - Use logical properties in CSS: `margin-inline-start`, `padding-inline-end`, `inset-inline-start`.
   - In Flutter, use `EdgeInsetsDirectional` instead of `EdgeInsets.only(left: ...)`.
4. **Icons & Logos**:
   - Use `backend/public/assets/brand/logo.svg` (`/assets/brand/logo.svg`) for brand banners and headers.
   - Use `backend/public/assets/brand/icon.svg` (`/assets/brand/icon.svg`) for marks and icons.
   - Use `backend/public/assets/img/default_full_logo.png` (`/assets/img/default_full_logo.png`) or `/assets/brand/icon.svg` as fallback images.
5. **Specification Reference**:
   - Consult `/DESIGN.md` and `/tokens/design-tokens.json` for all measurements, paddings, and status colors.
