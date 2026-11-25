# MUQTARIN Store (مقترن ستور) - Design System & Visual Identity


> **Single Source of Truth** for UI/UX styling, typography, colors, and components across all platforms:

> - Laravel Backend & Admin Panel (`backend`)
> - Web Storefront (`backend/public/frontend`)
> - Flutter Customer App (`mobile-apps/customer`)
> - Flutter Seller App (`mobile-apps/seller`)
> - Flutter Delivery Boy App (`mobile-apps/deliveryboy`)


---

## 1. Brand Essence & Visual Identity

- **Official Name (EN)**: `MUQTARIN Store`
- **Official Name (AR)**: `مقترن ستور`
- **Tagline**: `نربط الفكرة بالتنفيذ • Connecting Ideas to Execution`
- **Design Philosophy**: High clarity, intentional minimalism, responsive Arabic-first RTL support, high contrast (WCAG AA compliant), avoiding AI generic slop.
- **Brand reference**: MUQTARIN brand guide — https://app.vendoratravel.com/ — philosophy: الاقتران (coupling idea to execution).
- **Approved logo (2026-11-26)**: the official MUQTARIN interlocking symbol, unmodified. Dark lockup `logo-dark.svg` is the approved primary lockup; light lockup `logo.svg` is its companion for light backgrounds. The symbol itself has no gold and no text — gold is the *store* accent in UI only.

### Official Brand Assets

All standard brand assets are stored in [`backend/public/assets/brand/`](backend/public/assets/brand/) and served publicly at `/assets/brand/`:

| Asset | Format | Repo Path | Web URL Path | Usage |
|---|---|---|---|---|
| **Full Brand Logo (light)** | SVG | `backend/public/assets/brand/logo.svg` | `/assets/brand/logo.svg` | Web headers, Admin sidebar, Auth screens (light) |
| **Full Brand Logo (dark)** | SVG | `backend/public/assets/brand/logo-dark.svg` | `/assets/brand/logo-dark.svg` | Dark headers, dark mode |
| **App Icon / Symbol** | SVG | `backend/public/assets/brand/icon.svg` | `/assets/brand/icon.svg` | Mobile app icons, Favicon, Touch icon |
| **App Icon / Symbol** | High-Res Raster | `backend/public/assets/brand/icon.jpg` | `/assets/brand/icon.jpg` | App splash screens, Store listing — **STALE: old crimson artwork, regenerate from the new symbol before release** |
| **Full Brand Logo** | High-Res Raster | `backend/public/assets/brand/logo.jpg` | `/assets/brand/logo.jpg` | Fallbacks, email templates, social meta — **STALE: old crimson artwork, regenerate from the new logo before release** |

> **Do not confuse with the parent software company:** the store always uses the wordmark `MUQTARIN Store` / `مقترن ستور`, never `MUQTARIN` alone, and Gold `#F6C667` as the store accent. The parent site's symbol has no gold.

---

## 2. Color Palette & Semantic Tokens

> **There is no `design-tokens.json` in this project — by decision of the owner.** Colours and fonts live in this file, in `.agents/rules/design-system.md`, and in CSS variables / Flutter theme files in code. Do **not** create a `design-tokens.json` file anywhere.

### Primary & Secondary Brand Colors

```
┌──────────────────────────────────────┐     ┌──────────────────────────────────────┐
│  MUQTARIN Pine                        │     │  MUQTARIN Emerald                    │
│  HEX: #0F4C3A                        │     │  HEX: #22C55E                        │
│  RGB: (15, 76, 58)                   │     │  RGB: (34, 197, 94)                  │
│  Flutter: Color(0xFF0F4C3A)          │     │  Flutter: Color(0xFF22C55E)          │
│  Primary actions, headers            │     │  Accents, active states, links       │
└──────────────────────────────────────┘     └──────────────────────────────────────┘
```

- **`primary` (`#0F4C3A`)**: Used for Call-to-Action (CTA) buttons, selected tabs, cart badge, active icons, dark headers.
- **`primary_hover` (`#0A3528`)**: Darkened tone for mouse hover, active tap down.
- **`primary_light` (`#E6F2ED`)**: Soft pine tint for item count badges, selected card backgrounds.
- **`emerald` (`#22C55E`)**: Brand accent, success accents, the `Store` word in the wordmark.
- **`emerald_dark` (`#16A34A`)**: Text-safe emerald on white — use this for emerald *text*, never `#22C55E` at small sizes.
- **`emerald_hover` (`#15803D`)**: Darker emerald for hover states.
- **`gold` (`#F6C667`)**: The store accent — offers, ratings, badges, gold highlights. The parent company's symbol has no gold.
- **`gold_dark` (`#B45309`)**: Text-safe gold on white.
- **`secondary` / Ink (`#1F2937`)**: High-contrast text, headings, dark mode surfaces.
- **`canvas` (`#F8FAFC`)**: Page background (light mode). **`surface` (`#FFFFFF`)**: Card surfaces.

### CSS Variables

```css
:root {
  --pine: #0F4C3A; --pine-hover: #0A3528; --pine-light: #E6F2ED;
  --emerald: #22C55E; --emerald-dark: #16A34A; --emerald-hover: #15803D;
  --gold: #F6C667; --gold-dark: #B45309;
  --ink: #1F2937; --canvas: #F8FAFC; --surface: #FFFFFF; --muted: #64748B;
}
```

### Functional & Status Colors (Orders & Workflow)

| State | HEX | Flutter Value | Purpose |
|---|---|---|---|
| **Success / Delivered** | `#16A34A` | `Color(0xFF16A34A)` | Completed orders, active store, success toast |
| **Pending / Awaiting** | `#D97706` | `Color(0xFFD97706)` | Pending payment, order approval needed |
| **Received** | `#2563EB` | `Color(0xFF2563EB)` | Order received by merchant |
| **Processing** | `#7C3AED` | `Color(0xFF7C3AED)` | In packaging / warehouse prep |
| **Shipped** | `#0F766E` | `Color(0xFF0F766E)` | Order on the way with courier |
| **Error / Cancelled** | `#DC2626` | `Color(0xFFDC2626)` | Cancelled orders, destructive actions |
| **Info / Notice** | `#0284C7` | `Color(0xFF0284C7)` | System messages, tracking updates |

### Neutral Greys Scale

- **`neutral-50` (`#F8FAFC`)**: Page background (Light mode)
- **`neutral-100` (`#F1F5F9`)**: Card hover background, input field background
- **`neutral-200` (`#E2E8F0`)**: Card borders, dividers, subtle outlines
- **`neutral-500` (`#64748B`)**: Secondary labels, placeholder text, timestamps
- **`neutral-900` (`#0F172A`)**: Primary text in light mode

---

## 3. Bilingual Typography System

Typography must handle seamless switching between **Arabic (RTL)** and **English (LTR)**.

- **Arabic Font Family**: `'Tajawal', 'Cairo', sans-serif`
  - Tajawal is primary; Cairo is fallback only.
- **English Font Family**: `'Inter', sans-serif`
  - `'Space Grotesk'` for display / wordmark only.
  - Old fonts `Rubik` / `Poppins` / `Cairo`-as-primary are retired.

### Font Scale & Hierarchy

| Role | Size (px) | Size (rem) | Weight | Line Height | Usage |
|---|---|---|---|---|---|
| **H1 (Hero)** | `32px` | `2.0rem` | `Bold (700)` | `1.2` | Page hero titles, store header |
| **H2 (Section)** | `24px` | `1.5rem` | `Bold (700)` | `1.3` | Section headings, category titles |
| **H3 (Card Title)**| `20px` | `1.25rem` | `SemiBold (600)`| `1.4` | Product names, modal titles |
| **Body (Default)** | `16px` | `1.0rem` | `Regular (400)` | `1.5` | Descriptions, specs, form labels |
| **Body (Small)** | `14px` | `0.875rem`| `Medium (500)` | `1.4` | Meta descriptions, table rows |
| **Caption** | `12px` | `0.75rem` | `Regular (400)` | `1.3` | Timestamps, badge tags, fine print |

---

## 4. Spacing, Radiuses & Elevation

### Spacing Scale (8pt Grid)

All padding, margin, and gaps MUST use values from the spacing scale:

- `4px` (`xs`), `8px` (`sm`), `12px` (`md`), `16px` (`base`), `24px` (`lg`), `32px` (`xl`), `48px` (`2xl`)

### Border Radiuses

- **`sm` (`6px`)**: Badges, small pills, tags.
- **`md` (`8px`)**: Form inputs, standard buttons.
- **`lg` (`12px`)**: Product cards, modal dialogs, store banners.
- **`xl` (`16px`)**: Bottom sheets, floating navigation bars.
- **`full` (`9999px`)**: Round avatar pictures, circular icon buttons.

### Elevation & Shadows

- **Card Shadow**: `0 4px 12px rgba(0, 0, 0, 0.06)`
- **Brand Glow**: `0 6px 18px rgba(15, 76, 58, 0.24)` (used on primary CTA buttons)

---

## 5. Rules for AI Agents (AI Slop Prevention)

Every AI Agent writing frontend code, templates, or Flutter widgets in this repository MUST follow these invariants:

1. **NEVER invent new hex colors**: Always import or reference `primaryColor`, `secondaryColor`, or the semantic tokens defined above.
2. **NEVER hardcode English-only layout assumptions**: All layouts MUST support bidirectional text (`Directionality`, `start`/`end` instead of `left`/`right`).
3. **NEVER use low-contrast text**: Text on `#0F4C3A` must be pure white `#FFFFFF`. Text on light background must be `#1F2937` or `#334155`. Never use small white text on Emerald `#22C55E` — use Pine `#0F4C3A` or Emerald Dark `#16A34A` for text on light backgrounds.
4. **Button Height Standardization**:
   - Web buttons: Height `44px` or `48px` with radius `8px`.
   - Mobile Flutter buttons: Minimum tap target `48px`.
5. **No Broken Image Placeholders**: When an image asset is dynamic, always provide `backend/public/assets/img/default_full_logo.png`
(`/assets/img/default_full_logo.png`) or `backend/public/assets/brand/icon.svg` (`/assets/brand/icon.svg`) as a graceful fallback.
6. **NEVER create `design-tokens.json`**: the owner forbade it in the whole project. Colours and fonts live in this file, in `.agents/rules/design-system.md`, and in code variables.
