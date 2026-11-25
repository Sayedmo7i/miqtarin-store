# Global Preferences & Cross-Project Skill Memory

This document specifies the preference intelligence system, cross-project memory, and privacy guarantees implemented by MO7I Universal Intelligence.

---

## 1. Storage Location & Privacy Mandate

All persistent user preferences, history, and operational memory are stored locally in the user profile:
`~/.config/mo7i/universal-intelligence/` (fallback: `~/.mo7i/universal-intelligence/`)

### Strict Privacy Rules
1. **Never in Version Control**: Preference and memory files are strictly user-level and must NEVER be committed to repositories.
2. **Zero Secret Persistence**: Storing passwords, API keys, tokens, or credentials in preferences or knowledge files is strictly prohibited.
3. **Write Protection Gate**: The orchestrator reads preferences automatically, but **NEVER writes to `preferences.json` without explicit user confirmation.**

---

## 2. Preference Intelligence Dimensions

The orchestrator tracks preferences across 11 key engineering dimensions:
1. **Programming Languages** (e.g., TypeScript, Python, Rust, Go)
2. **Frameworks & Runtimes** (e.g., Next.js App Router, FastAPI, Bun, Node.js)
3. **Architecture Patterns** (e.g., Modular Monolith, Clean Architecture, Serverless)
4. **Development Tools** (e.g., pnpm, Turbopack, Biome, ESLint)
5. **AI Models & Providers** (e.g., Antigravity harness models, Pro for planning, Flash for execution)
6. **Image Generation Engines** (e.g., Nano Banana via Antigravity, native tool fallback)
7. **Design Systems & UI** (e.g., Tailwind CSS, Radix UI, shadcn/ui)
8. **Testing Approaches** (e.g., Vitest, Playwright, TDD)
9. **Documentation Standards** (e.g., GitHub Markdown, KaTeX math, Mermaid diagrams)
10. **Deployment Environments** (e.g., Cloudflare Workers, Vercel, Docker)
11. **Coding Conventions** (e.g., functional style, strict typing, immutable state)

---

## 3. Observed Behavior vs. Confirmed Preferences

The orchestrator carefully distinguishes between two classes of user data:
- **Observed Workflow Patterns**: Frequent tool choices observed during sessions (e.g., "User ran `pnpm test` in 4 consecutive sessions"). These are recorded in `skill-usage-history.json` as historical telemetry but **never** treated as hard constraints.
- **Confirmed Long-Term Preferences**: Explicit directives confirmed by the user (e.g., "Always use TypeScript for new files"). Only these are recorded in `preferences.json`.

*A single prompt instruction (e.g., "test this using Jest") must NEVER be permanently saved as a global preference without explicit confirmation.*

---

## 4. `preferences.json` Schema

```json
{
  "$schema": "https://json-schema.org/draft/2020-12/schema",
  "version": "1.1.0",
  "global": {
    "languages": ["typescript", "python"],
    "frameworks": ["nextjs-app-router", "react"],
    "tools": {
      "package_manager": "pnpm",
      "linter": "eslint",
      "test_runner": "vitest"
    },
    "model_preferences": {
      "preferred_provider": "antigravity",
      "planning_tier": "pro",
      "execution_tier": "flash",
      "image_generation": {
        "preferred_engine": "nano-banana",
        "fallback_engine": "native-generate-image"
      }
    },
    "governance": {
      "auto_cleanup_temporary": true,
      "enforce_test_evidence": true
    }
  },
  "projects": {
    "/root/github.com/Sayedmo7i/MO7I-Universal-Intelligence": {
      "governance": {
        "license": "MIT"
      }
    }
  }
}
```

---

## 5. Cross-Project Behavior & Isolation

The orchestrator automatically detects the active repository context:
1. **Reads Project Instructions First**: Local `AGENTS.md`, `CLAUDE.md`, or repository configs are ingested.
2. **No Preference Override**: A global preference (e.g., preferred test runner is Vitest) **never overrides** an explicit project configuration (e.g., repo already configured with Jest).
3. **Reusing Global Skills Safely**: Universal utilities (e.g., `find-skills`, `agent-browser`) installed globally are reused across projects without duplicating them in each repository.
4. **Preserving Project Isolation**: Project-scoped skills and temporary test fixtures remain strictly confined to their respective repository directories.
