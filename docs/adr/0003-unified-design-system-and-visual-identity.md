# 0003 Unified Design System and Visual Identity

## Status
accepted

## Context
The platform previously contained scattered branding identifiers (CodeCanyon leftovers, eShop Pro, eZeeMart) and divergent styling patterns across the Laravel web/admin panels and the three Flutter mobile apps. Furthermore, AI agents generating UI code introduced arbitrary hex colors, inconsistent paddings, and font mismatches (AI slop).

## Decision
1. **Canonical Brand Identity**: Officially establish **Mo7i Store** (`متجر مُحي`) as the brand name, with unified logo and icon assets saved in `backend/public/assets/brand/` (served at `/assets/brand/`).
2. **Single Source of Truth**: Formulate `/DESIGN.md` and structured JSON tokens in `/tokens/design-tokens.json` to define all semantic colors (Primary: `#B52046`, Secondary: `#201A1A`), bilingual typography (`Cairo` for Arabic, `Rubik`/`Poppins` for English), and spacing scales.
3. **AI Agent Enforcement**: Codify `.agents/rules/design-system.md` to mandate semantic token reuse, WCAG AA contrast compliance, and bidirectional (RTL/LTR) layouts for any agent writing frontend or mobile code.
4. **Visual Identity Showcase**: Implement a dedicated `/identity` route displaying the visual tokens, logos, typography, and component specifications.

## Consequences
All future UI modifications across both Laravel views and Flutter applications must adhere to the design tokens. Hardcoding arbitrary colors, fonts, or English-only layouts is strictly prevented by the quality rules.
