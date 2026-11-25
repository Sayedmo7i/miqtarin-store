# Mo7i Store (متجر مُحي) - Design System & Visual Identity

> **Single Source of Truth** for UI/UX styling, typography, colors, and components across all platforms:
> - Laravel Backend & Admin Panel (`backend`)
> - Web Storefront (`backend/public/frontend`)
> - Flutter Customer App (`mobile-apps/customer`)
> - Flutter Seller App (`mobile-apps/seller`)
> - Flutter Delivery Boy App (`mobile-apps/deliveryboy`)

---

## 1. Brand Essence & Visual Identity

- **Official Name (EN)**: `Mo7i Store`
- **Official Name (AR)**: `متجر مُحي`
- **Tagline**: `المنصة الإلكترونية الموحدة • Unified E-Commerce Platform`
- **Design Philosophy**: High clarity, intentional minimalism, responsive Arabic-first RTL support, high contrast (WCAG AA compliant), avoiding AI generic slop.

### Official Brand Assets

All standard brand assets are stored in [`backend/public/assets/brand/`](backend/public/assets/brand/) and served publicly at `/assets/brand/`:

| Asset | Format | Repo Path | Web URL Path | Usage |
|---|---|---|---|---|
| **Full Brand Logo** | SVG | `backend/public/assets/brand/logo.svg` | `/assets/brand/logo.svg` | Web headers, Admin sidebar, Auth screens |
| **Full Brand Logo** | High-Res Raster | `backend/public/assets/brand/logo.jpg` | `/assets/brand/logo.jpg` | Fallbacks, email templates, social meta |
| **App Icon / Monogram** | SVG | `backend/public/assets/brand/icon.svg` | `/assets/brand/icon.svg` | Mobile app icons, Favicon, Touch icon |
| **App Icon / Monogram** | High-Res Raster | `backend/public/assets/brand/icon.jpg` | `/assets/brand/icon.jpg` | App splash screens, Store listing |

---

## 2. Color Palette & Semantic Tokens

### Primary & Secondary Brand Colors

```
┌──────────────────────────────────────┐     ┌──────────────────────────────────────┐
│  Mo7i Crimson                        │     │  Mo7i Charcoal                       │
│  HEX: #B52046                        │     │  HEX: #201A1A                        │
│  RGB: (181, 32, 70)                  │     │  RGB: (32, 26, 26)                   │
│  Flutter: Color(0xFFB52046)          │     │  Flutter: Color(0xFF201A1A)          │
│  Primary actions, active states      │     │  Headings, dark surfaces, contrast   │
└──────────────────────────────────────┘     └──────────────────────────────────────┘
```

- **`primary` (`#B52046`)**: Used for Call-to-Action (CTA) buttons, selected tabs, cart badge, active icons.
- **`primary_hover` (`#961837`)**: Darkened tone for mouse hover, active tap down.
- **`primary_light` (`#FDF2F4`)**: Soft pink-crimson tint for item count badges, selected card backgrounds.
- **`secondary` (`#201A1A`)**: High-contrast text, dark mode surfaces, navbar headers.

### Functional & Status Colors (Orders & Workflow)

| State | HEX | Flutter Value | Purpose |
|---|---|---|---|
| **Success / Delivered** | `#0DAE4A` | `Color(0xFF0DAE4A)` | Completed orders, active store, success toast |
| **Pending / Awaiting** | `#E69914` | `Color(0xFFE69914)` | Pending payment, order approval needed |
| **Received** | `#4285F4` | `Color(0xFF4285F4)` | Order received by merchant |
| **Processing** | `#736AFF` | `Color(0xFF736AFF)` | In packaging / warehouse prep |
| **Shipped** | `#C44D6B` | `Color(0xFFC44D6B)` | Order on the way with courier |
| **Error / Cancelled** | `#EE1C2F` | `Color(0xFFEE1C2F)` | Cancelled orders, destructive actions |
| **Info / Notice** | `#2196F3` | `Color(0xFF2196F3)` | System messages, tracking updates |

### Neutral Greys Scale
- **`neutral-50` (`#F8FAFC`)**: Page background (Light mode)
- **`neutral-100` (`#F1F5F9`)**: Card hover background, input field background
- **`neutral-200` (`#E2E8F0`)**: Card borders, dividers, subtle outlines
- **`neutral-500` (`#64748B`)**: Secondary labels, placeholder text, timestamps
- **`neutral-900` (`#0F172A`)**: Primary text in light mode

---

## 3. Bilingual Typography System

Typography must handle seamless switching between **Arabic (RTL)** and **English (LTR)**.

- **Arabic Font Family**: `'Cairo', 'Tajawal', sans-serif`
  - High legibility for numbers and Arabic diacritics.
- **English Font Family**: `'Rubik', 'Poppins', sans-serif`
  - Matches the geometric curved aesthetic of the Arabic font.

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
- **Brand Glow**: `0 6px 18px rgba(181, 32, 70, 0.24)` (used on primary CTA buttons)

---

## 5. Rules for AI Agents (AI Slop Prevention)

Every AI Agent writing frontend code, templates, or Flutter widgets in this repository MUST follow these invariants:

1. **NEVER invent new hex colors**: Always import or reference `primaryColor`, `secondaryColor`, or the semantic tokens defined above.
2. **NEVER hardcode English-only layout assumptions**: All layouts MUST support bidirectional text (`Directionality`, `start`/`end` instead of `left`/`right`).
3. **NEVER use low-contrast text**: Text on `#B52046` must be pure white `#FFFFFF`. Text on light background must be `#201A1A` or `#334155`.
4. **Button Height Standardization**:
   - Web buttons: Height `44px` or `48px` with radius `8px`.
   - Mobile Flutter buttons: Minimum tap target `48px`.
5. **No Broken Image Placeholders**: When an image asset is dynamic, always provide `backend/public/assets/img/default_full_logo.png` (`/assets/img/default_full_logo.png`) or `backend/public/assets/brand/icon.svg` (`/assets/brand/icon.svg`) as a graceful fallback.
