# mo7i-store Glossary

Domain and technical terminology for the متجر مُحي (mo7i-store) platform.

## Architecture & Workflows

**Monorepo**:
A single version-controlled repository containing both the backend service and all mobile applications.
_Avoid_: Multi-repo

**Quality Gate**:
An automated set of verification steps (syntax check, linter, static analysis, unit/widget tests) that must pass before code can be merged into the main branch.
_Avoid_: Manual QA check

## Domain Roles

**Customer**:
An end user who browses products, places orders, and makes payments.
_Avoid_: Client, buyer

**Seller**:
A registered merchant who lists items, manages catalog products, and fulfills customer orders.
_Avoid_: Vendor, shopkeeper

**Delivery Boy**:
An agent assigned to pick up orders from sellers and deliver them to customers.
_Avoid_: Courier, driver

## Visual Identity & Design System

**Mo7i Store (متجر مُحي)**:
The official canonical brand identity, name, and trademark for the multi-vendor platform.
_Avoid_: eShop Pro, eShop Plus, eZeeMart

**Design System**:
The single source of truth (`DESIGN.md`) providing standardized components, color schemes, typography, and UX patterns across the entire monorepo.
_Avoid_: Ad-hoc styling, custom CSS per page

**Design Tokens**:
Platform-agnostic key-value variables (`tokens/design-tokens.json`) representing low-level visual values (colors, spacing, radiuses) consumed by both Web and Flutter.
_Avoid_: Hardcoded hex codes

**Semantic Color**:
A token name defined by its functional purpose in the UI (e.g. `primary`, `successStatusColor`, `errorColor`) rather than its physical hue.
_Avoid_: Raw color names like `red`, `dark_pink`
